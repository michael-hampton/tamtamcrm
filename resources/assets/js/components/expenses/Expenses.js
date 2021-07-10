import React, { Component } from 'react'
import DataTable from '../common/DataTable'
import AddExpense from './edit/AddExpense'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import ExpenseFilters from './ExpenseFilters'
import ExpenseItem from './ExpenseItem'
import queryString from 'query-string'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import CustomerRepository from '../repositories/CustomerRepository'
import CompanyRepository from '../repositories/CompanyRepository'
import { getDefaultTableFields } from '../presenters/ExpensePresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class Excuspenses extends Component {
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
            expenses: [],
            companies: [],
            cachedData: [],
            bulk: [],
            dropdownButtonActions: ['generate_invoice'],
            filters: {
                status_id: 'active',
                user_id: queryString.parse(this.props.location.search).user_id || '',
                customer_id: queryString.parse(this.props.location.search).customer_id || '',
                expense_category_id: queryString.parse(this.props.location.search).category_id || '',
                searchText: queryString.parse(this.props.location.search).number || '',
                company_id: queryString.parse(this.props.location.search).company_id || '',
                start_date: '',
                end_date: ''
            },
            custom_fields: [],
            customers: [],
            showRestoreButton: false,
            entity_id: queryString.parse(this.props.location.search).entity_id || false,
            entity_type: queryString.parse(this.props.location.search).entity_type || false
        }

        this.updateExpenses = this.updateExpenses.bind(this)
        this.expenseList = this.expenseList.bind(this)
        this.filterExpenses = this.filterExpenses.bind(this)
        this.getCompanies = this.getCompanies.bind(this)
        this.getCustomers = this.getCustomers.bind(this)
    }

    componentDidMount () {
        this.getCustomers()
        this.getCustomFields()
        this.getCompanies()
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    getCompanies () {
        const companyRepository = new CompanyRepository()
        companyRepository.get().then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ companies: response }, () => {
                console.log('companies', this.state.companies)
            })
        })
    }

    filterExpenses (filters) {
        this.setState({ filters: filters })
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

    onPageChanged (data) {
        let { expenses, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            expenses = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = expenses.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    updateExpenses (expenses, do_filter = false, filters = null) {
        const should_filter = !this.state.cachedData.length || do_filter === true
        const cachedData = !this.state.cachedData.length ? expenses : this.state.cachedData

        if (should_filter) {
            expenses = filterStatuses(expenses, '', this.state.filters)
        }

        this.setState({
            filters: filters !== null ? filters : this.state.filters,
            expenses: expenses,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(expenses.length / this.state.pageLimit)
            this.onPageChanged({ invoices: expenses, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    expenseList (props) {
        const { pageLimit, customers, custom_fields, companies, currentInvoices, cachedData } = this.state
        return <ExpenseItem showCheckboxes={props.showCheckboxes} expenses={currentInvoices} customers={customers}
            show_list={props.show_list} entities={cachedData}
            pageLimit={pageLimit}
            viewId={props.viewId}
            companies={companies}
            onPageChanged={this.onPageChanged.bind(this)}
            custom_fields={custom_fields}
            ignoredColumns={props.default_columns} updateExpenses={this.updateExpenses}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.Expense) {
            custom_fields[0] = all_custom_fields.Expense
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/Expense')
            .then((r) => {
                this.setState({
                    custom_fields: r.data.fields && Object.keys(r.data.fields).length ? r.data.fields : []
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
        const { cachedData, expenses, customers, custom_fields, view, companies, error, isOpen, error_message, success_message, show_success, currentInvoices, pageLimit } = this.state
        const { start_date, end_date } = this.state.filters
        const fetchUrl = `/api/expenses?start_date=${start_date}&end_date=${end_date}`
        const addButton = customers.length ? <AddExpense
            entity_id={this.state.entity_id}
            entity_type={this.state.entity_type}
            custom_fields={custom_fields}
            customers={customers}
            companies={companies}
            action={this.updateExpenses}
            expenses={cachedData}
        /> : null
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable-large fixed-margin-datatable-large-mobile'
        const total = expenses.length

        return customers.length ? (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <ExpenseFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.updateExpenses}
                                    setFilterOpen={this.setFilterOpen.bind(this)} customers={customers}
                                    expenses={expenses} companies={companies}
                                    filters={this.state.filters} filter={this.filterExpenses}
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
                                    companies={companies}
                                    customers={customers}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Expense"
                                    bulk_save_url="/api/expense/bulk"
                                    view={view}
                                    columnMapping={{
                                        customer_id: 'CUSTOMER',
                                        company_id: 'COMPANY',
                                        status_id: 'status'
                                    }}
                                    disableSorting={['id']}
                                    defaultColumn='amount'
                                    userList={this.expenseList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.updateExpenses}
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
