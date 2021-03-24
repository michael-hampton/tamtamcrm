import React from 'react'
import { Modal, ModalBody } from 'reactstrap'
import AddButtons from '../../common/AddButtons'
import { translations } from '../../utils/_translations'
import Details from './Details'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import PlanSubscriptionModel from '../../models/PlanSubscriptionModel'

export default class AddPlan extends React.Component {
    constructor (props) {
        super(props)

        this.planModel = new PlanSubscriptionModel(null)
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

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value
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
            plan_id: this.state.plan_id
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

                        <Details plan_types={this.props.plan_types} hasErrorFor={this.hasErrorFor} plan={this.state}
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
