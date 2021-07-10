import React, { Component } from 'react'
import { Alert, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import { translations } from '../../utils/_translations'
import BottomNavigationButtons from '../../common/BottomNavigationButtons'
import Overview from './Overview'
import AlertPopup from '../../common/AlertPopup'
import PlanSubscriptionModel from '../../models/PlanSubscriptionModel'
import InvoiceRepository from '../../repositories/InvoiceRepository'

export default class PlanSubscription extends Component {
    constructor (props) {
        super(props)

        this.state = {
            entity: this.props.entity,
            show_alert: false,
            activeTab: '1',
            show_success: false,
            invoices: []
        }

        this.planModel = new PlanSubscriptionModel(this.state.entity)
        this.triggerAction = this.triggerAction.bind(this)
        this.toggleTab = this.toggleTab.bind(this)
        this.refresh = this.refresh.bind(this)
        this.getInvoices = this.getInvoices.bind(this)
    }

    componentDidMount () {
        this.getInvoices()
    }

    refresh (entity) {
        this.planModel = new PlanSubscriptionModel(this.state.entity)
        this.setState({ entity: entity })
    }

    getInvoices () {
        const invoiceRepository = new InvoiceRepository()
        invoiceRepository.get().then(response => {
            if (!response) {
                this.setState({ show_alert: true })
                return false
            }

            this.setState({ invoices: response }, () => {
                console.log('invoices', this.state.invoices)
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
                <Overview model={this.planModel} entity={this.state.entity}
                    invoices={this.state.invoices} />

                {this.state.show_success &&
                <Alert color="primary">
                    {translations.action_completed}
                </Alert>
                }

                <BottomNavigationButtons button1_click={(e) => this.toggleTab('2')}
                    button1={{ label: translations.refund }}
                    button2_click={(e) => this.triggerAction('archive')}
                    button2={{ label: translations.archive }}/>

                <AlertPopup is_open={this.state.show_alert} message={this.state.error_message} onClose={(e) => {
                    this.setState({ show_alert: false })
                }}/>
            </React.Fragment>
        )
    }
}
