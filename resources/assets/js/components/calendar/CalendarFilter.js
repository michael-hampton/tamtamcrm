import React, { Component } from 'react'
import { Form, FormGroup, Input } from 'reactstrap'
import FilterTile from '../common/FilterTile'
import TaskDropdown from '../common/dropdowns/TaskDropdown'
import UserDropdown from '../common/dropdowns/UserDropdown'
import CustomerDropdown from '../common/dropdowns/CustomerDropdown'
import EventTypeDropdown from '../common/dropdowns/EventTypeDropdown'

export default class CalendarFilter extends Component {
    constructor (props) {
        super(props)
        this.state = {

            dropdownButtonActions: ['download'],

            filters: { status_id: 'active', task_id: '', user_id: '', customer_id: '' }
        }

        this.getFilters = this.getFilters.bind(this)
        this.filterEvents = this.filterEvents.bind(this)
    }

    filterEvents (event) {
        const column = event.target.id
        const value = event.target.value

        if (value === 'all') {
            const updatedRowState = this.state.filters.filter(filter => filter.column !== column)
            this.setState({ filters: updatedRowState }, function () {
                this.props.filter(this.state.filters)
            })
            return true
        }

        this.setState(prevState => ({
            filters: {
                ...prevState.filters,
                [column]: value
            }
        }), function () {
            this.props.filter(this.state.filters)
        })

        return true
    }

    getFilters () {
        return (
            <Form inline className="pull-right">

                <TaskDropdown
                    task={this.props.filters.task_id}
                    name="task_id"
                    handleInputChanges={this.filterEvents}
                />
                <UserDropdown
                    user={this.props.filters.user_id}
                    name="event_user.user_id"
                    handleInputChanges={this.filterEvents}
                />
                <CustomerDropdown
                    customer={this.props.filters.customer_id}
                    handleInputChanges={this.filterEvents}
                />
                <EventTypeDropdown
                    renderErrorFor={this.renderErrorFor}
                    handleInputChanges={this.filterEvents}
                    customers={this.props.customers}
                />

                <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                    <Input type='select'
                        onChange={this.filterEvents}
                        id="status_id"
                        name="status_id"
                    >
                        <option value="">Select Status</option>
                        <option value='active'>Active</option>
                        <option value='archived'>Archived</option>
                        <option value='deleted'>Deleted</option>
                    </Input>
                </FormGroup>
            </Form>
        )
    }

    render () {
        const filters = this.getFilters()

        return (<FilterTile filters={filters}/>)
    }
}
