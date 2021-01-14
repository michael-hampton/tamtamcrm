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

export default class Payments extends Component {
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
                alert('error')
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
                alert('error')
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
                alert('error')
            }

            this.setState({ customers: response }, () => {
                console.log('customers', this.state.customers)
            })
        })
    }

    updateCustomers (payments) {
        const cachedData = !this.state.cachedData.length ? payments : this.state.cachedData
        this.setState({
            payments: payments,
            cachedData: cachedData
        })
    }

    filterPayments (filters) {
        this.setState({ filters: filters })
    }

    customerList (props) {
        const { payments, custom_fields, invoices, credits, customers } = this.state
        return <PaymentItem showCheckboxes={props.showCheckboxes} payments={payments} customers={customers}
            show_list={props.show_list}
            viewId={props.viewId}
            credits={credits}
            invoices={invoices} custom_fields={custom_fields}
            ignoredColumns={props.ignoredColumns} updateCustomers={this.updateCustomers}
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
        const { payments, custom_fields, invoices, credits, view, filters, customers, error, isOpen, error_message, success_message, show_success } = this.state
        const { status_id, searchText, customer_id, gateway_id, start_date, end_date } = this.state.filters
        const fetchUrl = `/api/payments?search_term=${searchText}&status=${status_id}&customer_id=${customer_id}&gateway_id=${gateway_id}&start_date=${start_date}&end_date=${end_date}`
        const addButton = invoices.length ? <AddPayment
            custom_fields={custom_fields}
            invoices={invoices}
            credits={credits}
            action={this.updateCustomers}
            payments={payments}
        /> : null
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'

        return <Row>
            <div className="col-12">
                <div className="topbar">
                    <Card>
                        <CardBody>
                            <PaymentFilters setFilterOpen={this.setFilterOpen.bind(this)} customers={customers}
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
                                default_columns={getDefaultTableFields()}
                                setSuccess={this.setSuccess.bind(this)}
                                setError={this.setError.bind(this)}
                                customers={customers}
                                dropdownButtonActions={this.state.dropdownButtonActions}
                                entity_type="Payment"
                                bulk_save_url="/api/payment/bulk"
                                view={view}
                                columnMapping={{ customer_id: 'CUSTOMER' }}
                                // order={['id', 'number', 'date', 'customer_name', 'total', 'balance', 'status_id']}
                                disableSorting={['id']}
                                defaultColumn='number'
                                userList={this.customerList}
                                fetchUrl={fetchUrl}
                                updateState={this.updateCustomers}
                            />
                        </CardBody>
                    </Card>
                </div>
            </div>
        </Row>
    }
}
