import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'
import CustomerDropdown from '../../common/dropdowns/CustomerDropdown'

export default function Contacts (props) {
    const send_to = props.contacts.length ? props.contacts.map((contact, index) => {
        const invitations = props.invitations.length ? props.invitations.filter(invitation => parseInt(invitation.contact_id) === contact.id) : []
        const checked = invitations.length ? 'checked="checked"' : ''
        return <FormGroup key={index} check>
            <Label check>
                <Input checked={checked} value={contact.id} onChange={props.handleContactChange}
                    type="checkbox"/> {`${contact.first_name} ${contact.last_name}`}
            </Label>
        </FormGroup>
    }) : null

    return (
        <Card>
            <CardHeader>{translations.customer}</CardHeader>

            <CardBody>
                {props.hide_customer === true &&
                <FormGroup>
                    <Label>{translations.customer}</Label>
                    <CustomerDropdown
                        handleInputChanges={props.handleInput}
                        customer={props.invoice.customer_id}
                        customers={props.customers}
                        errors={props.errors}
                    />
                </FormGroup>
                }

                {send_to}
            </CardBody>
        </Card>

    )
}
