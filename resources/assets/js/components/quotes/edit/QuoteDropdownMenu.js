import React, { Component } from 'react'
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap'
import axios from 'axios'
import SuccessMessage from '../../common/SucessMessage'
import ErrorMessage from '../../common/ErrorMessage'

export default class QuoteDropdownMenu extends Component {
    constructor (props, context) {
        super(props, context)
        this.state = {
            dropdownOpen: false,
            showSuccessMessage: false,
            showErrorMessage: false
        }
        this.toggleMenu = this.toggleMenu.bind(this)
        this.changeStatus = this.changeStatus.bind(this)
    }

    downloadPdf (response, id) {
        const linkSource = `data:application/pdf;base64,${response.data.data}`
        const downloadLink = document.createElement('a')
        const fileName = `quote_${id}.pdf`

        downloadLink.href = linkSource
        downloadLink.download = fileName
        downloadLink.click()
    }

    changeStatus (action) {
        if (!this.props.invoice_id) {
            return false
        }

        const data = this.props.formData
        axios.post(`/api/quote/${this.props.invoice_id}/${action}`, data)
            .then((response) => {
                let message = `${action} completed successfully`

                if (action === 'clone_to_quote') {
                    this.props.invoices.push(response.data)
                    this.props.action(this.props.invoices)
                    message = `Quote was cloned successfully. Quote ${response.data.number} has been created`
                }

                if (action === 'download') {
                    this.downloadPdf(response, this.props.invoice_id)
                    message = 'The PDF file has been downloaded'
                }

                if (action === 'clone_to_invoice') {
                    message = `The quote was successfully converted to an invoice. Invoice ${response.data.number} has been created`
                }

                if (action === 'approve') {
                    message = 'The quote has been marked as approved'
                }

                if (action === 'mark_sent') {
                    const index = this.props.invoices.findIndex(invoice => invoice.id === this.props.invoice_id)
                    this.props.invoices[index] = response.data
                    this.props.action(this.props.invoices)
                    message = 'The quote has been marked as sent'
                }

                if (action === 'email') {
                    message = 'The email has been sent successfully'
                }

                this.setState({
                    showSuccessMessage: message,
                    showErrorMessage: false
                })
            })
            .catch((error) => {
                this.setState({
                    showErrorMessage: true,
                    showSuccessMessage: false
                })
                console.warn(error)
            })
    }

    toggleMenu (event) {
        this.setState({
            dropdownOpen: !this.state.dropdownOpen
        })
    }

    render () {
        const changeStatusButton = <DropdownItem color="primary" onClick={() => this.changeStatus('mark_sent')}>Mark
            Sent</DropdownItem>

        const approveButton = this.props.status_id !== 4
            ? <DropdownItem className="primary"
                onClick={() => this.changeStatus('approve')}>Approve</DropdownItem> : null

        const sendEmailButton = this.props.status_id === 1
            ? <DropdownItem className="primary" onClick={() => this.changeStatus('email')}>Send
                Email</DropdownItem> : null

        const downloadButton = <DropdownItem className="primary"
            onClick={() => this.changeStatus('download')}>Download</DropdownItem>

        const cloneInvoiceButton = <DropdownItem className="primary"
            onClick={() => this.changeStatus('clone_to_invoice').bind(this)}>Convert
            to
            Invoice</DropdownItem>

        const cloneButton = <DropdownItem className="primary"
            onClick={() => this.changeStatus('clone_to_quote').bind(this)}>Clone
            Quote
        </DropdownItem>

        const deleteButton = this.props.status_id === 1
            ? <DropdownItem className="primary"
                onClick={() => this.changeStatus('delete')}>Delete</DropdownItem> : null

        const archiveButton = this.props.status_id === 1
            ? <DropdownItem className="primary"
                onClick={() => this.changeStatus('archive')}>Archive</DropdownItem> : null

        const successMessage = this.state.showSuccessMessage !== false && this.state.showSuccessMessage !== ''
            ? <SuccessMessage message={this.state.showSuccessMessage}/> : null
        const errorMessage = this.state.showErrorMessage === true
            ? <ErrorMessage message="Something went wrong"/> : null

        return (
            <React.Fragment>
                <Dropdown isOpen={this.state.dropdownOpen} toggle={this.toggleMenu}>
                    <DropdownToggle caret>
                        Actions
                    </DropdownToggle>

                    <DropdownMenu>
                        <DropdownItem header>Header</DropdownItem>
                        {changeStatusButton}
                        {approveButton}
                        {sendEmailButton}
                        {downloadButton}
                        {deleteButton}
                        {archiveButton}
                        {cloneInvoiceButton}
                        {cloneButton}
                        {this.props.task_id ? <DropdownItem className="primary" onClick={this.handleTaskChange}>Get
                            Products</DropdownItem> : null}
                    </DropdownMenu>
                </Dropdown>
                {successMessage}
                {errorMessage}
            </React.Fragment>
        )
    }
}
