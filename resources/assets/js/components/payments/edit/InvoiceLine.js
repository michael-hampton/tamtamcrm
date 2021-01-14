import React, { Component } from 'react'
import InvoiceLineInputs from './InvoiceLineInputs'
import CreditLineInputs from './CreditLineInputs'
import PaymentModel from '../../models/PaymentModel'
import axios from 'axios'
import { consts } from '../../utils/_consts'

export default class InvoiceLine extends Component {
    constructor (props) {
        super(props)

        this.paymentModel = new PaymentModel(this.props.invoices, this.props.payment, this.props.credits)

        if (!this.props.invoices) {
            this.getInvoices()
        }

        this.invoice_amount = 0
        this.credit_amount = 0
        this.invoice_id = null
        this.credit_id = null

        if (this.props.refund && this.props.payment.paymentables && this.props.payment.paymentables.length && this.props.allInvoices.length === 1 && this.props.allInvoices[0].paymentables.length) {
            this.props.payment.paymentables.forEach((paymentable) => {
                if (paymentable.paymentable_type.includes('Invoice') && this.props.allInvoices[0].id === paymentable.invoice_id) {
                    this.invoice_amount += parseFloat(paymentable.amount)
                    this.invoice_id = paymentable.invoice_id
                }
            })
        }

        if (this.props.refund && this.props.payment.paymentables && this.props.payment.paymentables.length && this.props.allCredits && this.props.allCredits.length === 1) {
            this.props.payment.paymentables.forEach((paymentable) => {
                if (paymentable.paymentable_type.includes('Credit') && this.props.allCredits[0].id === paymentable.credit_id) {
                    this.credit_amount += parseFloat(paymentable.amount)
                    this.credit_id = paymentable.invoice_id
                }
            })
        }

        this.state = {
            lines: this.props.lines && this.props.lines.length ? this.props.lines : [{
                invoice_id: this.invoice_id,
                amount: this.invoice_amount
            }],
            credit_lines: this.props.credit_lines && this.props.credit_lines.length ? this.props.credit_lines : [{
                credit_id: this.credit_id,
                amount: this.credit_amount
            }],
            amount: 0,
            customer_id: null
        }

        this.handleChange = this.handleChange.bind(this)
        this.addLine = this.addLine.bind(this)
        this.addCredit = this.addCredit.bind(this)
        this.removeLine = this.removeLine.bind(this)
        this.removeCredit = this.removeCredit.bind(this)
    }

    componentDidMount () {
        if (this.invoice_amount > 0 && this.invoice_id !== null) {
            this.props.onChange(this.state.lines)
        }

        if (this.credit_amount > 0 && this.credit_id !== null) {
            this.props.onCreditChange(this.state.lines)
        }
    }

    getInvoices () {
        axios.get('/api/invoice')
            .then((r) => {
                this.paymentModel.invoices = r.data
            })
            .catch((e) => {
                console.error(e)
            })
    }

    getCredits () {
        axios.get('/api/credits')
            .then((r) => {
                this.paymentModel.credits = r.data
            })
            .catch((e) => {
                console.error(e)
            })
    }

