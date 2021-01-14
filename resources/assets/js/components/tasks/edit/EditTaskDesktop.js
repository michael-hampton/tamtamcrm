import React, { Component } from 'react'
import 'react-dates/initialize' // necessary for latest version
import 'react-dates/lib/css/_datepicker.css'
import {
    Button,
    Card,
    CardBody,
    CardHeader,
    Col,
    DropdownItem,
    FormGroup,
    Input,
    Label,
    Modal,
    ModalBody,
    Nav,
    NavItem,
    NavLink,
    Row,
    TabContent,
    TabPane
} from 'reactstrap'
import moment from 'moment'
import TaskModel from '../../models/TaskModel'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import Emails from '../../emails/Emails'
import FileUploads from '../../documents/FileUploads'
import Comments from '../../comments/Comments'
import DropdownMenuBuilder from '../../common/DropdownMenuBuilder'
import { toast, ToastContainer } from 'react-toastify'
import CustomerDropdown from '../../common/dropdowns/CustomerDropdown'
import ProjectDropdown from '../../common/dropdowns/ProjectDropdown'
import TaskStatusDropdown from '../../common/dropdowns/TaskStatusDropdown'
import UserDropdown from '../../common/dropdowns/UserDropdown'
import TaskTimeDesktop from './TaskTimeDesktop'
import AddButtons from '../../common/AddButtons'
import ColorPickerNew from '../../common/ColorPickerNew'

export default class EditTaskDesktop extends Component {
    constructor (props) {
        super(props)

        this.taskModel = new TaskModel(this.props.task, this.props.customers)
        this.initialState = this.taskModel.fields
        this.taskModel.start_date = this.initialState.start_date
        this.taskModel.due_date = this.initialState.due_date

        this.state = this.initialState

        this.handleSave = this.handleSave.bind(this)
        this.handleTaskTimeChange = this.handleTaskTimeChange.bind(this)
        this.handleDelete = this.handleDelete.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleMultiSelect = this.handleMultiSelect.bind(this)
        this.timerAction = this.timerAction.bind(this)
        this.toggle = this.toggle.bind(this)
        this.toggleTab = this.toggleTab.bind(this)
        this.updateList = this.updateList.bind(this)
        this.toggleMenu = this.toggleMenu.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
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

    toggleMenu (event) {
        this.setState({
            dropdownOpen: !this.state.dropdownOpen
        })
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    timerAction (e) {
        const data = {
            action: e.target.id
        }

        this.taskModel.timerAction(data).then(response => {
            if (!response) {
                this.setState({ errors: this.taskModel.errors, message: this.taskModel.error_message })
                return
            }

            this.setState({ action: e.target.id })
        })
    }

    getFormData () {
        return {
            timers: this.state.timers,
            task_rate: this.state.task_rate,
            include_documents: this.state.include_documents,
            is_recurring: this.state.is_recurring,
            recurring_start_date: this.state.recurring_start_date,
            recurring_end_date: this.state.recurring_end_date,
            recurring_due_date: this.state.recurring_due_date,
            last_sent_date: this.state.last_sent_date,
            next_send_date: this.state.next_send_date,
            recurring_frequency: this.state.recurring_frequency,
            customer_id: this.state.customer_id,
            assigned_to: this.state.assigned_to,
            name: this.state.name,
            description: this.state.description,
            contributors: this.state.selectedUsers,
            due_date: moment(this.state.due_date).format('YYYY-MM-DD'),
            start_date: moment(this.state.start_date).format('YYYY-MM-DD'),
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            public_notes: this.state.public_notes,
            private_notes: this.state.private_notes,
            project_id: this.state.project_id,
            task_status_id: this.state.task_status_id
        }
    }

    updateList (response, toggle = true) {
        if (this.props.allTasks) {
            const index = this.props.allTasks.findIndex(task => task.id === this.props.task.id)
            this.props.allTasks[index] = response
            this.props.action(this.props.allTasks)
            this.setState({ timers: response.timers })
            this.taskModel = new TaskModel(response, this.props.customers)
        }
    }

    handleSave () {
        this.taskModel.save(this.getFormData()).then(response => {
            if (!response) {
                this.setState({
                    showErrorMessage: true,
                    loading: false,
                    errors: this.taskModel.errors,
                    message: this.taskModel.error_message
                })
                return
            }

            if (!this.state.id) {
                const allTasks = this.props.tasks
                allTasks.push(response)
                this.props.action(allTasks)
                localStorage.removeItem('taskForm')
                this.setState(this.initialState)

                console.log('response', response)

                return
            }

            const index = this.props.tasks.findIndex(task => task.id === this.state.id)
            this.props.tasks[index] = response
            this.props.action(this.props.tasks)
            this.setState({ loading: false, changesMade: false })
        })
    }

    handleDelete () {
        this.setState({
            editMode: false
        })
        if (this.props.onDelete) {
            this.props.onDelete(this.props.task)
        }
    }

    handleChange (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            [e.target.name]: value,
            changesMade: true
        })
    }

