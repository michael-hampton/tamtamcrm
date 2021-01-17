import React, { Component } from 'react'
import { FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class TaxRateDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            taxRates: []
        }
    }

    componentDidMount () {
        if (!this.props.taxRates || !this.props.taxRates.length) {
            this.setState({ taxRates: JSON.parse(localStorage.getItem('tax_rates')) })
        } else {
            this.setState({ taxRates: this.props.taxRates })
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

    render () {
        let taxRateList = null
        if (this.state.taxRates && !this.state.taxRates.length) {
            taxRateList = <option value="">Loading...</option>
        } else {
            taxRateList = this.state.taxRates.map((taxRate, index) => (
                <option key={index} data-name={taxRate.name} data-rate={taxRate.rate}
                    value={taxRate.id}>{`${taxRate.name} (${taxRate.rate})`}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'tax_id'
        const lineId = this.props.lineId ? this.props.lineId : 0
        const class_name = this.props.className ? this.props.className : ''

        return (
            <FormGroup className={class_name}>
                <Label>{this.props.label || translations.tax}</Label>
                <Input data-line={lineId} value={this.props.taxRate} onChange={this.props.handleInputChanges}
                    type="select"
                    name={name} id={name}>
                    <option value="0">No Tax</option>
                    {taxRateList}
                </Input>
                {this.renderErrorFor(name)}
            </FormGroup>
        )
    }
}
