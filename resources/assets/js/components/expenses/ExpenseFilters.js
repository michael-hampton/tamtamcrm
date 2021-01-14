import React, { Component } from 'react'
import { Col, FormGroup, Row } from 'reactstrap'
import TableSearch from '../common/TableSearch'
import CustomerDropdown from '../common/dropdowns/CustomerDropdown'
import CompanyDropdown from '../common/dropdowns/CompanyDropdown'
import DateFilter from '../common/DateFilter'
import CsvImporter from '../common/CsvImporter'
import FilterTile from '../common/FilterTile'
import ExpenseCategoryDropdown from '../common/dropdowns/ExpenseCategoryDropdown'
import StatusDropdown from '../common/StatusDropdown'
import { expenseStatuses } from '../utils/_consts'

export default class ExpenseFilters extends Component {
    constructor (props) {
        super(props)
        this.state = {
            isOpen: false,
            dropdownButtonActions: ['download'],
            filters: {
                status_id: 'active',
                searchText: '',
                customer_id: '',
                expense_category_id: '',
                company_id: '',
                start_date: '',
                end_date: '',
                user_id: ''
            }
        }

        this.filterExpenses = this.filterExpenses.bind(this)
        this.getFilters = this.getFilters.bind(this)
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterExpenses (event) {
        if ('start_date' in event) {
            this.setState(prevState => ({
                filters: {
                    ...prevState.filters,
                    start_date: event.start_date,
                    end_date: event.end_date
                }
            }), () => this.props.filter(this.state.filters))
            return
        }

        const column = event.target.id
        const value = event.target.value

        if (value === 'all') {
            const updatedRowState = this.state.filters.filter(filter => filter.column !== column)
            this.setState({ filters: updatedRowState }, () => this.props.filter(this.state.filters))
            return true
        }

        this.setState(prevState => ({
            filters: {
                ...prevState.filters,
                [column]: value
            }
        }), () => this.props.filter(this.state.filters))

        return true
    }

    getFilters () {
        const { searchText, status_id, customer_id, company_id, start_date, end_date, expense_category_id, user_id } = this.state.filters
        return (
            <Row form>
                <Col md={2}>
                    <TableSearch onChange={this.filterExpenses}/>
                </Col>

                <Col md={3}>
                    <CustomerDropdown
                        customers={this.props.customers}
                        customer={this.props.filters.customer_id}
                        handleInputChanges={this.filterExpenses}
                        name="customer_id"
                    />
                </Col>

                <Col md={3}>
                    <CompanyDropdown
                        companies={this.props.companies}
                        company={this.props.filters.company_id}
                        handleInputChanges={this.filterExpenses}
                        name="company_id"
                    />
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <StatusDropdown filterStatus={this.filterExpenses}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={1} className="mt-3 mt-md-0">
                    <CsvImporter statuses={expenseStatuses} companies={this.props.companies}
                        customers={this.props.customers} filename="expenses.csv"
                        url={`/api/expenses?search_term=${searchText}&status=${status_id}&customer_id=${customer_id}&company_id=${company_id}&expense_category_id=${expense_category_id}&user_id=${user_id}&start_date=${start_date}&end_date=${end_date}&page=1&per_page=5000`}/>
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <DateFilter onChange={this.filterExpenses}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <FormGroup>
                        <ExpenseCategoryDropdown
                            name="expense_category_id"
                            category={this.props.filters.expense_category_id}
                            renderErrorFor={this.renderErrorFor}
                            handleInputChanges={this.filterExpenses}
                        />
                    </FormGroup>
                </Col>
            </Row>
        )
    }

    render () {
        const filters = this.getFilters()

        return (<FilterTile setFilterOpen={this.props.setFilterOpen} filters={filters}/>)
    }
}
