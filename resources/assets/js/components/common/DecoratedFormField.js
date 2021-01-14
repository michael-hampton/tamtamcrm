import React, { Component } from 'react'
import { Input, InputGroup, InputGroupAddon, InputGroupText } from 'reactstrap'

export default class DecoratedFormField extends Component {
    render () {
        return (
            <InputGroup className="mb-2">
                <InputGroupAddon addonType="prepend">
                    <InputGroupText><i className={`fa ${this.props.icon}`}/></InputGroupText>
                </InputGroupAddon>
                <Input className={this.props.hasErrorFor(this.props.name) ? 'is-invalid' : ''} name={this.props.name}
                    onChange={this.props.handleChange} value={this.props.value}/>
                {this.props.renderErrorFor(this.props.name)}
            </InputGroup>
        )
    }
}
