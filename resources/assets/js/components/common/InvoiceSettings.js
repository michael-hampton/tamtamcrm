import React from 'react'
import { Card, CardBody, CardHeader, Col, FormGroup, Input, Label, Row } from 'reactstrap'
import TaxRateDropdown from './dropdowns/TaxRateDropdown'
import DesignDropdown from './dropdowns/DesignDropdown'
import { translations } from '../utils/_translations'

export default function InvoiceSettings (props) {
    const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
    const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
    const account_settings = user_account[0].account.settings

    return (
        <Card>
            <CardHeader>{translations.fees}</CardHeader>
            <CardBody>
                <Row form>
                    {account_settings.show_transaction_fee &&
                    <Col md={6}>
                        <FormGroup>
                            <Label for="exampleEmail">{translations.transaction_fee}</Label>
                            <Input onChange={props.handleSurcharge} type="text" name="transaction_fee"
                                id="transaction_fee" value={props.settings.transaction_fee}/>
                        </FormGroup>
                    </Col>
                    }

                    {account_settings.show_shipping_cost &&
                    <Col md={6}>
                        <FormGroup>
                            <Label for="exampleEmail">{translations.shipping_cost}</Label>
                            <Input onChange={props.handleSurcharge} type="text" name="shipping_cost" id="shipping_cost"
                                value={props.settings.shipping_cost}/>
                        </FormGroup>
                        {/* <FormGroup check> */}
                        {/*    <Label check for="examplePassword"> */}
                        {/*        <Input onChange={props.handleSurcharge} type="checkbox" name="transaction_fee_tax" id="examplePassword" checked={props.settings.transaction_fee_tax} /> */}
                        {/*        {translations.transaction_tax} */}
                        {/*    </Label> */}
                        {/* </FormGroup> */}
                    </Col>
                    }

                </Row>

                <Row form>
                    {account_settings.show_gateway_fee &&
                    <Col md={6}>
                        <FormGroup>
                            <Label for="exampleEmail">{translations.gateway_fee}</Label>
                            <Input onChange={props.handleSurcharge} type="text" name="gateway_fee" id="gateway_fee"
                                value={props.settings.gateway_fee}/>

                            <Label check for="examplePassword" className="pl-4">
                                <Input onChange={props.handleSurcharge} type="checkbox" name="gateway_percentage"
                                    id="examplePassword" checked={props.settings.gateway_percentage}/>
                                {translations.is_percentage}
                            </Label>
                        </FormGroup>
                    </Col>
                    }

                    {account_settings.show_tax_rate1 &&
                    <Col md={6}>
                        <FormGroup>
                            <Label>{translations.tax}</Label>
                            <TaxRateDropdown
                                name="tax"
                                handleInputChanges={props.handleInput}
                                errors={props.errors}
                            />
                        </FormGroup>
                    </Col>
                    }

                    {account_settings.show_tax_rate2 &&
                    <Col md={6}>
                        <FormGroup>
                            <Label>{translations.tax}</Label>
                            <TaxRateDropdown
                                name="tax_2"
                                handleInputChanges={props.handleInput}
                                errors={props.errors}
                            />
                        </FormGroup>
                    </Col>
                    }

                    {account_settings.show_tax_rate3 &&
                    <Col md={6}>
                        <FormGroup>
                            <Label>{translations.tax}</Label>
                            <TaxRateDropdown
                                name="tax_3"
                                handleInputChanges={props.handleInput}
                                errors={props.errors}
                            />
                        </FormGroup>
                    </Col>
                    }

                </Row>

                {/* <Row form> */}
                {/*    <Col md={6} /> */}
                {/*    <Col md={6}> */}
                {/*        <FormGroup check> */}
                {/*            <Label check for="examplePassword"> */}
                {/*                <Input onChange={props.handleSurcharge} type="checkbox" name="shipping_cost_tax" id="shipping_cost_tax" checked={props.settings.shipping_cost_tax} /> */}
                {/*                {translations.shipping_tax} */}
                {/*            </Label> */}
                {/*        </FormGroup> */}
                {/*    </Col> */}
                {/* </Row> */}

                <Row form>
                    <Col md={6}>
                        <FormGroup>
                            <Label>{translations.discount}</Label>
                            <Input
                                value={props.discount}
                                type='text'
                                name='discount'
                                id='discount'
                                onChange={props.handleInput}
                            />
                        </FormGroup>
                    </Col>

                    <Col md={6}>
                        <FormGroup>
                            <Label>{translations.discount_type}</Label>
                            <Input
                                value={props.is_amount_discount}
                                type='select'
                                name='is_amount_discount'
                                id='is_amount_discount'
                                onChange={props.handleInput}
                            >
                                <option value="false">Percent</option>
                                <option value="true">Amount</option>
                            </Input>
                        </FormGroup>
                    </Col>
                </Row>

                {props.is_mobile &&
                <FormGroup>
                    <Label>{translations.design}</Label>
                    <DesignDropdown name="design_id" design={props.design_id} handleChange={props.handleInput}/>
                </FormGroup>
                }
            </CardBody>
        </Card>

    )
}
