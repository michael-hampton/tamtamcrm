/* eslint-disable no-unused-vars */
import React from 'react'
import styled from 'styled-components'
import CalendarWeekRow from './CalendarWeekRow'

const monthNames = [
    'January',
    'February',
    'March',
    'April',
    'May',
    'June',
    'July',
    'August',
    'September',
    'October',
    'November',
    'December'
]
const Container = styled.div`
  display: flex;
  flex-direction: column;
`
const Header = styled.div`
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 0.5rem;
`
const RowContainer = styled.div`
  display: grid;
  grid-auto-rows: 1fr;
`
const Calendar = props => {
    const { year, month, events } = props
    console.log('events second', events)
    const weeks = []
    const firstDayInMonth = new Date(year, month - 1, 1)
    const lastDayInMonth = new Date(year, month, 0)
    const firstDayInCalendar = new Date(
        year,
        month - 1,
        1 - firstDayInMonth.getDay()
    )
    const lastDayInCalendar = new Date(
        year,
        month - 1,
        lastDayInMonth.getDate() + 6 - lastDayInMonth.getDay()
    )
    let currentDate = firstDayInCalendar
    while (currentDate <= lastDayInCalendar) {
        if (currentDate.getDay() === 0) {
            weeks.push([])
        }
        weeks[weeks.length - 1].push(new Date(currentDate))
        currentDate = new Date(currentDate.setDate(currentDate.getDate() + 1))
    }
    return (
        <Container>
            <Header>
                <h1>
                    {monthNames[month - 1]} {year}
                </h1>
            </Header>
            <RowContainer>
                {weeks.map((dates, i) => (
                    <CalendarWeekRow
                        custom_fields={props.custom_fields}
                        year={year}
                        month={month}
                        dates={dates}
                        action={props.action}
                        allEvents={events}
                        events={events.filter(
                            event =>
                                new Date(Date.parse(event.beginDate)) <= dates[dates.length - 1] &&
                                new Date(Date.parse(event.endDate)) >= dates[0]
                        )}
                        key={i}
                    />
                ))}
            </RowContainer>
        </Container>
    )
}
export default Calendar
