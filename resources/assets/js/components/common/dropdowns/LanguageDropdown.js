import React, { Component } from 'react'
import axios from 'axios'
import Select from 'react-select'
import { translations } from '../../utils/_translations'

export default class LanguageDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            languages: []
        }

        this.getLanguages = this.getLanguages.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'languages')) {
            this.setState({ languages: JSON.parse(localStorage.getItem('languages')) })
        } else {
            this.getLanguages()
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

    getLanguages () {
        axios.get('/api/languages')
            .then((r) => {
                this.setState({
                    langauges: r.data
                }, function () {
                    if (!this.props.multiple) {
                        this.state.languages.unshift({ id: '', name: 'Select Language' })
                    }
                })
            })
            .catch((e) => {
                console.error(e)
            })
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

    render () {
        const language = this.props.language_id ? this.state.languages.filter(option => option.id === parseInt(this.props.language_id)) : null
        const name = this.props.name ? this.props.name : 'language_id'
        return (
            <React.Fragment>
                <Select
                    placeholder={translations.select_option}
                    className="flex-grow-1"
                    classNamePrefix="select"
                    name={name}
                    value={language}
                    options={this.state.languages}
                    getOptionLabel={option => option.name}
                    getOptionValue={option => option.id}
                    onChange={(value) => this.handleChange(value, name)}
                />
                {this.renderErrorFor(name)}
            </React.Fragment>
        )
    }
}
