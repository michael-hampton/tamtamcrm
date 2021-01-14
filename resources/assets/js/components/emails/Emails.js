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

export default class Emails extends Component {
    constructor (props) {
        super(props)

        this.state = {
            settings: [],
            id: localStorage.getItem('account_id'),
            loaded: false,
            active_email_tab: '1',
            preview: null,
            subject: '',
            body: '',
            showSpinner: true,
            showPreview: false,
            template_type: this.props.template,
            template_name: 'Invoice'
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.toggleEmailTab = this.toggleEmailTab.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.getPreview = this.getPreview.bind(this)
        this.buildPreviewData = this.buildPreviewData.bind(this)
    }

    componentDidMount () {
        this.getAccount()
    }

    async getAccount () {
        return axios.get(`api/accounts/${this.state.id}`)
            .then((r) => {
                this.setState({
                    loaded: true,
                    settings: r.data.settings
                }, () => this.getPreview())
            })
            .catch((e) => {
                alert('There was an issue updating the settings')
            })
    }

    handleChange (event) {
        const name = event.target.name
        const template_name = name === 'template_type' ? event.target[event.target.selectedIndex].getAttribute('data-name') : null
        this.setState({ [name]: event.target.value }, () => {
            if (name === 'template_type') {
                if (template_name !== null) {
                    this.setState({ template_name: name })
                }

                this.getPreview()
            }
        })
    }

    handleSettingsChange (name, value) {
        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }), () => {
            const { subject, body } = this.buildPreviewData()
            this.setState({
                subject: subject,
                body: body
            })
        })
    }

    buildPreviewData () {
        const subjectKey = this.state.template_type.replace('template', 'subject')
        const bodyKey = this.state.template_type

        const subject = !this.state.settings[subjectKey] ? '' : this.state.settings[subjectKey]
        const body = !this.state.settings[bodyKey] ? '' : this.state.settings[bodyKey]

        return {
            subject: subject,
            body: body,
            bodyKey: bodyKey
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
        const fields = this.state.settings[this.state.template_type] && this.state.settings[this.state.template_type].length
            ? <EmailFields custom_only={true} return_form={false} settings={this.state.settings}
                template_type={this.props.template}
                handleSettingsChange={this.handleSettingsChange}
                handleChange={this.handleChange}/> : null
        const preview = this.state.showPreview && this.state.preview && Object.keys(this.state.preview).length && this.state.settings[this.state.template_type] && this.state.settings[this.state.template_type].length
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

        return (
            <React.Fragment>
                <ToastContainer/>

                <Nav tabs>
                    <NavItem>
                        <NavLink
                            className={this.state.active_email_tab === '1' ? 'active' : ''}
                            onClick={() => {
                                this.toggleEmailTab('1')
                            }}>
                            {translations.preview}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.active_email_tab === '2' ? 'active' : ''}
                            onClick={() => {
                                this.toggleEmailTab('2')
                            }}>
                            {translations.customise}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.active_email_tab === '3' ? 'active' : ''}
                            onClick={() => {
                                this.toggleEmailTab('3')
                            }}>
                            {translations.history}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.active_email_tab === '4' ? 'active' : ''}
                            onClick={() => {
                                this.toggleEmailTab('4')
                            }}>
                            {translations.pdf}
                        </NavLink>
                    </NavItem>
                </Nav>

                <TabContent activeTab={this.state.active_email_tab} className="bg-transparent">

                    <TabPane tabId="1">
                        <Card>
                            <CardHeader>{translations.preview}</CardHeader>
                            <CardBody>
                                {fields}
                                {spinner}
                                {preview}
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="2">
                        <Card>
                            <CardHeader>{translations.customise}</CardHeader>
                            <CardBody>
                                {editor}
                            </CardBody>
                        </Card>
                    </TabPane>

                    <TabPane tabId="3">
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

                    <TabPane tabId="4">
                        <Card>
                            <CardHeader>{translations.pdf}</CardHeader>
                            <CardBody>
                                <ViewPdf width={this.props.width} model={this.props.model}/>
                            </CardBody>
                        </Card>
                    </TabPane>
                </TabContent>
            </React.Fragment>
        )
    }
}
