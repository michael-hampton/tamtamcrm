import React, { Component } from 'react'
import DataTable from '../common/DataTable'
import AddPayment from './edit/AddPayment'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import PaymentItem from './PaymentItem'
import PaymentFilters from './PaymentFilters'
import queryString from 'query-string'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import CustomerRepository from '../repositories/CustomerRepository'
import CreditRepository from '../repositories/CreditRepository'
import InvoiceRepository from '../repositories/InvoiceRepository'
import { getDefaultTableFields } from '../presenters/PaymentPresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class Payments extends Component {
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
                ignore: ['paymentables', 'assigned_to', 'id', 'customer', 'invoice_id', 'deleted_at', 'customer_id', 'refunded', 'task_id', 'company_id'],
                viewMode: false,
                viewedId: null,
                title: null
            },
            payments: [],
            cachedData: [],
            custom_fields: [],
            dropdownButtonActions: ['download'],
            bulk: [],
            filters: {
                status_id: 'active',
                customer_id: queryString.parse(this.props.location.search).customer_id || '',
                searchText: queryString.parse(this.props.location.search).number || '',
                start_date: '',
                end_date: '',
                gateway_id: queryString.parse(this.props.location.search).gateway_id || ''
            },
            invoices: [],
            credits: [],
            customers: [],
            showRestoreButton: false
        }

        this.updateCustomers = this.updateCustomers.bind(this)
        this.customerList = this.customerList.bind(this)
        this.getInvoices = this.getInvoices.bind(this)
        this.getCredits = this.getCredits.bind(this)
        this.filterPayments = this.filterPayments.bind(this)
    }

    componentDidMount () {
        this.getInvoices()
        this.getCredits()
        this.getCustomers()
        this.getCustomFields()
    }

    onPageChanged (data) {
        let { payments, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            payments = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = payments.slice(offset, offset + pageLimit)

        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.Payment) {
            custom_fields[0] = all_custom_fields.Payment
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/Payment')
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

    getInvoices () {
        const invoiceRepository = new InvoiceRepository()
        invoiceRepository.get().then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ invoices: response }, () => {
                console.log('invoices', this.state.invoices)
            })
        })
    }

    getCredits () {
        const creditRepository = new CreditRepository()
        creditRepository.get().then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ credits: response }, () => {
                console.log('credits', this.state.credits)
            })
        })
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

    updateCustomers (payments, do_filter = false, filters = null) {
        const should_filter = !this.state.cachedData.length || do_filter === true
        const cachedData = !this.state.cachedData.length ? payments : this.state.cachedData

        if (should_filter) {
            payments = filterStatuses(payments, '', this.state.filters)
        }

        this.setState({
            filters: filters !== null ? filters : this.state.filters,
            payments: payments,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(payments.length / this.state.pageLimit)
            this.onPageChanged({
                invoices: payments,
                currentPage: this.state.currentPage,
                totalPages: totalPages
            }, should_filter)
        })
    }

    filterPayments (filters) {
        this.setState({ filters: filters })
    }

    customerList (props) {
        const { pageLimit, custom_fields, invoices, credits, customers, currentInvoices, cachedData } = this.state
        return <PaymentItem showCheckboxes={props.showCheckboxes} payments={currentInvoices} customers={customers}
            show_list={props.show_list} entities={cachedData}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit}
            viewId={props.viewId}
            credits={credits}
            invoices={invoices} custom_fields={custom_fields}
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
        const { cachedData, payments, custom_fields, invoices, credits, view, filters, customers, error, isOpen, error_message, success_message, show_success, currentInvoices, currentPage, totalPages, pageLimit } = this.state
        const { gateway_id, start_date, end_date } = this.state.filters
        const fetchUrl = `/api/payments?gateway_id=${gateway_id}&start_date=${start_date}&end_date=${end_date}`
        const addButton = invoices.length ? <AddPayment
            custom_fields={custom_fields}
            invoices={invoices}
            credits={credits}
            action={this.updateCustomers}
            payments={cachedData}
        /> : null
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = payments.length

        return <Row>
            <div className="col-12">
                <div className="topbar">
                    <Card>
                        <CardBody>
                            <PaymentFilters
                                pageLimit={pageLimit}
                                cachedData={cachedData}
                                updateList={this.updateCustomers}
                                setFilterOpen={this.setFilterOpen.bind(this)} customers={customers}
                                payments={payments} invoices={invoices}
                                filters={filters} filter={this.filterPayments}
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
                                entity_type="Payment"
                                bulk_save_url="/api/payment/bulk"
                                view={view}
                                columnMapping={{ customer_id: 'CUSTOMER', status_id: 'status' }}
                                // order={['id', 'number', 'date', 'customer_name', 'total', 'balance', 'status_id']}
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
    }
}
