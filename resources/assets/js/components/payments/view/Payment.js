import React, { Component } from 'react'
import { Alert, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import { translations } from '../../utils/_translations'
import PaymentModel from '../../models/PaymentModel'
import Refund from '../edit/Refund'
import BottomNavigationButtons from '../../common/BottomNavigationButtons'
import GatewayModel from '../../models/GatewayModel'
import Overview from './Overview'

export default class Payment extends Component {
    constructor (props) {
        super(props)

        this.state = {
            entity: this.props.entity,
            activeTab: '1',
            show_success: false,
            gateways: []
        }

        this.gatewayModel = new GatewayModel()
        this.paymentModel = new PaymentModel(this.state.entity.invoices, this.state.entity, this.state.entity.credits)
        this.triggerAction = this.triggerAction.bind(this)
        this.toggleTab = this.toggleTab.bind(this)
        this.refresh = this.refresh.bind(this)
    }

    componentDidMount () {
        this.getGateways()
    }

    refresh (entity) {
        this.paymentModel = new PaymentModel(this.state.entity.invoices, entity, this.state.entity.credits)
        this.setState({ entity: entity })
    }

    getGateways () {
        this.gatewayModel.getGateways().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ gateways: response }, () => {
                console.log('gateways', this.state.gateways)
            })
        })
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    triggerAction (action) {
        this.paymentModel.completeAction(this.state.entity, action).then(response => {
            this.setState({ show_success: true }, () => {
                this.props.updateState(response, this.refresh)
            })

            setTimeout(
                function () {
                    this.setState({ show_success: false })
                }
                    .bind(this),
                2000
            )
        })
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
                </Nav>

                <TabContent activeTab={this.state.activeTab}>
                    <TabPane tabId="1">
                        <Overview model={this.paymentModel} customers={this.props.customers} entity={this.state.entity}
                            gateways={this.state.gateways} gatewayModel={this.gatewayModel}/>
                    </TabPane>

                    <TabPane tabId="2">
                        <Refund customers={this.props.customer} payment={this.state.entity}
                            modal={false}
                            allInvoices={[]}
                            allCredits={[]}
                            invoices={null}
                            credits={null}
                            paymentables={this.state.entity.paymentables}
                        />
                    </TabPane>
                </TabContent>

                {this.state.show_success &&
                <Alert color="primary">
                    {translations.action_completed}
                </Alert>
                }

                <BottomNavigationButtons button1_click={(e) => this.toggleTab('2')}
                    button1={{ label: translations.refund }}
                    button2_click={(e) => this.triggerAction('archive')}
                    button2={{ label: translations.archive }}/>
            </React.Fragment>
        )
    }
}
