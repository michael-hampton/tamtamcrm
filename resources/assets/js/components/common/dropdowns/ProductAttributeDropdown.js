import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class ProductAttributeDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            values: [],
            product_id: 0
        }

        this.getValues = this.getValues.bind(this)
    }

    componentWillReceiveProps (nextProps) {
        if (nextProps.product_id !== this.state.product_id) {
            this.setState({ product_id: nextProps.product_id, values: [] }, () => {
                this.getValues()
            })

            this.setState({ product_id: nextProps.product_id })
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
        axios.get(`/api/products/${this.state.product_id}`)
            .then((r) => {
                this.setState({
                    values: r.data.attributes
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    render () {
        let valueList = null
        if (!this.state.values || !this.state.values.length) {
            valueList = <option value="">Loading...</option>
        } else {
            valueList = this.state.values.map(value => {
                console.log('values', value.values)
                const attribute_values = Array.prototype.map.call(value.values, function (item) {
                    return item.value
                }).join(',')
                return <option data-price={value.price} key={value.id}
                    value={value.id}>{attribute_values} {value.price}</option>
            })
        }

        const name = this.props.name && this.props.name ? this.props.name : 'attribute_id'
        const dataId = this.props.dataId ? this.props.dataId : 0

        return (
            <FormGroup className="mr-2">
                <Input value={this.props.attribute_value_id} onChange={this.props.handleInputChanges} type="select"
                    data-line={dataId}
                    name={name} id={name}>
                    <option value="">{translations.select_option}</option>
                    {valueList}
                </Input>
                {this.renderErrorFor('attribute_value_id')}
            </FormGroup>
        )
    }
}
