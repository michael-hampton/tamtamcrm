import React, { Component } from 'react'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'
import InvoiceRepository from '../../repositories/InvoiceRepository'

export default class InvoiceDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            invoices: []
        }

        this.getInvoices = this.getInvoices.bind(this)
        this.count = 0
    }

    componentDidMount () {
        if (!this.props.invoices || !this.props.invoices.length) {
            this.getInvoices()
        } else {
            this.state.invoices = this.props.invoices
        }
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback d-block'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    hasErrorFor (field) {
        return this.props.errors && !!this.props.errors[field]
    }

    getInvoices () {
        const invoiceRepository = new InvoiceRepository()
        invoiceRepository.get(this.props.status).then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ invoices: response }, () => {
                console.log('invoices', this.state.invoices)
            })
        })
    }

    render () {
        let invoiceList = null
        let { invoices } = this.state

        if (!invoices) {
            invoiceList = <option value="">Loading...</option>
        } else {
            if (this.props.customer_id) {
                invoices = invoices.filter(invoice => invoice.customer_id === parseInt(this.props.customer_id))
            }

            if (this.props.allowed_invoices && this.props.allowed_invoices.length) {
                invoices = invoices.filter(invoice => this.props.allowed_invoices.includes(invoice.id))
            }

            const totals = []

            if (this.props.refund && this.props.refund === true && this.props.payment.paymentables && this.props.payment.paymentables.length) {
                this.props.payment.paymentables.map((paymentable) => {
                    if (paymentable.paymentable_type.includes('Invoice')) {
                        totals[paymentable.invoice_id] = paymentable.amount
                    }
                })
            }

            if (this.props.is_recurring) {
                invoices = invoices.filter(invoice => !invoice.recurring_invoice_id)
            }

            this.count = invoices.length

            invoiceList = invoices.map((invoice, index) => {
                const amount = totals.length && totals[invoice.id] ? totals[invoice.id] : invoice.balance
                return <option key={index} value={invoice.id}>{invoice.number} ({amount})</option>
            })
        }

        const name = this.props.name && this.props.name ? this.props.name : 'invoice_id'
        const error_name = this.props.error_name ? this.props.error_name : name
        const data_id = this.props.data_id ? this.props.data_id : 0

        const selected = !this.props.is_recurring && this.props.invoices && this.props.invoices.length === 1 ? this.props.invoices[0].id : this.props.invoice_id

        const selectList = this.props.multiple && this.props.multiple === true ? (
            <Input onChange={this.props.handleInputChanges} multiple type="select"
                data-id={data_id}
                name={name} id={name}>
                {invoiceList}
            </Input>
        ) : <Input data-id={data_id} value={selected} onChange={this.props.handleInputChanges}
            type="select"
            name={name} id={name}>
            <option value="">{translations.select_option}</option>
            {invoiceList}
        </Input>

        return this.props.hide_when_empty && !this.count ? null : (
            <FormGroup>
                {selectList}
                {this.renderErrorFor(error_name)}
            </FormGroup>
        )
    }
}
