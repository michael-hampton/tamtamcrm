import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class AttributeDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            departments: []
        }

        this.getAttributes = this.getAttributes.bind(this)
    }

    componentDidMount () {
        if (!this.props.attributes || !this.props.attributes.length) {
            this.getAttributes()
        } else {
            this.setState({ attributes: this.props.attributes })
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

    getAttributes () {
        axios.get('/api/attributes')
            .then((r) => {
                this.setState({
                    attributes: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    render () {
        let attributeList = null
        if (!this.state.attributes || !this.state.attributes.length) {
            attributeList = <option value="">Loading...</option>
        } else {
            attributeList = this.state.attributes.map((attribute, index) => (
                <option key={index} value={attribute.id}>{attribute.name}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'attribute_id'
        const data_id = this.props.data_id ? this.props.data_id : 0

        return (
            <FormGroup className="mr-2">
                <Input value={this.props.attribute_id} onChange={this.props.handleInputChanges} type="select"
                    data-id={data_id}
                    name={name} id={name}>
                    <option value="">{translations.select_option}</option>
                    {attributeList}
                </Input>
                {this.renderErrorFor('attribute_id')}
            </FormGroup>
        )
    }
}
