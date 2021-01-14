import React from 'react'
import { Card, CardBody, CardHeader, Col, FormGroup, Input, Label, Row } from 'reactstrap'
import FormBuilder from '../../settings/FormBuilder'
import DropdownDate from '../../common/DropdownDate'
import { translations } from '../../utils/_translations'
import PasswordField from '../../common/PasswordField'

export default class DetailsForm extends React.Component {
    constructor (props) {
        super(props)

        this.defaultValues = {
            year: 'Select Year',
            month: 'Select Month',
            day: 'Select Day'
        }

        this.classes = {
            dateContainer: 'form-row',
            yearContainer: 'col-md-4 mb-3',
            monthContainer: 'col-md-4 mb-3',
            dayContainer: 'col-md-4 mb-3'
        }

        this.buildGenderDropdown = this.buildGenderDropdown.bind(this)
    }

    buildGenderDropdown () {
        const arrOptions = ['male', 'female']

        const options = arrOptions.map(option => {
            return <option key={option} value={option}>{option}</option>
        })

        return (
            <FormGroup>
                <Label for="gender">Gender(*):</Label>
                <Input value={this.props.user.gender}
                    className={this.props.hasErrorFor('gender') ? 'is-invalid' : ''}
                    type="select"
                    name="gender"
                    onChange={this.props.handleInput.bind(this)}>
                    <option value="">Select gender</option>
                    {options}
                </Input>
                {this.props.renderErrorFor('gender')}
            </FormGroup>
        )
    }

    render () {
        const genderList = this.buildGenderDropdown()
        const customFields = this.props.custom_fields ? this.props.custom_fields : []
        const customForm = customFields && customFields.length ? <FormBuilder
            handleChange={this.props.handleInput.bind(this)}
            formFieldsRows={customFields}
        /> : null
        return (<Card>
            <CardHeader>{translations.name}</CardHeader>
            <CardBody>
                <Row form>
                    <Col md={6}>
                        <FormGroup>
                            <Label for="username">{translations.username}(*):</Label>
                            <Input className={this.props.hasErrorFor('username') ? 'is-invalid' : ''}
                                placeholder={translations.username}
                                type="text"
                                name="username"
                                value={this.props.user.username}
                                onChange={this.props.handleInput.bind(this)}/>
                            <small className="form-text text-muted">Your username must be
                                    "firstname"."lastname"
                                    eg
                                    joe.bloggs.
                            </small>
                            {this.props.renderErrorFor('username')}
                        </FormGroup>
                    </Col>

                    <Col md={6}>
                        <FormGroup>
                            <Label for="email">{translations.email}(*):</Label>
                            <Input className={this.props.hasErrorFor('email') ? 'is-invalid' : ''}
                                placeholder={translations.email}
                                type="email"
                                name="email"
                                value={this.props.user.email}
                                onChange={this.props.handleInput.bind(this)}/>
                            {this.props.renderErrorFor('email')}
                        </FormGroup>
                    </Col>
                </Row>

                <Row form>
                    <Col md={6}>
                        <FormGroup>
                            <Label for="first_name">{translations.first_name}(*):</Label>
                            <Input className={this.props.hasErrorFor('first_name') ? 'is-invalid' : ''}
                                type="text"
                                name="first_name"
                                value={this.props.user.first_name}
                                placeholder={translations.first_name}
                                onChange={this.props.handleInput.bind(this)}/>
                            {this.props.renderErrorFor('first_name')}
                        </FormGroup>
                    </Col>

                    <Col md={6}>
                        <FormGroup>
                            <Label for="last_name">{translations.last_name}(*):</Label>
                            <Input className={this.props.hasErrorFor('last_name') ? 'is-invalid' : ''}
                                type="text"
                                value={this.props.user.last_name}
                                placeholder={translations.last_name}
                                name="last_name"
                                onChange={this.props.handleInput.bind(this)}/>
                            {this.props.renderErrorFor('last_name')}
                        </FormGroup>
                    </Col>
                </Row>

                <Row form>
                    <Col md={6}>
                        {genderList}
                    </Col>

                    <Col md={6}>
                        <DropdownDate selectedDate={this.props.user.dob} classes={this.classes}
                            defaultValues={this.defaultValues}
                            onDateChange={this.props.setDate}/>
                    </Col>
                </Row>

                <Row form>
                    <Col md={4}>
                        <FormGroup>
                            <Label for="phone_number">{translations.phone_number}:</Label>
                            <Input className={this.props.hasErrorFor('phone_number') ? 'is-invalid' : ''}
                                value={this.props.user.phone_number}
                                type="tel"
                                name="phone_number"
                                onChange={this.props.handleInput.bind(this)}/>
                            {this.props.renderErrorFor('phone_number')}
                        </FormGroup>
                    </Col>

                    <Col md={4}>
                        <FormGroup>
                            <Label for="job_description">{translations.job_description}:</Label>
                            <Input className={this.props.hasErrorFor('job_description') ? 'is-invalid' : ''}
                                type="text"
                                placeholder={translations.job_description}
                                value={this.props.user.job_description}
                                name="job_description"
                                onChange={this.props.handleInput.bind(this)}/>
                            {this.props.renderErrorFor('job_description')}
                        </FormGroup>
                    </Col>

                    <Col md={4}>
                        <FormGroup>
                            <PasswordField password={this.props.user.password}
                                handleChange={this.props.handleInput.bind(this)}
                                hasErrorFor={this.props.hasErrorFor}
                                renderErrorFor={this.props.renderErrorFor}/>
                            {/* <Label for="password">{translations.password}:</Label> */}
                            {/* <Input className={this.props.hasErrorFor('password') ? 'is-invalid' : ''} */}
                            {/*    value={this.props.user.password} */}
                            {/*    type="password" */}
                            {/*    name="password" onChange={this.props.handleInput.bind(this)}/> */}
                            {/* <small className="form-text text-muted">Your password must be more than 8 */}
                            {/*        characters */}
                            {/*        long, */}
                            {/*        should contain at-least 1 Uppercase, 1 Lowercase, 1 Numeric and 1 */}
                            {/*        special */}
                            {/*        character.. */}
                            {/* </small> */}
                            {/* {this.props.renderErrorFor('password')} */}
                        </FormGroup>
                    </Col>
                </Row>
                {customForm}
            </CardBody>
        </Card>

        )
    }
}
