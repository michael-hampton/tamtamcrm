import React from 'react'
import { ListGroup, ListGroupItem, ListGroupItemHeading } from 'reactstrap'

export default function EntityListTile (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    return <ListGroup className="mt-4 col-12">
        <ListGroupItem className={listClass}>
            <ListGroupItemHeading><i
                className={`fa ${props.icon} mr-4`}/>{`${props.entity} > ${props.title}`}
            </ListGroupItemHeading>
        </ListGroupItem>
    </ListGroup>
}