    handleTaskTimeChange (timers) {
        this.setState({ timers: timers, changesMade: true })
    }

    handleMultiSelect (e) {
        this.setState({ selectedUsers: Array.from(e.target.selectedOptions, (item) => item.value) })
    }

    reload (data) {
        this.taskModel = new TaskModel(data, this.props.customers)
        this.initialState = this.taskModel.fields
        this.taskModel.start_date = this.initialState.start_date
        this.taskModel.due_date = this.initialState.due_date
        this.initialState.modal = true
        this.setState(this.initialState)
    }

    render () {
        console.log('timers', this.state.timers)
        const email_editor = this.state.id
            ? <Emails width={400} model={this.taskModel} emails={this.state.emails} template="email_template_task"
                show_editor={true}
                customers={this.props.customers} entity_object={this.state} entity="task"
                entity_id={this.state.id}/> : null
        const button_action = (this.taskModel.isRunning) ? ('stop_timer') : ((!this.state.timers || !this.state.timers.length) ? ('start_timer') : ('resume_timer'))
        const button_text = (this.taskModel.isRunning) ? (translations.stop) : ((!this.state.timers || !this.state.timers.length) ? (translations.start) : (translations.resume))

        const form = <React.Fragment>
            <Nav tabs>
                <NavItem>
                    <NavLink
                        className={this.state.activeTab === '1' ? 'active' : ''}
                        onClick={() => {
                            this.toggleTab('1')
                        }}>
                        {translations.details}
                    </NavLink>
                </NavItem>

                {!this.props.add &&
                <React.Fragment>
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '2' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('2')
                            }}>
                            {translations.documents}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '3' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('3')
                            }}>
                            {translations.email}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '4' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('4')
                            }}>
                            {translations.comments}
                        </NavLink>
                    </NavItem>
                </React.Fragment>

                }
            </Nav>

            <TabContent activeTab={this.state.activeTab}>
                <TabPane tabId="1">
                    {!this.props.add &&
                    <DropdownMenuBuilder reload={this.reload.bind(this)} invoices={this.state}
                        formData={this.getFormData()}
                        model={this.taskModel}
                        action={this.props.action}/>
                    }

                    <Row form>
                        <Col md={4}>
                            <Card>
                                <CardBody>
                                    {!this.state.invoice_id &&
                                    <FormGroup className="mb-3">
                                        <Label>{translations.customer}</Label>
                                        <CustomerDropdown
                                            customer={this.state.customer_id}
                                            renderErrorFor={this.renderErrorFor}
                                            handleInputChanges={this.handleChange}
                                            customers={this.props.customers}
                                        />
                                        {this.renderErrorFor('customer_id')}
                                    </FormGroup>
                                    }

                                    {!this.state.invoice_id &&
                                    <FormGroup>
                                        <Label>{translations.project}</Label>
                                        <ProjectDropdown handleInputChanges={this.handleChange}
                                            customer-id={this.state.customer_id}
                                            project={this.state.project_id} name="project_id"
                                        />
                                    </FormGroup>
                                    }

                                    <FormGroup>
                                        <Label>{translations.assigned_user}</Label>
                                        <UserDropdown handleInputChanges={this.handleChange}
                                            user_id={this.state.assigned_to} name="assigned_to"
                                            users={this.props.users}/>
                                    </FormGroup>
                                </CardBody>
                            </Card>
                        </Col>

                        <Col md={4}>
                            <Card>
                                <CardBody>
                                    <FormGroup>
                                        <Label for="number">{translations.number}:</Label>
                                        <Input className={this.hasErrorFor('number') ? 'is-invalid' : ''} type="text"
                                            name="number" value={this.state.number} id="number"
                                            onChange={this.handleChange}/>
                                        {this.renderErrorFor('number')}
                                    </FormGroup>
                                    <FormGroup>
                                        <Label>{translations.status}</Label>
                                        <TaskStatusDropdown
                                            task_type={1}
                                            status={this.state.task_status_id}
                                            handleInputChanges={this.handleChange}
                                        />
                                    </FormGroup>

                                    <FormGroup>
                                        <Label for="task_rate">{translations.task_rate}:</Label>
                                        <Input className={this.hasErrorFor('task_rate') ? 'is-invalid' : ''}
                                            type="text"
                                            name="task_rate" value={this.state.task_rate} id="task_rate"
                                            onChange={this.handleChange}/>
                                        {this.renderErrorFor('task_rate')}
                                    </FormGroup>
                                </CardBody>
                            </Card>
                        </Col>

                        <Col md={4}>
                            <Card>
                                <CardBody>

                                    <FormGroup>
                                        <Label for="name">{translations.name}(*):</Label>
                                        <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''} type="text"
                                            name="name"
                                            value={this.state.name}
                                            id="taskTitle" onChange={this.handleChange}/>
                                        {this.renderErrorFor('name')}
                                    </FormGroup>
                                    <FormGroup>
                                        <Label for="description">{translations.description}:</Label>
                                        <Input className={this.hasErrorFor('description') ? 'is-invalid' : ''}
                                            type="textarea"
                                            name="description" value={this.state.description} id="description"
                                            onChange={this.handleChange.bind(this)}/>
                                        {this.renderErrorFor('description')}
                                    </FormGroup>

                                    <ColorPickerNew color={this.state.column_color} onChange={(color) => {
                                        this.setState({ column_color: color })
                                    }}/>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>

                    <Row>
                        <TaskTimeDesktop model={this.taskModel} timers={this.state.timers || []}
                            handleTaskTimeChange={this.handleTaskTimeChange}/>
                    </Row>
                </TabPane>

                <TabPane tabId="2">
                    <Card>
                        <CardHeader>{translations.documents}</CardHeader>
                        <CardBody>
                            <FileUploads entity_type="Task" entity={this.state}
                                user_id={this.state.user_id}/>
                        </CardBody>
                    </Card>
                </TabPane>

                <TabPane tabId="3">
                    {email_editor}
                </TabPane>

                <TabPane tabId="4">
                    <Comments entity_type="Task" entity={this.state}
                        user_id={this.state.user_id}/>
                </TabPane>
            </TabContent>

            <Button onClick={(e) => {
                this.taskModel.completeAction(this.state, button_action).then(response => {
                    this.setState({ show_success: true }, () => {
                        this.updateList(response, false)
                    })

                    toast.success(translations.times_updated, {
                        position: 'top-center',
                        autoClose: 5000,
                        hideProgressBar: false,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: true,
                        progress: undefined
                    })
                })
            }}>{button_text}</Button>
        </React.Fragment>

        const button = this.props.add === true ? <AddButtons toggle={this.toggle}/>
            : <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>{translations.edit_task}
            </DropdownItem>

        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return this.props.modal && this.props.modal === true
            ? <React.Fragment>
                {button}
                <Modal size="lg" isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle}
                        title={this.taskModel.isNew ? translations.add_task : translations.edit_task}/>

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

                        {form}
                    </ModalBody>
                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleSave.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment> : form
    }
}
