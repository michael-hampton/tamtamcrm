import React, { Component } from 'react'
import DataTable from '../common/DataTable'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import CreditFilters from './CreditFilters'
import CreditItem from './CreditItem'
import EditCredit from './edit/EditCredit'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import CustomerRepository from '../repositories/CustomerRepository'
import queryString from 'query-string'
import { getDefaultTableFields } from '../presenters/CreditPresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class Credits extends Component {
    constructor (props) {
        super(props)
        this.state = {
            currentInvoices: [],
            currentPage: 1,
            totalPages: null,
            pageLimit: !localStorage.getItem('number_of_rows') ? Math.ceil(window.innerHeight / 90) : localStorage.getItem('number_of_rows'),
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
            credits: [],
            cachedData: [],
            customers: [],
            custom_fields: [],
            dropdownButtonActions: ['download', 'email'],
            bulk: [],
            filters: {
                status_id: 'active',
                customer_id: queryString.parse(this.props.location.search).customer_id || '',
                user_id: queryString.parse(this.props.location.search).user_id || '',
                project_id: queryString.parse(this.props.location.search).project_id || '',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            showRestoreButton: false,
            entity_id: queryString.parse(this.props.location.search).entity_id || false,
            entity_type: queryString.parse(this.props.location.search).entity_type || false
        }

        this.updateCustomers = this.updateCustomers.bind(this)
        this.customerList = this.customerList.bind(this)
        this.filterCredits = this.filterCredits.bind(this)
    }

    componentDidMount () {
        this.getCustomers()
        this.getCustomFields()
    }

    onPageChanged (data) {
        let { credits, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            credits = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = credits.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    filterCredits (filters) {
        this.setState({ filters: filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
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

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.Credit) {
            custom_fields[0] = all_custom_fields.Credit
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/Credit')
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

    updateCustomers (credits, do_filter = false, filters = null) {
        const should_filter = !this.state.cachedData.length || do_filter === true
        const cachedData = !this.state.cachedData.length ? credits : this.state.cachedData

        if (should_filter) {
            credits = filterStatuses(credits, '', this.state.filters)
        }

        this.setState({
            filters: filters !== null ? filters : this.state.filters,
            credits: credits,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(credits.length / this.state.pageLimit)
            this.onPageChanged({ invoices: credits, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    customerList (props) {
        const { pageLimit, customers, custom_fields, currentInvoices, cachedData } = this.state
        return <CreditItem showCheckboxes={props.showCheckboxes} credits={currentInvoices} customers={customers}
            show_list={props.show_list}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit} entities={cachedData}
            custom_fields={custom_fields}
            viewId={props.viewId}
            ignoredColumns={props.default_columns} updateCustomers={this.updateCustomers}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
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
        const { cachedData, customers, credits, custom_fields, view, filters, error, isOpen, error_message, success_message, show_success, currentInvoices, pageLimit } = this.state
        const fetchUrl = `/api/credits?start_date=${this.state.filters.start_date}&end_date=${this.state.filters.end_date}`
        const addButton = customers.length ? <EditCredit
            entity_id={this.state.entity_id}
            entity_type={this.state.entity_type}
            custom_fields={custom_fields}
            customers={customers}
            add={true}
            action={this.updateCustomers}
            credits={cachedData}
            modal={true}
        /> : null
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = credits.length

        return customers.length ? (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <CreditFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.updateCustomers}
                                    setFilterOpen={this.setFilterOpen.bind(this)} credits={credits}
                                    customers={customers}
                                    filters={filters} filter={this.filterCredits}
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
                                    customers={customers}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Credit"
                                    bulk_save_url="/api/credit/bulk"
                                    view={view}
                                    columnMapping={{ customer_id: 'CUSTOMER', status_id: 'status' }}
                                    disableSorting={['id']}
                                    defaultColumn='number'
                                    userList={this.customerList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.updateCustomers}
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
        ) : null
    }
}
