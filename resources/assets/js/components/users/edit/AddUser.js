import React from 'react'
import {
    Card,
    CardBody,
    CardHeader,
    DropdownItem,
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
import { toast, ToastContainer } from 'react-toastify'
import { icons } from '../../utils/_icons'
import UserModel from '../../models/UserModel'

class AddUser extends React.Component {
    constructor (props) {
        super(props)

        this.initialState = {
            modal: false,
            customize: false,
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
            customized_permissions: [],
            message: '',
            custom_value1: '',
            custom_value2: '',
            custom_value3: '',
            custom_value4: '',
            is_admin: false,
            activeTab: '1',
            can_save: true,
            password_changed: false
        }

        this.state = this.initialState
        this.account_id = JSON.parse(localStorage.getItem('appState')).user.account_id

        this.userModel = new UserModel()

        this.toggle = this.toggle.bind(this)
        this.toggleTab = this.toggleTab.bind(this)
        this.handleMultiSelect = this.handleMultiSelect.bind(this)
        this.setDate = this.setDate.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.setNotifications = this.setNotifications.bind(this)
        this.setSelectedAccounts = this.setSelectedAccounts.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.getUser = this.getUser.bind(this)
        this.searchUsers = this.searchUsers.bind(this)
        this._validate = this._validate.bind(this)
    }

    static getDerivedStateFromProps (props, state) {
        if (props.user_id && props.user_id !== state.id) {
            return { id: props.user_id }
        }

        return null
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'userForm')) {
            const storedValues = JSON.parse(localStorage.getItem('userForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }

        if (this.props.user_id) {
            this.getUser(this.props.user_id)
        }
    }

    componentDidUpdate (prevProps, prevState) {
        if (this.props.user_id && this.props.user_id !== prevProps.user_id) {
            this.getUser(this.props.user_id)
        }
    }

    setSelectedAccounts (selectedAccounts) {
        this.setState({ selectedAccounts: selectedAccounts })
    }

    getUser (id) {
        axios.get(`/api/users/edit/${id}`, { headers: { Authorization: `Bearer ${localStorage.getItem('access_token')}` } })
            .then((r) => {
                const data = {
                    id: id,
                    can_save: true,
                    roles: r.data.roles,
                    user: r.data.user,
                    gender: r.data.user.gender,
                    dob: r.data.user.dob,
                    username: r.data.user.username,
                    email: r.data.user.email,
                    first_name: r.data.user.first_name,
                    last_name: r.data.user.last_name,
                    phone_number: r.data.user.phone_number,
                    job_description: r.data.user.job_description,
                    has_custom_permissions: r.data.user.has_custom_permissions,
                    custom_value1: r.data.user.custom_value1,
                    custom_value2: r.data.user.custom_value2,
                    custom_value3: r.data.user.custom_value3,
                    custom_value4: r.data.user.custom_value4,
                    password: r.data.user.password,
                    selectedRoles: r.data.selectedIds,
                    selectedAccounts: r.data.user.account_users[0]
                }

                this.userModel = new UserModel(data)

                this.setState(this.userModel.fields)
            })
            .catch((e) => {
                console.error(e)
            })
    }

    searchUsers () {
        const users = JSON.parse(localStorage.getItem('users'))
        const user = users.filter(user => user.email === this.state.email)

        if (user.length) {
            if (confirm('The user already exists. Do you want to add them to this account?')) {
                this.getUser(user[0].id)
            }
        } else {
            this.setState({ can_save: false })
        }
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

        if (!password || !password.length) {
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

    setNotifications (notifications) {
        this.setState(prevState => ({
            selectedAccounts: {
                ...prevState.selectedAccounts,
                notifications: { email: notifications },
                account_id: this.account_id
            }
        }))
    }

    setPermissions (permissions, customize) {
        this.setState({ customized_permissions: permissions, customize: customize })
    }

    handleClick () {
        if (!this.state.can_save) {
            this.searchUsers()
            return false
        }

        const is_valid = this._validate()
        if (is_valid !== true && is_valid.length) {
            this.setState({ password_error: is_valid })
            return false
        } else {
            this.setState({ password_error: '' })
        }

        this.setState({ loading: true })
        this.userModel.save({
            account_id: localStorage.getItem('account_id'),
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
            password: this.state.password_changed ? this.state.password : '',
            role: this.state.selectedRoles,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            customized_permissions: this.state.customize === true ? this.state.customized_permissions : {}
        }).then(response => {
            if (!response) {
                this.setState({
                    showErrorMessage: true,
                    loading: false,
                    errors: this.userModel.errors,
                    message: this.userModel.error_message
                })

                toast.error(translations.updated_unsuccessfully.replace('{entity}', translations.user), {
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

            toast.success(translations.updated_successfully.replace('{entity}', translations.user), {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })

            if (!this.state.id) {
                this.props.users.unshift(response)
                this.props.action(this.props.users, true)
                localStorage.removeItem('userForm')
                this.setState(this.initialState)
                this.toggle()
                return
            }

            const index = this.props.users.findIndex(user => user.id === this.state.id)
            this.props.users[index] = response
            this.props.action(this.props.users, true)
            this.setState({ loading: false, changesMade: false, modalOpen: false })
            this.toggle()
        })
    }

    handleInput (event) {
        const { name, value } = event.target
        const { password } = this.state

        this.setState({
            [name]: value
        }, () => {
            if (name === 'email' && this.props.add) {
                this.searchUsers()
            }

            if (name === 'password' && value !== password) {
                this.setState({ password_changed: true })
            }

            localStorage.setItem('userForm', JSON.stringify(this.state))
        })
    }

    toggle () {
        if (this.state.id && this.state.modal && this.state.changesMade) {
            if (window.confirm('Your changes have not been saved?')) {
                this.setState({ ...this.initialState })
            }

            return
        }

        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                localStorage.removeItem('userForm')

                if (this.props.add) {
                    this.setState(this.initialState)
                }
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
        const button = this.props.add === true ? <AddButtons toggle={this.toggle}/>
            : <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>{translations.edit_user}
            </DropdownItem>

        return (
            <React.Fragment>
                {button}
                <Modal size="lg" isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle}
                        title={this.state.id ? translations.edit_user : translations.add_user}/>

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
                                <PermissionsForm has_custom_permissions={this.state.has_custom_permissions}
                                    setPermissions={this.setPermissions.bind(this)}
                                    handleInput={this.handleInput} errors={this.state.errors}
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
