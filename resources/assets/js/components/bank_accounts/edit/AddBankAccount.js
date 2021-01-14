import React from 'react'
import { Modal, ModalBody } from 'reactstrap'
import AddButtons from '../../common/AddButtons'
import { translations } from '../../utils/_translations'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import Details from './Details'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import BankAccountModel from '../../models/BankAccountModel'

class AddBankAccount extends React.Component {
    constructor (props) {
        super(props)
        this.bankAccountModel = new BankAccountModel(null)
        this.initialState = this.bankAccountModel.fields
        this.state = this.initialState
        this.toggle = this.toggle.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleClick = this.handleClick.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'bankAccountForm')) {
            const storedValues = JSON.parse(localStorage.getItem('bankAccountForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
    }

    hasErrorFor (field) {
        return !!this.state.errors[field]
    }

    handleChange (event) {
        this.setState({ name: event.target.value })
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value
        }, () => localStorage.setItem('bankAccountForm', JSON.stringify(this.state)))
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

    handleClick (event) {
        const data = {
            name: this.state.name,
            description: this.state.description,
            bank_id: this.state.bank_id,
            public_notes: this.state.public_notes,
            private_notes: this.state.private_notes,
            assigned_to: this.state.assigned_to,
            username: this.state.username,
            password: this.state.password,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4
        }

        this.bankAccountModel.save(data).then(response => {
            if (!response) {
                this.setState({ errors: this.bankAccountModel.errors, message: this.bankAccountModel.error_message })
                return
            }

            this.props.bank_accounts.push(response)
            this.props.action(this.props.bank_accounts)
            this.setState(this.initialState)
            localStorage.removeItem('bankAccountForm')
        })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState(this.initialState, () => localStorage.removeItem('bankAccountForm'))
            }
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <div>
                <AddButtons toggle={this.toggle}/>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_bank_account}/>

                    <ModalBody className={theme}>
                        <Details banks={this.props.banks} is_new={true} errors={this.state.errors}
                            bank_account={this.state}
                            handleInput={this.handleInput.bind(this)} hasErrorFor={this.hasErrorFor}
                            renderErrorFor={this.renderErrorFor}/>

                        <CustomFieldsForm handleInput={this.handleInput.bind(this)}
                            custom_value1={this.state.custom_value1}
                            custom_value2={this.state.custom_value2}
                            custom_value3={this.state.custom_value3}
                            custom_value4={this.state.custom_value4}
                            custom_fields={this.props.custom_fields}/>
                    </ModalBody>
                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </div>
        )
    }
}

export default AddBankAccount