    handleChange (e) {
        const name = e.target.name
        const idx = e.target.dataset.id
        let is_credit = false
        let is_invoice = false

        const lines = [...this.state.lines]
        const credit_lines = [...this.state.credit_lines]

        const allowed_invoices = lines.length > 0 ? lines.map(({ invoice_id }) => invoice_id) : null
        const allowed_credits = credit_lines.length > 0 ? credit_lines.map(({ invoice_id }) => invoice_id) : null

        let amount = 0
        let manual_update = false

        if (name === 'invoice_id' || (e.target.dataset.invoice && e.target.dataset.invoice.length)) {
            let invoice_id = e.target.dataset.invoice && e.target.dataset.invoice.length ? e.target.dataset.invoice : e.target.value

            if (invoice_id === 'test' && lines[idx]) {
                invoice_id = lines[idx].invoice_id
            }

            const invoice = this.paymentModel.getInvoice(invoice_id)

            if (name === 'invoice_id' && allowed_invoices.includes(invoice_id)) {
                return false
            }

            if (name === 'invoice_id' && invoice.balance <= 0 && !this.props.refund) {
                return false
            }

            is_invoice = true

            if (!invoice) {
                return
            }

            let invoice_total = name !== 'amount' ? parseFloat(invoice.balance) : parseFloat(e.target.value)

            if (this.props.refund && name !== 'amount') {
                this.props.payment.paymentables.forEach((paymentable) => {
                    if (paymentable.paymentable_type.includes('Invoice') && paymentable.invoice_id === parseInt(e.target.value)) {
                        invoice_total = parseFloat(paymentable.amount)
                    }
                })
            }

            let refunded_amount = 0

            if (this.props.payment.paymentables && this.props.payment.paymentables.length > 0) {
                refunded_amount = this.paymentModel.calculateRefundedAmount(this.props.payment.paymentables)
            }

            if ((refunded_amount + invoice_total) > invoice.balance) {
                invoice_total = invoice.total - refunded_amount
                manual_update = true
            }

            this.props.customerChange(invoice.customer_id)
            lines[idx].amount = parseFloat(invoice_total)
            lines[idx].invoice_id = invoice_id

            const invoice_sum = this.state.lines.reduce(function (a, b) {
                return a + parseFloat(b.amount)
            }, 0)
            const credit_sum = this.state.credit_lines.reduce(function (a, b) {
                return a + parseFloat(b.amount)
            }, 0)

            // if (sum <= 0 && this.props.allInvoices && this.props.allInvoices.length === 1) {
            //     amount = invoice_total
            // } else {
            amount = invoice_sum + credit_sum
            // }
        }

        if (name === 'credit_id' || (e.target.dataset.credit && e.target.dataset.credit.length)) {
            let credit_id = e.target.dataset.credit && e.target.dataset.credit.length ? e.target.dataset.credit : e.target.value

            if (credit_id === 'test' && credit_lines[idx]) {
                credit_id = credit_lines[idx].credit_id
            }

            const credit = this.paymentModel.getCredit(credit_id)

            if (name === 'credit_id' && allowed_credits.includes(credit_id)) {
                return false
            }

            if (name === 'credit_id' && credit.balance <= 0) {
                return false
            }

            is_credit = true

            if (!credit) {
                return
            }

            let credit_total = e.target.dataset.credit && e.target.dataset.credit.length && name === 'amount' ? parseFloat(e.target.value) : parseFloat(credit.balance)

            let refunded_amount = 0

            if (this.props.paymentables && this.props.paymentables.length > 0) {
                refunded_amount = this.paymentModel.calculateRefundedAmount(this.props.paymentables)
            }

            if ((refunded_amount + credit_total) > credit.balance) {
                credit_total = credit.total - refunded_amount
                manual_update = true
            }

            this.props.customerChange(credit.customer_id)
            credit_lines[idx].amount = parseFloat(credit_total)
            credit_lines[idx].credit_id = credit_id

            const invoice_sum = this.state.lines.reduce(function (a, b) {
                return a + parseFloat(b.amount)
            }, 0)
            const credit_sum = this.state.credit_lines.reduce(function (a, b) {
                return a + parseFloat(b.amount)
            }, 0)

            // if (invoice_sum <= 0 && this.props.allCredits && this.props.allCredits.length === 1) {
            //     amount = credit_total
            // } else {
            amount = invoice_sum + credit_sum
            // }
        }

        if (!manual_update && is_invoice === true) {
            lines[e.target.dataset.id][e.target.name] = e.target.value
        }

        if (!manual_update && is_credit === true) {
            credit_lines[e.target.dataset.id][e.target.name] = e.target.value
        }

        this.setState({
            amount: parseInt('' + (amount * 100)) / 100,
            lines: lines,
            credit_lines: credit_lines
        }, () => {
            this.props.onChange(this.state.lines)
            if (name === 'invoice_id' && is_invoice === true) {
                this.addLine()
            }

            this.props.onCreditChange(this.state.credit_lines)
            if (name === 'credit_id' && is_credit === true) {
                this.addCredit()
            }

            if (amount > 0) {
                this.props.handleAmountChange(parseInt('' + (amount * 100)) / 100)
            }
        })
    }

