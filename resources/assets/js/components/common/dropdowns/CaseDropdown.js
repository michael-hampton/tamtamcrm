import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class CaseDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            cases: []
        }

        this.getCases = this.getCases.bind(this)
    }

    componentDidMount () {
        if (!this.props.cases || !this.props.cases.length) {
            this.getCases()
        } else {
            this.state.cases = this.props.cases
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

    getCases () {
        axios.get('/api/cases')
            .then((r) => {
                this.setState({
                    cases: r.data
                }, () => console.log('cases', this.state.cases))
            })
            .catch((e) => {
                console.error(e)
            })
    }

    render () {
        let caseList = null
        const { cases } = this.state

        if (!cases) {
            caseList = <option value="">Loading...</option>
        } else {
            caseList = cases.map((case_obj, index) => (
                <option key={index} value={case_obj.id}>{case_obj.number} ({case_obj.subject})</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'parent_id'
        const error_name = this.props.error_name ? this.props.error_name : name
        const data_id = this.props.data_id ? this.props.data_id : 0

        const selectList = this.props.multiple && this.props.multiple === true ? (
            <Input onChange={this.props.handleInputChanges} multiple type="select"
                data-id={data_id}
                name={name} id={name}>
                {caseList}
            </Input>
        ) : <Input data-id={data_id} value={this.props.case_id} onChange={this.props.handleInputChanges}
            type="select"
            name={name} id={name}>
            <option value="">{translations.select_option}</option>
            {caseList}
        </Input>

        return (
            <FormGroup>
                {selectList}
                {this.renderErrorFor(error_name)}
            </FormGroup>
        )
    }
}
