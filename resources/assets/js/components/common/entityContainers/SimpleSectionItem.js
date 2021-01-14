import React from 'react'
import { ListGroupItem, ListGroupItemHeading, ListGroupItemText } from 'reactstrap'

export default function SimpleSectionItem (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''
    const custom_class = props.custom_class ? props.custom_class : ''

    return <ListGroupItem className={`${listClass} ${custom_class} col-12 col-md-6 pull-left`}>
        <ListGroupItemText>{props.heading}</ListGroupItemText>
        <ListGroupItemHeading>
            {props.value}
        </ListGroupItemHeading>
    </ListGroupItem>
}
