import React from 'react'
import { ListGroup, Row } from 'reactstrap'
import { translations } from '../../utils/_translations'
import { icons } from '../../utils/_icons'
import InfoItem from '../../common/entityContainers/InfoItem'

export default function Details (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    const billing = props.entity.billing && Object.keys(props.entity.billing).length
        ? <React.Fragment>
            {props.entity.billing.address_1} <br/>
            {props.entity.billing.address_2} <br/>
            {props.entity.billing.city} {props.entity.billing.zip}

        </React.Fragment> : null

    const shipping = props.entity.shipping && Object.keys(props.entity.shipping).length
        ? <React.Fragment>
            {props.entity.shipping.address_1} <br/>
            {props.entity.shipping.address_2} <br/>
            {props.entity.shipping.city} {props.entity.shipping.zip}

        </React.Fragment> : null

    return <Row>
        <ListGroup className="col-12">
            {props.entity.contacts.map((contact, index) => (
                <React.Fragment>
                    <InfoItem icon={icons.envelope}
                        first_value={`${contact.first_name} ${contact.last_name}`}
                        value={`${contact.email}`} title={translations.email}/>
                    <InfoItem icon={icons.phone}
                        first_value={`${contact.first_name} ${contact.last_name}`}
                        value={`${contact.phone}`} title={translations.phone_number}/>
                </React.Fragment>
            ))}

            <InfoItem icon={icons.link} value={props.entity.website}
                title={translations.website}/>
            <InfoItem icon={icons.building} value={props.entity.vat_number}
                title={translations.vat_number}/>
            <InfoItem icon={icons.list} value={props.entity.number}
                title={translations.number}/>
            <InfoItem icon={icons.map_marker} value={billing} title={translations.billing_address}/>
            <InfoItem icon={icons.map_marker} value={shipping}
                title={translations.shipping_address}/>
        </ListGroup>
    </Row>
}
