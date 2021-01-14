import React, { Component } from 'react'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import Invoice from '../invoice/view/Invoice'
import RecurringInvoice from '../recurringInvoices/view/RecurringInvoice'
import Payment from '../payments/view/Payment'
import Customer from '../customers/view/Customer'
import Expense from '../expenses/view/Expense'
import Quote from '../quotes/view/Quote'
import RecurringQuote from '../recurringQuotes/view/RecurringQuote'
import Credit from '../credits/view/Credit'
import Project from '../projects/view/Project'
import Company from '../companies/view/Company'
import Order from '../orders/view/Order'
import Lead from '../leads/view/Lead'
import Promocode from '../promocodes/view/Promocode'
import Product from '../products/view/Product'
import User from '../users/view/User'
import Case from '../cases/view/Case'
import Subscription from '../subscriptions/view/Subscription'
import Task from '../tasks/view/Task'
import TaxRate from '../TaxRates/view/TaxRate'
import Token from '../tokens/view/Token'
import Group from '../groups/view/Group'
import Gateway from '../gateways/view/Gateway'
import Deal from '../deals/view/Deal'
import PurchaseOrder from '../purchase_orders/view/PurchaseOrder'
import ViewExpenseCategory from '../expense_categories/view/ViewExpenseCategory'
import BankAccount from '../bank_accounts/view/BankAccount'

export default class ViewEntity extends Component {
    constructor (props) {
        super(props)
        this.state = {
            roles: [],
            modal: true
        }

        this.toggle = this.toggle.bind(this)
        this.updateState = this.updateState.bind(this)
    }

    toggle (edit = false) {
        this.setState({
            modal: false,
            errors: []
        }, () => this.props.toggle(this.props.entity, this.props.title, edit))
    }

    updateState (response, callbackFunction, is_add = false) {
        if (is_add === true) {
            const allInvoices = this.props.entities
            allInvoices.push(response)
            this.props.updateState(allInvoices)
            return
        }
        const index = this.props.entities.findIndex(entity => entity.id === response.id)
        this.props.entities[index] = response
        this.props.updateState(this.props.entities)

        callbackFunction(this.props.entities[index])
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
            ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <Modal centered={true} backdrop="static" isOpen={this.props.viewed} toggle={this.toggle}
                    className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>{this.props.title ? this.props.title : 'Details'}</ModalHeader>
                    <ModalBody className={`${theme} view-entity`}>
                        {this.props.edit &&
                        this.props.edit
                        }

                        {this.props.entity && this.props.entity_type && ['Invoice'].includes(this.props.entity_type) &&
                        <Invoice entities={this.props.entities} customers={this.props.customers}
                            entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['RecurringInvoice'].includes(this.props.entity_type) &&
                        <RecurringInvoice entities={this.props.entities} customers={this.props.customers}
                            entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Credit'].includes(this.props.entity_type) &&
                        <Credit entities={this.props.entities} customers={this.props.customers}
                            entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Order'].includes(this.props.entity_type) &&
                        <Order entities={this.props.entities} customers={this.props.customers}
                            entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Customer'].includes(this.props.entity_type) &&
                        <Customer entities={this.props.entities} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Promocode'].includes(this.props.entity_type) &&
                        <Promocode entities={this.props.entities} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Product'].includes(this.props.entity_type) &&
                        <Product entities={this.props.entities} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && (this.props.entity_type === 'Payment') &&
                        <Payment entities={this.props.entities} customers={this.props.customers}
                            entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Expense'].includes(this.props.entity_type) &&
                        <Expense entities={this.props.entities} customers={this.props.customers}
                            entity={this.props.entity}
                            updateState={this.updateState}/>
                        }

                        {this.props.entity && this.props.entity_type && ['ExpenseCategory'].includes(this.props.entity_type) &&
                        <ViewExpenseCategory entities={this.props.entities} customers={this.props.customers}
                            entity={this.props.entity}
                            updateState={this.updateState}/>
                        }

                        {this.props.entity && this.props.entity_type && ['Quote'].includes(this.props.entity_type) &&
                        <Quote entities={this.props.entities} customers={this.props.customers}
                            entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['RecurringQuote'].includes(this.props.entity_type) &&
                        <RecurringQuote entities={this.props.entities} customers={this.props.customers}
                            entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Project'].includes(this.props.entity_type) &&
                        <Project entities={this.props.entities} customers={this.props.customers}
                            entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['User'].includes(this.props.entity_type) &&
                        <User entities={this.props.entities} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Deal'].includes(this.props.entity_type) &&
                        <Deal entities={this.props.entities} customers={this.props.customers} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Company'].includes(this.props.entity_type) &&
                        <Company entities={this.props.entities} customers={this.props.customers}
                            entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Lead'].includes(this.props.entity_type) &&
                        <Lead entities={this.props.entities} customers={this.props.customers} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Case'].includes(this.props.entity_type) &&
                        <Case entities={this.props.entities} customers={this.props.customers} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Subscription'].includes(this.props.entity_type) &&
                        <Subscription entities={this.props.entities} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Task'].includes(this.props.entity_type) &&
                        <Task entities={this.props.entities} customers={this.props.customers} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Tax Rate'].includes(this.props.entity_type) &&
                        <TaxRate entities={this.props.entities} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Token'].includes(this.props.entity_type) &&
                        <Token entities={this.props.entities} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Group'].includes(this.props.entity_type) &&
                        <Group entity={this.props.entity} updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['Gateway'].includes(this.props.entity_type) &&
                        <Gateway entities={this.props.entities} entity={this.props.entity}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['PurchaseOrder'].includes(this.props.entity_type) &&
                        <PurchaseOrder entities={this.props.entities} entity={this.props.entity}
                            companies={this.props.companies}
                            updateState={this.updateState}/>}

                        {this.props.entity && this.props.entity_type && ['BankAccount'].includes(this.props.entity_type) &&
                        <BankAccount entities={this.props.entities} entity={this.props.entity}
                            updateState={this.updateState}/>}

                    </ModalBody>
                    <ModalFooter>
                        <Button onClick={this.toggle} color="secondary">Close</Button>
                    </ModalFooter>
                </Modal>
            </React.Fragment>
        )
    }
}
