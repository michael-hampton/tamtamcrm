import React, { Component } from 'react'
import { Card, CardBody, CardHeader, Col, Nav, NavItem, NavLink, Row, Spinner, TabContent, TabPane } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import PaymentModel from '../../models/PaymentModel'
import SectionItem from '../../common/entityContainers/SectionItem'
import Transaction from './Transaction'
import CustomerSettings from '../edit/CustomerSettings'
import CustomerModel from '../../models/CustomerModel'
import GatewayModel from '../../models/GatewayModel'
import FileUploads from '../../documents/FileUploads'
import BottomNavigationButtons from '../../common/BottomNavigationButtons'
import MetaItem from '../../common/entityContainers/MetaItem'
import Overview from './Overview'
import Details from './Details'
import ErrorLog from './ErrorLog'
import AlertPopup from '../../common/AlertPopup'
import CustomerRepository from '../../repositories/CustomerRepository'
import CompanyGatewayRepository from '../../repositories/CompanyGatewayRepository'

export default class Customer extends Component {
    constructor (props) {
        super(props)

        this.state = {
            activeTab: '1',
            show_success: false,
            show_alert: false,
            gateways: [],
            transactions: [],
            gateway_tokens: [],
            error_logs: [],
            file_count: this.props.entity.files.length || 0
        }

        this.customerModel = new CustomerModel(this.props.entity)
        this.gatewayModel = new GatewayModel()
        this.gateways = this.customerModel.gateways
        this.modules = JSON.parse(localStorage.getItem('modules'))

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        this.user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.settings = this.user_account[0].account.settings

        this.triggerAction = this.triggerAction.bind(this)
        this.toggleTab = this.toggleTab.bind(this)
    }

    componentDidMount () {
        this.getGateways()
        this.getGatewayTokens()
    }

    getGateways () {
        const gatewayRepository = new CompanyGatewayRepository()
        gatewayRepository.getGateways().then(response => {
            if (!response) {
                this.setState({ show_alert: true })
                return
            }

            this.setState({ gateways: response }, () => {
                console.log('gateways', this.state.gateways)
            })
        })
    }

    getGatewayTokens () {
        const customerRepository = new CustomerRepository()
        customerRepository.gatewayTokens(this.props.entity.id).then(response => {
            if (!response) {
                this.setState({ show_alert: true })
                return
            }

            this.setState({ gateway_tokens: response }, () => {
                console.log('gateway_tokens', this.state.gateway_tokens)
            })
        })
    }

    getTransactions () {
        const customerRepository = new CustomerRepository()
        customerRepository.transactions(this.props.entity.id).then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ transactions: response }, () => {
                console.log('transactions', this.state.transactions)
            })
        })
    }

    getErrorLogs () {
        const customerRepository = new CustomerRepository()
        customerRepository.error_logs(this.props.entity.id).then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ error_logs: response }, () => {
                console.log('error_logs', this.state.error_logs)
            })
        })
    }

    triggerAction (action) {
        const paymentModel = new PaymentModel(null, this.props.entity)
        paymentModel.completeAction(this.props.entity, action)
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab }, () => {
                if (tab === '3' && !this.state.transactions.length) {
                    this.getTransactions()
                }

                if (tab === '7' && !this.state.error_logs.length) {
                    this.getErrorLogs()
                }
            })
        }
    }

    render () {
        const gateway_tokens = this.state.gateways.length && this.state.gateway_tokens.length ? this.state.gateway_tokens.map((gatewayToken) => {
            console.log('gateways', this.state.gateways)
            console.log('gateway tokens', this.state.gateway_tokens)
            const companyGateway = this.state.gateways.filter(gateway => gateway.id === parseInt(gatewayToken.company_gateway_id))

            console.log('meta', gatewayToken.meta)

            const link = this.gatewayModel.getClientUrl(
                companyGateway[0].gateway_key,
                gatewayToken.customer_reference
            )

            return <SectionItem link={link}
                icon={icons.credit_card}
                title={`${translations.token} > ${companyGateway[0].gateway.name}`}
                subtitle={<MetaItem meta={gatewayToken.meta}/>}/>
        }) : null

        return (
            <React.Fragment>
                <Nav tabs>
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
                            {translations.details}
                        </NavLink>
                    </NavItem>
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '3' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('3')
                            }}
                        >
                            {translations.transactions}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '4' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('4')
                            }}
                        >
                            {translations.documents} ({this.state.file_count})
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '7' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('7')
                            }}
                        >
                            {translations.error_log}
                        </NavLink>
                    </NavItem>
                </Nav>

                <TabContent activeTab={this.state.activeTab}>
                    <TabPane tabId="1">
                        <Overview settings={this.settings} model={this.customerModel} gateway_tokens={gateway_tokens}
                            entity={this.props.entity}/>
                    </TabPane>

                    <TabPane tabId="2">
                        <Details entity={this.props.entity}/>
                    </TabPane>

                    <TabPane tabId="3">
                        {this.state.transactions.length ? <Transaction transactions={this.state.transactions}/>
                            : <Spinner style={{
                                width: '3rem',
                                height: '3rem'
                            }}/>}
                    </TabPane>

                    <TabPane tabId="4">
                        <Row>
                            <Col>
                                <Card>
                                    <CardHeader> {translations.documents} </CardHeader>
                                    <CardBody>
                                        <FileUploads updateCount={(count) => {
                                            this.setState({ file_count: count })
                                        }} entity_type="Customer" entity={this.props.entity}
                                        user_id={this.props.entity.user_id}/>
                                    </CardBody>
                                </Card>
                            </Col>
                        </Row>
                    </TabPane>

                    <TabPane tabId="5">
                        <CustomerSettings customer={this.props.entity}/>
                    </TabPane>

                    <TabPane tabId="7">
                        {this.state.error_logs ? <ErrorLog error_logs={this.state.error_logs}/> : <Spinner style={{
                            width: '3rem',
                            height: '3rem'
                        }}/>}
                    </TabPane>
                </TabContent>

                <BottomNavigationButtons button1_click={(e) => this.toggleTab('5')}
                    button1={{ label: translations.settings }}
                    button2_click={(e) => {
                        e.preventDefault()
                        window.location.href = `/#/gateway-settings?customer_id=${this.props.entity.id}`
                    }}
                    button2={{ label: translations.gateways }}/>

                <AlertPopup is_open={this.state.show_alert} message={this.state.error_message} onClose={(e) => {
                    this.setState({ show_alert: false })
                }}/>
            </React.Fragment>

        )
    }
}
