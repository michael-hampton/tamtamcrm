import React, { Component } from 'react'
import { Input, InputGroup, InputGroupAddon, Label, UncontrolledTooltip } from 'reactstrap'
import { translations } from '../utils/_translations'
import { icons } from '../utils/_icons'

export default class PasswordField extends Component {
    constructor (props) {
        super(props)
        this.state = {
            password_obscured: true,
            check: false,
            error: '',
            showSuccessMessage: false,
            showErrorMessage: false,
            message: '',
            account_id: Object.prototype.hasOwnProperty.call(localStorage, 'account_id') ? localStorage.getItem('account_id') : ''
        }

        this.toggle = this.toggle.bind(this)
    }

    toggle () {
        this.setState({
            password_obscured: !this.state.password_obscured,
            errors: []
        })
    }

    render () {
        const { password_obscured } = this.state
        return (
            <React.Fragment>
                <UncontrolledTooltip placement="top" target="UncontrolledTooltipExample">
                    {password_obscured ? translations.show_password : translations.hide_password}
                </UncontrolledTooltip>
                <Label>{translations.password}</Label>
                <InputGroup>
                    <Input className={this.props.hasErrorFor('password') ? 'is-invalid' : ''}
                        onChange={this.props.handleChange} type={password_obscured ? 'password' : 'text'}
                        name="password" value={this.props.password}
                        placeholder="password"/>
                    <InputGroupAddon addonType="append">
                        <span className="input-group-text" id="UncontrolledTooltipExample" onClick={this.toggle}><i
                            className={password_obscured ? `fa ${icons.visibility}` : `fa ${icons.visibility_off}`}/></span>
                    </InputGroupAddon>
                    {this.props.renderErrorFor('password')}
                </InputGroup>
            </React.Fragment>
        )
    }
}
