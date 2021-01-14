import React from 'react'
import { Card, CardBody, Modal, ModalBody, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import AddButtons from '../../common/AddButtons'
import { translations } from '../../utils/_translations'
import Details from './Details'
import CaseModel from '../../models/CaseModel'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import Contacts from './Contacts'
import Links from './Links'

export default class AddCase extends React.Component {
    constructor (props) {
        super(props)

        this.caseModel = new CaseModel(null, this.props.customers)
        this.initialState = this.caseModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleContactChange = this.handleContactChange.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'caseForm')) {
            const storedValues = JSON.parse(localStorage.getItem('caseForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
    }

    handleInput (e) {
        if (e.target.name === 'customer_id') {
            const customer_data = this.caseModel.customerChange(e.target.value)

            this.setState({
                customerName: customer_data.name,
                contacts: customer_data.contacts,
                address: customer_data.address
            }, () => localStorage.setItem('caseForm', JSON.stringify(this.state)))

            // if (this.settings.convert_product_currency === true) {
            //     const customer = new CustomerModel(customer_data.customer)
            //     const currency_id = customer.currencyId
            //     const currency = JSON.parse(localStorage.getItem('currencies')).filter(currency => currency.id === currency_id)
            //     const exchange_rate = currency[0].exchange_rate
            //     this.setState({ exchange_rate: exchange_rate, currency_id: currency_id })
            // }
        }

        this.setState({
            [e.target.name]: e.target.value
        }, () => localStorage.setItem('caseForm', JSON.stringify(this.state)))
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

    handleContactChange (e) {
        const invitations = this.caseModel.buildInvitations(e.target.value, e.target.checked)
        // update the state with the new array of options
        this.setState({ invitations: invitations }, () => console.log('invitations', invitations))
    }

    handleClick () {
        const data = {
            invitations: this.state.invitations,
            subject: this.state.subject,
            message: this.state.message,
            customer_id: this.state.customer_id,
            due_date: this.state.due_date,
            priority_id: this.state.priority_id,
            private_notes: this.state.private_notes,
            category_id: this.state.category_id,
            assigned_to: this.state.assigned_to,
            link_type: this.state.link_type,
            link_project_value: this.state.link_project_value,
            link_product_value: this.state.link_product_value
        }

        this.caseModel.save(data).then(response => {
            if (!response) {
                this.setState({ errors: this.caseModel.errors, message: this.caseModel.error_message })
                return
            }
            this.props.cases.push(response)
            this.props.action(this.props.cases)
            this.setState(this.initialState)
            localStorage.removeItem('caseForm')
        })
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState({
                    subject: '',
                    message: '',
                    customer_id: '',
                    due_date: '',
                    private_notes: '',
                    priority_id: '',
                    category_id: ''
                }, () => localStorage.removeItem('caseForm'))
            }
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <AddButtons toggle={this.toggle}/>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_case}/>

                    <ModalBody className={theme}>
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
                                    {translations.comments}
                                </NavLink>
                            </NavItem>
                        </Nav>

                        <TabContent activeTab={this.state.activeTab}>
                            <TabPane tabId="1">
                                <Card>
                                    <CardBody>
                                        <Details cases={this.props.cases} customers={this.props.customers}
                                            errors={this.state.errors}
                                            hasErrorFor={this.hasErrorFor} case={this.state}
                                            handleInput={this.handleInput} renderErrorFor={this.renderErrorFor}/>
                                    </CardBody>
                                </Card>

                                <Contacts handleInput={this.handleInput} case={this.state}
                                    errors={this.state.errors}
                                    contacts={this.state.contacts}
                                    invitations={this.state.invitations}
                                    handleContactChange={this.handleContactChange}/>

                                <Card>
                                    <CardBody>
                                        <Links cases={this.props.cases} customers={this.props.customers}
                                            errors={this.state.errors}
                                            hasErrorFor={this.hasErrorFor} case={this.state}
                                            handleInput={this.handleInput} renderErrorFor={this.renderErrorFor}/>
                                    </CardBody>
                                </Card>

                            </TabPane>

                            <TabPane tabId="2"/>
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
