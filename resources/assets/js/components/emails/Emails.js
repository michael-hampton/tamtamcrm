import React, { Component } from 'react'
import { Card, CardBody, CardHeader, Nav, NavItem, NavLink, Spinner, TabContent, TabPane } from 'reactstrap'
import axios from 'axios'
import { toast, ToastContainer } from 'react-toastify'
import EmailEditorForm from '../emails/EmailEditorForm'
import ViewEmails from '../emails/ViewEmails'
import EmailFields from '../settings/EmailFields'
import EmailPreview from '../settings/EmailPreview'
import { translations } from '../utils/_translations'
import ViewPdf from './ViewPdf'
import AlertPopup from '../common/AlertPopup'
import AccountRepository from "../repositories/AccountRepository";
import AppSwitch from "../common/AppSwitch";

export default class Emails extends Component {
    constructor (props) {
        super(props)

        this.state = {
            show_html: false,
            templates: [],
            settings: [],
            id: localStorage.getItem('account_id'),
            loaded: false,
            active_email_tab: window.innerWidth <= 768 ? '1' : '3',
            preview: null,
            subject: '',
            body: '',
            showSpinner: true,
            showPreview: false,
            template_type: this.props.template,
            template_name: 'Invoice',
            is_mobile: window.innerWidth <= 768,
            show_alert: false
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.toggleEmailTab = this.toggleEmailTab.bind(this)
        this.getTemplates = this.getTemplates.bind(this)
        this.getPreview = this.getPreview.bind(this)
        this.buildPreviewData = this.buildPreviewData.bind(this)
        this.handleWindowSizeChange = this.handleWindowSizeChange.bind(this)
    }

    componentDidMount () {
        this.getTemplates()

        window.addEventListener('resize', this.handleWindowSizeChange)
    }

    // make sure to remove the listener
    // when the component is not mounted anymore
    componentWillUnmount () {
        window.removeEventListener('resize', this.handleWindowSizeChange)
    }

    handleWindowSizeChange () {
        this.setState({ is_mobile: window.innerWidth <= 768 })
    }

    getTemplates() {
        const accountRepository = new AccountRepository()
        accountRepository.getTemplates().then(response => {
            if (!response) {
                alert('error')
                return false
            }

            this.setState({
                loaded: true,
                templates: response,
                cached_settings: response
            }, () => {
                console.log(response)
                this.getPreview()
            })
        })
    }

    handleChange (event) {
        const name = event.target.name
        const template_name = name === 'template_type' ? event.target[event.target.selectedIndex].getAttribute('data-name') : null

        this.setState({ [name]: event.target.value }, () => {
            if (name === 'template_type') {
                if (template_name !== null) {
                    this.setState({ template_name: template_name, template_type: template_name })
                }

                this.getPreview()
            }
        })
    }

    handleSettingsChange(event) {
        const name = event.target.name
        let value = event.target.type === 'checkbox' ? event.target.checked : event.target.value
        value = (value === 'true') ? true : ((value === 'false') ? false : (value))

        const user_id = JSON.parse(localStorage.getItem('appState')).user.id

        const templates = {...this.state.templates}

        if (!templates[this.state.template_type]) {
            templates[this.state.template_type] = {
                template: this.state.template_name,
                account_id: parseInt(this.state.id),
                user_id: parseInt(user_id),
                enabled: true
            }
        }

        templates[this.state.template_type][name] = value

        this.setState({templates: templates})
    }

    buildPreviewData () {
        const template = this.state.templates[this.state.template_type]

        const subject = template.subject
        const body = template.message

        return {
            subject: subject,
            body: body,
            bodyKey: this.state.template_type
        }
    }

    getPreview () {
        this.setState({ showSpinner: true, showPreview: false })
        const { subject, body, bodyKey } = this.buildPreviewData()

        axios.post('api/template', {
            subject: subject,
            body: body,
            template: bodyKey,
            entity_id: this.props.entity_id,
            entity: this.props.entity
        })
            .then((r) => {
                this.setState({
                    preview: r.data,
                    showSpinner: false,
                    showPreview: true,
                    subject: subject,
                    body: body
                }, () => {
                    console.log('preview', r.data)
                    console.log('subject', this.state.subject)
                })
            })
            .catch((e) => {
                toast.error('There was an issue updating the settings')
            })
    }

    toggleEmailTab (tab) {
        if (this.state.active_email_tab !== tab) {
            this.setState({ active_email_tab: tab }, () => {
                if (tab === '1') {
                    this.getPreview()
                }
            })
        }
    }

    render () {
        const fields = this.state.templates[this.state.template_type] && Object.keys(this.state.templates[this.state.template_type]).length
            ? <EmailFields custom_only={true} return_form={false} templates={this.state.templates}
                template_type={this.props.template}
                           selected_template={this.state.template_type}
                handleSettingsChange={this.handleSettingsChange}
                handleChange={this.handleChange}/> : null
        const preview = this.state.showPreview && this.state.preview && Object.keys(this.state.preview).length && this.state.templates[this.state.template_type] && Object.keys(this.state.templates[this.state.template_type]).length
            ? <EmailPreview preview={this.state.preview} entity={this.props.entity} entity_id={this.props.entity_id}
                            template_type={this.state.template_type}/> : null
        const editor = this.state.subject.length && this.state.body.length
            ? <EmailEditorForm
                model={this.props.model}
                entity_object={this.props.entity_object}
                customers={this.props.customers}
                subject={this.state.subject}
                body={this.state.body}
                handleSettingsChange={this.handleSettingsChange}
                calculated_template={this.state.template_type}
                template_type={this.props.template}
                show_editor={true} entity={this.props.entity}
                entity_id={this.props.entity_id}/> : null
        const spinner = this.state.showSpinner === true ? <Spinner style={{ width: '3rem', height: '3rem' }}/> : null
        const preview_container = <Card className={this.state.is_mobile ? 'mt-3' : ''}>
            <CardHeader>{translations.preview}</CardHeader>
            <CardBody>
                {this.state.is_mobile && fields}
                {spinner}
                {preview}
            </CardBody>
        </Card>

        const customize_container = <Card>
            <CardHeader>{translations.customise}</CardHeader>
            <CardBody>
                {editor}
            </CardBody>
        </Card>

        return (
            <React.Fragment>
                <ToastContainer/>

                {!this.state.is_mobile &&
                <div className="col-md-6">
                    {fields}
                    {customize_container}
                    {preview_container}
                </div>
                }

                <Nav tabs className={this.state.is_mobile ? 'col-md-6' : ''}>
                    {this.state.is_mobile &&
                    <NavItem>
                        <NavLink
                            className={this.state.active_email_tab === '1' ? 'active' : ''}
                            onClick={() => {
                                this.toggleEmailTab('1')
                            }}>
                            {translations.preview}
                        </NavLink>
                    </NavItem>

                    }

                    {this.state.is_mobile &&
                    <NavItem>
                        <NavLink
                            className={this.state.active_email_tab === '2' ? 'active' : ''}
                            onClick={() => {
                                this.toggleEmailTab('2')
                            }}>
                            {translations.customise}
                        </NavLink>
                    </NavItem>

                    }

                    <NavItem>
                        <NavLink
                            className={this.state.active_email_tab === '3' ? 'active' : ''}
                            onClick={() => {
                                this.toggleEmailTab('3')
                            }}>
                            {translations.pdf}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.active_email_tab === '4' ? 'active' : ''}
                            onClick={() => {
                                this.toggleEmailTab('4')
                            }}>
                            {translations.history}
                        </NavLink>
                    </NavItem>
                </Nav>

                <TabContent activeTab={this.state.active_email_tab} className="bg-transparent">

                    <TabPane tabId="1">
                        {preview_container}
                    </TabPane>

                    <TabPane tabId="2">
                        {customize_container}
                    </TabPane>

                    <TabPane tabId="3">
                        <Card>
                            <CardHeader>{translations.pdf}</CardHeader>
                            <CardBody>
                                <ViewPdf show_html={this.state.show_html} width={this.props.width} model={this.props.model}/>
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="4">
                        <Card>
                            <CardHeader>{translations.history}</CardHeader>
                            <CardBody>
                                <ViewEmails template_type={this.state.template_type}
                                    handleSettingsChange={this.handleSettingsChange}
                                    active_id={this.state.active_id}
                                    emails={this.props.emails}/>
                            </CardBody>
                        </Card>
                    </TabPane>
                </TabContent>

                <AlertPopup is_open={this.state.show_alert} message={this.state.error_message} onClose={(e) => {
                    this.setState({ show_alert: false })
                }}/>
            </React.Fragment>
        )
    }
}
