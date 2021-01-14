import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class AttributeValueDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            values: []
        }

        this.getValues = this.getValues.bind(this)
    }

    componentDidMount () {
        if (!this.props.values || !this.props.values.length) {
            this.getValues()
        } else {
            this.setState({ values: this.props.values })
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

    getValues () {
        axios.get('/api/attributeValues')
            .then((r) => {
                this.setState({
                    values: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    render () {
        let valueList = null
        if (!this.state.values.length) {
            valueList = <option value="">Loading...</option>
        } else {
            valueList = this.state.values.map(value => {
                return <option key={value.id} value={value.id}>{value.value}</option>
            })
        }

        const name = this.props.name && this.props.name ? this.props.name : 'attribute_value_id'
        const data_id = this.props.data_id ? this.props.data_id : 0

        return (
            <FormGroup className="mr-2">
                <Input multiple value={this.props.attribute_value_id} onChange={this.props.handleInputChanges}
                    type="select"
                    data-id={data_id}
                    name={name} id={name}>
                    <option value="">{translations.select_option}</option>
                    {valueList}
                </Input>
                {this.renderErrorFor('attribute_value_id')}
            </FormGroup>
        )
    }
}
