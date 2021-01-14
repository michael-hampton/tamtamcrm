import React from 'react'
import { Card, CardBody, CardHeader, Modal, ModalBody, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import Contact from '../../common/Contact'
import AddButtons from '../../common/AddButtons'
import SettingsForm from './SettingsForm'
import AddressForm from './AddressForm'
import DetailsForm from './DetailsForm'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import Notes from '../../common/Notes'
import { translations } from '../../utils/_translations'
import CompanyModel from '../../models/CompanyModel'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'

class AddCompany extends React.Component {
    constructor (props) {
        super(props)

        this.companyModel = new CompanyModel(null)
        this.initialState = this.companyModel.fields
        this.state = this.initialState
        this.toggle = this.toggle.bind(this)
        this.updateContacts = this.updateContacts.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleMultiSelect = this.handleMultiSelect.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.handleFileChange = this.handleFileChange.bind(this)
        this.removeLogo = this.removeLogo.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'companyForm')) {
            const storedValues = JSON.parse(localStorage.getItem('companyForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
    }

    handleInput (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            [e.target.name]: value
        }, () => localStorage.setItem('companyForm', JSON.stringify(this.state)))
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    handleFileChange (e) {
        this.setState({
            [e.target.name]: e.target.files[0]
        })
    }

    handleMultiSelect (e) {
        this.setState({ selectedUsers: Array.from(e.target.selectedOptions, (item) => item.value) })
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

    removeLogo () {
        this.setState({ logo: '' })
    }

    updateContacts (contacts) {
        this.setState({ contacts: contacts })
    }

    handleClick () {
        const formData = new FormData()
        formData.append('logo', this.state.logo)
        formData.append('company_logo', this.state.company_logo)
        formData.append('name', this.state.name)
        formData.append('website', this.state.website)
        formData.append('phone_number', this.state.phone_number)
        formData.append('email', this.state.email)
        formData.append('address_1', this.state.address_1)
        formData.append('address_2', this.state.address_2)
        formData.append('town', this.state.town)
        formData.append('city', this.state.city)
        formData.append('postcode', this.state.postcode)
        formData.append('country_id', this.state.country_id)
        formData.append('contacts', JSON.stringify(this.state.contacts))
        formData.append('currency_id', this.state.currency_id)
        formData.append('industry_id', this.state.industry_id)
        formData.append('assigned_to', this.state.assigned_to)
        formData.append('private_notes', this.state.private_notes)
        formData.append('public_notes', this.state.public_notes)
        formData.append('custom_value1', this.state.custom_value1)
        formData.append('custom_value2', this.state.custom_value2)
        formData.append('custom_value3', this.state.custom_value3)
        formData.append('custom_value4', this.state.custom_value4)

        this.companyModel.save(formData).then(response => {
            if (!response) {
                this.setState({ errors: this.companyModel.errors, message: this.companyModel.error_message })
                return
            }
            this.props.brands.push(response)
            this.props.action(this.props.brands)
            localStorage.removeItem('companyForm')
            this.setState(this.initialState)
        })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState(this.initialState, () => localStorage.removeItem('companyForm'))
            }
        })
    }

    render () {
        const { message } = this.state
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <AddButtons toggle={this.toggle}/>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_company}/>

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
                                    {translations.company}
                                </NavLink>
                            </NavItem>
                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '2' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('2')
                                    }}>
                                    {translations.contacts}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '3' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('3')
                                    }}>
                                    {translations.address}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '4' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('4')
                                    }}>
                                    {translations.settings}
                                </NavLink>
                            </NavItem>
                        </Nav>
                        <TabContent activeTab={this.state.activeTab} className="bg-transparent">
                            <TabPane tabId="1">
                                <DetailsForm removeLogo={this.removeLogo} errors={this.state.errors}
                                    handleInput={this.handleInput}
                                    company={this.state}
                                    handleFileChange={this.handleFileChange}/>

                                <CustomFieldsForm handleInput={this.handleInput}
                                    custom_value1={this.state.custom_value1}
                                    custom_value2={this.state.custom_value2}
                                    custom_value3={this.state.custom_value3}
                                    custom_value4={this.state.custom_value4}
                                    custom_fields={this.props.custom_fields}/>
                            </TabPane>

                            <TabPane tabId="2">

                                <Card>
                                    <CardHeader>
                                        <Contact onChange={this.updateContacts}/>
                                    </CardHeader>

                                    <CardBody/>
                                </Card>
                            </TabPane>

                            <TabPane tabId="3">
                                <AddressForm errors={this.state.errors}
                                    company={this.state} handleInput={this.handleInput}/>
                            </TabPane>

                            <TabPane tabId="4">
                                <SettingsForm errors={this.state.errors} company={this.state}
                                    handleInput={this.handleInput}/>

                                <Notes handleInput={this.handleInput} public_notes={this.state.public_notes}
                                    errors={this.state.errors}
                                    private_notes={this.state.private_notes}/>
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

export default AddCompany
