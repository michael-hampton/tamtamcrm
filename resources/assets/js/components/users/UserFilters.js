import React, { Component } from 'react'
import { Col, FormGroup, Row } from 'reactstrap'
import DepartmentDropdown from '../common/dropdowns/DepartmentDropdown'
import RoleDropdown from '../common/dropdowns/RoleDropdown'
import TableSearch from '../common/TableSearch'
import FilterTile from '../common/FilterTile'
import DateFilter from '../common/DateFilter'
import CsvImporter from '../common/CsvImporter'
import StatusDropdown from '../common/StatusDropdown'
import { filterStatuses } from '../utils/_search'

export default class UserFilters extends Component {
    constructor (props) {
        super(props)
        this.state = {
            isOpen: false,
            dropdownButtonActions: ['download'],

            filters: {
                start_date: '',
                end_date: '',
                status: 'active',
                role_id: '',
                department_id: '',
                searchText: ''
            }
        }

        this.getFilters = this.getFilters.bind(this)
        this.filterUsers = this.filterUsers.bind(this)
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterUsers (event) {
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
        const { status, role_id, department_id, searchText, start_date, end_date } = this.state.filters

        return (
            <Row form>
                <Col md={3}>
                    <TableSearch onChange={(e) => {
                        const value = typeof e.target.value === 'string' ? e.target.value.toLowerCase() : e.target.value
                        const search_results = this.props.cachedData.filter(obj => Object.keys(obj).some(key => obj[key] && obj[key].length ? obj[key].toString().toLowerCase().includes(value) : false))
                        this.props.updateList(search_results || [], false, this.state.filters)
                    }}/>
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <DepartmentDropdown
                        name="department_id"
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
                                this.props.updateList(results || [], false, this.state.filters)
                            })
                        }}
                        departments={this.props.departments}
                    />
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <RoleDropdown
                        name="role_id"
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
                                this.props.updateList(results || [], false, this.state.filters)
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
                                this.props.updateList(results || [], false, this.state.filters)
                            })
                        }}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={1} className="mt-3 mt-md-0">
                    <CsvImporter filename="users.csv"
                        url={`/api/users?search_term=${searchText}&status=${status}&role_id=${role_id}&department_id=${department_id}&start_date=${start_date}&end_date=${end_date}&page=1&per_page=5000`}/>
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <DateFilter onChange={this.filterUsers}/>
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
