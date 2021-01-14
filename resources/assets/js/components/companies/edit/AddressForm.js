import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import CountryDropdown from '../../common/dropdowns/CountryDropdown'
import { translations } from '../../utils/_translations'

export default class AddressForm extends React.Component {
    constructor (props) {
        super(props)

        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    hasErrorFor (field) {
        return !this.props.errors || !!this.props.errors[field]
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
        return (<Card>
            <CardHeader>Address</CardHeader>
            <CardBody>
                <FormGroup>
                    <Label for="address_1">{translations.address_1}(*):</Label>
                    <Input className={this.hasErrorFor('address_1') ? 'is-invalid' : ''}
                        placeholder={translations.address_1}
                        type="text"
                        name="address_1"
                        value={this.props.company.address_1}
                        onChange={this.props.handleInput}/>
                    {this.renderErrorFor('address_1')}
                </FormGroup>

                <FormGroup>
                    <Label for="address_2">{translations.address_2}:</Label>
                    <Input className={this.hasErrorFor('address_2') ? 'is-invalid' : ''}
                        placeholder={translations.address_2}
                        type="text"
                        name="address_2"
                        value={this.props.company.address_2}
                        onChange={this.props.handleInput}/>
                    {this.renderErrorFor('address_2')}
                </FormGroup>

                <FormGroup>
                    <Label for="town">{translations.town}(*):</Label>
                    <Input className={this.hasErrorFor('town') ? 'is-invalid' : ''}
                        placeholder={translations.town}
                        type="text"
                        name="town"
                        value={this.props.company.town}
                        onChange={this.props.handleInput}/>
                    {this.renderErrorFor('town')}
                </FormGroup>

                <FormGroup>
                    <Label for="city">{translations.city}(*):</Label>
                    <Input className={this.hasErrorFor('city') ? 'is-invalid' : ''}
                        placeholder={translations.city}
                        type="text"
                        name="city"
                        value={this.props.company.city}
                        onChange={this.props.handleInput}/>
                    {this.renderErrorFor('city')}
                </FormGroup>

                <FormGroup>
                    <Label for="postcode">{translations.postcode}(*):</Label>
                    <Input className={this.hasErrorFor('postcode') ? 'is-invalid' : ''}
                        placeholder={translations.postcode}
                        type="text"
                        name="postcode"
                        value={this.props.company.postcode}
                        onChange={this.props.handleInput}/>
                    {this.renderErrorFor('postcode')}
                </FormGroup>

                <FormGroup>
                    <Label for="postcode">{translations.country}(*):</Label>
                    <CountryDropdown
                        country={this.props.company.country_id}
                        errors={this.props.errors}
                        handleInputChanges={this.props.handleInput.bind(this)}
                    />
                </FormGroup>
            </CardBody>
        </Card>
        )
    }
}
