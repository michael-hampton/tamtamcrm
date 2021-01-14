import React from 'react'
import { Button, Card, CardBody, CardHeader, CustomInput, FormGroup, Input, Label } from 'reactstrap'
import FormBuilder from '../../settings/FormBuilder'
import { translations } from '../../utils/_translations'

export default class DetailsForm extends React.Component {
    constructor (props) {
        super(props)

        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
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
        const customFields = this.props.custom_fields ? this.props.custom_fields : []

        if (customFields[0] && Object.keys(customFields[0]).length) {
            customFields[0].forEach((element, index, array) => {
                if (this.props[element.name] && this.props[element.name].length) {
                    customFields[0][index].value = this.props[element.name]
                }
            })
        }

        const customForm = customFields && customFields.length ? <FormBuilder
            handleChange={this.props.handleInput.bind(this)}
            formFieldsRows={customFields}
        /> : null

        return (<Card>
            <CardHeader>{translations.details}</CardHeader>
            <CardBody>
                <FormGroup>
                    <Label for="name">{translations.name}(*):</Label>
                    <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''}
                        type="text"
                        placeholder="Name"
                        name="name"
                        value={this.props.company.name}
                        onChange={this.props.handleInput}/>
                    {this.renderErrorFor('name')}
                </FormGroup>

                <FormGroup>
                    <Label for="website">Website(*):</Label>
                    <Input className={this.hasErrorFor('website') ? 'is-invalid' : ''}
                        type="text"
                        name="website"
                        placeholder="Website"
                        value={this.props.company.website}
                        onChange={this.props.handleInput}/>
                    {this.renderErrorFor('website')}
                </FormGroup>

                <FormGroup>
                    <Label for="phone_number">Phone Number(*):</Label>
                    <Input className={this.hasErrorFor('phone_number') ? 'is-invalid' : ''}
                        placeholder="Phone Number"
                        type="tel"
                        name="phone_number"
                        value={this.props.company.phone_number}
                        onChange={this.props.handleInput}/>
                    {this.renderErrorFor('phone_number')}
                </FormGroup>

                <FormGroup>
                    <Label for="email">Email(*):</Label>
                    <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''}
                        placeholder="Email"
                        type="email"
                        name="email"
                        value={this.props.company.email}
                        onChange={this.props.handleInput}/>
                    {this.renderErrorFor('email')}
                </FormGroup>

                <FormGroup>
                    <Label>Logo</Label>
                    <CustomInput className="mt-4 mb-4"
                        onChange={this.props.handleFileChange} type="file"
                        id="company_logo" name="company_logo"
                        label="Logo"/>
                </FormGroup>

                {!!this.props.company.logo && !!this.props.company.logo.length &&
                    <React.Fragment>
                        <img src={`${this.props.company.logo}`}/>
                        <Button color="danger" onClick={this.props.removeLogo}>{translations.remove}</Button>
                    </React.Fragment>

                }

                {customForm}
            </CardBody>
        </Card>
        )
    }
}
