import React from 'react'
import {
    Card,
    CardBody,
    CardHeader,
    FormGroup,
    Modal,
    ModalBody,
    Nav,
    NavItem,
    NavLink,
    TabContent,
    TabPane
} from 'reactstrap'
import axios from 'axios'
import AddButtons from '../../common/AddButtons'
import Notifications from '../../common/Notifications'
import DetailsForm from './DetailsForm'
import PermissionsForm from './PermissionsForm'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import { translations } from '../../utils/_translations'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'

class AddUser extends React.Component {
    constructor (props) {
        super(props)

        this.initialState = {
            modal: false,
            username: '',
            email: '',
            first_name: '',
            last_name: '',
            dob: '',
            job_description: '',
            phone_number: '',
            gender: '',
            department: 0,
            role_id: 0,
            password: '',
            loading: false,
            errors: [],
            password_error: '',
            roles: [],
            selectedAccounts: [],
            selectedRoles: [],
            notifications: [],
            message: '',
            custom_value1: '',
            custom_value2: '',
            custom_value3: '',
            custom_value4: '',
            is_admin: false,
            activeTab: '1'
        }

        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.toggleTab = this.toggleTab.bind(this)
        this.handleMultiSelect = this.handleMultiSelect.bind(this)
        this.setDate = this.setDate.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.setNotifications = this.setNotifications.bind(this)
        this.setSelectedAccounts = this.setSelectedAccounts.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'userForm')) {
            const storedValues = JSON.parse(localStorage.getItem('userForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
    }

    setNotifications (notifications) {
        this.setState(prevState => ({
            selectedAccounts: {
                ...prevState.selectedAccounts,
                notifications: { email: notifications },
                account_id: this.account_id,
                permissions: ''
            }
        }))
    }

    setSelectedAccounts (selectedAccounts) {
        this.setState({ selectedAccounts: selectedAccounts })
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    hasErrorFor (field) {
        return field === 'password' ? this.state.password_error.length : !!this.state.errors[field]
    }

    renderErrorFor (field) {
        if (field === 'password') {
            return this.state.password_error.length
                ? <span className='invalid-feedback'>
                    <strong>{this.state.password_error}</strong>
                </span> : null
        }

        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.state.errors[field][0]}</strong>
                </span>
            )
        }
    }

    _validatePassword (value) {
        const pattern = '^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{8,}$'
        const regExp = new RegExp(pattern)

        return regExp.test(value)
    }

    _validate () {
        const { password } = this.state

        if (!password.length || !password.trim().length) {
            return translations.please_enter_your_password
        }

        if (password.length < 8) {
            return translations.password_is_too_short
        }

        if (!this._validatePassword(password)) {
            return translations.password_is_too_easy
        }

        return true
    }

    handleClick () {
        const is_valid = this._validate()
        if (is_valid !== true && is_valid.length) {
            this.setState({ password_error: is_valid })
            return false
        } else {
            this.setState({ password_error: '' })
        }

        axios.post('/api/users', {
            username: this.state.username,
            company_user: this.state.selectedAccounts,
            department: this.state.department,
            email: this.state.email,
            first_name: this.state.first_name,
            last_name: this.state.last_name,
            job_description: this.state.job_description,
            phone_number: this.state.phone_number,
            dob: this.state.dob,
            gender: this.state.gender,
            password: this.state.password,
            role: this.state.selectedRoles,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4
        })
            .then((response) => {
                this.toggle()
                const newUser = response.data
                this.props.users.push(newUser)
                this.props.action(this.props.users)
                localStorage.removeItem('userForm')
                this.setState(this.initialState)
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    this.setState({
                        errors: error.response.data.errors
                    })
                } else {
                    this.setState({ message: error.response.data })
                }
            })
    }

    handleInput (event) {
        const { name, value } = event.target

        this.setState({
            [name]: value
        }, () => localStorage.setItem('userForm', JSON.stringify(this.state)))
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState(this.initialState, () => localStorage.removeItem('userForm'))
            }
        })
    }

    handleMultiSelect (e) {
        this.setState({ selectedRoles: Array.from(e.target.selectedOptions, (item) => item.value) }, () => localStorage.setItem('userForm', JSON.stringify(this.state)))
    }

    setDate (date) {
        this.setState({ dob: date }, localStorage.setItem('userForm', JSON.stringify(this.state)))
    }

    render () {
        const { message } = this.state
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <AddButtons toggle={this.toggle}/>
                <Modal size="lg" isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_user}/>

                    <ModalBody className={theme}>

                        {message && <div className="alert alert-danger" role="alert">
                            {message}
                        </div>}

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
                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '2' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('2')
                                    }}>
                                    {translations.permissions}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '3' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('3')
                                    }}>
                                    {translations.notifications}
                                </NavLink>
                            </NavItem>
                        </Nav>

                        <TabContent activeTab={this.state.activeTab} className="bg-transparent">
                            <TabPane tabId="1">
                                <DetailsForm user={this.state} setDate={this.setDate} errors={this.state.errors}
                                    hasErrorFor={this.hasErrorFor} renderErrorFor={this.renderErrorFor}
                                    handleInput={this.handleInput}/>

                                <CustomFieldsForm handleInput={this.handleInput}
                                    custom_value1={this.state.custom_value1}
                                    custom_value2={this.state.custom_value2}
                                    custom_value3={this.state.custom_value3}
                                    custom_value4={this.state.custom_value4}
                                    custom_fields={this.props.custom_fields}/>

                            </TabPane>

                            <TabPane tabId="2">
                                <PermissionsForm handleInput={this.handleInput} errors={this.state.errors}
                                    setAccounts={this.setSelectedAccounts}
                                    departments={this.props.departments} accounts={this.props.accounts}
                                    selectedAccounts={this.state.selectedAccounts}
                                    handleMultiSelect={this.handleMultiSelect}
                                    selectedRoles={this.state.selectedRoles}/>
                            </TabPane>

                            <TabPane tabId="3">
                                <Card>
                                    <CardHeader>Notifications</CardHeader>
                                    <CardBody>
                                        <FormGroup>
                                            <Notifications onChange={this.setNotifications}/>
                                        </FormGroup>
                                    </CardBody>
                                </Card>
                            </TabPane>
                        </TabContent>
                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        )
    }
}

export default AddUser
