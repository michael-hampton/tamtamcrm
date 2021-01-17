import React, { Component } from 'react'
import { InputGroup, InputGroupAddon, InputGroupText, Label } from 'reactstrap'

export default class AmountField extends Component {
    render () {
        return (
            <InputGroup className="mb-2">
                <Label>{this.props.label}</Label>
                {this.props.icon &&
                <InputGroupAddon addonType="prepend">
                    <InputGroupText><i className={`fa ${this.props.icon}`}/></InputGroupText>
                </InputGroupAddon>
                }

                <input className={`form-control w-100 ${this.props.hasErrorFor(this.props.name) ? 'is-invalid' : ''}`}
                    name={this.props.name}
                    type="number" min="0.01" step="0.01" pattern="^\d*(\.\d{0,2})?$" onChange={this.props.onChange}
                    value={this.props.value}/>
                {this.props.renderErrorFor(this.props.name)}
            </InputGroup>
        )
    }
}
