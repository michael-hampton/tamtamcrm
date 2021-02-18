import React, { Component } from 'react'
import { Col, FormGroup, Row } from 'reactstrap'
import TableSearch from '../common/TableSearch'
import DateFilter from '../common/DateFilter'
import FilterTile from '../common/FilterTile'
import StatusDropdown from '../common/StatusDropdown'
import { filterStatuses } from '../utils/_search'
import BankDropdown from '../common/dropdowns/BankDropdown'
import CsvImporter from '../common/CsvImporter'

export default class BankAccountFilters extends Component {
    constructor (props) {
        super(props)
        this.state = {
            isOpen: false,
            dropdownButtonActions: ['download'],
            filters: {
                searchText: '',
                status: 'active',
                start_date: '',
                end_date: ''
            }
        }

        this.filterBankAccounts = this.filterBankAccounts.bind(this)
        this.getFilters = this.getFilters.bind(this)
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterBankAccounts (event) {
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
    }

    getFilters () {
        const { status_id, searchText, start_date, end_date } = this.state.filters

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

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                     <BankDropdown
                         banks={this.props.banks}
                         bank_id={this.props.filters.bank_id}
                         handleInputChanges={this.filterBankAccounts}
                     />
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
                                this.props.updateList({
                                    invoices: results,
                                    currentPage: 1,
                                    totalPages: totalPages,
                                    filters: this.state.filters
                                })
                            })
                        }} statuses={this.statuses}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <FormGroup>
                        <DateFilter onChange={this.filterBankAccounts}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={1} className="mt-3 mt-md-0">
                     <CsvImporter filename="bank_account.csv"
                         url={`/api/bank_accounts?search_term=${searchText}&status=${status_id}&start_date=${start_date}&end_date=${end_date}&page=1&per_page=5000`}/>
                 </Col>
            </Row>
        )
    }

    render () {
        const filters = this.getFilters()

        return (<FilterTile setFilterOpen={this.props.setFilterOpen} filters={filters}/>)
    }
}
