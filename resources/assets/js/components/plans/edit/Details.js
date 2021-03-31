import React, { Component } from 'react'
import { Alert, FormGroup, Input, Label, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import { translations } from '../../utils/_translations'
import UserDropdown from '../../common/dropdowns/UserDropdown'
import { icons } from '../../utils/_icons'
import SwitchWithIcon from '../../common/SwitchWithIcon'

export default class Details extends Component {
    constructor (props) {
        super(props)

        this.state = {
            activeTab: '1'
        }

        this.toggleTab = this.toggleTab.bind(this)
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    render () {
        return (
            <React.Fragment>
                <Nav tabs className="nav-justified disable-scrollbars">
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '1' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('1')
                            }}
                        >
                            {translations.overview}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '2' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('2')
                            }}
                        >
                            {translations.settings}
                        </NavLink>
                    </NavItem>
                </Nav>

                <TabContent activeTab={this.state.activeTab}>
                    <TabPane tabId="1">
                        <FormGroup className="mb-3">
                            <Label>{translations.name}</Label>
                            <Input className={this.props.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="name"
                                value={this.props.plan.name} onChange={this.props.handleInput}/>
                            {this.props.renderErrorFor('name')}
                        </FormGroup>

                        <FormGroup className="mb-3">
                            <Label>{translations.description}</Label>
                            <Input className={this.props.hasErrorFor('description') ? 'is-invalid' : ''} type="text" name="description"
                                value={this.props.plan.description} onChange={this.props.handleInput}/>
                            {this.props.renderErrorFor('description')}
                        </FormGroup>

                        <FormGroup className="mb-3">
                            <Label>{translations.code}</Label>
                            <Input className={this.props.hasErrorFor('code') ? 'is-invalid' : ''} type="text" name="code"
                                value={this.props.plan.code} onChange={this.props.handleInput}/>
                            {this.props.renderErrorFor('code')}
                        </FormGroup>

                        <FormGroup className="mb-3">
                            <Label>{translations.price}</Label>
                            <Input className={this.props.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="price"
                                value={this.props.plan.price} onChange={this.props.handleInput}/>
                            {this.props.renderErrorFor('price')}
                        </FormGroup>

                        <FormGroup>
                            <Label for="postcode">{translations.assigned_user}:</Label>
                            <UserDropdown
                                user_id={this.props.plan.assigned_to}
                                name="assigned_to"
                                errors={this.props.errors}
                                handleInputChanges={this.props.handleInput}
                            />
                        </FormGroup>
                    </TabPane>

                    <TabPane tabId="2">
                        <div className="form-row">
                            <FormGroup className="col-md-6">
                                <Label>{translations.invoice_frequency}</Label>
                                <Input value={this.props.plan.invoice_period} type="text" onChange={this.props.handleInput} name="invoice_period" placeholder={translations.days} />
                            </FormGroup>
                            <FormGroup className="col-md-6">
                                <Label>{translations.frequency}</Label>
                                <Input value={this.props.plan.invoice_interval} type="select" onChange={this.props.handleInput} name="invoice_interval">
                                    <option value="">{translations.select_option}</option>
                                    <option value="day">{translations.day}</option>
                                    <option value="month">{translations.month}</option>
                                    <option value="year">{translations.year}</option>
                                </Input>
                            </FormGroup>
                        </div>

                        <div className="form-row">
                            <FormGroup className="col-md-6">
                                <Label>{translations.grace_period}</Label>
                                <Input value={this.props.plan.grace_period} type="text" onChange={this.props.handleInput} name="grace_period" />
                            </FormGroup>
                            <FormGroup className="col-md-6">
                                <Label>{translations.frequency}</Label>
                                <Input value={this.props.plan.grace_interval} type="select" onChange={this.props.handleInput} name="grace_interval">
                                    <option value="">{translations.select_option}</option>
                                    <option value="day">{translations.day}</option>
                                    <option value="month">{translations.month}</option>
                                    <option value="year">{translations.year}</option>
                                </Input>
                            </FormGroup>
                        </div>

                        <div className="form-row">
                            <FormGroup className="col-md-6">
                                <Label>{translations.trial_period}</Label>
                                <Input value={this.props.plan.trial_period} type="text" onChange={this.props.handleInput} name="trial_period" />
                            </FormGroup>
                            <FormGroup className="col-md-6">
                                <Label>{translations.frequency}</Label>
                                <Input value={this.props.plan.trial_interval} type="select" onChange={this.props.handleInput} name="trial_interval">
                                    <option value="">{translations.select_option}</option>
                                    <option value="day">{translations.day}</option>
                                    <option value="month">{translations.month}</option>
                                    <option value="year">{translations.year}</option>
                                </Input>
                            </FormGroup>
                        </div>

                        <SwitchWithIcon
                            icon={icons.customer}
                            label={translations.can_cancel_plan}
                            checked={this.props.plan.can_cancel_plan}
                            name="can_cancel_plan"
                            handleInput={this.props.handleInput}
                            // help_text={item.help_text}
                        />

                        <SwitchWithIcon
                            icon={icons.customer}
                            label={translations.auto_billing_enabled}
                            checked={this.props.plan.auto_billing_enabled}
                            name="auto_billing_enabled"
                            handleInput={this.props.handleInput}
                            // help_text={item.help_text}
                        />

                        <FormGroup className="mb-3">
                            <Label>{translations.active_subscribers_limit}</Label>
                            <Input className={this.props.hasErrorFor('active_subscribers_limit') ? 'is-invalid' : ''} type="text" name="active_subscribers_limit"
                                value={this.props.plan.active_subscribers_limit} onChange={this.props.handleInput}/>
                            {this.props.renderErrorFor('active_subscribers_limit')}
                        </FormGroup>
                    </TabPane>
                </TabContent>

                {this.state.show_success &&
                <Alert color="primary">
                    {translations.action_completed}
                </Alert>
                }
            </React.Fragment>
        )
    }
}
