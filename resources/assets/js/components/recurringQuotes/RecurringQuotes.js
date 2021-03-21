import React, { Component } from 'react'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import RecurringQuoteItem from './RecurringQuoteItem'
import RecurringQuoteFilters from './RecurringQuoteFilters'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import CustomerRepository from '../repositories/CustomerRepository'
import QuoteRepository from '../repositories/QuoteRepository'
import queryString from 'query-string'
import UpdateRecurringQuote from './edit/UpdateRecurringQuote'
import { getDefaultTableFields } from '../presenters/RecurringQuotePresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class RecurringQuotes extends Component {
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
            cachedData: [],
            custom_fields: [],
            customers: [],
            allQuotes: [],
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
            entity_type: queryString.parse(this.props.location.search).entity_type || false

        }

        this.ignore = []

        this.updateInvoice = this.updateInvoice.bind(this)
        this.userList = this.userList.bind(this)
        this.filterInvoices = this.filterInvoices.bind(this)
        this.getQuotes = this.getQuotes.bind(this)
    }

    componentDidMount () {
        this.getCustomers()
        this.getQuotes()
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

    getQuotes () {
        const quoteRepository = new QuoteRepository()
        quoteRepository.get().then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ allQuotes: response }, () => {
                console.log('allQuotes', this.state.allQuotes)
            })
        })
    }

    filterInvoices (filters) {
        this.setState({ filters: filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    userList (props) {
        const { pageLimit, custom_fields, customers, allQuotes, currentInvoices, cachedData } = this.state
        return <RecurringQuoteItem showCheckboxes={props.showCheckboxes} allQuotes={allQuotes}
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

    renderErrorFor () {

    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.RecurringQuote) {
            custom_fields[0] = all_custom_fields.RecurringQuote
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/RecurringQuote')
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
        const { cachedData, invoices, custom_fields, customers, allQuotes, view, filters, error, isOpen, error_message, success_message, show_success, currentInvoices, currentPage, totalPages, pageLimit } = this.state
        const { start_date, end_date } = this.state.filters
        const fetchUrl = `/api/recurring-quote?start_date=${start_date}&end_date=${end_date}`
        const addButton = customers.length ? <UpdateRecurringQuote
            allQuotes={allQuotes}
            entity_id={this.state.entity_id}
            entity_type={this.state.entity_type}
            custom_fields={custom_fields}
            customers={customers}
            invoice={{}}
            add={true}
            action={this.updateInvoice}
            invoices={cachedData}
            modal={true}
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
                                <RecurringQuoteFilters
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
                                    entity_type="RecurringQuote"
                                    bulk_save_url="/api/recurring-quote/bulk"
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
