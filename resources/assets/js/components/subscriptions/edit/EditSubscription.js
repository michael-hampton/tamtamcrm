import React from 'react'
import { DropdownItem, Modal, ModalBody } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import SubscriptionModel from '../../models/SubscriptionModel'
import Details from './Details'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import { toast, ToastContainer } from 'react-toastify'

export default class EditSubscription extends React.Component {
    constructor (props) {
        super(props)

        this.subscriptionModel = new SubscriptionModel(this.props.subscription)
        this.initialState = this.subscriptionModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    static getDerivedStateFromProps (props, state) {
        if (props.subscription & props.subscription.id && props.subscription.id !== state.id) {
            const invoiceModel = new SubscriptionModel(props.subscription)
            return invoiceModel.fields
        }

        return null
    }

    componentDidUpdate (prevProps, prevState) {
        if (this.props.subscription && this.props.subscription.id && this.props.subscription.id !== prevProps.subscription.id) {
            this.subscriptionModel = new SubscriptionModel(this.props.subscription)
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

    handleClick () {
        const data = {
            name: this.state.name,
            target_url: this.state.target_url,
            event_id: this.state.event_id
        }

        this.subscriptionModel.save(data).then(response => {
            if (!response) {
                this.setState({
                    errors: this.subscriptionModel.errors,
                    message: this.subscriptionModel.error_message
                })

                toast.error(translations.updated_unsuccessfully.replace('{entity}', translations.webhook), {
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

            toast.success(translations.updated_successfully.replace('{entity}', translations.webhook), {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })

            const index = this.props.subscriptions.findIndex(subscription => subscription.id === this.props.subscription.id)
            this.props.subscriptions[index] = response
            this.props.action(this.props.subscriptions, true)
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
                <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>{translations.edit_webhook}
                </DropdownItem>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_webhook}/>

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

                        <Details hasErrorFor={this.hasErrorFor} subscription={this.state}
                            renderErrorFor={this.renderErrorFor} handleInput={this.handleInput.bind(this)}/>
                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        )
    }
}
