import React, { Component } from 'react'
import Select from 'react-select'
import { translations } from '../../utils/_translations'
import CompanyRepository from '../../repositories/CompanyRepository'

export default class CompanyDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            companies: []
        }

        this.getCompanies = this.getCompanies.bind(this)
    }

    componentDidMount () {
        if (!this.props.companies || !this.props.companies.length) {
            this.getCompanies()
        } else {
            this.props.companies.unshift({ id: '', name: 'Select Company' })
            this.setState({ companies: this.props.companies })
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

    handleChange (value, name) {
        const e = {
            target: {
                id: name,
                name: name,
                value: value.id
            }
        }

        this.props.handleInputChanges(e)
    }

    getCompanies () {
        const companyRepository = new CompanyRepository()
        companyRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ companies: response }, () => {
                console.log('companies', this.state.companies)

                if (!this.props.multiple) {
                    this.state.companies.unshift({ id: '', name: 'Select Company' })
                }
            })
        })
    }

    render () {
        const name = this.props.name && this.props.name ? this.props.name : 'company_id'
        const company = this.props.company_id ? this.state.companies.filter(option => option.id === parseInt(this.props.company_id)) : null

        return (
            <React.Fragment>
                <Select value={company}
                    placeholder={translations.select_option}
                    className="flex-grow-1"
                    classNamePrefix="select"
                    name={name}
                    options={this.state.companies}
                    getOptionLabel={option => option.name}
                    getOptionValue={option => option.id}
                    onChange={(value) => this.handleChange(value, name)}
                />
                {this.renderErrorFor('company_id')}
            </React.Fragment>
        )
    }
}
