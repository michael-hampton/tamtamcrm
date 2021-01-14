import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class IndustryDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            industries: []
        }

        this.getIndustries = this.getIndustries.bind(this)
    }

    componentDidMount () {
        if (!this.props.industries || !this.props.industries.length) {
            this.getIndustries()
        } else {
            this.setState({ industries: this.props.industries })
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

    getIndustries () {
        axios.get('/api/industries')
            .then((r) => {
                this.setState({
                    industries: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    render () {
        let industryList = null
        if (this.state.industries && !this.state.industries.length) {
            industryList = <option value="">Loading...</option>
        } else {
            industryList = this.state.industries.map((industry, index) => (
                <option key={index} value={industry.id}>{industry.name}</option>
            ))
        }

        return (
            <FormGroup className="ml-2">
                <Input value={this.props.industry_id} onChange={this.props.handleInputChanges} type="select"
                    name="industry_id" id="industry_id">
                    <option value="">{translations.select_option}</option>
                    {industryList}
                </Input>
                {this.renderErrorFor('industry_id')}
            </FormGroup>
        )
    }
}
