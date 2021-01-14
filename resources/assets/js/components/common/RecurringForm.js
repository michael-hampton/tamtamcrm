import React, { Component } from 'react'
import { Card, CardBody, CardHeader, Collapse, Form, FormGroup, Input, Label } from 'reactstrap'
import { icons } from '../utils/_icons'
import { translations } from '../utils/_translations'
import Datepicker from './Datepicker'

export default class RecurringForm extends Component {
    constructor (props) {
        super(props)

        this.state = {
            isOpen: false
        }

        this.toggleFilters = this.toggleFilters.bind(this)
    }

    toggleFilters () {
        this.setState({ isOpen: !this.state.isOpen }, () => {
            const e = {}
            e.target = {
                name: 'is_recurring',
                value: this.state.isOpen
            }

            this.props.handleInput(e)
        })
    }

    render () {
        return (
            <Card>
                <CardHeader>
                    <span onClick={this.toggleFilters}
                        style={{ marginBottom: '1rem', fontSize: '18px' }}>
                        <i style={{ display: (this.state.isOpen ? 'none' : 'block'), marginTop: '6px' }}
                            className={`fa fa-fw ${icons.right} pull-left`}/>
                        <i style={{ display: (!this.state.isOpen ? 'none' : 'block'), marginTop: '6px' }}
                            className={`fa fa-fw ${icons.down} pull-left`}/>
                    </span>
                    {translations.recurring}</CardHeader>
                <Collapse
                    isOpen={this.state.isOpen}
                >
                    <CardBody>
                        <Form>
                            <FormGroup>
                                <Label for="start_date">{translations.start_date}(*):</Label>
                                <Datepicker name="recurring_start_date" date={this.props.recurring.recurring_start_date}
                                    handleInput={this.props.handleInput}
                                    className={this.props.hasErrorFor('start_date') ? 'form-control is-invalid' : 'form-control'}/>
                                {this.props.renderErrorFor('start_date')}
                            </FormGroup>

                            <FormGroup>
                                <Label for="recurring_end_date">{translations.end_date}(*):</Label>
                                <Datepicker name="recurring_end_date" date={this.props.recurring.recurring_end_date}
                                    handleInput={this.props.handleInput}
                                    className={this.props.hasErrorFor('recurring_end_date') ? 'form-control is-invalid' : 'form-control'}/>
                                {this.props.renderErrorFor('recurring_end_date')}
                            </FormGroup>

                            <FormGroup>
                                <Label for="recurring_due_date">{translations.due_date}(*):</Label>
                                <Datepicker name="recurring_due_date" date={this.props.recurring.recurring_due_date}
                                    handleInput={this.props.handleInput}
                                    className={this.props.hasErrorFor('recurring_due_date') ? 'form-control is-invalid' : 'form-control'}/>
                                {this.props.renderErrorFor('recurring_due_date')}
                            </FormGroup>

                            <FormGroup>
                                <Label>{translations.frequency}</Label>
                                <Input
                                    value={this.props.recurring.recurring_frequency}
                                    type='text'
                                    name='recurring_frequency'
                                    placeholder="Days"
                                    id='frequency'
                                    onChange={this.props.handleInput}
                                />
                            </FormGroup>
                        </Form>
                    </CardBody>
                </Collapse>
            </Card>

        )
    }
}
