import React from 'react'
import { Button, Form, FormGroup, Input, Label, Modal, ModalBody, ModalFooter } from 'reactstrap'
import axios from 'axios'
import DateTime from 'react-datetime'
import EventTypeDropdown from '../common/dropdowns/EventTypeDropdown'
import CustomerDropdown from '../common/dropdowns/CustomerDropdown'
import FormBuilder from '../settings/FormBuilder'
import { translations } from '../utils/_translations'
import DefaultModalHeader from '../common/ModalHeader'

class CreateEvent extends React.Component {
    constructor (props) {
        super(props)

        this.state = {
            modal: false,
            title: '',
            beginDate: '',
            endDate: '',
            customer_id: this.props.customer_id ? this.props.customer_id : '',
            location: '',
            description: '',
            custom_value1: '',
            custom_value2: '',
            custom_value3: '',
            custom_value4: '',
            event_type: '',
            loading: false,
            customers: [],
            users: [],
            errors: [],
            selectedUsers: [],
            submitSuccess: false
        }

        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.toggle = this.toggle.bind(this)
        this.handleMultiSelect = this.handleMultiSelect.bind(this)
        this.getUserList = this.getUserList.bind(this)
        this.buildForm = this.buildForm.bind(this)
        this.handleInput = this.handleInput.bind(this)
    }

    componentDidMount () {
        this.getUsers()

        if (Object.prototype.hasOwnProperty.call(localStorage, 'eventForm')) {
            const storedValues = JSON.parse(localStorage.getItem('eventForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
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

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value
        }, () => localStorage.setItem('eventForm', JSON.stringify(this.state)))
    }

    handleClick (event) {
        this.setState({
            submitSuccess: false,
            loading: true
        })

        axios.post('/api/events', {
            customer_id: this.state.customer_id,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            users: this.state.selectedUsers,
            title: this.state.title,
            description: this.state.description,
            event_type: this.state.event_type,
            location: this.state.location,
            beginDate: this.state.beginDate,
            endDate: this.state.endDate,
            task_id: this.props.task_id ? this.props.task_id : 0
        })
            .then((response) => {
                this.toggle()
                this.setState({
                    title: null,
                    content: null,
                    contributors: null,
                    due_date: null,
                    loading: false,
                    submitSuccess: true
                })

                if (this.props.action) {
                    const firstEvent = response.data
                    this.props.events.push(firstEvent)
                    this.props.action(this.props.events)
                }

                localStorage.removeItem('eventForm')
            })
            .catch((error) => {
                this.setState({
                    errors: error.response.data.errors
                })
            })
    }

    getUsers () {
        axios.get('/api/users')
            .then((r) => {
                console.log('users', r.data)
                this.setState({
                    users: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState({
                    name: null,
                    icon: null
                }, () => localStorage.removeItem('eventForm'))
            }
        })
    }

    handleMultiSelect (e) {
        this.setState({ selectedUsers: Array.from(e.target.selectedOptions, (item) => item.value) })
    }

    getUserList () {
        let userList
        if (!this.state.users.length) {
            userList = <option value="">Loading...</option>
        } else {
            userList = this.state.users.map((user, index) => (
                <option key={index} value={user.id}>{user.first_name + ' ' + user.last_name}</option>
            ))
        }

        return (
            <FormGroup>
                <Label for="users">Attendees</Label>
                <Input onChange={this.handleMultiSelect} type="select" name="users" id="users" multiple>
                    {userList}
                </Input>
                {this.renderErrorFor('users')}
            </FormGroup>
        )
    }

    handleStartDate (date) {
        this.setState({ beginDate: date._d })
    }

    handleEndDate (date) {
        this.setState({ endDate: date._d })
    }

    buildForm () {
        const userList = this.getUserList()
        const customFields = this.props.custom_fields ? this.props.custom_fields : []
        const customForm = customFields && customFields.length ? <FormBuilder
            handleChange={this.handleInput.bind(this)}
            formFieldsRows={customFields}
        /> : null

        return (
            <Form>
                <FormGroup>
                    <Label for="title">{translations.title}(*):</Label>
                    <Input className={this.hasErrorFor('title') ? 'is-invalid' : ''}
                        value={this.state.title}
                        type="text" name="title"
                        id="taskTitle" onChange={this.handleInput.bind(this)}/>
                    {this.renderErrorFor('title')}
                </FormGroup>

                <FormGroup>
                    <Label for="description">{translations.description}(*):</Label>
                    <Input className={this.hasErrorFor('description') ? 'is-invalid' : ''}
                        value={this.state.description}
                        type="text" name="description"
                        id="description" onChange={this.handleInput.bind(this)}/>
                    {this.renderErrorFor('description')}
                </FormGroup>

                <FormGroup>
                    <Label for="location">{translations.location}:</Label>
                    <Input className={this.hasErrorFor('location') ? 'is-invalid' : ''} type="text"
                        value={this.state.location}
                        name="location"
                        id="location"
                        onChange={this.handleInput.bind(this)}/>
                    {this.renderErrorFor('location')}
                </FormGroup>

                <FormGroup>
                    <Label for="beginDate">{translations.start_date}:</Label>
                    <DateTime dateFormat="YYYY-MM-DD" inputProps={{ name: 'beginDate' }}
                        className={this.hasErrorFor('beginDate') ? 'is-invalid' : ''}
                        onChange={this.handleStartDate.bind(this)}/>
                    {this.renderErrorFor('beginDate')}
                </FormGroup>

                <FormGroup>
                    <Label for="endDate">{translations.end_date}:</Label>
                    <DateTime dateFormat="YYYY-MM-DD" inputProps={{ name: 'endDate' }}
                        className={this.hasErrorFor('endDate') ? 'is-invalid' : ''}
                        onChange={this.handleEndDate.bind(this)}/>
                    {this.renderErrorFor('endDate')}
                </FormGroup>

                <FormGroup>
                    <Label>{translations.event_type}</Label>
                    <EventTypeDropdown
                        errors={this.state.errors}
                        event_type={this.state.event_type}
                        handleInputChanges={this.handleInput}
                    />
                </FormGroup>

                <FormGroup>
                    <Label>{translations.customer}</Label>
                    <CustomerDropdown
                        name="customer_id"
                        errors={this.state.errors}
                        handleInputChanges={this.handleInput}
                        customer={this.state.customer_id}
                    />
                </FormGroup>

                {userList}
                {customForm}
            </Form>
        )
    }

    render () {
        const form = this.buildForm()
        const saveButton = <Button color="primary" onClick={this.handleClick.bind(this)}>{translations.save}</Button>

        if (this.props.modal) {
            const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

            return (
                <React.Fragment>
                    <Button color="success" onClick={this.toggle}>Add Event</Button>

                    <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                        <DefaultModalHeader toggle={this.toggle} title={translations.create_event}/>

                        <ModalBody className={theme}>
                            {form}
                        </ModalBody>

                        <ModalFooter>
                            {saveButton}
                            <Button color="secondary" onClick={this.toggle}>{translations.close}</Button>
                        </ModalFooter>
                    </Modal>
                </React.Fragment>
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

export default CreateEvent
