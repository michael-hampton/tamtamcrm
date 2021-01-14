import React from 'react'
import { ListGroupItem, ListGroupItemHeading, ListGroupItemText } from 'reactstrap'
import { icons } from '../../utils/_icons'
import FormatMoney from '../../common/FormatMoney'
import FormatDate from '../../common/FormatDate'

export default function Paymentable (props) {
    console.log('line item', props.line_item)
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    return <a className="mb-2" href={props.link}>
        <ListGroupItem className={listClass}>
            <ListGroupItemHeading>
                <i className={`fa ${icons.document} mr-4`}/> {props.entity} > {props.line_item.number}

            </ListGroupItemHeading>

            <ListGroupItemText>
                <FormatMoney amount={props.line_item.amount}/> - <FormatDate
                    date={props.line_item.date}/>
            </ListGroupItemText>
        </ListGroupItem>
    </a>
}
