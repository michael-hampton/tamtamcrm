import React from 'react'
import { Button, DropdownItem, Modal, ModalBody } from 'reactstrap'
import axios from 'axios'
import SuccessMessage from '../../common/SucessMessage'
import ErrorMessage from '../../common/ErrorMessage'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import ProjectModel from '../../models/ProjectModel'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import Details from './Details'
import DropdownMenuBuilder from '../../common/DropdownMenuBuilder'
import CustomFieldsForm from '../../common/CustomFieldsForm'

class EditProject extends React.Component {
    constructor (props) {
        super(props)

        this.projectModel = new ProjectModel(this.props.project)
        this.initialState = this.projectModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleClick = this.handleClick.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.getProject = this.getProject.bind(this)
        this.toggleMenu = this.toggleMenu.bind(this)
        this.changeStatus = this.changeStatus.bind(this)
    }

    componentDidMount () {
        this.getProject()
    }

    toggleMenu (event) {
        this.setState({
            dropdownOpen: !this.state.dropdownOpen
        })
    }

    hasErrorFor (field) {
        return !!this.state.errors[field]
    }

    handleChange (event) {
        this.setState({ name: event.target.value })
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value,
            changesMade: true
        })
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
            column_color: this.state.column_color,
            name: this.state.name,
            description: this.state.description,
            customer_id: this.state.customer_id,
            private_notes: this.state.private_notes,
            public_notes: this.state.public_notes,
            due_date: this.state.due_date,
            assigned_to: this.state.assigned_to,
            budgeted_hours: this.state.budgeted_hours,
            task_rate: this.state.task_rate,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4
        }
    }

    changeStatus (action) {
        if (!this.props.project_id) {
            return false
        }

        const data = this.getFormData()
        axios.post(`/api/project/${this.props.project_id}/${action}`, data)
            .then((response) => {
                if (action === 'download') {
                    this.downloadPdf(response)
                }

                this.setState({ showSuccessMessage: true })
            })
            .catch((error) => {
                this.setState({ showErrorMessage: true })
                console.warn(error)
            })
    }

    handleClick (event) {
        const data = this.getFormData()

        this.projectModel.save(data).then(response => {
            if (!response) {
                this.setState({ errors: this.projectModel.errors, message: this.projectModel.error_message })
                return
            }

            const index = this.props.projects.findIndex(project => project.id === this.props.project.id)
            this.props.projects[index] = response
            this.props.action(this.props.projects)
            this.setState({
                editMode: false,
                changesMade: false
            })
            this.toggle()
        })
    }

    getProject () {
        axios.get(`/api/projects/${this.props.project_id}`)
            .then((r) => {
                if (r.data) {
                    this.setState(r.data)
                }
            })
            .catch((e) => {
                console.error(e)
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
        const button = this.props.listView && this.props.listView === true
            ? <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>{translations.edit_project}
            </DropdownItem>
            : <Button className="mr-2 ml-2" color="primary" onClick={this.toggle}>Edit Project</Button>

        const successMessage = this.state.showSuccessMessage === true
            ? <SuccessMessage message="Invoice was updated successfully"/> : null
        const errorMessage = this.state.showErrorMessage === true
            ? <ErrorMessage message="Something went wrong"/> : null
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <div>
                {button}
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_project}/>

                    <ModalBody className={theme}>
                        <DropdownMenuBuilder invoices={this.state} formData={this.getFormData()}
                            model={this.projectModel}
                            action={this.props.action}/>
                        {successMessage}
                        {errorMessage}

                        <Details is_new={false} errors={this.state.errors} project={this.state}
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

export default EditProject
