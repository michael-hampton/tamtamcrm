import React, { Component } from 'react'
import { Card, CardBody, ListGroup, ListGroupItem, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import { translations } from '../utils/_translations'
import { invoice_pdf_fields } from '../models/InvoiceModel'
import { customer_pdf_fields } from '../models/CustomerModel'
import { account_pdf_fields } from '../models/AccountModel'
import { user_pdf_fields } from '../models/UserModel'

export default class Variables extends Component {
    constructor (props) {
        super(props)
        this.state = {
            show_email_variables: false,
            success: false,
            error: false,
            id: localStorage.getItem('account_id'),
            activeTab: '1',
            showConfirm: false
        }

        this.toggleTab = this.toggleTab.bind(this)
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        return (
            <React.Fragment>

                <Card className="mb-0 border-0">
                    <CardBody className="p-0">
                        <div className="d-flex justify-content-between align-items-center">
                            <h4 className="pl-3 pt-2">{translations.variables}</h4>
                            <a className="pull-right pr-3">{translations.save}</a>
                        </div>
                        <Nav tabs className="nav-justified disable-scrollbars">
                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '1' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('1')
                                    }}>
                                    {translations.invoice}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '2' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('2')
                                    }}>
                                    {translations.customer}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '3' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('3')
                                    }}>
                                    {translations.account}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '4' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('4')
                                    }}>
                                    {translations.user}
                                </NavLink>
                            </NavItem>

                            {this.state.show_email_variables &&
                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '5' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('5')
                                    }}>
                                    {translations.user}
                                </NavLink>
                            </NavItem>
                            }
                        </Nav>
                    </CardBody>
                </Card>

                <TabContent activeTab={this.state.activeTab}>
                    <TabPane tabId="1" className="px-0">
                        <Card className="border-0">
                            <CardBody>
                                <VariableGrid fields={invoice_pdf_fields}/>
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="2" className="px-0">
                        <Card className="border-0">
                            <CardBody>
                                <VariableGrid fields={customer_pdf_fields}/>
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="3" className="px-0">
                        <Card className="border-0">
                            <CardBody>
                                <VariableGrid fields={account_pdf_fields}/>
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="4" className="px-0">
                        <Card className="border-0">
                            <CardBody>
                                <VariableGrid fields={user_pdf_fields}/>
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="5" className="px-0">
                        <Card className="border-0">
                            <CardBody/>
                        </Card>
                    </TabPane>
                </TabContent>
            </React.Fragment>

        )
    }
}

class VariableGrid extends Component {
    render () {
        const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

        return (<ListGroup style={{ height: '400px', overflowY: 'auto' }}>
            {this.props.fields.map((field, idx) => {
                return <ListGroupItem key={idx} className={listClass}>{field}</ListGroupItem>
            })}
        </ListGroup>)
    }
}
