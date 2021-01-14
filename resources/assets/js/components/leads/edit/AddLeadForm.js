import React from 'react'
import { Modal, ModalBody, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import axios from 'axios'
import AddButtons from '../../common/AddButtons'
import LeadModel from '../../models/LeadModel'
import Contact from './Contact'
import Address from './Address'
import Details from './Details'
import Notes from '../../common/Notes'
import { translations } from '../../utils/_translations'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import CustomFieldsForm from '../../common/CustomFieldsForm'

class AddLeadForm extends React.Component {
    constructor (props) {
        super(props)

        this.leadModel = new LeadModel(null)
        this.initialState = this.leadModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.toggleTab = this.toggleTab.bind(this)
        this.handleInputChanges = this.handleInputChanges.bind(this)
        this.handleClick = this.handleClick.bind(this)
    }

    componentDidMount () {
        this.getSourceTypes()
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    handleInputChanges (e) {
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    handleClick (event) {
        this.setState({ loading: true })
        const data = {
            public_notes: this.state.public_notes,
            private_notes: this.state.private_notes,
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            website: this.state.website,
            industry_id: this.state.industry_id,
            first_name: this.state.first_name,
            last_name: this.state.last_name,
            email: this.state.email,
            phone: this.state.phone,
            address_1: this.state.address_1,
            address_2: this.state.address_2,
            zip: this.state.zip,
            city: this.state.city,
            job_title: this.state.job_title,
            company_name: this.state.company_name,
            description: this.state.description,
            name: this.state.name,
            valued_at: this.state.valued_at,
            assigned_to: this.state.assigned_to,
            source_type: this.state.source_type,
            task_type: this.props.task_type,
            task_status_id: this.state.task_status_id,
            project_id: this.state.project_id,
            column_color: this.state.column_color
        }

        this.leadModel.save(data).then(response => {
            if (!response) {
                this.setState({
                    loading: false,
                    errors: this.leadModel.errors,
                    message: this.leadModel.error_message
                })
                return
            }
            this.props.leads.push(response)
            this.props.action(this.props.leads)
            this.setState(this.initialState)
            localStorage.removeItem('leadForm')
        })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState(this.initialState, () => localStorage.removeItem('leadForm'))
            }
        })
    }

    getSourceTypes () {
        axios.get('/api/tasks/source-types')
            .then((r) => {
                this.setState({
                    sourceTypes: r.data,
                    err: ''
                })
            })
            .then((r) => {
                console.warn(this.state.users)
            })
            .catch((e) => {
                console.error(e)
                this.setState({
                    err: e
                })
            })
    }

    render () {
        const { loading } = this.state
        const contact = <Contact handleInputChanges={this.handleInputChanges} errors={this.state.errors}
            lead={this.state}/>
        const address = <Address handleInputChanges={this.handleInputChanges} errors={this.state.errors}
            lead={this.state}/>
        const details = <Details users={this.props.users} sourceTypes={this.state.sourceTypes}
            handleInputChanges={this.handleInputChanges} errors={this.state.errors}
            lead={this.state}/>
        const custom_fields = <CustomFieldsForm handleInput={this.handleInput} custom_value1={this.state.custom_value1}
            custom_value2={this.state.custom_value2}
            custom_value3={this.state.custom_value3}
            custom_value4={this.state.custom_value4}
            custom_fields={this.props.custom_fields}/>

        const notes = <Notes handleInput={this.handleInputChanges} private_notes={this.state.private_notes}
            public_notes={this.state.public_notes}/>
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <AddButtons toggle={this.toggle}/>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_lead}/>

                    <ModalBody className={theme}>

                        <React.Fragment>
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
                                        {translations.contact}
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
                                        {translations.notes}
                                    </NavLink>
                                </NavItem>
                            </Nav>

                            <TabContent activeTab={this.state.activeTab}>
                                <TabPane tabId="1">
                                    {details}
                                    {custom_fields}
                                </TabPane>

                                <TabPane tabId="2">
                                    {contact}
                                </TabPane>

                                <TabPane tabId="3">
                                    {address}
                                </TabPane>

                                <TabPane tabId="4">
                                    {notes}
                                </TabPane>
                            </TabContent>
                        </React.Fragment>
                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={loading}/>
                </Modal>
            </React.Fragment>
        )
    }
}

export default AddLeadForm
