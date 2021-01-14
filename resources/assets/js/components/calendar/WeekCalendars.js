import React, { Component } from 'react'
import WeekCalendar from 'react-week-events'
import 'react-week-calendar/dist/styles.css'

class WeekCalendars extends Component {
    state = {
        events: [
            {
                name: 'Event 1',
                date: new Date()
            },
            {
                name: 'Event 2',
                date: new Date()
            },
            {
                name: 'Event 3',
                date: new Date()
            }
        ]
    }

    eventRender = (event, i) => (
        <div onClick={() => console.log(`Event: ${event.name} on position: ${i}`)}>
            {event.name}
        </div>
    )

    emptyRender = () => <div>No events</div>

    render () {
        const { events } = this.state
        return (
            <WeekCalendar
                events={events}
                emptyRender={this.emptyRender}
                eventRender={this.eventRender}
                past={true}
            />
        )
    }
}
