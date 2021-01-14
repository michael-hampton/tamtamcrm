import React, { Component } from 'react'
import { Card, CardBody, Col, Form, Nav, NavItem, NavLink, Row, Spinner, TabContent, TabPane } from 'reactstrap'
import axios from 'axios'
import EmailFields from './EmailFields'
import EmailPreview from './EmailPreview'
import { translations } from '../utils/_translations'
import Variables from './Variables'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from './Header'
import AccountRepository from '../repositories/AccountRepository'

class TemplateSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            success: false,
            error: false,
            showSpinner: false,
            showPreview: false,
            id: localStorage.getItem('account_id'),
            loaded: false,
            preview: null,
            activeTab: '1',
            template_type: 'email_template_invoice',
            template_name: 'Invoice',
            company_logo: null,
            settings: {
                email_template_payment: '',
                email_subject_payment: '',
                email_template_quote: '',
                email_subject_quote: '',
                email_template_credit: '',
                email_subject_credit: '',
                email_template_reminder1: '',
                email_subject_reminder1: '',
                email_template_reminder2: '',
                email_subject_reminder2: '',
                email_template_reminder3: '',
                email_subject_reminder3: '',
                email_subject_invoice: '',
                email_template_invoice: '',
                email_subject_lead: '',
                email_template_lead: '',
                email_subject_order_received: '',
                email_subject_order_sent: '',
                email_template_order_received: '',
                email_template_order_sent: ''
            }
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.toggle = this.toggle.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.getPreview = this.getPreview.bind(this)
    }

    componentDidMount () {
        this.getAccount()
    }

    getAccount () {
        const accountRepository = new AccountRepository()
        accountRepository.getById(this.state.id).then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({
                loaded: true,
                settings: response.settings
            }, () => {
                console.log(response)
            })
        })
    }

    handleChange (event) {
        this.setState({ [event.target.name]: event.target.value })

        if (event.target.name === 'template_type') {
            const name = event.target[event.target.selectedIndex].getAttribute('data-name')
            this.setState({ template_name: name })
        }
    }

    handleSettingsChange (event) {
        const name = event.target.name
        const value = event.target.type === 'checkbox' ? event.target.checked : event.target.value

        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }

    handleFileChange (e) {
        this.setState({
            [e.target.name]: e.target.files[0]
        })
    }

    toggle (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab }, () => {
                if (tab === '2') {
                    this.getPreview()
                }
            })
        }
    }

    getPreview () {
        this.setState({ showSpinner: true, showPreview: false })
        const subjectKey = this.state.template_type.replace('template', 'subject')
        const bodyKey = this.state.template_type

        const subject = !this.state.settings[subjectKey] ? '' : this.state.settings[subjectKey]
        const body = !this.state.settings[bodyKey] ? '' : this.state.settings[bodyKey]

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
                    showPreview: true
                })
            })
            .catch((e) => {
                this.setState({ error: true })
            })
    }

    handleSubmit (e) {
        const formData = new FormData()
        formData.append('settings', JSON.stringify(this.state.settings))
        formData.append('company_logo', this.state.company_logo)
        formData.append('_method', 'PUT')

        axios.post('/api/accounts/1', formData, {
            headers: {
                'content-type': 'multipart/form-data'
            }
        })
            .then((response) => {
                this.setState({ success: true })
            })
            .catch((error) => {
                console.error(error)
                // this.setState({
                //     errors: error.response.data.errors
                // })
            })
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        const fields = <EmailFields return_form={true} settings={this.state.settings}
            template_type={this.state.template_type}
            handleSettingsChange={this.handleSettingsChange}
            handleChange={this.handleChange}/>

        const preview = this.state.showPreview && this.state.preview && Object.keys(this.state.preview).length && this.state.settings[this.state.template_type] && this.state.settings[this.state.template_type].length
            ? <EmailPreview preview={this.state.preview} entity={this.props.entity} entity_id={this.props.entity_id}
                template_type={this.state.template_type}/> : null
        const spinner = this.state.showSpinner === true ? <Spinner style={{ width: '3rem', height: '3rem' }}/> : null

        const tabs = <Nav tabs className="nav-justified setting-tabs disable-scrollbars">
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '1' ? 'active' : ''}
                    onClick={() => {
                        this.toggle('1')
                    }}>
                    {translations.edit}
                </NavLink>
            </NavItem>
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '2' ? 'active' : ''}
                    onClick={() => {
                        this.toggle('2')
                    }}>
                    {translations.preview}
                </NavLink>
            </NavItem>
        </Nav>

        return (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={translations.settings_not_saved}/>

                <Header title={translations.template_settings} handleSubmit={this.handleSubmit}
                    tabs={tabs}/>

                <div className="settings-container settings-container-narrow fixed-margin-mobile">
                    <TabContent activeTab={this.state.activeTab}>
                        <TabPane tabId="1">
                            <Card>
                                <CardBody>
                                    <Row>
                                        <Col md={8}>
                                            <Form>
                                                {fields}
                                            </Form>
                                        </Col>

                                        <Col md={4}>
                                            <Variables class="fixed-margin-mobile"/>
                                        </Col>
                                    </Row>

                                </CardBody>
                            </Card>
                        </TabPane>

                        <TabPane tabId="2">
                            <Card>
                                <CardBody>
                                    {spinner}
                                    {preview}
                                </CardBody>
                            </Card>
                        </TabPane>
                    </TabContent>
                </div>
            </React.Fragment>
        )
    }
}

export default TemplateSettings
