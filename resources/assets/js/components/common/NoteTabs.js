import React, { Component } from 'react'
import {
    Card,
    CardBody,
    Col,
    FormGroup,
    Input,
    Label,
    Nav,
    NavItem,
    NavLink,
    Row,
    TabContent,
    TabPane
} from 'reactstrap'
import { translations } from '../utils/_translations'
import UserDropdown from './dropdowns/UserDropdown'
import ProjectDropdown from './dropdowns/ProjectDropdown'
import DesignDropdown from './dropdowns/DesignDropdown'

export default class NoteTabs extends Component {
    constructor (props) {
        super(props)

        this.state = {
            active_note_tab: '1',
            show_success: false
        }

        this.toggleNoteTabs = this.toggleNoteTabs.bind(this)
    }

    toggleNoteTabs (tab) {
        if (this.state.active_note_tab !== tab) {
            this.setState({ active_note_tab: tab })
        }
    }

    render () {
        return (
            <Card>
                <CardBody>
                    <Nav tabs>
                        <NavItem>
                            <NavLink
                                className={this.state.active_note_tab === '1' ? 'active' : ''}
                                onClick={() => {
                                    this.toggleNoteTabs('1')
                                }}
                            >
                                {translations.customer_note}
                            </NavLink>
                        </NavItem>
                        <NavItem>
                            <NavLink
                                className={this.state.active_note_tab === '2' ? 'active' : ''}
                                onClick={() => {
                                    this.toggleNoteTabs('2')
                                }}
                            >
                                {translations.internal_note}
                            </NavLink>
                        </NavItem>

                        <NavItem>
                            <NavLink
                                className={this.state.active_note_tab === '3' ? 'active' : ''}
                                onClick={() => {
                                    this.toggleNoteTabs('3')
                                }}
                            >
                                {translations.terms}
                            </NavLink>
                        </NavItem>

                        <NavItem>
                            <NavLink
                                className={this.state.active_note_tab === '4' ? 'active' : ''}
                                onClick={() => {
                                    this.toggleNoteTabs('4')
                                }}
                            >
                                {translations.footer}
                            </NavLink>
                        </NavItem>

                        <NavItem>
                            <NavLink
                                className={this.state.active_note_tab === '5' ? 'active' : ''}
                                onClick={() => {
                                    this.toggleNoteTabs('5')
                                }}
                            >
                                {translations.settings}
                            </NavLink>
                        </NavItem>
                    </Nav>

                    <TabContent activeTab={this.state.active_note_tab}>
                        <TabPane tabId="1">
                            <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                                <Label>{translations.customer_note}</Label>
                                <Input
                                    placeholder={this.props.model && this.props.model.default_notes.length ? this.props.model.default_notes : ''}
                                    value={this.props.customer_note}
                                    type='textarea'
                                    name='customer_note'
                                    id='customer_note'
                                    onChange={this.props.handleInput}
                                />
                            </FormGroup>

                        </TabPane>

                        <TabPane tabId="2">
                            <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                                <Label>{translations.internal_note}</Label>
                                <Input
                                    value={this.props.internal_note}
                                    type='textarea'
                                    name='internal_note'
                                    id='internal_note'
                                    onChange={this.props.handleInput}
                                />
                            </FormGroup>
                        </TabPane>

                        <TabPane tabId="3">
                            <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                                <Label>{translations.terms}</Label>
                                <Input
                                    placeholder={this.props.model && this.props.model.default_terms.length ? this.props.model.default_terms : ''}
                                    value={this.props.terms}
                                    type='textarea'
                                    name='terms'
                                    id='notes'
                                    onChange={this.props.handleInput}
                                />
                            </FormGroup>
                        </TabPane>

                        <TabPane tabId="4">
                            <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                                <Label>{translations.footer}</Label>
                                <Input
                                    placeholder={this.props.model && this.props.model.default_footer.length ? this.props.model.default_footer : ''}
                                    value={this.props.footer}
                                    type='textarea'
                                    name='footer'
                                    id='footer'
                                    onChange={this.props.handleInput}
                                />
                            </FormGroup>
                        </TabPane>

                        <TabPane tabId="5">
                            <Row form>
                                <Col md={6}>
                                    <FormGroup>
                                        <Label for="postcode">{translations.assigned_user}:</Label>
                                        <UserDropdown
                                            user_id={this.props.invoice.assigned_to}
                                            name="assigned_to"
                                            errors={this.props.errors}
                                            handleInputChanges={this.props.handleInput}
                                        />
                                    </FormGroup>
                                </Col>

                                <Col md={6}>
                                    <FormGroup>
                                        <Label for="postcode">{translations.design}:</Label>
                                        <DesignDropdown name="design_id" design={this.props.invoice.design_id}
                                            handleChange={this.props.handleInput}/>
                                    </FormGroup>
                                </Col>
                            </Row>

                            <Row>
                                <Col md={6}>
                                    <FormGroup>
                                        <Label>{translations.project}</Label>
                                        <ProjectDropdown
                                            projects={this.props.projects}
                                            name="project_id"
                                            handleInputChanges={this.props.handleInput}
                                            project={this.props.invoice.project_id}
                                            customer_id={this.props.invoice.customer_id}
                                        />
                                    </FormGroup>
                                </Col>

                                <Col md={6}>
                                    <FormGroup>
                                        <Label for="po_number">{translations.exchange_rate}:</Label>
                                        <Input value={this.props.invoice.exchange_rate} type="text" id="exchange_rate"
                                            name="exchange_rate"
                                            onChange={this.props.handleInput}/>
                                    </FormGroup>
                                </Col>

                                <FormGroup>
                                    <Label>{translations.reminder}</Label>
                                    <Input type="select" name="reminder" value={this.props.invoice.late_fee_reminder}
                                        onChange={this.props.handleInput}>
                                        <option value="">{translations.select_option}</option>
                                        <option
                                            value="1">{`${translations.reminder_1} ${translations.amount} - ${this.props.model.settings.amount_to_charge_1} ${translations.percent} - ${this.props.model.settings.percent_to_charge_1}`}</option>
                                        <option
                                            value="2">{`${translations.reminder_2} ${translations.amount} - ${this.props.model.settings.amount_to_charge_2} ${translations.percent} - ${this.props.model.settings.percent_to_charge_2}`}</option>
                                        <option
                                            value="3">{`${translations.reminder_3} ${translations.amount} - ${this.props.model.settings.amount_to_charge_3} ${translations.percent} - ${this.props.model.settings.percent_to_charge_3}`}</option>
                                    </Input>
                                </FormGroup>

                            </Row>
                        </TabPane>
                    </TabContent>

                </CardBody>
            </Card>)
    }
}
