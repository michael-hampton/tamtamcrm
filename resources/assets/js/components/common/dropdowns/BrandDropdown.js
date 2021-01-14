import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class BrandDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            brands: []
        }

        this.getBrands = this.getBrands.bind(this)
    }

    componentDidMount () {
        if (!this.props.brands || !this.props.brands.length) {
            this.getBrands()
        } else {
            this.setState({ brands: this.props.brands })
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

    getBrands () {
        axios.get('/api/brands')
            .then((r) => {
                this.setState({
                    brands: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    buuildMultiple (name, brandList) {
        return (
            <Input value={this.props.brand} onChange={this.props.handleInputChanges} type="select" multiple
                name={name} id={name}>
                {brandList}
            </Input>
        )
    }

    buildSingle (name, brandList) {
        return (
            <Input value={this.props.brand} onChange={this.props.handleInputChanges} type="select"
                name={name} id={name}>
                <option value="">{translations.select_option}</option>
                {brandList}
            </Input>
        )
    }

    render () {
        let brandList = null
        if (!this.state.brands.length) {
            brandList = <option value="">Loading...</option>
        } else {
            brandList = this.state.brands.map((brand, index) => (
                <option key={index} value={brand.id}>{brand.name}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'brand_id'
        const input = this.props.multiple && this.props.multiple === true ? this.buuildMultiple(name, brandList) : this.buildSingle(name, brandList)

        return (
            <FormGroup>
                {input}
                {this.renderErrorFor('brand_id')}
            </FormGroup>
        )
    }
}
