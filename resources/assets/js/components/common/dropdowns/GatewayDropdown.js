import React, { Component } from 'react'
import axios from 'axios'
import { Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class GatewayDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            gateways: []
        }

        this.getGateways = this.getGateways.bind(this)
    }

    componentDidMount () {
        if (!this.props.gateways || !Object.keys(this.props.gateways).length) {
            this.getGateways()
        } else {
            console.log('gateways', this.props.gateways)
            this.setState({ gateways: this.props.gateways })
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

    getGateways () {
        axios.get('/api/company_gateways')
            .then((r) => {
                this.setState({
                    gateways: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    render () {
        let groupList = null
        if (this.state.gateways && !Object.keys(this.state.gateways).length) {
            groupList = <option value="">Loading...</option>
        } else {
            groupList = Object.keys(this.state.gateways).map((index) => {
                return <option key={index}
                    value={this.state.gateways[index].gateway.key}>{this.state.gateways[index].gateway.name}</option>
            })
        }

        return (
            <React.Fragment>
                <Input value={this.props.gateway_key} onChange={this.props.handleInputChanges} type="select"
                    name="gateway_key" id="gateway_key">
                    <option value="">{translations.select_option}</option>
                    {groupList}
                </Input>
                {this.renderErrorFor('gateway_id')}
            </React.Fragment>
        )
    }
}
