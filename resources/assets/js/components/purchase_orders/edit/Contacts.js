import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'
import CompanyDropdown from '../../common/dropdowns/CompanyDropdown'
import AddCompany from '../../companies/edit/AddCompany'

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
            <CardHeader>{translations.company}</CardHeader>
            <CardBody>
                {props.hide_customer === true &&
                <FormGroup>
                    <Label>{translations.company}
                        <AddCompany small_button={true} brands={props.companies} users={[]}
                            action={(companies, update = false) => {
                                this.props.updateCustomers(companies)
                            }}
                            custom_fields={[]}/>
                    </Label>
                    <CompanyDropdown
                        handleInputChanges={props.handleInput}
                        company_id={props.invoice.company_id}
                        companies={props.companies}
                        errors={props.errors}
                    />
                </FormGroup>
                }

                {send_to}
            </CardBody>
        </Card>

    )
}
