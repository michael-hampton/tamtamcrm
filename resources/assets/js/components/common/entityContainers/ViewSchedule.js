import React from 'react'
import { ListGroup, ListGroupItem } from 'reactstrap'
import { translations } from '../../utils/_translations'
import FormatDate from '../FormatDate'

export default function ViewSchedule (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    console.log('schedule', props.recurringDates)

    const contactList = props.recurringDates.map((schedule, index) => {
        return <ListGroupItem className="d-flex justify-content-between align-items-center"><span><FormatDate
            date={schedule.date_to_send}/></span> <span><FormatDate date={schedule.due_date}/></span> </ListGroupItem>
    })

    return <React.Fragment>
        <ListGroup horizontal className="flex-fill d-flex justify-content-between align-items-center">
            <ListGroupItem className="border-0">{translations.send_date}</ListGroupItem>
            <ListGroupItem className="border-0">{translations.due_date} </ListGroupItem>
        </ListGroup>

        <ListGroup className="mt-2 col-12">
            {contactList}
        </ListGroup>
    </React.Fragment>
}
