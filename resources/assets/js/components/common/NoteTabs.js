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
                                {translations.public_notes}
                            </NavLink>
                        </NavItem>
                        <NavItem>
                            <NavLink
                                className={this.state.active_note_tab === '2' ? 'active' : ''}
                                onClick={() => {
                                    this.toggleNoteTabs('2')
                                }}
                            >
                                {translations.private_notes}
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
                                <Label>{translations.public_notes}</Label>
                                <Input
                                    value={this.props.public_notes}
                                    type='textarea'
                                    name='public_notes'
                                    id='public_notes'
                                    onChange={this.props.handleInput}
                                />
                            </FormGroup>

                        </TabPane>

                        <TabPane tabId="2">
                            <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                                <Label>{translations.private_notes}</Label>
                                <Input
                                    value={this.props.private_notes}
                                    type='textarea'
                                    name='private_notes'
                                    id='private_notes'
                                    onChange={this.props.handleInput}
                                />
                            </FormGroup>
                        </TabPane>

                        <TabPane tabId="3">
                            <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                                <Label>{translations.terms}</Label>
                                <Input
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

                                {!!this.props.show_exchange &&
                                <Col md={6}>
                                    <FormGroup>
                                        <Label for="po_number">{translations.exchange_rate}:</Label>
                                        <Input value={this.props.invoice.exchange_rate} type="text" id="exchange_rate"
                                            name="exchange_rate"
                                            onChange={this.props.handleInput}/>
                                    </FormGroup>
                                </Col>
                                }

                            </Row>
                        </TabPane>
                    </TabContent>

                </CardBody>
            </Card>)
    }
}
