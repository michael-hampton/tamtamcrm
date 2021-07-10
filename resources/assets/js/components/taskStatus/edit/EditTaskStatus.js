import React from 'react'
import { DropdownItem, FormGroup, Input, Label, Modal, ModalBody } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import TaskStatusModel from '../../models/TaskStatusModel'
import ColorPickerNew from '../../common/ColorPickerNew'
import { taskTypes } from '../../utils/_consts'
import { toast, ToastContainer } from 'react-toastify'

class EditTaskStatus extends React.Component {
    constructor (props) {
        super(props)

        this.taskStatusModel = new TaskStatusModel(this.props.task_status)
        this.initialState = this.taskStatusModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleFileChange = this.handleFileChange.bind(this)
    }

    static getDerivedStateFromProps (props, state) {
        if (props.task_status && props.task_status.id !== state.id) {
            const invoiceModel = new TaskStatusModel(props.task_status)
            return invoiceModel.fields
        }

        return null
    }

    componentDidUpdate (prevProps, prevState) {
        if (this.props.task_status && this.props.task_status.id !== prevProps.task_status.id) {
            this.taskStatusModel = new TaskStatusModel(this.props.task_status)
        }
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

    getFormData () {
        return {
            name: this.state.name,
            description: this.state.description,
            column_color: this.state.column_color,
            task_type: this.state.task_type
        }
    }

    handleClick () {
        this.setState({ loading: true })
        this.taskStatusModel.update(this.getFormData()).then(response => {
            if (!response) {
                this.setState({ errors: this.taskStatusModel.errors, message: this.taskStatusModel.error_message })

                toast.error(translations.updated_unsuccessfully.replace('{entity}', translations.task_status), {
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

            toast.success(translations.updated_successfully.replace('{entity}', translations.task_status), {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })

            const index = this.props.statuses.findIndex(task_status => task_status.id === this.state.id)
            this.props.statuses[index] = response
            this.props.action(this.props.statuses, true)
            this.setState({ changesMade: false, loading: false })
            this.toggle()
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
                <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>{translations.edit_task_status}
                </DropdownItem>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_task_status}/>

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

                        <FormGroup>
                            <Label for="name">{translations.name} <span className="text-danger">*</span></Label>
                            <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''}
                                value={this.state.name}
                                type="text"
                                name="name"
                                id="name"
                                placeholder={translations.name} onChange={this.handleInput.bind(this)}/>
                            {this.renderErrorFor('name')}
                        </FormGroup>

                        <FormGroup>
                            <Label for="description">{translations.description} <span
                                className="text-danger">*</span></Label>
                            <Input className={this.hasErrorFor('description') ? 'is-invalid' : ''}
                                value={this.state.description}
                                type="text"
                                name="description"
                                id="description"
                                placeholder={translations.description} onChange={this.handleInput.bind(this)}/>
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

export default EditTaskStatus
