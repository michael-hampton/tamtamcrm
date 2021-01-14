import React, { Component } from 'react'
import { Button, Col, FormGroup, Row } from 'reactstrap'
import TableSearch from '../common/TableSearch'
import DateFilter from '../common/DateFilter'
import CsvImporter from '../common/CsvImporter'
import FilterTile from '../common/FilterTile'
import UserDropdown from '../common/dropdowns/UserDropdown'
import CustomerDropdown from '../common/dropdowns/CustomerDropdown'
import TaskStatusDropdown from '../common/dropdowns/TaskStatusDropdown'
import StatusDropdown from '../common/StatusDropdown'
import ProjectDropdown from '../common/dropdowns/ProjectDropdown'

export default class DealFilters extends Component {
    constructor (props) {
        super(props)

        this.state = {
            isOpen: false,
            dropdownButtonActions: ['download'],
            filters: {
                start_date: '',
                end_date: '',
                project_id: '',
                status_id: 'active',
                task_status_id: '',
                user_id: '',
                customer_id: '',
                task_type: '',
                searchText: ''
            }
        }

        this.filterDeals = this.filterDeals.bind(this)
        this.getFilters = this.getFilters.bind(this)
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterDeals (event) {
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
        const { searchText, start_date, end_date, customer_id, project_id, task_status_id, task_type, user_id } = this.state.filters

        return (

            <Row form>
                <Col md={2}>
                    <TableSearch onChange={this.filterTasks}/>
                </Col>

                <Col md={3}>
                    <CustomerDropdown
                        customer={this.props.filters.customer_id}
                        handleInputChanges={this.filterTasks}
                        name="customer_id"
                    />
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <UserDropdown
                        handleInputChanges={this.filterTasks}
                        users={this.props.users}
                        name="user_id"
                    />
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">

                    <TaskStatusDropdown
                        task_type={2}
                        handleInputChanges={this.filterTasks}
                    />
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <StatusDropdown filterStatus={this.filterProjects}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={1} className="mt-3 mt-md-0">
                    <CsvImporter customers={this.props.customers} filename="tasks.csv"
                        url={`/api/tasks?search_term=${searchText}&project_id=${project_id}&task_status=${task_status_id}&task_type=${task_type}&customer_id=${customer_id}&user_id=${user_id}&start_date=${start_date}&end_date=${end_date}&page=1&per_page=5000`}/>
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <ProjectDropdown
                        handleInputChanges={this.filterTasks}
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
                        location.href = '/#/kanban?type=deal'
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
