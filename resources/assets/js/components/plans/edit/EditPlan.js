import React from 'react'
import { DropdownItem, Modal, ModalBody } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import Details from './Details'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import { toast, ToastContainer } from 'react-toastify'
import PlanSubscriptionModel from '../../models/PlanSubscriptionModel'

export default class EditPlan extends React.Component {
    constructor (props) {
        super(props)

        this.planModel = new PlanSubscriptionModel(this.props.plan)
        this.initialState = this.planModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
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
        const formData = {
            name: this.state.name,
            account_id: this.state.account_id,
            plan_id: this.state.plan_id,
            number_of_licences: this.state.number_of_licences
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
