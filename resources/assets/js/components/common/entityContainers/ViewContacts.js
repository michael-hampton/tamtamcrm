import React from 'react'
import { ListGroup, ListGroupItem, ListGroupItemHeading } from 'reactstrap'
import CustomerModel from '../../models/CustomerModel'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import FormatDate from '../FormatDate'

export default function ViewContacts (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''
    const customer = props.customers && props.customers.length ? props.customers.filter(customer => customer.id === parseInt(props.entity.customer_id)) : []

    let contactList = null

    if (customer.length) {
        const customerModel = new CustomerModel(customer[0])
        const invitations = props.entity.invitations

        contactList = invitations.map((invitation, index) => {
            const link = props.entity.invitation_link.replace('$key', invitation.key)
            const contact = customerModel.findContact(invitation.contact_id)

            return contact !== false ? <ListGroupItem key={index} className={listClass}>
                <a href={link}>
                    <ListGroupItemHeading><i
                        className={`fa ${icons.contact} mr-4`}/>{(!contact.fullName.length ? customerModel.displayName : contact.fullName)}
                    </ListGroupItemHeading>
                    {!!invitation.sent_date.length &&
                    <p>{translations.sent} <FormatDate date={invitation.sent_date} with_time={true}/></p>
                    }
                    {!!invitation.viewed_date.length &&
                    <p>{translations.viewed} <FormatDate date={invitation.viewed_date} with_time={true}/></p>
                    }
                </a>
                <p className="small"><span
                    onClick={(e) => props.entity.copyToClipboard(link)}> {translations.copy_to_clipboard}</span>
                </p>
            </ListGroupItem> : null
        })
    }

    return <ListGroup className="mt-2 col-12">
        {contactList}
    </ListGroup>
}
