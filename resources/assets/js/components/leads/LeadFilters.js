import React, { Component } from 'react'
import { Col, FormGroup, Row } from 'reactstrap'
import TableSearch from '../common/TableSearch'
import DateFilter from '../common/DateFilter'
import CsvImporter from '../common/CsvImporter'
import FilterTile from '../common/FilterTile'
import StatusDropdown from '../common/StatusDropdown'
import UserDropdown from '../common/dropdowns/UserDropdown'
import { filterStatuses } from "../utils/_search";

export default class LeadFilters extends Component {
    constructor (props) {
        super(props)

        this.state = {
            isOpen: false,
            dropdownButtonActions: ['download'],
            filters: {
                status_id: 'active',
                customer_id: '',
                searchText: '',
                start_date: '',
                end_date: '',
                user_id: ''
            }
        }

        this.filterLeads = this.filterLeads.bind(this)
        this.getFilters = this.getFilters.bind(this)
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterLeads (event) {
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
        const { status_id, searchText, start_date, end_date, customer_id, user_id } = this.state.filters

        return (
             <Row form>
                <Col md={2}>
                    <TableSearch onChange={(e) => {
                        const value = typeof e.target.value === 'string' ? e.target.value.toLowerCase() : e.target.value
                         const search_results = this.props.cachedData.filter(obj => Object.keys(obj).some(key => obj[key] && obj[key].length ? obj[key].toString().toLowerCase().includes(value) : false))
                         const totalPages = search_results && search_results.length ? Math.ceil(search_results / this.props.pageLimit) : 0
                        this.props.updateList({
                            invoices: search_results && search_results.length ? search_results : [],
                            currentPage: 1,
                            totalPages: totalPages
                        })
                    }}/>
                </Col>

                /* <Col md={3}>
                    <CustomerDropdown
                        customers={this.props.customers}
                        customer={this.props.filters.customer_id}
                        handleInputChanges={(e) => {
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
                        }}
                        name="customer_id"
                    />
                </Col> */

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <UserDropdown
                        handleInputChanges={(e) => {
                            const name = e.target.name
                            const value = e.target.value
                            this.setState(prevState => ({
                                filters: {
                                    ...prevState.filters,
                                    [name]: value
                                }
                            }), () => {
                                const results = filterStatuses(this.props.cachedData, value, this.state.filters)
                                const totalPages = results && results.length ? Math.ceil(results.length / this.props.pageLimit) : 0
                                this.props.updateList({ invoices: results, currentPage: 1, totalPages: totalPages, filters: this.state.filters })
                            })
                        }}
                        users={this.props.users}
                        name="user_id"
                    />
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">

                    <TaskStatusDropdown
                        task_type={3}
                        handleInputChanges={(e) => {
                            const name = e.target.name
                            const value = e.target.value
                            this.setState(prevState => ({
                                filters: {
                                    ...prevState.filters,
                                    [name]: value
                                }
                            }), () => {
                                const results = filterStatuses(this.props.cachedData, value, this.state.filters)
                                const totalPages = results && results.length ? Math.ceil(results.length / this.props.pageLimit) : 0
                                this.props.updateList({ invoices: results, currentPage: 1, totalPages: totalPages, filters: this.state.filters })
                            })
                        }}
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
                                this.props.updateList({ invoices: results, currentPage: 1, totalPages: totalPages, filters: this.state.filters })
                            })
                        }}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={1} className="mt-3 mt-md-0">
                    <CsvImporter customers={this.props.customers} filename="tasks.csv"
                        url={`/api/tasks?search_term=${searchText}&project_id=${project_id}&task_status=${task_status_id}&task_type=${task_type}&customer_id=${customer_id}&user_id=${user_id}&start_date=${start_date}&end_date=${end_date}&page=1&per_page=5000`}/>
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <ProjectDropdown
                        handleInputChanges={(e) => {
                            const name = e.target.name
                            const value = e.target.value
                            this.setState(prevState => ({
                                filters: {
                                    ...prevState.filters,
                                    [name]: value
                                }
                            }), () => {
                                const results = filterStatuses(this.props.cachedData, value, this.state.filters)
                                const totalPages = results && results.length ? Math.ceil(results.length / this.props.pageLimit) : 0
                                this.props.updateList({ invoices: results, currentPage: 1, totalPages: totalPages, filters: this.state.filters })
                            })
                        }}
                        name="project_id"
                    />
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <DateFilter onChange={this.filterTasks}/>
                    </FormGroup>
                </Col>
                <Col sm={12} md={1} className="mt-3 mt-md-0">
                    <Button color="primary" onClick={() => {
                        location.href = '/#/kanban?type=task'
                    }}>Kanban view </Button>
                </Col>
            </Row>
        )
    }

    render () {
        const filters = this.getFilters()

        return (<FilterTile setFilterOpen={this.props.setFilterOpen} filters={filters}/>)
    }
}
