import React, {useState, useEffect, useRef, Component} from "react";
import {Col, FormGroup, Input, Label, Row, Form, Button} from 'reactstrap'
import {translations} from "../utils/_translations";
import {consts} from "../utils/_consts";
import AccountRepository from "../repositories/AccountRepository";

export default class Reminders extends Component {
    constructor(props) {
        super(props)

        this.state = {
            reminders: [{
                amount_to_charge: 0,
                amount_type: 'fixed',
                scheduled_to_send: null,
                number_of_days_after: 1,
                enabled: false,
                subject: '',
                message: ''
            }]
        }
    }

    componentDidMount () {
        this.getReminders()
    }

    static getDerivedStateFromProps (props, state) {
        if (props.reminders && props.reminders.length && props.reminders !== state.reminders) {
            return { reminders: props.reminders }
        }

        return null
    }

    getReminders () {
        const accountRepository = new AccountRepository()
        accountRepository.getReminders().then(response => {
            if (!response) {
                alert('error')
                return false
            }

            this.setState({
                reminders: response,
            }, () => {
                console.log(response)
            })
        })
    }

    // handle input change
    handleInputChange = (e, index) => {
        let {name, value, type} = e.target;

        if (type === 'checkbox') {
            value = e.target.checked
        }

        const list = [...this.state.reminders];
        list[index][name] = value;
        this.setState({reminders: list});
    };

    // handle click event of the Remove button
    handleRemoveClick = index => {
        const list = [...this.state.reminders];
        list.splice(index, 1);
        this.setState({reminders: list}, () => {
            this.props.setReminders(this.state.reminders)
        });
    };

    // handle click event of the Add button
    handleAddClick = () => {
        this.setState({
            reminders: [...this.state.reminders, {
                amount_to_charge: 0,
                amount_type: 'fixed',
                scheduled_to_send: null,
                number_of_days_after: 1,
                enabled: false,
                subject: '',
                message: ''
            }]
        }, () => {
            this.props.setReminders(this.state.reminders)
        })
    };

    render() {
        const {reminders} = this.state

        return (
            <div className="App">
                <h3>{translations.reminders}</h3>
                {reminders.map((x, i) => {
                    console.log('x', x)
                    return (
                        <Form>
                            <Row form>
                                <Col md={2}>
                                    <FormGroup>
                                        <Label for="exampleEmail">{translations.late_fee_amount}</Label>
                                        <Input
                                            name="amount_to_charge"
                                            placeholder={translations.late_fee_amount}
                                            value={x.amount_to_charge}
                                            onChange={e => this.handleInputChange(e, i)}
                                        />
                                    </FormGroup>
                                </Col>
                                <Col md={2}>
                                    <FormGroup>
                                        <Label for="exampleEmail">{translations.amount_type}</Label>
                                        <Input
                                            type="select"
                                            name="amount_type"
                                            placeholder={translations.late_fee_amount}
                                            value={x.amount_type}
                                            onChange={e => this.handleInputChange(e, i)}
                                        >
                                            <option value="fixed">{translations.fixed}</option>
                                            <option value="percent">{translations.percent}</option>
                                        </Input>
                                    </FormGroup>
                                </Col>
                                <Col md={2}>
                                    <FormGroup>
                                        <Label for="examplePassword">{translations.schedule}</Label>
                                        <Input
                                            type="select"
                                            name="scheduled_to_send"
                                            value={x.scheduled_to_send}
                                            onChange={e => this.handleInputChange(e, i)}
                                        >
                                            <option value="">{translations.select_option}</option>
                                            <option
                                                value={consts.reminder_schedule_after_invoice_date}>{translations.after_invoice_date}</option>
                                            <option
                                                value={consts.reminder_schedule_before_due_date}>{translations.before_due_date}</option>
                                            <option
                                                value={consts.reminder_schedule_after_due_date}>{translations.after_due_date}</option>
                                        </Input>
                                    </FormGroup>
                                </Col>
                                <Col md={2}>
                                    <FormGroup>
                                        <Label for="examplePassword">{translations.days}</Label>
                                        <Input
                                            name="number_of_days_after"
                                            placeholder={translations.number_of_days}
                                            value={x.number_of_days_after}
                                            onChange={e => this.handleInputChange(e, i)}
                                        />
                                    </FormGroup>
                                </Col>
                                <Col md={2}>
                                    <FormGroup>
                                        <Label for="examplePassword">{translations.enabled}</Label>
                                        <FormGroup check>
                                            <Input
                                                value={x.enabled}
                                                type="checkbox"
                                                name="enabled"
                                                id="enabled"
                                                onChange={e => this.handleInputChange(e, i)}
                                            />
                                            <Label for="exampleCheck" check>{translations.enabled}</Label>
                                        </FormGroup>
                                    </FormGroup>
                                </Col>
                            </Row>

                            <Row form>
                                <Col md={4}>
                                    <FormGroup>
                                        <Label for="examplePassword">{translations.subject}</Label>
                                        <Input
                                            name="subject"
                                            placeholder={translations.subject}
                                            value={x.subject}
                                            onChange={e => this.handleInputChange(e, i)}
                                        />
                                    </FormGroup>
                                </Col>
                                <Col md={4}>
                                    <FormGroup>
                                        <Label for="exampleCheck" check>{translations.message}</Label>
                                        <Input
                                            className="textarea-lg"
                                            value={x.message}
                                            type="textarea"
                                            name="message"
                                            id="message"
                                            onChange={e => this.handleInputChange(e, i)}
                                        />
                                    </FormGroup>
                                </Col>
                            </Row>

                            <div className="btn-box">
                                {reminders.length !== 1 && <Button color="danger"
                                                                   className="mr-2"
                                                                   onClick={() => this.handleRemoveClick(i)}>{translations.remove}</Button>}
                                {reminders.length - 1 === i &&
                                <Button color="success" onClick={this.handleAddClick}>{translations.add}</Button>}
                            </div>
                        </Form>

                    );
                })}
            </div>
        );
    }

}