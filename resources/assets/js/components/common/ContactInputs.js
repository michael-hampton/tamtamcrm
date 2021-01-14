import React, { Component } from 'react'
import { Card, CardBody, CardHeader, Col, Collapse, FormGroup, Input, Label, Row } from 'reactstrap'
import { translations } from '../utils/_translations'
import { icons } from '../utils/_icons'
import DeleteModal from './DeleteModal'

export default class ContactInputs extends Component {
    constructor (props) {
        super(props)
        this.state = {
            collapse: null,
            contacts: this.props.contacts,
            errors: [],
            showSuccessMessage: false,
            showErrorMessage: false,
            message: ''
        }

        this.toggle = this.toggle.bind(this)
    }

    toggle (e) {
        const event = e.currentTarget.dataset.event
        this.setState({ collapse: this.state.collapse === Number(event) ? null : Number(event) })
    }

    render () {
        const { collapse } = this.state
        const { contacts } = this.props

        return (
            <div className="container">
                {contacts.map((contact, idx) => {
                    const icon = collapse === idx ? icons.angle_up : icons.angle_down

                    return (
                        <Card style={{ marginBottom: '1rem' }} key={idx}>
                            <CardHeader onClick={this.toggle} data-event={idx}>
                                <h5 className="mb-1 d-flex justify-content-between align-items-center">
                                    {`${contact.first_name} ${contact.last_name}`}
                                    <i className={`fa ${icon}`}/>
                                </h5>

                                <h6 className="text-muted">
                                    {contact.email}
                                </h6>
                            </CardHeader>
                            <Collapse isOpen={collapse === idx}>
                                <CardBody>
                                    <Row form>
                                        <Col md={5}>
                                            <FormGroup>
                                                <Label for="exampleEmail">{translations.first_name}</Label>
                                                <Input type="text"
                                                    onChange={this.props.handleChange}
                                                    data-id={idx}
                                                    value={contact.first_name}
                                                    data-field="first_name"
                                                />
                                            </FormGroup>
                                        </Col>
                                        <Col md={5}>
                                            <FormGroup>
                                                <Label for="examplePassword">{translations.last_name}</Label>
                                                <Input type="text"
                                                    onChange={this.props.handleChange}
                                                    data-id={idx}
                                                    value={contact.last_name}
                                                    data-field="last_name"
                                                />
                                            </FormGroup>
                                        </Col>

                                        <Col md={2}>
                                            <FormGroup className="mt-4" check>
                                                <Label check>
                                                    <Input type="checkbox"
                                                        onChange={this.props.handleChange}
                                                        data-id={idx}
                                                        checked={contact.is_primary}
                                                        data-field="is_primary"
                                                    />
                                                    {translations.primary_contact}
                                                </Label>
                                            </FormGroup>
                                        </Col>
                                    </Row>

                                    <Row form>
                                        <Col md={4}>
                                            <FormGroup>
                                                <Label for="exampleEmail">{translations.email}</Label>
                                                <Input type="text"
                                                    onChange={this.props.handleChange}
                                                    data-id={idx}
                                                    value={contact.email}
                                                    data-field="email"
                                                />
                                            </FormGroup>
                                        </Col>
                                        <Col md={4}>
                                            <FormGroup>
                                                <Label for="examplePassword">{translations.phone_number}</Label>
                                                <Input type="text"
                                                    onChange={this.props.handleChange}
                                                    data-id={idx}
                                                    value={contact.phone}
                                                    data-field="phone"
                                                />
                                            </FormGroup>
                                        </Col>

                                        <Col md={4}>
                                            <FormGroup>
                                                <Label for="examplePassword">{translations.password}</Label>
                                                <Input type="password"
                                                    onChange={this.props.handleChange}
                                                    data-id={idx}
                                                    value={contact.password}
                                                    data-field="password"
                                                />
                                            </FormGroup>
                                        </Col>
                                    </Row>
                                </CardBody>
                            </Collapse>

                            <DeleteModal is_button={true} deleteFunction={this.props.removeContact} archive={false}
                                id={idx}/>
                        </Card>
                    )
                })}
            </div>
        )
    }
}
