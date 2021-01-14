import React, { Component } from 'react'
import { Col, FormGroup, Row } from 'reactstrap'
import CompanyDropdown from '../common/dropdowns/CompanyDropdown'
import CustomerGroupDropdown from '../common/dropdowns/CustomerGroupDropdown'
import TableSearch from '../common/TableSearch'
import FilterTile from '../common/FilterTile'
import DateFilter from '../common/DateFilter'
import CsvImporter from '../common/CsvImporter'
import StatusDropdown from '../common/StatusDropdown'

export default class CustomerFilters extends Component {
    constructor (props) {
        super(props)
        this.state = {
            isOpen: false,
            dropdownButtonActions: ['download'],
            filters: {
                status: 'active',
                company_id: '',
                group_settings_id: '',
                searchText: '',
                start_date: '',
                end_date: ''
            }

        }

        this.filterCustomers = this.filterCustomers.bind(this)
        this.getFilters = this.getFilters.bind(this)
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterCustomers (event) {
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
        const { searchText, status, company_id, group_settings_id, start_date, end_date } = this.props.filters

        return (
            <Row form>
                <Col md={2}>
                    <TableSearch onChange={this.filterCustomers}/>
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <StatusDropdown name="status" filterStatus={this.filterCustomers}/>
                    </FormGroup>
                </Col>

                <Col md={3}>
                    <CompanyDropdown
                        companies={this.props.companies}
                        company_id={this.props.filters.company_id}
                        handleInputChanges={this.filterCustomers}
                    />
                </Col>

                <Col md={3}>
                    <CustomerGroupDropdown
                        customer_group={this.props.filters.group_settings_id}
                        handleInputChanges={this.filterCustomers}
                    />
                </Col>

                <Col md={1}>
                    <CsvImporter filename="customers.csv"
                        url={`/api/customers?search_term=${searchText}&status=${status}&company_id=${company_id}&group_settings_id=${group_settings_id}&start_date=${start_date}&end_date=${end_date}&page=1&per_page=5000`}/>
                </Col>

                <Col md={2}>
                    <FormGroup>
                        <DateFilter onChange={this.filterCustomers}/>
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
