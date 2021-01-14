import React, { Component } from 'react'
import axios from 'axios'
import { Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class GroupSettingsDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            groups: []
        }

        this.getGroups = this.getGroups.bind(this)
    }

    componentDidMount () {
        if (!this.props.groups || !this.props.groups.length) {
            this.getGroups()
        } else {
            this.setState({ groups: this.props.groups })
        }
    }

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

    getGroups () {
        axios.get('/api/groups')
            .then((r) => {
                this.setState({
                    groups: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    render () {
        let groupList = null
        if (this.state.groups && !this.state.groups.length) {
            groupList = <option value="">Loading...</option>
        } else {
            groupList = this.state.groups.map((group, index) => (
                <option key={index} value={group.id}>{group.name}</option>
            ))
        }

        return (
            <React.Fragment>
                <Input value={this.props.group_settings_id} onChange={this.props.handleInputChanges} type="select"
                    name="group_settings_id" id="group_settings_id">
                    <option value="">{translations.select_option}</option>
                    {groupList}
                </Input>
                {this.renderErrorFor('group_settings_id')}
            </React.Fragment>
        )
    }
}
