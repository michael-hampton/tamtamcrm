import React from 'react'
import { ListGroupItemHeading } from 'reactstrap'
import { icons } from '../../utils/_icons'

export default function SectionItem (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    return <a href={props.link}
        className={`${listClass} list-group-item list-group-item-action flex-column align-items-start mb-2`}>
        <div className="d-flex w-100 justify-content-between">
            <ListGroupItemHeading><i style={{ fontSize: '24px' }}
                className={`fa ${props.icon} mr-4`}/>{props.title}
            </ListGroupItemHeading> <i className={`fa ${icons.right}`}/>
        </div>
        {props.subtitle &&
        <p className="mb-1">{props.subtitle}</p>
        }
    </a>
}
