import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../utils/_translations'

export default function Notes (props) {
    return (
        <Card>
            <CardHeader>{translations.notes}</CardHeader>
            <CardBody>
                {Object.prototype.hasOwnProperty.call(props, 'internal_note') &&
                <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                    <Label>{translations.internal_note}</Label>
                    <Input
                        value={props.internal_note}
                        type='textarea'
                        name='internal_note'
                        id='internal_note'
                        onChange={props.handleInput}
                    />
                </FormGroup>
                }

                {Object.prototype.hasOwnProperty.call(props, 'customer_note') &&
                <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                    <Label>{translations.customer_note}</Label>
                    <Input
                        value={props.customer_note}
                        type='textarea'
                        name='customer_note'
                        id='customer_note'
                        onChange={props.handleInput}
                    />
                </FormGroup>
                }

                {Object.prototype.hasOwnProperty.call(props, 'terms') &&
                <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                    <Label>{translations.terms}</Label>
                    <Input
                        value={props.terms}
                        type='textarea'
                        name='terms'
                        id='notes'
                        onChange={props.handleInput}
                    />
                </FormGroup>
                }

                {Object.prototype.hasOwnProperty.call(props, 'footer') &&
                <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                    <Label>{translations.footer}</Label>
                    <Input
                        value={props.footer}
                        type='textarea'
                        name='footer'
                        id='footer'
                        onChange={props.handleInput}
                    />
                </FormGroup>
                }
            </CardBody>
        </Card>

    )
}
