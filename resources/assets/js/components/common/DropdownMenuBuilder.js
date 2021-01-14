import React, { Component } from 'react'
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap'
import SuccessMessage from './SucessMessage'
import ErrorMessage from './ErrorMessage'
import { getEntityIcon, icons } from '../utils/_icons'
import { translations } from '../utils/_translations'

export default class DropdownMenuBuilder extends Component {
    constructor (props, context) {
        super(props, context)
        this.state = {
            dropdownOpen: false,
            showSuccessMessage: false,
            showErrorMessage: false
        }

        this.model = this.props.model
        this.toggleMenu = this.toggleMenu.bind(this)
        this.changeStatus = this.changeStatus.bind(this)
    }

    downloadPdf (response, id, dispatch_note = false) {
        const linkSource = `data:application/pdf;base64,${response.data}`
        const downloadLink = document.createElement('a')
        const fileName = `${dispatch_note === true ? 'dispatch_note' : this.props.model.entity}_${id}.pdf`

        downloadLink.href = linkSource
        downloadLink.download = fileName
        downloadLink.click()
    }

    removeByKey (myObj, deleteKeys) {
        return Object.keys(myObj)
            .filter(key => !deleteKeys.includes(key))
            .reduce((result, current) => {
                result[current] = myObj[current]
                return result
            }, {})
    }

    launchPortal () {
        if (this.props.model.getInvitationViewLink.length) {
            window.open(
                this.props.model.getInvitationViewLink,
                '_blank' // <- This is what makes it open in a new window.
            )
        }
    }

    loadInvoice (type) {
        let url = null

        switch (type) {
            case 'quote':
                url = (this.props.model.entity === 'Expense') ? (`/#/quotes?entity_id=${this.props.model.id}&entity_type=expense`) : ((this.props.model.entity === 'Task') ? (`/#/quotes?entity_id=${this.props.model.id}&entity_type=task`) : (`/#/quotes?entity_id=${this.props.model.id}&entity_type=project`))
                break

            case 'expense':
                url = (this.props.model.entity === 'Expense') ? (`/#/expenses?entity_id=${this.props.model.id}&entity_type=expense`) : ((this.props.model.entity === 'Task') ? (`/#/expenses?entity_id=${this.props.model.id}&entity_type=task`) : (`/#/expenses?entity_id=${this.props.model.id}&entity_type=project`))
                break

            case 'invoice':
                url = (this.props.model.entity === 'Expense') ? (`/#/invoice?entity_id=${this.props.model.id}&entity_type=expense`) : ((this.props.model.entity === 'Task') ? (`/#/invoice?entity_id=${this.props.model.id}&entity_type=task`) : (`/#/invoice?entity_id=${this.props.model.id}&entity_type=project`))
                break

            case 'credit':
                url = (this.props.model.entity === 'Expense') ? (`/#/credits?entity_id=${this.props.model.id}&entity_type=expense`) : ((this.props.model.entity === 'Task') ? (`/#/credits?entity_id=${this.props.model.id}&entity_type=task`) : (`/#/credits?entity_id=${this.props.model.id}&entity_type=project`))
                break

            case 'recurring_invoice':
                url = (this.props.model.entity === 'Expense') ? (`/#/recurring-invoices?entity_id=${this.props.model.id}&entity_type=expense`) : ((this.props.model.entity === 'Task') ? (`/#/recurring-invoices?entity_id=${this.props.model.id}&entity_type=task`) : (`/#/recurring-invoices?entity_id=${this.props.model.id}&entity_type=project`))
                break

            case 'recurring_quote':
                url = (this.props.model.entity === 'Expense') ? (`/#/recurring-quotes?entity_id=${this.props.model.id}&entity_type=expense`) : ((this.props.model.entity === 'Task') ? (`/#/recurring-quotes?entity_id=${this.props.model.id}&entity_type=task`) : (`/#/recurring-quotes?entity_id=${this.props.model.id}&entity_type=project`))
                break
        }

        location.href = url
    }

