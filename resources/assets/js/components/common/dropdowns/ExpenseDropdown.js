import React, { Component } from 'react'
import { Input } from 'reactstrap'
import { translations } from '../../utils/_translations'
import ExpenseRepository from '../../repositories/ExpenseRepository'

export default class ExpenseDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            expenses: []
        }

        this.getExpenses = this.getExpenses.bind(this)
    }

    componentDidMount () {
        if (!this.props.expenses || !this.props.expenses.length) {
            this.getExpenses()
        } else {
            this.setState({ expenses: this.props.expenses })
        }
    }

    getExpenses () {
        const expenseRepository = new ExpenseRepository()
        expenseRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ expenses: response }, () => {
                console.log('expenses', this.state.expenses)
            })
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

    buuildMultiple (name, expenseList) {
        return (
            <Input value={this.props.product} onChange={this.props.handleInputChanges} type="select" multiple
                name={name} id={name}>
                {expenseList}
            </Input>
        )
    }

    buildSingle (name, expenseList, dataId) {
        return (
            <Input data-line={dataId} value={this.props.expense} onChange={this.props.handleInputChanges} type="select"
                name={name} id={name}>
                <option value="">{translations.select_option}</option>
                {expenseList}
            </Input>
        )
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

    render () {
        let productList = null
        if (!this.state.expenses.length) {
            productList = <option value="">Loading...</option>
        } else {
            productList = this.state.expenses.map((expense, index) => (
                <option key={index} value={expense.id}>{expense.number} - {expense.reference_number}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'product_id'
        const dataId = this.props.dataId ? this.props.dataId : 0
        const input = this.props.multiple && this.props.multiple === true ? this.buuildMultiple(name, productList) : this.buildSingle(name, productList, dataId)

        return (
            <React.Fragment>
                {input}
                {this.renderErrorFor('expense_id')}
            </React.Fragment>
        )
    }
}
