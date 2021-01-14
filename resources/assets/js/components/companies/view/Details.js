import React from 'react'
import { ListGroup, Row } from 'reactstrap'
import { translations } from '../../utils/_translations'
import { icons } from '../../utils/_icons'
import InfoItem from '../../common/entityContainers/InfoItem'

export default function Details (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    const address = <React.Fragment>
        {props.entity.address_1} <br/>
        {props.entity.address_2} <br/>
        {props.entity.town} <br/>
        {props.entity.city} {props.entity.postcode}
    </React.Fragment>

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
            <InfoItem icon={icons.map_marker} value={address} title={translations.billing_address}/>
        </ListGroup>
    </Row>
}