    changeStatus (action) {
        if (!this.props.model.fields.id) {
            return false
        }

        const data = this.removeByKey(this.props.formData, ['invitations', 'date_to_send', 'created_at'])

        this.props.model.completeAction(data, action).then(response => {
            if (!response) {
                this.setState({
                    showSuccessMessage: false,
                    showErrorMessage: true
                })

                return
            }

            action = action.trim()

            let message = `${action} completed successfully`

            if (action === 'copy') {
                message = translations.token_copied
            }

            if (action === 'download' || action === 'dispatch_note') {
                this.downloadPdf(response, this.props.model.fields.id, action === 'dispatch_note')
                message = translations.downloaded
            }

            if (action === 'clone_to_invoice') {
                this.props.invoices.push(response)
                this.props.action(this.props.invoices)
                message = `${translations.invoice} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_invoice_to_recurring') {
                message = `${translations.recurring_invoice} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_quote_to_recurring') {
                message = `${translations.recurring_quote} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_recurring_to_invoice') {
                message = `${translations.invoice} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_recurring_to_quote') {
                message = `${translations.quote} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_to_quote') {
                this.props.invoices.push(response)
                this.props.action(this.props.invoices)
                message = `${translations.quote} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_to_credit') {
                this.props.invoices.push(response)
                this.props.action(this.props.invoices)
                message = `${translations.credit} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_to_task') {
                message = `${translations.task} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'start_recurring') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                this.props.action(this.props.invoices)
                this.props.reload(response)
                const recurring_text = this.props.model.entity === 'RecurringInvoice' ? translations.recurring_invoice : translations.recurring_quote
                message = `${recurring_text} ${response.number} ${translations.has_started}`
            }

            if (action === 'stop_recurring') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                this.props.action(this.props.invoices)
                this.props.reload(response)
                const recurring_text = this.props.model.entity === 'RecurringInvoice' ? translations.recurring_invoice : translations.recurring_quote
                message = `${recurring_text} ${response.number} ${translations.has_stopped}`
            }

            if (action === 'clone_to_lead') {
                message = `${translations.lead} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_to_project' || action === 'clone_project') {
                message = `${translations.project} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_task_to_deal') {
                message = `${translations.task} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_lead_to_task') {
                message = `${translations.task} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_lead_to_deal') {
                message = `${translations.deal} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_case_to_project') {
                message = `${translations.case} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'merge_case') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                this.props.action(this.props.invoices)
                message = `The ${this.props.model.entity} ${translations.case_merged}`
            }

