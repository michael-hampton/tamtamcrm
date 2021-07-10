import React from 'react'
import { DropdownItem, FormGroup, Input, Label, Modal, ModalBody } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import TokenModel from '../../models/TokenModel'
import DropdownMenuBuilder from '../../common/DropdownMenuBuilder'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import { toast, ToastContainer } from 'react-toastify'

export default class EditToken extends React.Component {
    constructor (props) {
        super(props)

        this.tokenModel = new TokenModel(this.props.token)
        this.initialState = this.tokenModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    static getDerivedStateFromProps (props, state) {
        if (props.token && props.token.id !== state.id) {
            const invoiceModel = new TokenModel(props.token)
            return invoiceModel.fields
        }

        return null
    }

    componentDidUpdate (prevProps, prevState) {
        if (this.props.token && this.props.token.id !== prevProps.token.id) {
            this.tokenModel = new TokenModel(this.props.token)
        }
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value,
            changesMade: true
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

    getFormData () {
        return {
            name: this.state.name,
            settings: this.state.settings
        }
    }

    handleClick () {
        const data = this.getFormData()

        this.tokenModel.save(data).then(response => {
            if (!response) {
                this.setState({ errors: this.tokenModel.errors, message: this.tokenModel.error_message })

                toast.error(translations.updated_unsuccessfully.replace('{entity}', translations.token), {
                    position: 'top-center',
                    autoClose: 5000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                })

                return
            }

            toast.success(translations.updated_successfully.replace('{entity}', translations.token), {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })

            const index = this.props.tokens.findIndex(token => token.id === this.props.token.id)
            this.props.tokens[index] = response
            this.props.action(this.props.tokens, true)
            this.setState({
                editMode: false,
                changesMade: false
            })
            this.toggle()
        })
    }

    toggle () {
        if (this.state.modal && this.state.changesMade) {
            if (window.confirm('Your changes have not been saved?')) {
                this.setState({ ...this.initialState })
            }

            return
        }

        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>{translations.edit_token}
                </DropdownItem>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_token}/>

                    <ModalBody className={theme}>
                        <ToastContainer
                            position="top-center"
                            autoClose={5000}
                            hideProgressBar={false}
                            newestOnTop={false}
                            closeOnClick
                            rtl={false}
                            pauseOnFocusLoss
                            draggable
                            pauseOnHover
                        />

                        <DropdownMenuBuilder invoices={this.props.tokens} formData={this.getFormData()}
                            model={this.tokenModel}
                            action={this.props.action}/>

                        <FormGroup>
                            <Label for="name">{translations.name} <span className="text-danger">*</span></Label>
                            <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''}
                                value={this.state.name}
                                type="text"
                                name="name"
                                id="name"
                                placeholder="Name" onChange={this.handleInput.bind(this)}/>
                            {this.renderErrorFor('name')}
                        </FormGroup>
                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        )
    }
}
