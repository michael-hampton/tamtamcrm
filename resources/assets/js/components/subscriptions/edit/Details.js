import React, { Component } from 'react'
import { FormGroup, Input, Label } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import DecoratedFormField from '../../common/DecoratedFormField'
import { consts } from '../../utils/_consts'

export default class Details extends Component {
    render () {
        return (
            <React.Fragment>
                <FormGroup>
                    <Label for="name">{translations.name} <span className="text-danger">*</span></Label>
                    <Input className={this.props.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="name"
                        id="name" value={this.props.subscription.name} placeholder={translations.name}
                        onChange={this.props.handleInput}/>
                    {this.props.renderErrorFor('name')}
                </FormGroup>

                <Label>{translations.target_url}</Label>
                <DecoratedFormField hasErrorFor={this.props.hasErrorFor}
                    renderErrorFor={this.props.renderErrorFor} name="target_url"
                    handleChange={this.props.handleInput}
                    value={this.props.subscription.target_url} icon={icons.link}/>

                <FormGroup>
                    <Label for="event_id">{translations.event}<span className="text-danger">*</span></Label>
                    <Input className={this.props.hasErrorFor('event_id') ? 'is-invalid' : ''} type="select"
                        name="event_id"
                        id="event_id" value={this.props.subscription.event_id}
                        onChange={this.props.handleInput.bind(this)}>
                        <option value="">{translations.select_event}</option>
                        <option value={consts.order_created_subscription}>{translations.order_created}</option>
                        <option value={consts.order_updated_subscription}>{translations.order_updated}</option>
                        <option value={consts.order_deleted_subscription}>{translations.order_deleted}</option>
                        <option value={consts.order_backordered_subscription}>{translations.order_backordered}</option>
                        <option value={consts.order_held_subscription}>{translations.order_held}</option>
                        <option value={consts.credit_created_subscription}>{translations.credit_created}</option>
                        <option value={consts.credit_updated_subscription}>{translations.credit_updated}</option>
                        <option value={consts.credit_deleted_subscription}>{translations.credit_deleted}</option>
                        <option value={consts.customer_created_subscription}>{translations.customer_created}</option>
                        <option value={consts.customer_updated_subscription}>{translations.customer_updated}</option>
                        <option value={consts.customer_deleted_subscription}>{translations.customer_deleted}</option>
                        <option value={consts.invoice_created_subscription}>{translations.invoice_created}</option>
                        <option value={consts.invoice_updated_subscription}>{translations.invoice_updated}</option>
                        <option value={consts.invoice_deleted_subscription}>{translations.invoice_deleted}</option>
                        <option value={consts.payment_created_subscription}>{translations.payment_created}</option>
                        <option value={consts.payment_updated_subscription}>{translations.payment_updated}</option>
                        <option value={consts.payment_deleted_subscription}>{translations.payment_deleted}</option>
                        <option value={consts.quote_created_subscription}>{translations.quote_created}</option>
                        <option value={consts.quote_updated_subscription}>{translations.quote_updated}</option>
                        <option value={consts.quote_approved_subscription}>{translations.quote_approved}</option>
                        <option value={consts.quote_rejected_subscription}>{translations.quote_rejected}</option>
                        <option value={consts.quote_deleted_subscription}>{translations.quote_deleted}</option>
                        <option value={consts.lead_created_subscription}>{translations.lead_created}</option>
                        <option value={consts.lead_updated_subscription}>{translations.lead_updated}</option>
                        <option value={consts.deal_created_subscription}>{translations.deal_created}</option>
                        <option value={consts.deal_updated_subscription}>{translations.deal_updated}</option>
                        <option value={consts.deal_deleted_subscription}>{translations.deal_deleted}</option>
                        <option value={consts.project_created_subscription}>{translations.project_created}</option>
                        <option value={consts.project_updated_subscription}>{translations.project_updated}</option>
                        <option value={consts.project_deleted_subscription}>{translations.project_deleted}</option>
                        <option value={consts.task_created_subscription}>{translations.task_created}</option>
                        <option value={consts.task_updated_subscription}>{translations.task_updated}</option>
                        <option value={consts.task_deleted_subscription}>{translations.task_deleted}</option>
                        <option value={consts.case_created_subscription}>{translations.case_created}</option>
                        <option value={consts.case_updated_subscription}>{translations.case_updated}</option>
                        <option value={consts.case_deleted_subscription}>{translations.case_deleted}</option>
                        <option value={consts.expense_created_subscription}>{translations.expense_created}</option>
                        <option value={consts.expense_updated_subscription}>{translations.expense_updated}</option>
                        <option value={consts.expense_deleted_subscription}>{translations.expense_deleted}</option>
                        <option
                            value={consts.purchase_order_created_subscription}>{translations.purchase_order_created}</option>
                        <option
                            value={consts.purchase_order_updated_subscription}>{translations.purchase_order_updated}</option>
                        <option
                            value={consts.purchase_order_approved_subscription}>{translations.purchase_order_approved}</option>
                        <option
                            value={consts.purchase_order_rejected_subscription}>{translations.purchase_order_rejected}</option>
                        <option
                            value={consts.purchase_order_deleted_subscription}>{translations.purchase_order_deleted}</option>
                        <option value={consts.company_created_subscription}>{translations.company_created}</option>
                        <option value={consts.company_updated_subscription}>{translations.company_updated}</option>
                        <option value={consts.company_deleted_subscription}>{translations.company_deleted}</option>
                        <option value={consts.late_invoices_subscription}>{translations.late_invoices}</option>
                    </Input>
                    {this.props.renderErrorFor('event_id')}
                </FormGroup>
            </React.Fragment>
        )
    }
}
