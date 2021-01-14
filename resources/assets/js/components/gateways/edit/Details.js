import React from 'react'
import { Card, CardBody, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'
import Config from './Config'
import { consts } from '../../utils/_consts'

export default class Details extends React.Component {
    constructor (props) {
        super(props)

        this.providers = [
            {
                key: consts.stripe_gateway,
                name: translations.stripe
            },
            {
                key: consts.authorize_gateway,
                name: translations.authorize
            },
            {
                key: consts.paypal_gateway,
                name: translations.paypal
            },
            {
                key: consts.custom_gateway,
                name: translations.custom
            }
        ]
    }

    render () {
        const options = Object.keys(this.providers).map((index) => {
            return <option key={index} value={this.providers[index].key}>{this.providers[index].name}</option>
        })
        return (
            <Card>
                <CardBody>
                    {!this.props.is_edit &&
                    <FormGroup>
                        <Label for="name">{translations.provider} <span className="text-danger">*</span></Label>
                        <Input value={this.props.gateway.gateway_key} onChange={this.props.handleInput} type="select"
                            name="gateway_key" id="gateway_key">
                            <option value="">{translations.select_option}</option>
                            {options}
                        </Input>
                        {this.props.renderErrorFor('name')}
                    </FormGroup>
                    }

                    <FormGroup>
                        <Label for="name">{translations.name} <span className="text-danger">*</span></Label>
                        <Input value={this.props.gateway.name} onChange={this.props.handleInput} type="text"
                            name="name" id="name"/>
                        {this.props.renderErrorFor('name')}
                    </FormGroup>

                    <FormGroup>
                        <Label for="name">{translations.description} <span className="text-danger">*</span></Label>
                        <Input value={this.props.gateway.description} onChange={this.props.handleInput} type="textarea"
                            name="description" id="description"/>
                        {this.props.renderErrorFor('description')}
                    </FormGroup>

                    <Config gateway={this.props.gateway} handleConfig={this.props.handleConfig}/>
                </CardBody>
            </Card>
        )
    }
}