            if (action === 'clone_to_order') {
                this.props.invoices.push(response)
                this.props.action(this.props.invoices)
                message = `${translations.order} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'clone_to_expense') {
                this.props.invoices.push(response)
                this.props.action(this.props.invoices)
                message = `${translations.expense} ${response.number} ${translations.has_been_created}`
            }

            if (action === 'approve') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                this.props.action(this.props.invoices)
                this.props.reload(response)
                message = `The ${this.props.model.entity} ${translations.approved}`
            }

            if (action === 'reject') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                this.props.action(this.props.invoices)
                this.props.reload(response)
                message = `The ${this.props.model.entity} ${translations.rejected}`
            }

            if (action === 'request_change') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                this.props.action(this.props.invoices)
                this.props.reload(response)
                message = `The ${this.props.model.entity} ${translations.request_change}`
            }

            if (action === 'mark_sent') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                this.props.action(this.props.invoices)
                this.props.reload(response)
                message = `The ${this.props.model.entity} ${translations.sent}`
            }

            if (action === 'create_payment') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                this.props.action(this.props.invoices)
                this.props.reload(response)
                message = `The ${this.props.model.entity} ${translations.paid}.`
            }

            if (action === 'cancel') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                this.props.reload(response)
                this.props.action(this.props.invoices)
                message = `The ${this.props.model.entity} ${translations.cancelled_invoice}`
            }

            if (action === 'reverse') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                this.props.action(this.props.invoices)
                this.props.reload(response)
                message = `The ${this.props.model.entity} ${translations.reversed_invoice}`
            }

            if (action === 'fulfill') {
                message = `The ${this.props.model.entity} ${translations.order_filfilled}`
            }

            if (action === 'hold_order') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                console.log('response', response)
                this.props.action(this.props.invoices)
                this.props.reload(response)
                message = `${translations.order_held}`
            }

            if (action === 'reverse_status') {
                const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.model.fields.id)
                this.props.invoices[index] = response
                this.props.action(this.props.invoices)
                this.props.reload(response)
                message = `${translations.order_unheld}`
            }

            if (action === 'refund') {
                message = `The ${this.props.model.entity} ${translations.refunded}`
            }

            if (action === 'email') {
                message = translations.emailed
            }

            this.setState({
                showSuccessMessage: message,
                showErrorMessage: false
            })
        })
    }

    toggleMenu (event) {
        this.setState({
            dropdownOpen: !this.state.dropdownOpen
        })
    }

    getOption (option) {
        switch (option) {
            case 'pdf':
                return <DropdownItem key={1} className="primary"
                    onClick={() => this.changeStatus('download')}><i
                        className={`fa ${icons.download} mr-2`}/>{translations.download}</DropdownItem>

            case 'dispatch_note':
                return <DropdownItem key={99} className="primary"
                    onClick={() => this.changeStatus('dispatch_note')}><i
                        className={`fa ${icons.download} mr-2`}/>{translations.dispatch_note}</DropdownItem>

            case 'email':
                return <DropdownItem key={2} className="primary" onClick={() => this.changeStatus('email')}>
                    <i className={`fa ${icons.email} mr-2`}/>{translations.send_email}
                </DropdownItem>

            case 'approve':
                return <DropdownItem key={3} className="primary" onClick={() => this.changeStatus('approve')}>
                    <i className={`fa ${icons.approve} mr-2`}/>{translations.approve}
                </DropdownItem>

            case 'reject':
                return <DropdownItem key={3} className="primary" onClick={() => this.changeStatus('reject')}>
                    <i className={`fa ${icons.reject} mr-2`}/>{translations.reject}
                </DropdownItem>

            case 'request_change':
                return <DropdownItem key={3} className="primary" onClick={() => this.changeStatus('request_change')}>
                    <i className={`fa ${icons.request_change} mr-2`}/>{translations.request_change}
                </DropdownItem>

            case 'markSent':
                return <DropdownItem key={4} onClick={() => this.changeStatus('mark_sent')}>
                    <i className={`fa ${icons.mark_sent} mr-2`}/>{translations.mark_sent}
                </DropdownItem>

            case 'cloneToInvoice':
                return <DropdownItem key={5} className="primary"
                    onClick={() => this.changeStatus('clone_to_invoice')}>
                    <i className={`fa ${icons.clone} mr-2`}/> {translations.clone_to_invoice}
                </DropdownItem>

            case 'cloneToQuote':
                return <DropdownItem key={6} className="primary" onClick={() => this.changeStatus('clone_to_quote')}>
                    <i className={`fa ${icons.clone} mr-2`}/> Clone To Quote
                </DropdownItem>

            case 'cloneCreditToQuote':
                return <DropdownItem key={7} className="primary"
                    onClick={() => this.changeStatus('clone_credit_to_quote')}>
                    <i className={`fa ${icons.clone} mr-2`}/> Clone Credit To Quote
                </DropdownItem>

            case 'cloneInvoiceToQuote':
                return <DropdownItem key={8} className="primary"
                    onClick={() => this.changeStatus('clone_invoice_to_quote')}>
                    <i className={`fa ${icons.clone} mr-2`}/> Clone To Quote
                </DropdownItem>

            case 'dispatch':
                return <DropdownItem key={9} className="primary"
                    onClick={() => this.changeStatus('dispatch')}>
                    <i className={`fa ${icons.clone} mr-2`}/> {translations.dispatch}
                </DropdownItem>

            case 'cloneOrderToInvoice':
                return <DropdownItem key={10} className="primary"
                    onClick={() => this.changeStatus('clone_order_to_invoice')}>
                    <i className={`fa ${icons.clone} mr-2`}/> {translations.clone_order_to_invoice}
                </DropdownItem>

            case 'cloneOrderToQuote':
                return <DropdownItem key={11} className="primary"
                    onClick={() => this.changeStatus('clone_order_to_quote')}>
                    <i className={`fa ${icons.clone} mr-2`}/> Clone Order To Quote
                </DropdownItem>

            case 'cloneRecurringToQuote':
                return <DropdownItem key={11} className="primary"
                    onClick={() => this.changeStatus('clone_recurring_to_quote')}>
                    <i className={`fa ${icons.clone} mr-2`}/> Clone Recurring Quote To Quote
                </DropdownItem>

            case 'cloneRecurringToInvoice':
                return <DropdownItem key={11} className="primary"
                    onClick={() => this.changeStatus('clone_recurring_to_invoice')}>
                    <i className={`fa ${icons.clone} mr-2`}/> Clone Recurring Invoice To Invoice
                </DropdownItem>

            case 'cloneQuoteToInvoice':
                return <DropdownItem key={12} className="primary"
                    onClick={() => this.changeStatus('clone_quote_to_invoice')}>
                    <i className={`fa ${icons.clone} mr-2`}/> {translations.clone_quote_to_invoice}
                </DropdownItem>

            case 'cloneToCredit':
                return <DropdownItem key={13} className="primary"
                    onClick={() => this.changeStatus('clone_to_credit')}>
                    <i className={`fa ${icons.clone} mr-2`}/>{translations.clone_credit}
                </DropdownItem>

            case 'clone_to_order':
                return <DropdownItem key={14} className="primary"
                    onClick={() => this.changeStatus('clone_to_order')}>
                    <i className={`fa ${icons.clone} mr-2`}/>{translations.clone_order}
                </DropdownItem>

            case 'markPaid':
                return <DropdownItem key={15} color="primary" onClick={() => this.changeStatus('create_payment')}>
                    <i className={`fa ${icons.mark_paid} mr-2`}/>{translations.mark_paid}
                </DropdownItem>

            case 'cloneExpense':
                return <DropdownItem key={16} className="primary"
                    onClick={() => this.changeStatus('clone_to_expense')}>
                    <i className={`fa ${icons.clone} mr-2`}/> {translations.clone_expense}
                </DropdownItem>

            case 'delete':
                return <DropdownItem key={17} className="primary"
                    onClick={() => this.changeStatus('delete')}>
                    <i className={`fa ${icons.delete} mr-2`}/>{translations.delete}</DropdownItem>

            case 'cancel':
                return <DropdownItem key={18} className="primary"
                    onClick={() => this.changeStatus('cancel')}>
                    <i className={`fa ${icons.cancel} mr-2`}/> {translations.cancel}
                </DropdownItem>

            case 'reverse':
                return <DropdownItem key={19} className="primary"
                    onClick={() => this.changeStatus('reverse')}>
                    <i className={`fa ${icons.reverse} mr-2`}/>{translations.reverse}
                </DropdownItem>

            case 'fulfill':
                return <DropdownItem key={25} className="primary"
                    onClick={() => this.changeStatus('fulfill')}>
                    <i className={`fa ${icons.archive} mr-2`}/>{translations.fulfill}
                </DropdownItem>

            case 'holdOrder':
                return <DropdownItem key={26} className="primary"
                    onClick={() => this.changeStatus('hold_order')}>
                    <i className={`fa ${icons.archive} mr-2`}/>{translations.hold_order}
                </DropdownItem>

            case 'reverse_status':
                return <DropdownItem key={31} className="primary"
                    onClick={() => this.changeStatus('reverse_status')}>
                    <i className={`fa ${icons.archive} mr-2`}/>{translations.unhold_order}
                </DropdownItem>

            case 'archive':
                return <DropdownItem key={32} className="primary"
                    onClick={() => this.changeStatus('archive')}>
                    <i className={`fa ${icons.archive} mr-2`}/>{translations.archive}
                </DropdownItem>

            case 'getProducts':
                return <DropdownItem key={33} className="primary" onClick={this.props.handleTaskChange}>
                    <i className={`fa ${icons.products} mr-2`}/>Get Products
                </DropdownItem>

            case 'refund':
                return <DropdownItem key={34} className="primary"
                    onClick={() => this.changeStatus('refund')}>{translations.refund}</DropdownItem>

            case 'copy':
                return <DropdownItem key={35} className="primary"
                    onClick={() => this.changeStatus('copy')}>
                    <i className={`fa ${icons.clone} mr-2`}/>{translations.copy}
                </DropdownItem>
            case 'cloneToTask':
                return <DropdownItem key={36} className="primary"
                    onClick={() => this.changeStatus('clone_to_task')}>
                    <i className={`fa ${icons.clone} mr-2`}/>{translations.clone_to_task}
                </DropdownItem>
            case 'cloneToLead':
                return <DropdownItem key={37} className="primary"
                    onClick={() => this.changeStatus('clone_to_lead')}>
                    <i className={`fa ${icons.clone} mr-2`}/>{translations.clone_to_lead}
                </DropdownItem>
            case 'cloneToProject':
                return <DropdownItem key={38} className="primary"
                    onClick={() => this.changeStatus('clone_to_project')}>
                    <i className={`fa ${icons.clone} mr-2`}/>{translations.clone_to_project}
                </DropdownItem>

            case 'cloneTaskToDeal':
                return <DropdownItem key={39} className="primary"
                    onClick={() => this.changeStatus('clone_task_to_deal')}>
                    <i className={`fa ${icons.clone} mr-2`}/>{translations.clone_to_deal}
                </DropdownItem>

            case 'cloneLeadToDeal':
                return <DropdownItem key={40} className="primary"
                    onClick={() => this.changeStatus('clone_lead_to_deal')}>
                    <i className={`fa ${icons.clone} mr-2`}/>{translations.clone_to_deal}
                </DropdownItem>

            case 'cloneLeadToTask':
                return <DropdownItem key={41} className="primary"
                    onClick={() => this.changeStatus('clone_lead_to_task')}>
                    <i className={`fa ${icons.clone} mr-2`}/>{translations.clone_to_task}
                </DropdownItem>

            case 'cloneCaseToProject':
                return <DropdownItem key={42} className="primary"
                    onClick={() => this.changeStatus('clone_case_to_project')}>
                    <i className={`fa ${icons.clone} mr-2`}/>{translations.clone_to_project}
                </DropdownItem>

            case 'mergeCase':
                return <DropdownItem key={43} className="primary"
                    onClick={() => this.changeStatus('merge_case')}>
                    <i className={`fa ${icons.clone} mr-2`}/>{translations.merge_case}
                </DropdownItem>

            case 'start_recurring':
                return <DropdownItem key={44} className="primary"
                    onClick={() => this.changeStatus('start_recurring')}>
                    <i className={`fa ${icons.start} mr-2`}/>{translations.start}
                </DropdownItem>

            case 'stop_recurring':
                return <DropdownItem key={45} className="primary"
                    onClick={() => this.changeStatus('stop_recurring')}>
                    <i className={`fa ${icons.stop} mr-2`}/>{translations.stop}
                </DropdownItem>

            case 'portal':
                return <DropdownItem key={46} className="primary"
                    onClick={() => this.launchPortal()}>
                    <i className={`fa ${icons.portal} mr-2`}/>{translations.portal}
                </DropdownItem>

            case 'cloneToRecurringInvoice':
                return <DropdownItem key={46} className="primary"
                    onClick={() => this.changeStatus('clone_invoice_to_recurring')}>
                    <i className={`fa ${getEntityIcon('RecurringInvoice')} mr-2`}/>{translations.clone_to_recurring_invoice}
                </DropdownItem>
            case 'cloneToRecurringQuote':
                return <DropdownItem key={47} className="primary"
                    onClick={() => this.changeStatus('clone_quote_to_recurring')}>
                    <i className={`fa ${getEntityIcon('RecurringInvoice')} mr-2`}/>{translations.clone_to_recurring_quote}
                </DropdownItem>
            case 'cloneProject':
                return <DropdownItem key={48} className="primary"
                    onClick={() => this.changeStatus('clone_project')}>
                    <i className={`fa ${getEntityIcon('Project')} mr-2`}/>{translations.clone_project}
                </DropdownItem>
            case 'newInvoice':
                return <DropdownItem key={49} className="primary"
                    onClick={() => this.loadInvoice('invoice')}>
                    <i className={`fa ${getEntityIcon('Invoice')} mr-2`}/>{translations.new_invoice}
                </DropdownItem>
            case 'newCredit':
                return <DropdownItem key={50} className="primary"
                    onClick={() => this.loadInvoice('credit')}>
                    <i className={`fa ${getEntityIcon('Credit')} mr-2`}/>{translations.new_credit}
                </DropdownItem>
            case 'newQuote':
                return <DropdownItem key={51} className="primary"
                    onClick={() => this.loadInvoice('quote')}>
                    <i className={`fa ${getEntityIcon('Quote')} mr-2`}/>{translations.new_quote}
                </DropdownItem>
            case 'newRecurringInvoice':
                return <DropdownItem key={52} className="primary"
                    onClick={() => this.loadInvoice('recurring_invoice')}>
                    <i className={`fa ${getEntityIcon('RecurringInvoice')} mr-2`}/>{translations.new_recurring_invoice}
                </DropdownItem>
            case 'newRecurringQuote':
                return <DropdownItem key={53} className="primary"
                    onClick={() => this.loadInvoice('recurring_quote')}>
                    <i className={`fa ${getEntityIcon('RecurringQuote')} mr-2`}/>{translations.new_recurring_quote}
                </DropdownItem>
            case 'newExpense':
                return <DropdownItem key={54} className="primary"
                    onClick={() => this.loadInvoice('expense')}>
                    <i className={`fa ${getEntityIcon('Expense')} mr-2`}/>{translations.new_expense}
                </DropdownItem>
        }
    }

    render () {
        const menuOptions = this.props.model.buildDropdownMenu()

        const actions = []

        console.log('menu options', menuOptions)

        menuOptions.forEach((element) => {
            actions.push(this.getOption(element))
        })

        const successMessage = this.state.showSuccessMessage !== false && this.state.showSuccessMessage !== ''
            ? <SuccessMessage message={this.state.showSuccessMessage}/> : null
        const errorMessage = this.state.showErrorMessage === true
            ? <ErrorMessage message="Something went wrong"/> : null

        return (
            <React.Fragment>
                <Dropdown isOpen={this.state.dropdownOpen} toggle={this.toggleMenu}>
                    <DropdownToggle caret>
                        {translations.action}
                    </DropdownToggle>

                    <DropdownMenu className="text-white settings-menu">
                        {actions}
                    </DropdownMenu>
                </Dropdown>
                {successMessage}
                {errorMessage}
            </React.Fragment>
        )
    }
}
