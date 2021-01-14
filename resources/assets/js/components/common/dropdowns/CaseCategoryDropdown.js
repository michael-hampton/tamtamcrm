import React, { Component } from 'react'
import axios from 'axios'
import { Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class CaseCategoryDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            categories: []
        }

        this.getCategories = this.getCategories.bind(this)
    }

    componentDidMount () {
        if (!this.props.categories || !this.props.categories.length) {
            this.getCategories()
        } else {
            this.setState({ categories: this.props.categories })
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

    getCategories () {
        axios.get('/api/case-categories')
            .then((r) => {
                this.setState({
                    categories: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    buuildMultiple (name, categoryList) {
        return (
            <Input value={this.props.category} onChange={this.props.handleInputChanges} type="select" multiple
                name={name} id={name}>
                {categoryList}
            </Input>
        )
    }

    buildSingle (name, categoryList) {
        return (
            <Input value={this.props.category} onChange={this.props.handleInputChanges} type="select"
                name={name} id={name}>
                <option value="">{translations.select_option}</option>
                {categoryList}
            </Input>
        )
    }

    render () {
        let categoryList = null
        if (!this.state.categories.length) {
            categoryList = <option value="">Loading...</option>
        } else {
            categoryList = this.state.categories.map((category, index) => (
                <option key={index} value={category.id}>{category.name}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'category'
        const input = this.props.multiple && this.props.multiple === true ? this.buuildMultiple(name, categoryList) : this.buildSingle(name, categoryList)

        return (
            <React.Fragment>
                {input}
                {this.renderErrorFor('category_id')}
            </React.Fragment>
        )
    }
}