    addLine () {
        const status = this.props.refund && this.props.refund === true ? consts.invoice_status_paid : this.props.status
        const allowedInvoices = this.props.refund ? this.props.payment.paymentables : this.paymentModel.getInvoicesByStatus(status)

        if (this.state.lines.length >= allowedInvoices.length || this.props.invoice_id) {
            return
        }

        this.setState((prevState) => ({
            lines: [...prevState.lines, { invoice_id: null, amount: 0 }]
        }), () => {
            this.props.onChange(this.state.lines)
        })
    }

    addCredit (e) {
        const allowedCredits = this.paymentModel.getCreditsByStatus(2)

        if (this.state.credit_lines.length >= allowedCredits.length) {
            return
        }

        this.setState((prevState) => ({
            credit_lines: [...prevState.credit_lines, { credit_id: null, amount: 0 }]
        }), () => this.props.onCreditChange(this.state.credit_lines))
    }

    removeLine (idx) {
        if (this.state.lines.length === 1) {
            return
        }

        this.setState({
            lines: this.state.lines.filter(function (line, sidx) {
                return sidx !== idx
            })
        }, () => this.props.onChange(this.state.lines))
    }

    removeCredit (idx) {
        // if (this.state.credit_lines.length === 1) {
        // return
        // }

        this.setState({
            credit_lines: this.state.credit_lines.filter(function (credit, sidx) {
                return sidx !== idx
            })
        }, () => this.props.onCreditChange(this.state.credit_lines))
    }

    render () {
        const { lines, credit_lines } = this.state

        const invoice_status = (this.props.invoiceStatus && true) ? (this.props.invoiceStatus) : ((this.props.status) ? (this.props.status) : null)
        const credit_status = (this.props.creditStatus && true) ? (this.props.creditStatus) : ((this.props.status) ? (this.props.status) : null)
        const invoices = this.props.allInvoices ? this.props.allInvoices : []
        const credits = this.props.allCredits ? this.props.allCredits : []
        const has_invoice = this.paymentModel.hasInvoice(lines)

        this.allowed_invoices = []

        if (this.props.invoice_id) {
            this.allowed_invoices.push(this.props.invoice_id)
        }

        console.log('test', this.paymentModel.paymentable_invoices)

        return (
            <form>
                {(!this.props.refund || this.paymentModel.paymentable_invoices.length) &&
                <InvoiceLineInputs hideEmpty={this.props.hideEmpty} allowed_invoices={this.allowed_invoices}
                    payment={this.props.payment}
                    refund={this.props.refund}
                    invoices={invoices} status={invoice_status} errors={this.props.errors}
                    onChange={this.handleChange} lines={lines}
                    removeLine={this.removeLine}
                    addLine={this.addLine}/>
                }

                {has_invoice && (!this.props.refund || this.paymentModel.paymentable_credits.length) &&
                <CreditLineInputs hideEmpty={this.props.hideEmpty} allowed_credits={this.allowed_credits}
                    refund={this.props.refund}
                    payment={this.props.payment} credits={credits}
                    status={credit_status} errors={this.props.errors}
                    onChange={this.handleChange} lines={credit_lines}
                    removeLine={this.removeCredit}
                    addLine={this.addCredit}/>
                }
                {/* <Button color="primary" onClick={this.addLine}>Add</Button> */}
            </form>
        )
    }
}
