import axios from 'axios'
import React from 'react'
import { Button } from 'reactstrap'
import CreateEvent from '../calendar/CreateEvent'

class EventTab extends React.Component {
    constructor (props) {
        super(props)
        console.log('task', this.props.task)
        this.state = {
            modal: false,
            events: [],
            errors: [],
            visible: 'collapse'
        }

        this.setEvents = this.setEvents.bind(this)
        this.handleSlideClick = this.handleSlideClick.bind(this)
    }

    componentDidMount () {
        axios.get(`/api/events/tasks/${this.props.task_id}`).then(data => {
            this.setState({ events: data.data })
        })
    }

    handleSlideClick () {
        this.setState({ visible: this.state.visible === 'collapse' ? 'collapse show' : 'collapse' })
    }

    setEvents (events) {
        this.setState({ events: events })
    }

    formatDate (dateString) {
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ]
        const d = new Date(dateString)
        const dayName = days[d.getDay()]
        const monthName = monthNames[d.getMonth()]
        const formattedDate = `${dayName} ${d.getDate()} ${monthName} ${d.getFullYear()}`
        return formattedDate
    }

    render () {
        const events = this.state.events.map((event, index) => {
            return (
                <a key={index} href="#"
                    className="list-group-item list-group-item-action flex-column align-items-start">
                    <div className="d-flex w-100 justify-content-between">
                        <h5 className="mb-1">{event.title}</h5>
                        <small>{this.formatDate(event.beginDate)} - {this.formatDate(event.endDate)}</small>
                    </div>
                </a>
            )
        })

        return (

            <React.Fragment>

                <Button color="success"
                    onClick={this.handleSlideClick}>{this.state.visible === 'collapse show' ? 'Hide Add' : 'Show Add'}</Button>
                <div className={this.state.visible}>

                    <CreateEvent
                        modal={false}
                        task_id={this.props.task_id}
                        action={this.setEvents}
                        events={this.state.events}
                        customer_id={this.props.customer_id}
                    />

                </div>

                <div className="viewContainer mt-5">
                    <div className="list-group">
                        {events}
                    </div>
                </div>

            </React.Fragment>
        )
    }
}

export default EventTab
