import React, { Component } from 'react'
import { Col, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../utils/_translations'

export default class TaxRateField extends Component {
    render () {
        return (
            <div>
                <FormGroup row>
                    <Col md={6}>
                        <Label for="exampleEmail">{translations.tax_name}</Label>
                        <Input type="email" name="tax_name" id="tax_name" value={this.props.name}
                            onChange={(event) => {
                                const e = {}

                                e.target = {
                                    name: this.props.tax_rate_name_field,
                                    value: event.target.value
                                }
                                this.props.onNameChanged(e)
                            }}/>
                    </Col>
                    <Col sm={6}>
                        <Label for="exampleEmail2">{translations.tax_amount}</Label>
                        <Input type="email" name="tax_amount" id="tax_amount" value={this.props.value}
                            onChange={(event) => {
                                const e = {}

                                e.target = {
                                    name: this.props.tax_rate_field,
                                    value: event.target.value
                                }
                                this.props.onAmountChanged(e)
                            }}/>
                    </Col>
                </FormGroup>
            </div>
        )
    }
}
