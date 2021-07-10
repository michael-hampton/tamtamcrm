import React from 'react'
import { Button, Form, Modal, ModalBody } from 'reactstrap'
import moment from 'moment'
import AddButtons from '../../common/AddButtons'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import Notes from '../../common/Notes'
import DealModel from '../../models/DealModel'
import Details from './Details'
import { translations } from '../../utils/_translations'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import axios from 'axios'

export default class AddDeal extends React.Component {
    constructor (props) {
        super(props)

        this.dealModel = new DealModel(null, this.props.customers)
        this.initialState = this.dealModel.fields

        if (this.props.task_status) {
            this.initialState.task_status_id = this.props.task_status
        }

        this.state = this.initialState
        this.toggle = this.toggle.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.buildForm = this.buildForm.bind(this)
    }

    componentDidMount () {
        this.getSourceTypes()
        if (Object.prototype.hasOwnProperty.call(localStorage, 'dealForm')) {
            // const storedValues = JSON.parse(localStorage.getItem('taskForm'))
            // this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
    }

    getSourceTypes () {
        axios.get('/api/tasks/source-types')
            .then((r) => {
                this.setState({
                    sourceTypes: r.data,
                    err: ''
                })
            })
            .then((r) => {
                console.warn(this.state.users)
            })
            .catch((e) => {
                console.error(e)
                this.setState({
                    err: e
                })
            })
    }

    handleInput (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            [e.target.name]: value
        }, () => localStorage.setItem('dealForm', JSON.stringify(this.state)))
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState(this.initialState, () => localStorage.removeItem('taskForm'))
            }
        })
    }

    handleClick (event) {
        this.setState({
            submitSuccess: false,
            loading: true
        })

        const data = {
            rating: this.state.rating,
            customer_id: this.state.customer_id,
            project_id: this.state.project_id,
            name: this.state.name,
            description: this.state.description,
            task_status_id: this.state.task_status_id,
            assigned_to: this.state.assigned_to,
            due_date: moment(this.state.due_date).format('YYYY-MM-DD'),
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            customer_note: this.state.customer_note,
            internal_note: this.state.internal_note,
            column_color: this.state.column_color
        }

        this.dealModel.save(data).then(response => {
            if (!response) {
                this.setState({ errors: this.dealModel.errors, message: this.taskModel.error_message })
                return
            }
            this.props.deals.unshift(response)
            this.props.action(this.props.deals, true)
            this.setState(this.initialState)
            localStorage.removeItem('dealForm')
        })
    }

    buildForm () {
        return (
            <Form>
                <Details sourceTypes={this.state.sourceTypes} deal={this.state} customers={this.props.customers}
                    errors={this.state.errors}
                    users={this.props.users} handleInput={this.handleInput}/>

                <CustomFieldsForm handleInput={this.handleInput} custom_value1={this.state.custom_value1}
                    custom_value2={this.state.custom_value2}
                    custom_value3={this.state.custom_value3}
                    custom_value4={this.state.custom_value4}
                    custom_fields={this.props.custom_fields}/>

                <Notes internal_note={this.state.internal_note} customer_note={this.state.customer_note}
                    handleInput={this.handleInput}/>

            </Form>
        )
    }

    render () {
        const form = this.buildForm()
        const saveButton = <Button color="primary" onClick={this.handleClick.bind(this)}> Add</Button>
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        const button = this.props.large_button
            ? <Button onClick={this.toggle} size="lg" color="primary" block>{translations.add_deal}</Button>
            : <AddButtons toggle={this.toggle}/>

        if (this.props.modal) {
            return (
                <div>
                    {button}
                    <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                        <DefaultModalHeader toggle={this.toggle} title={translations.add_deal}/>

                        <ModalBody className={theme}>
                            {form}
                        </ModalBody>

                        <DefaultModalFooter show_success={true} toggle={this.toggle}
                            saveData={this.handleClick.bind(this)}
                            loading={false}/>
                    </Modal>
                </div>
            )
        }

        return (
            <div>
                {this.state.submitSuccess && (
                    <div className="mt-3 alert alert-info" role="alert">
                                The event has been created successfully </div>
                )}
                {form}
                {saveButton}
            </div>
        )
    }
}
