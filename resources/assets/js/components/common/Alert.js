import React, { Component } from 'react'
import {
    Button,
    Modal,
    ModalBody,
    ModalFooter,
    ModalHeader,
    UncontrolledTooltip
} from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'

export default class Alert extends Component {
    constructor (props) {
        super(props)
        this.state = {
            is_open: this.props.is_open,
            check: false,
            errors: [],
            showSuccessMessage: false,
            showErrorMessage: false,
            message: ''
        }

        this.toggle = this.toggle.bind(this)
    }

    static getDerivedStateFromProps (props, state) {
        if (props.is_open && props.is_open !== state.is_open) {
            return props.is_open
        }

        return null
    }

    componentDidUpdate (prevProps, prevState) {
        if (this.props.is_open && this.props.is_open !== prevProps.is_open) {
            // this.invoiceModel = new InvoiceModel(this.props.invoice, this.props.customers)
        }
    }

    toggle () {
        this.setState({
            is_open: !this.state.is_open,
            errors: []
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        const color = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
            ? '#fff' : '#000'

        return (
            <React.Fragment>
                <UncontrolledTooltip placement="right" target="contactTooltip">
                    {translations.about}
                </UncontrolledTooltip>

                <i id="contactTooltip" onClick={this.toggle}
                    style={{
                        marginLeft: '12px',
                        marginRight: 'auto',
                        color: color,
                        fontSize: '20px',
                        cursor: 'pointer'
                    }}
                    className="fa fa-question-circle"/>

                <Modal centered={true} backdrop="static" isOpen={this.state.is_open} toggle={this.toggle}
                    className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>{translations.alert}</ModalHeader>
                    <ModalBody className={theme}>
                        {this.props.message}
                    </ModalBody>
                    <ModalFooter>
                        <Button onClick={this.toggle} color="secondary">{translations.close}</Button>
                    </ModalFooter>
                </Modal>
            </React.Fragment>
        )
    }
}
