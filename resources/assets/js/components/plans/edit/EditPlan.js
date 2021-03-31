import React from 'react'
import { Button, DropdownItem, Modal, ModalBody } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import Details from './Details'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import { toast, ToastContainer } from 'react-toastify'
import PlanModel from '../../models/PlanModel'

export default class EditPlan extends React.Component {
    constructor (props) {
        super(props)

        this.planModel = new PlanModel(this.props.plan)
        this.initialState = this.planModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.handleClick = this.handleClick.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    static getDerivedStateFromProps (props, state) {
        if (props.plan && props.plan.id !== state.id) {
            const planModel = new PlanModel(props.plan)
            return planModel.fields
        }

        return null
    }

    componentDidUpdate (prevProps, prevState) {
        if (this.props.plan && this.props.plan.id !== prevProps.plan.id) {
            this.planModel = new PlanModel(this.props.plan)
        }
    }

    handleInput (e) {
        const name = event.target.name
        let value = event.target.type === 'checkbox' ? event.target.checked : event.target.value
        value = (value === 'true') ? true : ((value === 'false') ? false : (value))

        this.setState({
            [name]: value
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
        const formData = {
            name: this.state.name,
            account_id: this.state.account_id,
            code: this.state.code,
            description: this.state.description,
            price: this.state.price,
            interval_unit: this.state.interval_unit,
            interval_count: this.state.interval_count,
            trial_period: this.state.trial_period,
            invoice_period: this.state.invoice_period,
            invoice_interval: this.state.invoice_interval,
            grace_period: this.state.grace_period,
            grace_interval: this.state.grace_interval,
            active_subscribers_limit: this.state.active_subscribers_limit,
            trial_interval: this.state.trial_interval,
            assigned_to: this.state.assigned_to,
            auto_billing_enabled: this.state.auto_billing_enabled,
            can_cancel_plan: this.state.can_cancel_plan
        }

        this.planModel.save(formData).then(response => {
            if (!response) {
                this.setState({ errors: this.planModel.errors, message: this.planModel.error_message })

                toast.error(translations.updated_unsuccessfully.replace('{entity}', translations.plan), {
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

            toast.success(translations.updated_successfully.replace('{entity}', translations.plan), {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })

            const index = this.props.plans.findIndex(plan => plan.id === this.props.plan.id)
            this.props.plans[index] = response
            this.props.action(this.props.plans, true)
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
        const { message } = this.state
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>{translations.edit_plan}
                </DropdownItem>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_plan}/>

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

                        {message && <div className="alert alert-danger" role="alert">
                            {message}
                        </div>}

                        <Details hasErrorFor={this.hasErrorFor} plan={this.state}
                            renderErrorFor={this.renderErrorFor} handleInput={this.handleInput.bind(this)}/>
                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.state.plan_changed === true ? this.change : this.handleClick}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        )
    }
}
