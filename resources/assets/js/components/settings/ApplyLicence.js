import React, { Component } from 'react'
import { FormGroup, Input, Label, Modal, ModalBody } from 'reactstrap'
import { translations } from '../utils/_translations'
import { icons } from '../utils/_icons'
import BlockButton from '../common/BlockButton'
import DefaultModalHeader from '../common/ModalHeader'
import DefaultModalFooter from '../common/ModalFooter'
import axios from 'axios'

export default class ApplyLicence extends Component {
    constructor (props) {
        super(props)
        this.state = {
            id: localStorage.getItem('account_id'),
            licence_number: null,
            licence_checked: false,
            errors: [],
            message: '',
            modal: false
        }

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.handleClick = this.handleClick.bind(this)
        this.checkLicence = this.checkLicence.bind(this)
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value,
            licence_checked: false
        })
    }

    hasErrorFor (field) {
        return !!this.state.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.state.errors[field][0]}</strong>
                </span>
            )
        }
    }

    handleClick () {
        if (!this.state.licence_number || !this.state.licence_number.toString().length) {
            alert('You must enter a licence number')
            return false
        }

        if(this.state.licence_checked === false) {
            alert('Please verify the licence')
            return false
        }

        axios.post(`/api/account/apply/${this.state.id}`, { licence_number: this.state.licence_number })
            .then((response) => {
                this.setState({
                    success: true
                }, () => this.props.callback(this.state))
            })
            .catch((error) => {
                console.error(error)
                this.setState({ error: true })
            })
    }

    checkLicence () {
        this.setState({ licence_checked: true })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    render () {
        const { message } = this.state
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <BlockButton icon={icons.cloud_upload} button_text={translations.apply_licence}
                    onClick={this.toggle} />

                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.apply_licence}/>

                    <ModalBody className={theme}>

                        {message && <div className="alert alert-danger" role="alert">
                            {message}
                        </div>}

                        <FormGroup>
                            <Label for="exampleEmail">{translations.licence_number}</Label>
                            <Input onChange={this.handleInput} type="number" name="licence_number" id="licence_number" placeholder={translations.licence_number} />
                        </FormGroup>

                        {!this.state.licence_checked &&
                        <button type="button" className="btn btn-primary" onClick={this.licence_checked}>Verify Licence</button>
                        }

                    </ModalBody>

                    {!!this.state.licence_checked && 
                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                    }
                </Modal>
            </React.Fragment>
        )
    }
}
