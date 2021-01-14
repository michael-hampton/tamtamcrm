import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import { Card, CardBody, FormGroup, Label } from 'reactstrap'
import axios from 'axios'
import SignatureCanvas from 'react-signature-canvas'
import { translations } from '../utils/_translations'
import { consts } from '../utils/_consts'
import { icons } from '../utils/_icons'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from './Header'
import AccountRepository from '../repositories/AccountRepository'

class EmailSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            success_message: translations.settings_saved,
            id: localStorage.getItem('account_id'),
            sigPad: {},
            settings: {},
            success: false,
            error: false
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.getAccount = this.getAccount.bind(this)
        this.trim = this.trim.bind(this)
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

    trim () {
        const value = this.state.sigPad.getTrimmedCanvas()
            .toDataURL('image/png')

        return new Promise((resolve, reject) => {
            this.setState(prevState => ({
                settings: {
                    ...prevState.settings,
                    email_signature: value
                }
            }), () => resolve(true))
        })
    }

    handleSubmit (e) {
        this.trim().then(result => {
            axios.put(`/api/accounts/${this.state.id}`, { settings: JSON.stringify(this.state.settings) }, {}).then((response) => {
                this.setState({ success: true })
            }).catch((error) => {
                this.setState({ error: true })
            })
        })
    }

    getFormFields () {
        const settings = this.state.settings

        const formFields = [
            [
                {
                    name: 'email_style',
                    label: translations.email_style,
                    type: 'select',
                    placeholder: translations.email_style,
                    value: settings.email_style,
                    options: [
                        {
                            value: consts.email_design_plain,
                            text: translations.plain
                        },
                        {
                            value: consts.email_design_light,
                            text: translations.light
                        },
                        {
                            value: consts.email_design_dark,
                            text: translations.dark
                        },
                        {
                            value: consts.email_design_custom,
                            text: translations.custom
                        }
                    ]
                },
                {
                    name: 'reply_to_email',
                    label: translations.reply_to_email,
                    type: 'text',
                    placeholder: translations.reply_to_email,
                    value: settings.reply_to_email
                },
                {
                    name: 'bcc_email',
                    label: translations.bcc_email,
                    type: 'text',
                    placeholder: translations.bcc_email,
                    value: settings.bcc_email
                }
                /* {
                    name: 'enable_email_markup',
                    label: translations.enable_email_markup,
                    type: 'switch',
                    placeholder: translations.enable_email_markup,
                    value: settings.enable_email_markup
                }, */
            ]
        ]

        return formFields
    }

    getForwardingFormFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'lead_forwarding_enabled',
                    label: translations.lead_forwarding_enabled,
                    icon: `fa ${icons.pdf}`,
                    type: 'switch',
                    placeholder: translations.lead_forwarding_enabled,
                    value: settings.lead_forwarding_enabled
                },
                {
                    name: 'case_forwarding_enabled',
                    label: translations.case_forwarding_enabled,
                    icon: `fa ${icons.image_file}`,
                    type: 'switch',
                    placeholder: translations.case_forwarding_enabled,
                    value: settings.case_forwarding_enabled
                },
                {
                    name: 'case_forwarding_address',
                    label: translations.case_forwarding_address,
                    type: 'text',
                    placeholder: translations.case_forwarding_address,
                    value: settings.case_forwarding_address
                },
                {
                    name: 'lead_forwarding_address',
                    label: translations.lead_forwarding_address,
                    type: 'text',
                    placeholder: translations.lead_forwarding_address,
                    value: settings.lead_forwarding_address
                }
            ]
        ]
    }

    getAttachmentFormFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'pdf_email_attachment',
                    label: translations.pdf_email_attachment,
                    icon: `fa ${icons.pdf}`,
                    type: 'switch',
                    placeholder: translations.pdf_email_attachment,
                    value: settings.pdf_email_attachment
                },
                {
                    name: 'document_email_attachment',
                    label: translations.document_email_attachment,
                    icon: `fa ${icons.image_file}`,
                    type: 'switch',
                    placeholder: translations.document_email_attachment,
                    value: settings.document_email_attachment
                },
                {
                    name: 'ubl_email_attachment',
                    label: translations.ubl_email_attachment,
                    icon: `fa ${icons.archive_file}`,
                    type: 'switch',
                    placeholder: translations.ubl_email_attachment,
                    value: settings.ubl_email_attachment
                }
            ]
        ]
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        return this.state.loaded === true ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={this.state.success_message}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={this.state.settings_not_saved}/>

                <Header title={translations.email_settings} handleSubmit={this.handleSubmit}/>

                <div className="settings-container fixed-margin-extra">
                    <Card>
                        <CardBody>
                            <FormBuilder
                                handleChange={this.handleSettingsChange}
                                formFieldsRows={this.getFormFields()}
                            />
                        </CardBody>
                    </Card>

                    <Card>
                        <CardBody>
                            <FormBuilder
                                handleChange={this.handleSettingsChange}
                                formFieldsRows={this.getAttachmentFormFields()}
                            />
                        </CardBody>
                    </Card>

                    <Card>
                        <CardBody>
                            <FormBuilder
                                handleChange={this.handleSettingsChange}
                                formFieldsRows={this.getForwardingFormFields()}
                            />
                        </CardBody>
                    </Card>

                    <Card>
                        <CardBody>
                            <FormGroup>
                                <Label>Email Signature</Label>
                                <SignatureCanvas
                                    canvasProps={{
                                        width: 1050,
                                        height: 200,
                                        className: 'sigCanvas border border-light'
                                    }}
                                    ref={(ref) => {
                                        this.state.sigPad = ref
                                    }}/>
                            </FormGroup>
                        </CardBody>
                    </Card>
                </div>
            </React.Fragment>
        ) : null
    }
}

export default EmailSettings
