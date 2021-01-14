import React, { Component } from 'react'
import 'react-dates/initialize' // necessary for latest version
import 'react-dates/lib/css/_datepicker.css'
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
import moment from 'moment'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import Notes from '../../common/Notes'
import DealModel from '../../models/DealModel'
import Details from './Details'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'
import axios from 'axios'
import FileUploads from '../../documents/FileUploads'
import Emails from '../../emails/Emails'
import Comments from '../../comments/Comments'
import DropdownMenuBuilder from '../../common/DropdownMenuBuilder'

export default class EditDeal extends Component {
    constructor (props) {
        super(props)

        this.dealModel = new DealModel(this.props.deal, this.props.customers)
        this.initialState = this.dealModel.fields

        this.state = this.initialState

        this.handleSave = this.handleSave.bind(this)
        this.handleDelete = this.handleDelete.bind(this)
        this.handleChange = this.handleChange.bind(this)

        this.toggle = this.toggle.bind(this)
        this.toggleMenu = this.toggleMenu.bind(this)
    }

    componentDidMount () {
        this.getSourceTypes()
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

    getFormData () {
        return {
            customer_id: this.state.customer_id,
            rating: this.state.rating,
            source_type: this.state.source_type,
            valued_at: this.state.valued_at,
            name: this.state.name,
            description: this.state.description,
            assigned_to: this.state.assigned_to,
            due_date: moment(this.state.due_date).format('YYYY-MM-DD'),
            custom_value1: this.state.custom_value1,
            custom_value2: this.state.custom_value2,
            custom_value3: this.state.custom_value3,
            custom_value4: this.state.custom_value4,
            public_notes: this.state.public_notes,
            private_notes: this.state.private_notes,
            task_status_id: this.state.task_status_id,
            project_id: this.state.project_id,
            column_color: this.state.column_color
        }
    }

    handleSave () {
        this.dealModel.update(this.getFormData()).then(response => {
            if (!response) {
                this.setState({ errors: this.dealModel.errors, message: this.dealModel.error_message })
                return
            }

            const index = this.props.deals.findIndex(deal => deal.id === this.props.deal.id)
            this.props.deals[index] = response
            this.props.action(this.props.deals)
            this.setState({
                editMode: false,
                changesMade: false
            })
            this.toggle()
        })
    }

    handleDelete () {
        this.setState({
            editMode: false
        })
        if (this.props.onDelete) {
            this.props.onDelete(this.props.deal)
        }
    }

    handleChange (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            [e.target.name]: value,
            changesMade: true
        })
    }

    render () {
        const email_editor = this.state.id
            ? <Emails width={400} model={this.dealModel} emails={this.state.emails} template="email_template_deal"
                show_editor={true}
                customers={this.props.customers} entity_object={this.state} entity="deal"
                entity_id={this.state.id}/> : null

        const button = this.props.listView && this.props.listView === true
            ? <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>Edit</DropdownItem>
            : null
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return <React.Fragment>
            {button}
            <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                <DefaultModalHeader toggle={this.toggle} title={translations.edit_deal}/>

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

                        <NavItem>
                            <NavLink
                                className={this.state.activeTab === '3' ? 'active' : ''}
                                onClick={() => {
                                    this.toggleTab('3')
                                }}>
                                {translations.documents}
                            </NavLink>
                        </NavItem>

                        <NavItem>
                            <NavLink
                                className={this.state.activeTab === '4' ? 'active' : ''}
                                onClick={() => {
                                    this.toggleTab('4')
                                }}>
                                {translations.email}
                            </NavLink>
                        </NavItem>
                    </Nav>

                    <TabContent activeTab={this.state.activeTab}>
                        <TabPane tabId="1">
                            <DropdownMenuBuilder invoices={this.state} formData={this.getFormData()}
                                model={this.dealModel}
                                action={this.props.action}/>

                            <Details sourceTypes={this.state.sourceTypes} deal={this.state}
                                customers={this.props.customers}
                                errors={this.state.errors}
                                users={this.props.users} handleInput={this.handleChange}/>

                            <CustomFieldsForm handleInput={this.handleChange} custom_value1={this.state.custom_value1}
                                custom_value2={this.state.custom_value2}
                                custom_value3={this.state.custom_value3}
                                custom_value4={this.state.custom_value4}
                                custom_fields={this.props.custom_fields}/>

                            <Notes private_notes={this.state.private_notes} public_notes={this.state.public_notes}
                                handleInput={this.handleChange}/>
                        </TabPane>

                        <TabPane tabId="2">
                            <Comments entity_type="Deal" entity={this.state}
                                user_id={this.state.user_id}/>
                        </TabPane>

                        <TabPane tabId="3">
                            <Card>
                                <CardHeader>{translations.documents}</CardHeader>
                                <CardBody>
                                    <FileUploads entity_type="Deal" entity={this.state}
                                        user_id={this.state.user_id}/>
                                </CardBody>
                            </Card>
                        </TabPane>

                        <TabPane tabId="4">
                            {email_editor}
                        </TabPane>
                    </TabContent>
                </ModalBody>
                <DefaultModalFooter show_success={true} toggle={this.toggle}
                    saveData={this.handleSave.bind(this)}
                    loading={false}/>
            </Modal>
        </React.Fragment>
    }
}
