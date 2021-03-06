import React from 'react'
import { FormGroup, Input, Label, Modal, ModalBody } from 'reactstrap'
import AddButtons from '../../common/AddButtons'
import { translations } from '../../utils/_translations'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import TaskStatusModel from '../../models/TaskStatusModel'
import ColorPickerNew from '../../common/ColorPickerNew'
import { taskTypes } from '../../utils/_consts'

class AddTaskStatus extends React.Component {
    constructor (props) {
        super(props)

        this.taskStatusModel = new TaskStatusModel(null)
        this.initialState = this.taskStatusModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleFileChange = this.handleFileChange.bind(this)
    }

    handleFileChange (e) {
        this.setState({
            [e.target.name]: e.target.files[0]
        })
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value
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
        this.taskStatusModel.save({
            name: this.state.name,
            description: this.state.description,
            column_color: this.state.column_color,
            task_type: this.state.task_type
        }).then(response => {
            if (!response) {
                this.setState({ errors: this.taskStatusModel.errors, message: this.taskStatusModel.error_message })
                return
            }

            this.props.statuses.unshift(response)
            this.props.action(this.props.statuses, true)
            this.setState(this.initialState)
        })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <AddButtons toggle={this.toggle}/>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_task_status}/>

                    <ModalBody className={theme}>
                        <FormGroup>
                            <Label for="name">{translations.name} <span className="text-danger">*</span></Label>
                            <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="name"
                                id="name" placeholder={translations.name} onChange={this.handleInput.bind(this)}/>
                            {this.renderErrorFor('name')}
                        </FormGroup>

                        <FormGroup>
                            <Label for="description">{translations.description} <span
                                className="text-danger">*</span></Label>
                            <Input className={this.hasErrorFor('description') ? 'is-invalid' : ''} type="text"
                                name="description"
                                id="name" placeholder={translations.description}
                                onChange={this.handleInput.bind(this)}/>
                            {this.renderErrorFor('description')}
                        </FormGroup>

                        <FormGroup>
                            <Label for="task_type">{translations.task_type}</Label>
                            <Input className={this.hasErrorFor('task_type') ? 'is-invalid' : ''} type="select"
                                name="task_type"
                                id="task_type" placeholder={translations.task_type}
                                onChange={this.handleInput.bind(this)}>
                                <option value="">{translations.select_option}</option>
                                <option value={taskTypes.deal}>{translations.deal}</option>
                                <option value={taskTypes.lead}>{translations.lead}</option>
                                <option value={taskTypes.task}>{translations.task}</option>
                            </Input>
                            {this.renderErrorFor('task_type')}
                        </FormGroup>

                        <ColorPickerNew color={this.state.column_color} onChange={(color) => {
                            this.setState({ column_color: color })
                        }}/>
                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        )
    }
}

export default AddTaskStatus
