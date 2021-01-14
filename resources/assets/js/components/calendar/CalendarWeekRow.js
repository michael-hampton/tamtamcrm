/* eslint-disable no-unused-vars */
import React from 'react'
import styled from 'styled-components'
import CalendarEvent from './CalendarEvent'

const Row = styled.div`
  display: grid;
  flex-basis: 100%;
  grid-template-columns: repeat(7, 1fr);
  grid-template-rows: 1.2rem repeat(5, 0.8rem);
  grid-auto-flow: dense;
  grid-gap: 3px 6px;
`
const DayHeader = styled.span`
  display: flex;
  justify-content: flex-end;
  align-items: center;
  color: ${props => (props.dimmed ? '#bbb' : '#000')};
  font-weight: ${props => (props.dimmed ? 'normal' : 'bold')};
`

class CalendarWeekRow extends React.Component {
    render () {
        const { year, month, dates, events, allEvents } = this.props

        return (
            <Row>
                {dates.map(date => (
                    <DayHeader
                        dimmed={date.getFullYear() !== year || date.getMonth() !== month - 1}
                        key={date}
                    >
                        {date.getDate()}
                    </DayHeader>
                ))}
                {events.map(event => {
                    const beginDate = new Date(Date.parse(event.beginDate))
                    const endDate = new Date(Date.parse(event.endDate))
                    const col =
                        dates.findIndex(
                            date => date.valueOf() === beginDate.valueOf()
                        ) + 1 || 1
                    const colSpan =
                        (dates.findIndex(
                            date => date.valueOf() === endDate.valueOf()
                        ) + 1 || 7) -
                        col +
                        1
                    return (
                        <CalendarEvent
                            custom_fields={this.props.custom_fields}
                            allEvents={allEvents}
                            events={events}
                            event={event}
                            col={col}
                            colSpan={colSpan}
                            action={this.props.action}
                            key={event.id}
                        />
                    )
                })}
            </Row>
        )
    }
}

export default CalendarWeekRow
