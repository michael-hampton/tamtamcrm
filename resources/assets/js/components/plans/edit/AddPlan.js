import React from 'react'
import { Modal, ModalBody } from 'reactstrap'
import AddButtons from '../../common/AddButtons'
import { translations } from '../../utils/_translations'
import Details from './Details'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import PlanModel from '../../models/PlanModel'

export default class AddPlan extends React.Component {
    constructor (props) {
        super(props)

        this.planModel = new PlanModel(null)
        this.initialState = this.planModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'planForm')) {
            const storedValues = JSON.parse(localStorage.getItem('planForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
    }

    handleInput (event) {
        const name = event.target.name
        let value = event.target.type === 'checkbox' ? event.target.checked : event.target.value
        value = (value === 'true') ? true : ((value === 'false') ? false : (value))

        this.setState({
            [name]: value
        }, () => localStorage.setItem('planForm', JSON.stringify(this.state)))
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

        this.planModel.save(data).then(response => {
            if (!response) {
                this.setState({ errors: this.planModel.errors, message: this.planModel.error_message })
                return
            }
            this.props.plans.unshift(response)
            this.props.action(this.props.plans, true)
            localStorage.removeItem('planForm')
            this.setState(this.initialState)
        })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState(this.initialState, () => localStorage.removeItem('planForm'))
            }
        })
    }

    render () {
        const { message } = this.state
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <AddButtons toggle={this.toggle}/>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_plan}/>

                    <ModalBody className={theme}>

                        {message && <div className="alert alert-danger" role="alert">
                            {message}
                        </div>}

                        <Details hasErrorFor={this.hasErrorFor} plan={this.state}
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
