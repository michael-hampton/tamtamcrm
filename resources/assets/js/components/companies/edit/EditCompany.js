import React from 'react'
import {
    Card,
    CardBody,
    CardHeader,
    DropdownItem,
    Modal,
    ModalBody,
    Nav,
    NavItem,
    NavLink,
    TabContent,
    TabPane
} from 'reactstrap'
import Contact from '../../common/Contact'
import SuccessMessage from '../../common/SucessMessage'
import ErrorMessage from '../../common/ErrorMessage'
import AddressForm from './AddressForm'
import SettingsForm from './SettingsForm'
import DetailsForm from './DetailsForm'
import CompanyDropdown from '../CompanyDropdown'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import Notes from '../../common/Notes'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import CompanyModel from '../../models/CompanyModel'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import FileUploads from '../../documents/FileUploads'

class EditCompany extends React.Component {
    constructor (props) {
        super(props)
        this.companyModel = new CompanyModel(this.props.brand)
        this.initialState = this.companyModel.fields
        this.state = this.initialState

        this.updateContacts = this.updateContacts.bind(this)
        this.toggle = this.toggle.bind(this)
        this.handleMultiSelect = this.handleMultiSelect.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.handleFileChange = this.handleFileChange.bind(this)
        this.removeLogo = this.removeLogo.bind(this)
    }

    handleInput (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            [e.target.name]: value,
            changesMade: true
        })
    }

    handleFileChange (e) {
        this.setState({
            [e.target.name]: e.target.files[0]
        })
    }

    removeLogo () {
        this.setState({ logo: '' })
    }

    updateContacts (contacts) {
        this.setState({ contacts: contacts })
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    handleMultiSelect (e) {
        this.setState({ selectedUsers: Array.from(e.target.selectedOptions, (item) => item.value) })
    }

    getFormData () {
        const formData = new FormData()
        formData.append('company_logo', this.state.company_logo)
        formData.append('logo', this.state.logo)
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
        formData.append('currency_id', this.state.currency_id)
        formData.append('industry_id', this.state.industry_id)
        formData.append('assigned_to', this.state.assigned_to)
        formData.append('custom_value1', this.state.custom_value1)
        formData.append('custom_value2', this.state.custom_value2)
        formData.append('custom_value3', this.state.custom_value3)
        formData.append('custom_value4', this.state.custom_value4)
        formData.append('private_notes', this.state.private_notes)
        formData.append('public_notes', this.state.public_notes)
        formData.append('contacts', JSON.stringify(this.state.contacts))
        formData.append('_method', 'PUT')

        return formData
    }

    handleClick () {
        const formData = this.getFormData()

        this.companyModel.save(formData).then(response => {
            if (!response) {
                this.setState({ errors: this.companyModel.errors, message: this.companyModel.error_message })
                return
            }

            const index = this.props.brands.findIndex(company => company.id === this.props.brand.id)
            this.props.brands[index] = response
            this.props.action(this.props.brands)
            this.setState({
                editMode: false,
                changesMade: false
            })
            this.toggle()
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
        const successMessage = this.state.showSuccessMessage === true
            ? <SuccessMessage message="Invoice was updated successfully"/> : null
        const errorMessage = this.state.showErrorMessage === true
            ? <ErrorMessage message="Something went wrong"/> : null
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>Edit</DropdownItem>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_company}/>

                    <ModalBody className={theme}>

                        <CompanyDropdown formData={this.getFormData()} id={this.state.id}/>
                        {successMessage}
                        {errorMessage}

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

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '5' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('5')
                                    }}>
                                    {translations.documents}
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
                                        {translations.contacts}
                                    </CardHeader>

                                    <CardBody>
                                        <Contact contacts={this.props.brand.contacts} onChange={this.updateContacts}/>
                                    </CardBody>
                                </Card>
                            </TabPane>

                            <TabPane tabId="3">
                                <AddressForm errors={this.state.errors}
                                    handleInput={this.handleInput}
                                    company={this.state}/>
                            </TabPane>

                            <TabPane tabId="4">
                                <SettingsForm errors={this.state.errors} company={this.state}
                                    handleInput={this.handleInput}/>

                                <Notes handleInput={this.handleInput} public_notes={this.state.public_notes}
                                    errors={this.state.errors}
                                    private_notes={this.state.private_notes}/>
                            </TabPane>

                            <TabPane tabId="5">
                                <Card>
                                    <CardHeader>{translations.documents}</CardHeader>
                                    <CardBody>
                                        <FileUploads entity_type="Company" entity={this.state}
                                            user_id={this.state.user_id}/>
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

export default EditCompany
