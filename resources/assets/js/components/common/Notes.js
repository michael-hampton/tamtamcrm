import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../utils/_translations'

export default function Notes (props) {
    return (
        <Card>
            <CardHeader>{translations.notes}</CardHeader>
            <CardBody>
                {Object.prototype.hasOwnProperty.call(props, 'private_notes') &&
                <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                    <Label>{translations.private_notes}</Label>
                    <Input
                        value={props.private_notes}
                        type='textarea'
                        name='private_notes'
                        id='private_notes'
                        onChange={props.handleInput}
                    />
                </FormGroup>
                }

                {Object.prototype.hasOwnProperty.call(props, 'public_notes') &&
                <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                    <Label>{translations.public_notes}</Label>
                    <Input
                        value={props.public_notes}
                        type='textarea'
                        name='public_notes'
                        id='public_notes'
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
