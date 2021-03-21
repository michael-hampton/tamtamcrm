import React, { Component } from 'react'
import { Col, FormGroup, Row } from 'reactstrap'
import CompanyDropdown from '../common/dropdowns/CompanyDropdown'
import CustomerGroupDropdown from '../common/dropdowns/CustomerGroupDropdown'
import TableSearch from '../common/TableSearch'
import FilterTile from '../common/FilterTile'
import DateFilter from '../common/DateFilter'
import CsvImporter from '../common/CsvImporter'
import StatusDropdown from '../common/StatusDropdown'
import { filterStatuses } from '../utils/_search'

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
                    <TableSearch onChange={(e) => {
                        const value = typeof e.target.value === 'string' ? e.target.value.toLowerCase() : e.target.value
                        const search_results = this.props.cachedData.filter(obj => Object.keys(obj).some(key => obj[key] && obj[key].length ? obj[key].toString().toLowerCase().includes(value) : false))
                        this.props.updateList(search_results && search_results.length ? search_results : [], false, this.state.filters)
                    }}/>
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <StatusDropdown filterStatus={(e) => {
                            this.setState(prevState => ({
                                filters: {
                                    ...prevState.filters,
                                    [e.target.id]: e.target.value
                                }
                            }), () => {
                                const results = filterStatuses(this.props.cachedData, e.target.value, this.state.filters)
                                this.props.updateList(results || [], false, this.state.filters)
                            })
                        }}/>
                    </FormGroup>
                </Col>

                <Col md={3}>
                    <CompanyDropdown
                        companies={this.props.companies}
                        company_id={this.props.filters.company_id}
                        handleInputChanges={(e) => {
                            this.setState(prevState => ({
                                filters: {
                                    ...prevState.filters,
                                    [e.target.name]: e.target.value
                                }
                            }), () => {
                                const results = filterStatuses(this.props.cachedData, e.target.value, this.state.filters)
                                this.props.updateList(results || [], false, this.state.filters)
                            })
                        }}
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
