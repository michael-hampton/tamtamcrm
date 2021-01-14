import React, { Component } from 'react'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'
import QuoteRepository from '../../repositories/QuoteRepository'

export default class QuoteDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            quotes: []
        }

        this.getQuotes = this.getQuotes.bind(this)
    }

    componentDidMount () {
        if (!this.props.quotes || !this.props.quotes.length) {
            this.getQuotes()
        } else {
            this.state.quotes = this.props.quotes
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

    getQuotes () {
        const quoteRepository = new QuoteRepository()
        quoteRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ quotes: response }, () => {
                console.log('quotes', this.state.quotes)
            })
        })
    }

    render () {
        let quoteList = null

        let quotes = this.props.quotes ? this.props.quotes : this.state.quotes

        if (!quotes) {
            quoteList = <option value="">Loading...</option>
        } else {
            if (this.props.is_recurring) {
                quotes = quotes.filter(quote => !quote.recurring_quote_id)
            }

            quoteList = quotes.map((quote, index) => (
                <option key={index} value={quote.id}>{quote.number}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'quotes_id'

        const selectList = this.props.multiple && this.props.multiple === true ? (
            <Input onChange={this.props.handleInputChanges} multiple type="select"
                name={name} id={name}>
                {quoteList}
            </Input>
        ) : <Input value={this.props.quote_id} onChange={this.props.handleInputChanges} type="select"
            name={name} id={name}>
            <option value="">{translations.select_option}</option>
            {quoteList}
        </Input>

        return (
            <FormGroup>
                {selectList}
                {this.renderErrorFor(name)}
            </FormGroup>
        )
    }
}
