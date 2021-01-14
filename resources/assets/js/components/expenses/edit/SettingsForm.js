import React from 'react'
import { Card, CardBody, CardHeader, Col, Collapse, FormGroup, Input, Label, Row } from 'reactstrap'
import CurrencyDropdown from '../../common/dropdowns/CurrencyDropdown'
import PaymentTypeDropdown from '../../common/dropdowns/PaymentTypeDropdown'
import Datepicker from '../../common/Datepicker'
import { translations } from '../../utils/_translations'
import { icons } from '../../utils/_icons'
import SwitchWithIcon from '../../common/SwitchWithIcon'

export default class SettingsForm extends React.Component {
    constructor (props) {
        super(props)

        this.state = {
            currencyOpen: this.props.expense.currencyOpen === true ? true : !!(this.props.expense.currency_id && this.props.expense.expense_currency_id > 0),
            paymentOpen: this.props.expense.paymentOpen === true ? true : !!(this.props.expense.payment_date && this.props.expense.payment_date.length > 1)
        }

        this.toggleCurrency = this.toggleCurrency.bind(this)
        this.togglePayment = this.togglePayment.bind(this)
        this.handleCheckboxChange = this.handleCheckboxChange.bind(this)

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.account_settings = user_account[0].account.settings
    }

    hasErrorFor (field) {
        return !!this.props.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    toggleCurrency (e) {
        this.setState({ currencyOpen: e.target.checked })
    }

    togglePayment (e) {
        this.setState({ paymentOpen: e.target.checked })
    }

    handleCheckboxChange (buttonName, event) {
        const value = event.target.checked
        const name = event.target.name
        const e = {}
        e.target = {
            name: name,
            value: value,
            type: 'checkbox'
        }

        this.setState({ [name]: value }, () => this.props.handleInput(e))
    }

    render () {
        return (<Card>
            <CardHeader>
                {translations.settings}
            </CardHeader>

            <CardBody>
                <SwitchWithIcon
                    icon={icons.customer}
                    label={translations.create_expense_invoice}
                    checked={this.props.expense.create_invoice}
                    name="create_invoice"
                    handleInput={this.props.handleInput}
                    help_text={translations.create_expense_invoice_help}/>

                <SwitchWithIcon
                    icon={icons.customer}
                    label={translations.create_expense_payment}
                    checked={this.state.paymentOpen}
                    name="paymentOpen"
                    handleInput={(e) => this.togglePayment(e)}
                    help_text={translations.create_expense_payment_help}/>

                <Collapse style={{ borderBottom: '2px dotted #CCC' }} isOpen={this.state.paymentOpen}>
                    <FormGroup>
                        <Label for="exampleEmail">{translations.payment_type}</Label>
                        <PaymentTypeDropdown payment_type={this.props.expense.payment_type_id}
                            handleInputChanges={this.props.handleInput}
                            name="payment_type_id"/>
                    </FormGroup>
                    <FormGroup>
                        <Label for="examplePassword">{translations.date}</Label>
                        <Datepicker className="form-control" name="payment_date"
                            date={this.props.expense.payment_date}
                            handleInput={this.props.handleInput}/>
                    </FormGroup>
                    <FormGroup>
                        <Label for="examplePassword">{translations.reference_number}</Label>
                        <Input value={this.props.expense.reference_number} type="text"
                            name="reference_number"
                            onChange={this.props.handleInput} id="reference_number"
                            placeholder={translations.reference_number}/>
                    </FormGroup>
                </Collapse>

                <SwitchWithIcon
                    icon={icons.customer}
                    label={translations.convert_expense_currency}
                    checked={this.state.currencyOpen}
                    name="currencyOpen"
                    handleInput={(e) => this.toggleCurrency(e)}
                    help_text={translations.convert_expense_currency_help}/>

                <Collapse style={{ borderBottom: '2px dotted #CCC' }} isOpen={this.state.currencyOpen}>
                    <Row form>
                        <Col md={6}>
                            <FormGroup>
                                <Label for="exampleEmail">{translations.currency}</Label>
                                <CurrencyDropdown currency_id={this.props.expense.invoice_currency_id}
                                    handleInputChanges={this.props.handleInput}
                                    name="invoice_currency_id"/>
                            </FormGroup>
                        </Col>
                        <Col md={6}>
                            <FormGroup>
                                <Label for="examplePassword">{translations.exchange_rate}</Label>
                                <Input type="text" name="exchange_rate" id="exchange_rate"
                                    onChange={this.props.handleInput}
                                    value={this.props.expense.exchange_rate}
                                    placeholder={translations.exchange_rate}/>
                            </FormGroup>
                        </Col>
                    </Row>
                </Collapse>

                <SwitchWithIcon
                    icon={icons.customer}
                    label={translations.include_expense_documents}
                    checked={this.props.expense.include_documents}
                    name="include_documents"
                    handleInput={this.props.handleInput}
                    help_text={translations.include_expense_documents_help}
                />
            </CardBody>
        </Card>
        )
    }
}
