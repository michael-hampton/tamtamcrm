import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default function CustomerForm (props) {
    const hasErrorFor = (field) => {
        return props.errors && !!props.errors[field]
    }

    const renderErrorFor = (field) => {
        if (hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    return (
        <Card>
            <CardHeader>{translations.details}</CardHeader>
            <CardBody>
                <FormGroup>
                    <Label for="name"> {translations.name} </Label>
                    <Input className={hasErrorFor('name') ? 'is-invalid' : ''} type="text"
                        id="name" defaultValue={props.customer.name}
                        onChange={props.onChange} name="name"
                        placeholder={translations.name}/>
                    {renderErrorFor('name')}
                </FormGroup>

                <FormGroup>
                    <Label for="phone"> {translations.phone_number} </Label>
                    <Input className={hasErrorFor('phone') ? 'is-invalid' : ''} type="text" id="phone"
                        defaultValue={props.customer.phone}
                        onChange={props.onChange} name="phone"
                        placeholder={translations.phone_number}/>
                    {renderErrorFor('phone')}
                </FormGroup>

                <FormGroup>
                    <Label htmlFor="website"> {translations.website} </Label>
                    <Input className={hasErrorFor('website') ? 'is-invalid' : ''}
                        type="text"
                        id="website"
                        defaultValue={props.customer.website}
                        onChange={props.onChange}
                        name="website"
                        placeholder={translations.website}/>
                    {renderErrorFor('website')}
                </FormGroup>

                <FormGroup>
                    <Label for="name"> {translations.vat_number} </Label>
                    <Input className={hasErrorFor('vat_number') ? 'is-invalid' : ''} type="text"
                        id="name" defaultValue={props.customer.vat_number}
                        onChange={props.onChange} name="vat_number"
                        placeholder={translations.vat_number}/>
                    {renderErrorFor('vat_number')}
                </FormGroup>
            </CardBody>
        </Card>

    )
}
