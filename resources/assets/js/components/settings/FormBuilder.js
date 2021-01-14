import React from 'react'
import { CustomInput, FormGroup, Input, Label } from 'reactstrap'
import CountryDropdown from '../common/dropdowns/CountryDropdown'
import CurrencyDropdown from '../common/dropdowns/CurrencyDropdown'
import Switch from '../common/Switch'
import PaymentTypeDropdown from '../common/dropdowns/PaymentTypeDropdown'
import PaymentTermsDropdown from '../common/dropdowns/PaymentTermsDropdown'
import { translations } from '../utils/_translations'
import LanguageDropdown from '../common/dropdowns/LanguageDropdown'
import { LearnMoreUrl } from '../common/LearnMore'
import Datepicker from '../common/Datepicker'
import UserDropdown from '../common/dropdowns/UserDropdown'

/**
 * A component which renders a form based on a given list of fields.
 */
class FormBuilder extends React.Component {
    constructor (props) {
        super(props)

        const formFields = this.getFormFields()

        // dynamically construct our initial state by using
        // each form field's name as an object property.
        const formFieldNames = formFields.reduce((obj, field) => {
            obj[field.name] = ''
            return obj
        }, {})

        // define the initial state, so we can use it later on
        // when we'll need to reset the form
        this.initialState = {
            ...formFieldNames
        }

        this.state = this.initialState
    }

    getFormFields () {
        const { formFieldsRows } = this.props
        const formFields = []

        formFieldsRows.forEach((formFieldsRow) => {
            formFields.push(...formFieldsRow)
        })

        return formFields
    }

    buildSwitch (field) {
        const class_name = field.class_name ? field.class_name : 'col-md-8'

        return (
            <a href="#"
                className={`${class_name} list-group-item-dark list-group-item list-group-item-action flex-column align-items-start`}>
                <div className="d-flex w-100 justify-content-between">
                    <h5 className="mb-1">
                        {field.icon &&
                        <i style={{ fontSize: '24px', marginRight: '20px' }} className={field.icon}/>
                        }
                        {field.label}
                    </h5>
                    <CustomInput
                        checked={field.value}
                        type="switch"
                        id={field.name}
                        name={field.name}
                        label=""
                        onChange={this.props.handleChange.bind(this)}/>
                </div>
                {field.help_text &&
                <h6 id="passwordHelpBlock" className="form-text text-muted">
                    {field.help_text}
                </h6>
                }
            </a>
        )
    }

    buildSelectList (field) {
        const arrayOfData = field.options
        const options = arrayOfData.map((data) =>
            <option
                key={data.value}
                value={data.value}
            >
                {data.text}
            </option>
        )

        return (
            <FormGroup>
                <Label>{field.label}</Label>
                <Input value={field.value} type="select" name={field.name} onChange={this.props.handleChange}>
                    <option value="">Select Item</option>
                    {options}
                </Input>
            </FormGroup>
        )
    }

    renderTextInput (field) {
        let returnedField = null

        if (field.name === '' || field.type === '' || field.label === '') {
            return
        }

        const id = field.id ? field.id : ''

        switch (field.type) {
            case 'currency':
                returnedField = <React.Fragment>
                    <FormGroup>
                        <Label>{field.label}</Label>
                        <CurrencyDropdown key={field.id}
                            currency_id={field.value}
                            errors={{}}
                            handleInputChanges={this.props.handleChange}
                        />
                    </FormGroup>
                </React.Fragment>
                break
            case 'user':
                returnedField = <React.Fragment>
                    <FormGroup>
                        <Label>{field.label}</Label>
                        <UserDropdown key={field.id}
                            user_id={field.value}
                            name={field.name}
                            errors={{}}
                            handleInputChanges={this.props.handleChange}
                        />
                    </FormGroup>
                </React.Fragment>
                break

            case 'language':
                returnedField = <React.Fragment>
                    <FormGroup>
                        <Label>{field.label}</Label>
                        <LanguageDropdown key={field.id}
                            language_id={field.value}
                            errors={{}}
                            handleInputChanges={this.props.handleChange}
                        />
                    </FormGroup>
                </React.Fragment>
                break

            case 'payment_terms':
                returnedField = <React.Fragment><Label>{translations.payment_terms}</Label>
                    <PaymentTermsDropdown
                        name={field.name}
                        payment_term={field.value}
                        handleInputChanges={this.props.handleChange}
                    />
                </React.Fragment>
                break

            case 'payment_type':
                returnedField = <React.Fragment><Label>{translations.payment_type}</Label> <PaymentTypeDropdown
                    name={field.name}
                    payment_type={field.value}
                    handleInputChanges={this.props.handleChange}
                />
                </React.Fragment>
                break

            case 'country':
                returnedField = <React.Fragment>
                    <FormGroup>
                        <Label>{field.label}</Label>
                        <CountryDropdown key={field.id}
                            country={field.value}
                            errors={{}}
                            handleInputChanges={this.props.handleChange}
                        />
                    </FormGroup>
                </React.Fragment>
                break
            case 'checkbox':
                returnedField = <Switch
                    key={field.id}
                    label={field.label}
                    name={field.name}
                    isOn={field.value}
                    handleToggle={this.props.handleCheckboxChange}
                />
                break

            case 'select':
                returnedField = this.buildSelectList(field)
                break
            case 'switch':
                returnedField = this.buildSwitch(field)
                break
            case 'date':
                returnedField = <Datepicker name={field.name} date={field.value} handleInput={this.props.handleChange}/>
                break
            default:
                returnedField = <React.Fragment>
                    <FormGroup>
                        <Label>
                            {field.label}

                            {field.help_url &&
                            <LearnMoreUrl url={field.help_url}/>
                            }
                        </Label>
                        <Input type={field.type}
                            className={field.inputClass || ''}
                            id={id}
                            value={field.value}
                            name={field.name}
                            placeholder={field.placeholder}
                            onChange={this.props.handleChange}
                        />
                    </FormGroup>
                </React.Fragment>
                break
        }

        return returnedField
    }

    render () {
        const { formFieldsRows } = this.props

        return (
            <React.Fragment>
                {/* eslint-disable react/no-array-index-key */}
                {formFieldsRows.map((formFieldsRow, i) => (
                    <div key={`r-${i}`}>
                        {formFieldsRow.map(field => this.renderTextInput(field))}
                    </div>
                ))}
                {/* eslint-enable react/no-array-index-key */}
            </React.Fragment>
        )
    }
}

export default FormBuilder
