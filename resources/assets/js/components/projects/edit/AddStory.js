import React from 'react'
import { Modal, ModalBody } from 'reactstrap'
import axios from 'axios'
import AddButtons from '../../common/AddButtons'
import { translations } from '../../utils/_translations'
import ProjectModel from '../../models/ProjectModel'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import Details from './Details'
import CustomFieldsForm from '../../common/CustomFieldsForm'

class AddStory extends React.Component {
    constructor (props) {
        super(props)
        this.projectModel = new ProjectModel(null)
        this.initialState = this.projectModel.fields
        this.state = this.initialState
        this.toggle = this.toggle.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.getStoryCount = this.getStoryCount.bind(this)
        this.handleClick = this.handleClick.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'projectForm')) {
            const storedValues = JSON.parse(localStorage.getItem('projectForm'))
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
        }, () => localStorage.setItem('projectForm', JSON.stringify(this.state)))
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

    /** To be done */
    getStoryCount () {
        axios.get('/story/count')
            .then((r) => {
                this.setState({
                    count: r.data.count,
                    err: ''
                })
            })
            .catch((e) => {
                this.setState({
                    err: e
                })
            })
    }

    handleClick (event) {
        const data = {
            column_color: this.state.column_color,
            name: this.state.name,
            description: this.state.description,
            customer_id: this.state.customer_id,
            storyId: this.state.count,
            public_notes: this.state.public_notes,
            private_notes: this.state.private_notes,
            due_date: this.state.due_date,
            assigned_to: this.state.assigned_to,
            budgeted_hours: this.state.budgeted_hours,
            task_rate: this.state.task_rate,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4
        }

        this.projectModel.save(data).then(response => {
            if (!response) {
                this.setState({ errors: this.projectModel.errors, message: this.projectModel.error_message })
                return
            }

            this.props.projects.push(response)
            this.props.action(this.props.projects)
            this.setState(this.initialState)
            localStorage.removeItem('projectForm')
        })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState(this.initialState, () => localStorage.removeItem('projectForm'))
            }
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <div>
                <AddButtons toggle={this.toggle}/>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_project}/>

                    <ModalBody className={theme}>
                        <Details is_new={true} errors={this.state.errors} project={this.state}
                            handleInput={this.handleInput.bind(this)} hasErrorFor={this.hasErrorFor}
                            renderErrorFor={this.renderErrorFor} customers={this.props.customers}/>

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

export default AddStory
