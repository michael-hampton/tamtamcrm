import React, { Component } from 'react'
import { FormGroup, Input, Label } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import DecoratedFormField from '../../common/DecoratedFormField'

export default class Details extends Component {
    render () {
        return (
            <React.Fragment>
                <FormGroup className="mb-3">
                    <Label>{translations.name}</Label>
                    <Input className={this.props.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="name"
                        value={this.props.tax_rate.name} onChange={this.props.handleInput}/>
                    {this.props.renderErrorFor('name')}
                </FormGroup>

                <Label>{translations.amount}</Label>
                <DecoratedFormField hasErrorFor={this.props.hasErrorFor}
                    renderErrorFor={this.props.renderErrorFor} name="rate"
                    handleChange={this.props.handleInput}
                    value={this.props.tax_rate.rate} icon={icons.percent}/>
            </React.Fragment>
        )
    }
}
