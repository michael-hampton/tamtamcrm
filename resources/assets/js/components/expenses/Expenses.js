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

export default class Expenses extends Component {
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
                ignore: ['user_id', 'assigned_to', 'company_id', 'customer_id', 'invoice_id', 'bank_id', 'deleted_at', 'customer_id', 'invoice_currency_id', 'payment_type_id', 'currency_id', 'recurring_expense_id', 'updated_at', 'expense_category_id'],
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
                alert('error')
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
                alert('error')
            }

            this.setState({ customers: response }, () => {
                console.log('customers', this.state.customers)
            })
        })
    }

    updateExpenses (expenses) {
        const cachedData = !this.state.cachedData.length ? expenses : this.state.cachedData
        this.setState({
            expenses: expenses,
            cachedData: cachedData
        })
    }

    expenseList (props) {
        const { expenses, customers, custom_fields, companies } = this.state
        return <ExpenseItem showCheckboxes={props.showCheckboxes} expenses={expenses} customers={customers}
            show_list={props.show_list}
            viewId={props.viewId}
            companies={companies}
            custom_fields={custom_fields}
            ignoredColumns={props.ignoredColumns} updateExpenses={this.updateExpenses}
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
        const { expenses, customers, custom_fields, view, companies, error, isOpen, error_message, success_message, show_success } = this.state
        const { searchText, status_id, customer_id, company_id, start_date, end_date, expense_category_id, user_id } = this.state.filters
        const fetchUrl = `/api/expenses?search_term=${searchText}&status=${status_id}&user_id=${user_id}&customer_id=${customer_id}&company_id=${company_id}&start_date=${start_date}&end_date=${end_date}&expense_category_id=${expense_category_id}`
        const addButton = customers.length ? <AddExpense
            entity_id={this.state.entity_id}
            entity_type={this.state.entity_type}
            custom_fields={custom_fields}
            customers={customers}
            companies={companies}
            action={this.updateExpenses}
            expenses={expenses}
        /> : null
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable-large fixed-margin-datatable-large-mobile'

        return customers.length ? (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <ExpenseFilters setFilterOpen={this.setFilterOpen.bind(this)} customers={customers}
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
                                    default_columns={getDefaultTableFields()}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    customers={customers}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Expense"
                                    bulk_save_url="/api/expense/bulk"
                                    view={view}
                                    columnMapping={{ customer_id: 'CUSTOMER', company_id: 'COMPANY' }}
                                    disableSorting={['id']}
                                    defaultColumn='amount'
                                    userList={this.expenseList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.updateExpenses}
                                />
                            </CardBody>
                        </Card>
                    </div>
                </div>
            </Row>
        ) : null
    }
}
