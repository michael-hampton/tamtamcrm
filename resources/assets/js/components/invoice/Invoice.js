import React, { Component } from 'react'
import queryString from 'query-string'
import EditInvoice from './edit/EditInvoice'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import InvoiceItem from './InvoiceItem'
import InvoiceFilters from './InvoiceFilters'
import Drawer from '@material-ui/core/Drawer'
import Button from '@material-ui/core/Button'
import List from '@material-ui/core/List'
import ListItem from '@material-ui/core/ListItem'
import ListItemText from '@material-ui/core/ListItemText'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import CustomerRepository from '../repositories/CustomerRepository'
import { getDefaultTableFields } from '../presenters/InvoicePresenter'

export default class Invoice extends Component {
    constructor (props) {
        super(props)

        this.state = {
            isMobile: window.innerWidth <= 768,
            isOpen: window.innerWidth > 670,
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.success_message,
            per_page: 5,
            view: {
                ignore: ['design_id', 'status_id', 'custom_surcharge_tax1', 'custom_surcharge_tax2'],
                viewMode: false,
                viewedId: null,
                title: null
            },
            invoices: [],
            cachedData: [],
            customers: [],
            bulk: [],
            dropdownButtonActions: ['email', 'download', 'cancel', 'archive', 'reverse', 'delete'],
            custom_fields: [],
            filters: {
                status_id: '',
                id: queryString.parse(this.props.location.search).id || '',
                user_id: queryString.parse(this.props.location.search).user_id || '',
                customer_id: queryString.parse(this.props.location.search).customer_id || '',
                project_id: queryString.parse(this.props.location.search).project_id || '',
                searchText: queryString.parse(this.props.location.search).number || '',
                start_date: '',
                end_date: ''
            },
            entity_id: queryString.parse(this.props.location.search).entity_id || false,
            entity_type: queryString.parse(this.props.location.search).entity_type || false,
            showRestoreButton: false,
            bottom_drawer_open: false
        }

        this.updateInvoice = this.updateInvoice.bind(this)
        this.userList = this.userList.bind(this)
        this.filterInvoices = this.filterInvoices.bind(this)
    }

    componentDidMount () {
        this.getCustomers()
        this.getCustomFields()
    }

    updateInvoice (invoices) {
        const cachedData = !this.state.cachedData.length ? invoices : this.state.cachedData
        this.setState({
            invoices: invoices,
            cachedData: cachedData
        })
    }

    filterInvoices (filters) {
        this.setState({ filters: filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    userList (props) {
        const { invoices, customers, custom_fields } = this.state
        return <InvoiceItem showCheckboxes={props.showCheckboxes}
            show_list={props.show_list}
            invoices={invoices} customers={customers}
            custom_fields={custom_fields}
            ignoredColumns={props.ignoredColumns} updateInvoice={this.updateInvoice}
            viewId={props.viewId}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
    }

    getCustomers () {
        const customerRepository = new CustomerRepository()
        customerRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ customers: response }, () => {
                console.log('customers', this.state.customers)
            })
        })
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.Invoice) {
            custom_fields[0] = all_custom_fields.Invoice
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/Invoice')
            .then((r) => {
                this.setState({
                    custom_fields: r.data.fields
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            }) */
    }

    toggleDrawer () {
        this.setState({ bottom_drawer_open: !this.state.bottom_drawer_open })
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    setError (message = null) {
        this.setState({ error: true, error_message: message === null ? translations.unexpected_error : message })
    }

    setSuccess (message = null) {
        this.setState({
            show_success: true,
            success_message: message === null ? translations.success_message : message
        })
    }

    render () {
        const { invoices, customers, custom_fields, view, filters, error, isOpen, error_message, success_message, show_success } = this.state
        const { status_id, customer_id, searchText, start_date, end_date, project_id, user_id, id } = this.state.filters
        const fetchUrl = `/api/invoice?search_term=${searchText}&status=${status_id}&user_id=${user_id}&id=${id}&customer_id=${customer_id}&project_id=${project_id}&start_date=${start_date}&end_date=${end_date}`
        const addButton = this.state.customers.length ? <EditInvoice
            entity_id={this.state.entity_id}
            entity_type={this.state.entity_type}
            custom_fields={custom_fields}
            customers={customers}
            add={true}
            action={this.updateInvoice}
            invoices={invoices}
            modal={true}
        /> : null

        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <InvoiceFilters setFilterOpen={this.setFilterOpen.bind(this)} invoices={invoices}
                                    customers={customers}
                                    filters={filters} filter={this.filterInvoices}
                                    saveBulk={this.saveBulk}/>
                                {addButton}
                            </CardBody>
                        </Card>
                    </div>

                    {error &&
                    <Snackbar open={error} autoHideDuration={3000} onClose={this.handleClose.bind(this)}>
                        <Alert severity="danger">
                            {error_message}
                        </Alert>
                    </Snackbar>
                    }

                    {show_success &&
                    <Snackbar open={show_success} autoHideDuration={3000} onClose={this.handleClose.bind(this)}>
                        <Alert severity="success">
                            {success_message}
                        </Alert>
                    </Snackbar>
                    }

                    <div className={margin_class}>
                        <Card>
                            <CardBody>
                                <DataTable
                                    default_columns={getDefaultTableFields()}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    customers={customers}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Invoice"
                                    bulk_save_url="/api/invoice/bulk"
                                    view={view}
                                    columnMapping={{ customer_id: 'CUSTOMER' }}
                                    // order={['id', 'number', 'date', 'customer_name', 'total', 'balance', 'status_id']}
                                    disableSorting={['id']}
                                    defaultColumn='number'
                                    userList={this.userList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.updateInvoice}
                                />
                            </CardBody>
                        </Card>
                    </div>

                    <Button onClick={this.toggleDrawer.bind(this)}>bottom</Button>
                    <Drawer anchor="bottom" open={this.state.bottom_drawer_open}
                        onClose={this.toggleDrawer.bind(this)}>
                        <List>
                            {['All mail', 'Trash', 'Spam'].map((text, index) => (
                                <ListItem button key={text}>
                                    {/* <ListItemIcon></ListItemIcon> */}
                                    <ListItemText primary={text}/>
                                </ListItem>
                            ))}
                        </List>
                    </Drawer>
                </div>

            </Row>
        )
    }
}
