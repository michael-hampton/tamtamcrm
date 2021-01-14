import React, { Component } from 'react'
import axios from 'axios'
import { Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class CaseTemplateDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            templates: []
        }

        this.getTemplates = this.getTemplates.bind(this)
    }

    componentDidMount () {
        if (!this.props.templates || !this.props.templates.length) {
            this.getTemplates()
        } else {
            this.setState({ templates: this.props.templates })
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

    getTemplates () {
        axios.get('/api/case_template')
            .then((r) => {
                this.setState({
                    templates: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    buildSingle (name, categoryList) {
        return (
            <Input value={this.props.template} onChange={this.props.handleInputChanges} type="select"
                name={name} id={name}>
                <option value="">{translations.select_option}</option>
                {categoryList}
            </Input>
        )
    }

    render () {
        let categoryList = null
        if (!this.state.templates.length) {
            categoryList = <option value="">Loading...</option>
        } else {
            categoryList = this.state.templates.map((template, index) => (
                <option key={index} value={template.id}>{template.name}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'template'
        const input = this.buildSingle(name, categoryList)

        return (
            <React.Fragment>
                {input}
                {this.renderErrorFor('template_id')}
            </React.Fragment>
        )
    }
}
