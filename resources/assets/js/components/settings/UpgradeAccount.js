import React, { Component } from 'react'
import { Row, FormGroup, Input, Label, Modal, ModalBody } from 'reactstrap'
import { translations } from '../utils/_translations'
import { icons } from '../utils/_icons'
import BlockButton from '../common/BlockButton'
import FormatMoney from '../common/FormatMoney'
import { consts } from '../utils/_consts'
import DefaultModalHeader from '../common/ModalHeader'
import DefaultModalFooter from '../common/ModalFooter'
import axios from 'axios'

export default class UpgradeAccount extends Component {
    constructor (props) {
        super(props)
        this.state = {
            id: localStorage.getItem('account_id'),
            number_of_licences: 1,
            package: '',
            period: '',
            confirm_text: '',
            errors: [],
            message: '',
            modal: false
        }

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleInput = this.handleInput.bind(this)
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value
        })
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

    handleClick () {
        if (!this.state.package.length || !this.state.period.length) {
            alert('You must enter a select and period')
            return false
        }

        axios.post(`/api/account/upgrade/${this.state.id}`, { package: this.state.package, period: this.state.period })
            .then((response) => {
                this.setState({
                    success: true
                }, () => this.props.callback(this.state))
            })
            .catch((error) => {
                console.error(error)
                this.setState({ error: true })
            })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    render () {
        const { message } = this.state
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <BlockButton className="mr-3" icon={icons.cloud_download} button_text={translations.upgrade_account}
                    onClick={this.toggle} />

                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.upgrade_account}/>

                    <ModalBody className={theme}>

                        {message && <div className="alert alert-danger" role="alert">
                            {message}
                        </div>}

                        <div className="col-12 p-3">
                            <Row>
                                <FormGroup check inline>
                                    <Input name="period" checked={this.state.period === 'monthly'} onChange={this.handleInput} value="monthly" id="InlineCheckboxes-checkbox-1" type="checkbox" />
                                    <Label for="InlineCheckboxes-checkbox-1" check>
                                        {translations.frequency_monthly} <FormatMoney amount={this.state.package === 'standard' ? consts.standard_monthly_account_price : consts.advanced_monthly_account_price} /> ({translations.per_licence})
                                    </Label>
                                </FormGroup>
                                <FormGroup check inline>
                                    <Input name="period" checked={this.state.period === 'yearly'} onChange={this.handleInput} value="yearly" id="InlineCheckboxes-checkbox-2" type="checkbox" />
                                    <Label for="InlineCheckboxes-checkbox-2" check>
                                        {translations.frequency_annually} <FormatMoney amount={this.state.package === 'standard' ? consts.standard_yearly_account_price : consts.advanced_yearly_account_price} /> ({translations.per_licence})
                                    </Label>
                                </FormGroup>
                            </Row>

                            <Row className="mt-2">
                                <FormGroup check inline>
                                    <Input name="package" checked={this.state.package === 'standard'} onChange={this.handleInput} value="standard" id="InlineCheckboxes-checkbox-1" type="checkbox" />
                                    <Label for="InlineCheckboxes-checkbox-1" check>
                                        {translations.standard}
                                    </Label>
                                </FormGroup>
                                <FormGroup check inline>
                                    <Input name="package" checked={this.state.package === 'advanced'} onChange={this.handleInput} value="advanced" id="InlineCheckboxes-checkbox-2" type="checkbox" />
                                    <Label for="InlineCheckboxes-checkbox-2" check>
                                        {translations.advanced}
                                    </Label>
                                </FormGroup>
                            </Row>

                            <Row className="mt-2">
                                <FormGroup>
                                    <Label for="exampleEmail">{translations.number_of_licences}</Label>
                                    <Input value={this.state.number_of_licences} onChange={this.handleInput} type="number" name="number_of_licences" id="number_of_licences" placeholder={translations.number_of_licences} />
                                </FormGroup>
                            </Row>
                        </div>
                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        )
    }
}
