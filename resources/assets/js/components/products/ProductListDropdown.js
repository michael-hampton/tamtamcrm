import React from 'react'
import axios from 'axios'
import { Dropdown, DropdownItem, DropdownMenu, DropdownToggle } from 'reactstrap'
import SuccessMessage from '../common/SucessMessage'
import ErrorMessage from '../common/ErrorMessage'

export default class ProductListDropdown extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            dropdownOpen: false,
            showSuccessMessage: false,
            showErrorMessage: false
        }

        this.toggleMenu = this.toggleMenu.bind(this)
        this.changeStatus = this.changeStatus.bind(this)
    }

    toggleMenu (event) {
        this.setState({
            dropdownOpen: !this.state.dropdownOpen
        })
    }

    changeStatus (action) {
        if (!this.props.id) {
            return false
        }

        const data = this.props.formData
        axios.post(`/api/company/${this.props.id}/${action}`, data)
            .then((response) => {
                if (action === 'download') {
                    this.downloadPdf(response)
                }

                this.setState({ showSuccessMessage: true })
            })
            .catch((error) => {
                this.setState({ showErrorMessage: true })
                console.warn(error)
            })
    }

    render () {
        const sendEmailButton = <DropdownItem className="primary" onClick={() => this.changeStatus('email')}>Send
            Email</DropdownItem>

        const deleteButton = <DropdownItem className="primary"
            onClick={() => this.changeStatus('delete')}>Delete</DropdownItem>

        const archiveButton = <DropdownItem className="primary"
            onClick={() => this.changeStatus('archive')}>Archive</DropdownItem>

        const cloneButton =
            <DropdownItem className="primary"
                onClick={() => this.changeStatus('clone_to_product')}>Clone</DropdownItem>

        const dropdownMenu = <Dropdown isOpen={this.state.dropdownOpen} toggle={this.toggleMenu}>
            <DropdownToggle caret>
                Actions
            </DropdownToggle>

            <DropdownMenu>
                {sendEmailButton}
                {deleteButton}
                {archiveButton}
                {cloneButton}
            </DropdownMenu>
        </Dropdown>

        const successMessage = this.state.showSuccessMessage === true
            ? <SuccessMessage message="Invoice was updated successfully"/> : null
        const errorMessage = this.state.showErrorMessage === true
            ? <ErrorMessage message="Something went wrong"/> : null

        return <React.Fragment>
            {dropdownMenu}
            {successMessage}
            {errorMessage}
        </React.Fragment>
    }
}
