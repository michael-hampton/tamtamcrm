import { ListGroupItem, ListGroupItemHeading, ListGroupItemText } from 'reactstrap'
import { translations } from '../../utils/_translations'
import React from 'react'
import FormatMoney from '../FormatMoney'

export default function LineItem (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    return <ListGroupItem className={listClass}>
        <ListGroupItemHeading
            className="d-flex justify-content-between align-items-center">
            {props.line_item.product_id}
            <span><FormatMoney amount={props.line_item.sub_total} customers={props.customers}/></span>
        </ListGroupItemHeading>
        <ListGroupItemText>
            {props.line_item.quantity} x <FormatMoney amount={props.line_item.unit_price}
                customers={props.customers}/> {translations.discount}: <FormatMoney
                amount={props.line_item.unit_discount} customers={props.customers}/> {translations.tax}: <FormatMoney
                amount={props.line_item.unit_tax} customers={props.customers}/>
            <br/>
            {props.line_item.description}
        </ListGroupItemText>
    </ListGroupItem>
}
