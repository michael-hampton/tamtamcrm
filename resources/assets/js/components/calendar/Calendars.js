import React from 'react'
import styled from 'styled-components'
import Calendar from './Calendar'
import axios from 'axios'
import { Card, CardBody, CardHeader, FormGroup } from 'reactstrap'
import WeekCalendar from './WeekCalendar'
import CalendarEvent from './CalendarEvent'
import CreateEvent from './CreateEvent'
import CalendarFilter from './CalendarFilter'

const Controls = styled.div`
  display: flex;
  justify-content: space-between;
`
const Button = styled.button`
  background: transparent;
  border: none;
  &:hover {
    color: #777;
  }
`

class Calendars extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            year: new Date().getFullYear(),
            month: new Date().getMonth() + 1,
            events: [],
            filters: { status_id: 'active', task_id: '', user_id: '', customer_id: '' },
            calendar_type: 'month',
            custom_fields: []
        }

        this.loadPrevMonth = this.loadPrevMonth.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.loadNextMonth = this.loadNextMonth.bind(this)
        this.setEvents = this.setEvents.bind(this)
        this.getEvents = this.getEvents.bind(this)
        this.filterEvents = this.filterEvents.bind(this)
        this.eventRender = this.eventRender.bind(this)
    }

    componentDidMount () {
        this.getEvents()
        this.getCustomFields()
    }

    setEvents (events) {
        this.setState({ events: events })
    }

    setMonth (month) {
        this.setState({ month: month })
    }

    filterEvents (filters) {
        console.log('filters', filters)
        this.setState({ filters: filters }, this.handleSubmit())
    }

    getEvents () {
        const url = (this.props.user_id) ? `/api/events/users/${this.props.user_id}` : (this.props.task_id) ? `/api/events/tasks/${this.props.task_id}` : '/api/events'
        axios.get(url)
            .then((r) => {
                this.setState({
                    events: r.data
                })
            })
            .catch((e) => {
                alert(e)
            })
    }

    getCustomFields () {
        axios.get('api/accounts/fields/Event')
            .then((r) => {
                this.setState({
                    custom_fields: r.data.fields
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    err: e
                })
            })
    }

    handleSubmit () {
        axios.post('/api/events/filterEvents',
            this.state.filters)
            .then((response) => {
                this.setState({ events: response.data })
            })
            .catch((error) => {
                alert(error)
            })
    }

    /**
     *
     * @param year
     */
    setYear (year) {
        this.setState({ year: year })
    }

    loadPrevMonth () {
        let prevMonth = this.state.month - 1
        if (prevMonth < 1) {
            this.setYear(this.state.year - 1)
            prevMonth = 12
        }
        this.setMonth(prevMonth)
    }

    loadNextMonth () {
        let nextMonth = this.state.month + 1
        if (nextMonth > 12) {
            this.setYear(this.state.year + 1)
            nextMonth = 1
        }
        this.setMonth(nextMonth)
    }

    renderErrorFor () {

    }

    resetFilters () {
        this.props.reset()
    }

    eventRender (event, i) {
        const { events, custom_fields } = this.state
        return (
            <CalendarEvent
                custom_fields={custom_fields}
                calendar_type="week"
                allEvents={events}
                events={events}
                event={event}

                key={event.id}
            />
        )
    }

    setCalendarType (event) {
        const type = event.target.getAttribute('data-type')
        this.setState({ calendar_type: type })
    }

    render () {
        const { events, year, month, custom_fields, calendarType, filters } = this.state

        const calendar = this.state.calendar_type === 'month'
            ? <React.Fragment>
                <Controls>
                    <Button onClick={this.loadPrevMonth}>&laquo; Prev Month</Button>
                    <Button onClick={this.loadNextMonth}>Next Month &raquo;</Button>
                </Controls>
                <Calendar
                    custom_fields={custom_fields}
                    year={year}
                    month={month}
                    events={events}
                    action={this.setEvents}
                />
            </React.Fragment>
            : <WeekCalendar
                custom_fields={custom_fields}
                calendar_type={calendarType}
                events={events}
                emptyRender={this.emptyRender}
                eventRender={this.eventRender}
                past={true}
            />

        return (
            <div>
                <Card>
                    <CardHeader>
                        <h2>Calendar</h2> <a data-type="week"
                            onClick={this.setCalendarType.bind(this)}> Week </a> | <a
                            data-type="month" onClick={this.setCalendarType.bind(this)}> Month </a>

                        <CalendarFilter events={events}
                            updateIgnoredColumns={this.updateIgnoredColumns}
                            filters={filters} filter={this.filterEvents}
                            saveBulk={this.saveBulk} ignoredColumns={this.state.ignoredColumns}/>

                        <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                            <CreateEvent
                                custom_fields={this.state.custom_fields}
                                action={this.setEvents}
                                events={this.state.events}
                                modal={true}
                            />
                        </FormGroup>
                    </CardHeader>
                    <CardBody>
                        {calendar}
                    </CardBody>
                </Card>
            </div>
        )
    }
}

export default Calendars
