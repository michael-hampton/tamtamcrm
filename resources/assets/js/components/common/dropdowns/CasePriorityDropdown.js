import React, { Component } from 'react'
import { Input } from 'reactstrap'
import { translations } from '../../utils/_translations'
import { consts } from '../../utils/_consts'

export default class CasePriorityDropdown extends Component {
    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback d-block'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    hasErrorFor (field) {
        return this.props.errors && !!this.props.errors[field]
    }

    render () {
        const name = this.props.name && this.props.name ? this.props.name : 'category'

        return (
            <React.Fragment>
                <Input value={this.props.priority} onChange={this.props.handleInputChanges} type="select"
                    name={name} id={name}>
                    <option value="">{translations.select_option}</option>
                    <option value={consts.low_priority}>{translations.low}</option>
                    <option value={consts.medium_priority}>{translations.medium}</option>
                    <option value={consts.high_priority}>{translations.high}</option>
                </Input>
                {this.renderErrorFor('category_id')}
            </React.Fragment>
        )
    }
}
