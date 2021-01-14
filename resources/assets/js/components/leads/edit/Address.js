import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class Address extends React.Component {
    constructor (props) {
        super(props)

        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    hasErrorFor (field) {
        return !!this.props.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    render () {
        return (
            <React.Fragment>
                <Card>
                    <CardHeader>Address</CardHeader>
                    <CardBody>
                        <FormGroup>
                            <Label for="address"> {translations.address_1} </Label>
                            <Input className={this.hasErrorFor('address_1') ? 'is-invalid' : ''}
                                type="text"
                                id="address_1"
                                value={this.props.lead.address_1}
                                onChange={this.props.handleInputChanges}
                                name="address_1"
                                placeholder={translations.address_1}/>
                            {this.renderErrorFor('address_1')}
                        </FormGroup>

                        <FormGroup>
                            <Label for="address"> {translations.address_2} </Label>
                            <Input className={this.hasErrorFor('address_2') ? 'is-invalid' : ''} type="text"
                                id="address_2"
                                value={this.props.lead.address_2}
                                onChange={this.props.handleInputChanges}
                                name="address_2"
                                placeholder={translations.address_2}/>
                            {this.renderErrorFor('address_2')}
                        </FormGroup>

                        <FormGroup>
                            <Label for="postcode"> {translations.postcode} </Label>
                            <Input className={this.hasErrorFor('zip') ? 'is-invalid' : ''}
                                type="text"
                                id="zip"
                                value={this.props.lead.zip}
                                onChange={this.props.handleInputChanges}
                                name="zip"
                                placeholder={translations.postcode}/>
                            {this.renderErrorFor('zip')}
                        </FormGroup>

                        <FormGroup>
                            <Label htmlFor="city"> {translations.city} </Label>
                            <Input className={this.hasErrorFor('city') ? 'is-invalid' : ''}
                                type="text"
                                id="city"
                                value={this.props.lead.city}
                                onChange={this.props.handleInputChanges}
                                name="city"
                                placeholder={translations.city}/>
                            {this.renderErrorFor('city')}
                        </FormGroup>

                    </CardBody>
                </Card>

            </React.Fragment>
        )
    }
}
