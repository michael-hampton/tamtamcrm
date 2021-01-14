import React, { useState } from 'react'
import { Button, Card, CardBody, CardHeader, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import AddressForm from './AddressForm'
import { toast, ToastContainer } from 'react-toastify'
import CustomerForm from './CustomerForm'
import SettingsForm from './SettingsForm'
import Contact from '../../common/Contact'
import NotesForm from './NotesForm'
import Notes from '../../common/Notes'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import { translations } from '../../utils/_translations'
import FileUploads from '../../documents/FileUploads'
import CustomerModel from '../../models/CustomerModel'

export default function CustomerTabs (props) {
    const setBilling = e => {
        setBillingValues({
            ...billing,
            [e.target.name]: e.target.value
        })
    }

    const setShipping = e => {
        setShippingValues({
            ...shipping,
            [e.target.name]: e.target.value
        })
    }

    const copyBilling = e => {
        setShippingValues({
            address_1: billing.address_1 ? billing.address_1 : '',
            address_2: billing.address_2 ? billing.address_2 : '',
            zip: billing.zip ? billing.zip : '',
            city: billing.city ? billing.city : '',
            country_id: billing.country_id ? billing.country_id : 225
        })
    }

    const copyShipping = e => {
        setBillingValues({
            address_1: shipping.address_1 ? shipping.address_1 : '',
            address_2: shipping.address_2 ? shipping.address_2 : '',
            zip: shipping.zip ? shipping.zip : '',
            city: shipping.city ? shipping.city : '',
            country_id: shipping.country_id ? shipping.country_id : 225
        })
    }

    const setSettings = e => {
        if (e.target.dataset && e.target.dataset.namespace === 'settings') {
            setSettingValues({
                ...settings,
                [e.target.name]: e.target.value
            })

            return
        }

        setCustomer(e)
    }

    const setCustomer = e => {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        setCustomerValues({
            ...customer,
            [e.target.name]: value
        })
    }

    const setContacts = contacts => {
        setContactValues({
            contacts: contacts
        })
    }

    const [errors, setErrors] = useState({})

    const [activeTab, setActiveTab] = useState('1')

    const [contacts, setContactValues] = useState({
        contacts: props.customer && props.customer.contacts ? props.customer.contacts : []
    })

    const [customer, setCustomerValues] = useState({
        id: props.customer ? props.customer.id : null,
        name: props.customer ? props.customer.name : '',
        company_id: props.customer ? props.customer.company_id : '',
        phone: props.customer ? props.customer.phone : '',
        group_id: props.customer ? props.customer.group_id : null,
        currency_id: props.customer ? props.customer.currency_id : '',
        default_payment_method: props.customer ? props.customer.default_payment_method : '',
        assigned_user: props.customer ? props.customer.assigned_user : '',
        custom_value1: props.customer ? props.customer.custom_value1 : '',
        custom_value2: props.customer ? props.customer.custom_value2 : '',
        custom_value3: props.customer ? props.customer.custom_value3 : '',
        custom_value4: props.customer ? props.customer.custom_value4 : '',
        public_notes: props.customer ? props.customer.public_notes : '',
        private_notes: props.customer ? props.customer.private_notes : '',
        website: props.customer ? props.customer.website : '',
        vat_number: props.customer ? props.customer.vat_number : '',
        size_id: props.customer ? props.customer.size_id : null,
        industry_id: props.customer ? props.customer.industry_id : null
    })

    const [settings, setSettingValues] = useState({
        payment_terms: props.customer ? props.customer.settings.payment_terms : ''
    })

    const [billing, setBillingValues] = useState({
        address_1: props.customer && props.customer.billing ? props.customer.billing.address_1 : '',
        address_2: props.customer && props.customer.billing ? props.customer.billing.address_2 : '',
        zip: props.customer && props.customer.billing ? props.customer.billing.zip : '',
        city: props.customer && props.customer.billing ? props.customer.billing.city : '',
        country_id: props.customer && props.customer.billing ? props.customer.billing.country_id : 225
    })

    const [shipping, setShippingValues] = useState({
        address_1: props.customer && props.customer.shipping ? props.customer.shipping.address_1 : '',
        address_2: props.customer && props.customer.shipping ? props.customer.shipping.address_2 : '',
        zip: props.customer && props.customer.shipping ? props.customer.shipping.zip : '',
        city: props.customer && props.customer.shipping ? props.customer.shipping.city : '',
        country_id: props.customer && props.customer.shipping ? props.customer.shipping.country_id : 225
    })

    const cleanContacts = contacts => {
        const removeEmpty = (obj) => {
            Object.keys(obj).forEach(k =>
                ((obj[k] && typeof obj[k] === 'object') && removeEmpty(obj[k])) ||
                ((!obj[k] && obj[k] !== undefined) && delete obj[k])
            )
            return obj
        }

        return removeEmpty(contacts).filter(value => Object.keys(value).length >= 4)
    }

    const updateForm = () => {
        const addresses = []
        const innerObj = {}
        innerObj.billing = billing
        innerObj.shipping = shipping
        addresses.push(innerObj)

        const cleanedContacts = contacts.contacts && contacts.contacts.length ? cleanContacts(contacts.contacts) : []

        if (cleanedContacts.length !== contacts.contacts.length) {
            toast.error(translations.invalid_contacts_error, {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })

            return false
        }

        const formdata = {
            name: customer.name,
            phone: customer.phone,
            company_id: customer.company_id,
            description: customer.description,
            public_notes: customer.public_notes,
            private_notes: customer.private_notes,
            website: customer.website,
            vat_number: customer.vat_number,
            currency_id: customer.currency_id,
            size_id: customer.size_id,
            industry_id: customer.industry_id,
            assigned_user: customer.assigned_user,
            default_payment_method: customer.default_payment_method,
            group_id: customer.group_id,
            custom_value1: customer.custom_value1,
            custom_value2: customer.custom_value2,
            custom_value3: customer.custom_value3,
            custom_value4: customer.custom_value4,
            addresses: addresses,
            contacts: contacts.contacts,
            settings: settings
        }

        if (contacts.contacts.length === 0) {
            toast.error(translations.empty_contacts_error, {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })

            return false
        }

        const customerModel = new CustomerModel(customer)
        customerModel.save(formdata).then(response => {
            if (!response) {
                this.setState({ errors: customerModel.errors, message: customerModel.error_message })
                return
            }

            const index = props.customers.findIndex(customer => parseInt(customer.id) === props.customer.id)
            props.customers[index] = response
            props.action(props.customers)
            this.setState({
                editMode: false,
                changesMade: false
            })
            props.toggle()
        })
    }

    const submitForm = () => {
        const addresses = []
        const innerObj = {}
        innerObj.billing = billing
        innerObj.shipping = shipping
        addresses.push(innerObj)

        const cleanedContacts = contacts.contacts && contacts.contacts.length ? cleanContacts(contacts.contacts) : []
        console.log('contacts 2', cleanedContacts)

        const formdata = {
            name: customer.name,
            phone: customer.phone,
            company_id: customer.company_id,
            description: customer.description,
            public_notes: customer.public_notes,
            private_notes: customer.private_notes,
            website: customer.website,
            vat_number: customer.vat_number,
            currency_id: customer.currency_id,
            size_id: customer.size_id,
            industry_id: customer.industry_id,
            assigned_user: customer.assigned_user,
            default_payment_method: customer.default_payment_method,
            group_id: customer.group_id,
            custom_value1: customer.custom_value1,
            custom_value2: customer.custom_value2,
            custom_value3: customer.custom_value3,
            custom_value4: customer.custom_value4,
            addresses: addresses,
            contacts: contacts.contacts,
            settings: settings
        }

        if (contacts.contacts.length === 0) {
            toast.error(translations.empty_contacts_error, {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })

            return false
        }

        const customerModel = new CustomerModel(null)

        customerModel.save(formdata).then(response => {
            if (!response) {
                this.setState({ errors: customerModel.errors, message: customerModel.error_message })
                return
            }

            props.customers.push(response)
            props.action(props.customers)
            toast.success('user mappings updated successfully')
            props.toggle()
        })
    }

    const method = props.type === 'add' ? submitForm : updateForm
    const button = <Button color="primary" onClick={method}>Send </Button>

    return (
        <React.Fragment>
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

            <Nav tabs>
                <NavItem>
                    <NavLink className={activeTab === '1' ? 'active' : ''} onClick={() => setActiveTab('1')}>
                        {translations.details}
                    </NavLink>
                </NavItem>

                <NavItem>
                    <NavLink className={activeTab === '2' ? 'active' : ''} onClick={() => setActiveTab('2')}>
                        {translations.contacts}
                    </NavLink>
                </NavItem>

                <NavItem>
                    <NavLink className={activeTab === '3' ? 'active' : ''} onClick={() => setActiveTab('3')}>
                        {translations.notes}
                    </NavLink>
                </NavItem>

                <NavItem>
                    <NavLink className={activeTab === '4' ? 'active' : ''} onClick={() => setActiveTab('4')}>
                        {translations.settings}
                    </NavLink>
                </NavItem>

                <NavItem>
                    <NavLink className={activeTab === '5' ? 'active' : ''} onClick={() => setActiveTab('5')}>
                        {translations.billing_address}
                    </NavLink>
                </NavItem>

                <NavItem>
                    <NavLink className={activeTab == '6' ? 'active' : ''} onClick={() => setActiveTab('6')}>
                        {translations.shipping_address}
                    </NavLink>
                </NavItem>

                <NavItem>
                    <NavLink className={activeTab == '7' ? 'active' : ''} onClick={() => setActiveTab('7')}>
                        {translations.documents}
                    </NavLink>
                </NavItem>
            </Nav>

            <TabContent activeTab={activeTab} className="bg-transparent">
                <TabPane tabId="1">
                    <CustomerForm errors={errors} onChange={setCustomer}
                        customer={customer}/>
                    <CustomFieldsForm custom_value1={customer.custom_value1}
                        custom_value2={customer.custom_value2}
                        custom_value3={customer.custom_value3}
                        custom_value4={customer.custom_value4} handleInput={setCustomer}
                        custom_fields={props.custom_fields}/>
                </TabPane>

                <TabPane tabId="2">
                    <Card>
                        <CardHeader>{translations.contacts}</CardHeader>
                        <CardBody>
                            <Contact errors={errors} onChange={setContacts} contacts={contacts.contacts}/>
                        </CardBody>
                    </Card>
                </TabPane>

                <TabPane tabId="3">
                    <Notes handleInput={setCustomer} custom_fields={props.custom_fields}
                        public_notes={customer.public_notes}
                        private_notes={customer.private_notes}/>

                    <Card>
                        <CardHeader>{translations.notes}</CardHeader>
                        <CardBody>
                            <NotesForm errors={errors} onChange={setCustomer} customer={customer}/>
                        </CardBody>
                    </Card>
                </TabPane>

                <TabPane tabId="4">
                    <SettingsForm onChange={setSettings} customer={customer} settings={settings}/>
                </TabPane>

                <TabPane tabId="5">
                    <Card>
                        <CardHeader>{translations.billing_address}</CardHeader>
                        <CardBody>
                            <AddressForm errors={errors} onChange={setBilling} customer={billing}/>
                            <Button className="mt-2" onClick={copyShipping} color="primary" size="lg"
                                block>{translations.copy_shipping}</Button>
                        </CardBody>
                    </Card>
                </TabPane>
                <TabPane tabId="6">
                    <Card>
                        <CardHeader>{translations.shipping_address}</CardHeader>
                        <CardBody>
                            <AddressForm onChange={setShipping} customer={shipping}/>
                            <Button className="mt-2" onClick={copyBilling} color="primary" size="lg"
                                block>{translations.copy_billing}</Button>
                        </CardBody>
                    </Card>
                </TabPane>
                <TabPane tabId="7">
                    <Card>
                        <CardHeader>{translations.documents}</CardHeader>
                        <CardBody>
                            {props.customer && props.customer.user_id &&
                            <FileUploads entity_type="Customer" entity={props.customer}
                                user_id={props.customer.user_id}/>}
                        </CardBody>
                    </Card>
                </TabPane>
            </TabContent>

            {button}
        </React.Fragment>
    )
}
