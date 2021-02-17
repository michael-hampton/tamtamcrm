import React, { Component } from 'react'
import { Col, FormGroup, Row } from 'reactstrap'
import TableSearch from '../common/TableSearch'
import FilterTile from '../common/FilterTile'
import DateFilter from '../common/DateFilter'
import CsvImporter from '../common/CsvImporter'
import StatusDropdown from '../common/StatusDropdown'
import { filterStatuses } from "../utils/_search";

export default class CompanyFilters extends Component {
    constructor (props) {
        super(props)

        this.state = {
            isOpen: false,
            dropdownButtonActions: ['download'],
            filters: {
                status_id: 'active',
                searchText: '',
                start_date: '',
                end_date: ''
            }
        }

        this.filterCompanies = this.filterCompanies.bind(this)
        this.getFilters = this.getFilters.bind(this)
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterCompanies (event) {
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

        const column = event.target.name
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
        const { searchText, status_id, start_date, end_date } = this.state.filters

        return (
            <Row form>
                <Col md={3}>
                    <TableSearch onChange={(e) => {
                        const value = typeof e.target.value === 'string' ? e.target.value.toLowerCase() : e.target.value
                        const search_results = this.props.cachedData.filter(obj => Object.keys(obj).some(key => obj[key] && obj[key].length ? obj[key].toString().toLowerCase().includes(value) : false))
                        const totalPages = search_results && search_results.length ? Math.ceil(search_results.length / this.props.pageLimit) : 0
                        this.props.updateList({
                            invoices: search_results && search_results.length ? search_results : [],
                            currentPage: 1,
                            totalPages: totalPages
                        })
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
                                const totalPages = results && results.length ? Math.ceil(results.length / this.props.pageLimit) : 0
                                this.props.updateList({ invoices: results, currentPage: 1, totalPages: totalPages, filters: this.state.filters })
                            })
                        }}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={1} className="mt-3 mt-md-0">
                    <FormGroup>
                        <CsvImporter filename="companies.csv"
                            url={`/api/companies?search_term=${searchText}&status=${status_id}&start_date=${start_date}&end_date=${end_date}&page=1&per_page=5000`}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <DateFilter onChange={this.filterCompanies}/>
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
