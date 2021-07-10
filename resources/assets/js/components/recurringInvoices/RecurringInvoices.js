import React, { Component } from 'react'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import RecurringInvoiceItem from './RecurringInvoiceItem'
import RecurringInvoiceFilters from './RecurringInvoiceFilters'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import CustomerRepository from '../repositories/CustomerRepository'
import InvoiceRepository from '../repositories/InvoiceRepository'
import queryString from 'query-string'
import UpdateRecurringInvoice from './edit/UpdateRecurringInvoice'
import { getDefaultTableFields } from '../presenters/RecurringInvoicePresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class RecurringInvoices extends Component {
    constructor (props) {
        super(props)
        this.state = {
            currentPage: 1,
            totalPages: null,
            pageLimit: !localStorage.getItem('number_of_rows') ? Math.ceil(window.innerHeight / 90) : localStorage.getItem('number_of_rows'),
            currentInvoices: [],
            isMobile: window.innerWidth <= 768,
            isOpen: window.innerWidth > 670,
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.success_message,
            per_page: 5,
            view: {
                ignore: [],
                viewMode: false,
                viewedId: null,
                title: null
            },
            invoices: [],
            allInvoices: [],
            cachedData: [],
            customers: [],
            bulk: [],
            dropdownButtonActions: ['download', 'start_recurring', 'stop_recurring'],
            filters: {
                user_id: queryString.parse(this.props.location.search).user_id || '',
                status_id: 'active',
                customer_id: queryString.parse(this.props.location.search).customer_id || '',
                project_id: queryString.parse(this.props.location.search).project_id || '',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            showRestoreButton: false,
            entity_id: queryString.parse(this.props.location.search).entity_id || false,
            entity_type: queryString.parse(this.props.location.search).entity_type || false,
            custom_fields: []
        }

        this.ignore = []

        this.updateInvoice = this.updateInvoice.bind(this)
        this.userList = this.userList.bind(this)
        this.filterInvoices = this.filterInvoices.bind(this)
        this.getInvoices = this.getInvoices.bind(this)
    }

    componentDidMount () {
        this.getCustomers()
        this.getCustomFields()
        this.getInvoices()
    }

    getInvoices () {
        const invoiceRepository = new InvoiceRepository()
        invoiceRepository.get().then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ allInvoices: response }, () => {
                console.log('allInvoices', this.state.allInvoices)
            })
        })
    }

    onPageChanged (data) {
        let { invoices, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            invoices = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = invoices.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    updateInvoice (invoices, do_filter = false, filters = null) {
        const should_filter = !this.state.cachedData.length || do_filter === true
        const cachedData = !this.state.cachedData.length ? invoices : this.state.cachedData

        if (should_filter) {
            invoices = filterStatuses(invoices, '', this.state.filters)
        }

        this.setState({
            filters: filters !== null ? filters : this.state.filters,
            invoices: invoices,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(invoices.length / this.state.pageLimit)
            this.onPageChanged({ invoices: invoices, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    filterInvoices (filters) {
        this.setState({ filters: filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    userList (props) {
        const { pageLimit, custom_fields, customers, allInvoices, currentInvoices, cachedData } = this.state

        return <RecurringInvoiceItem showCheckboxes={props.showCheckboxes} allInvoices={allInvoices}
            invoices={currentInvoices}
            show_list={props.show_list} entities={cachedData}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit}
            viewId={props.viewId}
            customers={customers} custom_fields={custom_fields}
            ignoredColumns={props.default_columns} updateInvoice={this.updateInvoice}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.RecurringInvoice) {
            custom_fields[0] = all_custom_fields.RecurringInvoice
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/RecurringInvoice')
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

    getCustomers () {
        const customerRepository = new CustomerRepository()
        customerRepository.get().then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ customers: response }, () => {
                console.log('customers', this.state.customers)
            })
        })
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
        const { cachedData, invoices, custom_fields, customers, allInvoices, view, filters, error, isOpen, error_message, success_message, show_success, currentInvoices, pageLimit } = this.state
        const { start_date, end_date } = this.state.filters
        const fetchUrl = `/api/recurring-invoice?start_date=${start_date}&end_date=${end_date}`
        const addButton = customers.length && allInvoices.length
            ? <UpdateRecurringInvoice
                allInvoices={allInvoices}
                custom_fields={custom_fields}
                customers={customers}
                modal={true}
                add={true}
                invoice={{}}
                invoice_id={null}
                action={this.updateInvoice}
                invoices={cachedData}
            /> : null
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = invoices.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <RecurringInvoiceFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.updateInvoice}
                                    customers={customers}
                                    setFilterOpen={this.setFilterOpen.bind(this)}
                                    invoices={invoices}
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

                                    pageLimit={pageLimit}
                                    onPageChanged={this.onPageChanged.bind(this)}
                                    currentData={currentInvoices}
                                    hide_pagination={true}

                                    default_columns={getDefaultTableFields()}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    customers={this.state.customers}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="RecurringInvoice"
                                    bulk_save_url="/api/recurring-invoice/bulk"
                                    view={view}
                                    columnMapping={{
                                        status_id: 'status',
                                        customer_id: 'CUSTOMER',
                                        number_of_occurrrances: translations.cycles_remaining
                                    }}
                                    disableSorting={['id']}
                                    defaultColumn='number'
                                    userList={this.userList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.updateInvoice}
                                />

                                {total > 0 &&
                                <div className="d-flex flex-row py-4 align-items-center">
                                    <PaginationNew totalRecords={total} pageLimit={parseInt(pageLimit)}
                                        pageNeighbours={1} onPageChanged={this.onPageChanged.bind(this)}/>
                                </div>
                                }
                            </CardBody>
                        </Card>
                    </div>
                </div>
            </Row>
        )
    }
}
