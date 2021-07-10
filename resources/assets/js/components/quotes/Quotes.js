import React, { Component } from 'react'
import axios from 'axios'
import EditQuote from './edit/EditQuote'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import QuoteItem from './QuoteItem'
import QuoteFilters from './QuoteFilters'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import queryString from 'query-string'
import { getDefaultTableFields } from '../presenters/QuotePresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class Quotes extends Component {
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
                ignore: ['user_id', 'next_send_date', 'updated_at', 'use_inclusive_taxes', 'last_sent_date', 'uses_inclusive_taxes', 'line_items', 'next_sent_date', 'first_name', 'last_name', 'design_id', 'status_id', 'custom_surcharge_tax1', 'custom_surcharge_tax2'],
                viewMode: false,
                viewedId: null,
                title: null
            },
            quotes: [],
            cachedData: [],
            customers: [],
            custom_fields: [],
            bulk: [],
            dropdownButtonActions: ['email', 'download', 'clone_quote_to_invoice'],
            filters: {
                status_id: 'active',
                user_id: queryString.parse(this.props.location.search).user_id || '',
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

        this.updateInvoice = this.updateInvoice.bind(this)
        this.userList = this.userList.bind(this)
        this.filterInvoices = this.filterInvoices.bind(this)
    }

    componentDidMount () {
        this.getCustomers()
        this.getCustomFields()
    }

    updateInvoice (quotes, do_filter = false, filters = null) {
        const should_filter = !this.state.cachedData.length || do_filter === true
        const cachedData = !this.state.cachedData.length ? quotes : this.state.cachedData

        if (should_filter) {
            quotes = filterStatuses(quotes, '', this.state.filters)
        }

        this.setState({
            filters: filters !== null ? filters : this.state.filters,
            quotes: quotes,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(quotes.length / this.state.pageLimit)
            this.onPageChanged({ invoices: quotes, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    onPageChanged (data) {
        let { quotes, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            quotes = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = quotes.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    filterInvoices (filters) {
        this.setState({ filters: filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    userList (props) {
        const { pageLimit, custom_fields, customers, currentInvoices, cachedData } = this.state
        return <QuoteItem showCheckboxes={props.showCheckboxes} quotes={currentInvoices} customers={customers}
            show_list={props.show_list}
            entities={cachedData}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit}
            custom_fields={custom_fields}
            viewId={props.viewId}
            ignoredColumns={props.default_columns} updateInvoice={this.updateInvoice}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
    }

    getCustomers () {
        axios.get('/api/customers')
            .then((r) => {
                this.setState({
                    customers: r.data
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            })
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.Quote) {
            custom_fields[0] = all_custom_fields.Quote
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/Quote')
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
        const { cachedData, quotes, custom_fields, customers, view, filters, error, isOpen, error_message, success_message, show_success, currentInvoices, pageLimit } = this.state
        const { start_date, end_date } = this.state.filters
        const fetchUrl = `/api/quote?start_date=${start_date}&end_date=${end_date}`
        const addButton = customers.length ? <EditQuote
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
        const total = quotes.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <QuoteFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.updateInvoice}
                                    setFilterOpen={this.setFilterOpen.bind(this)} quotes={quotes}
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

                                    pageLimit={pageLimit}
                                    onPageChanged={this.onPageChanged.bind(this)}
                                    currentData={currentInvoices}
                                    hide_pagination={true}

                                    default_columns={getDefaultTableFields()}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    customers={customers}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Quote"
                                    bulk_save_url="/api/quote/bulk"
                                    view={view}
                                    columnMapping={{ status_id: 'STATUS', customer_id: 'CUSTOMER' }}
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
