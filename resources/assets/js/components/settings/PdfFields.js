import React, { Component } from 'react'
import Select from 'react-select'

export default class PdfFields extends Component {
    constructor (props) {
        super(props)

        this.state = {
            id: localStorage.getItem('account_id'),
            selected: [],
            values: [],
            pdf_variables: [],
            initialState: [],
            settings: this.props.settings,
            errors: [],
            ignoredColumns: ['settings', 'deleted_at'],
            filters: {
                status: 'active'
            }
        }

        this.handleChange = this.handleChange.bind(this)
        this.handleRemove = this.handleRemove.bind(this)
    }

    componentDidMount () {
        const arrSelected = []
        const arrTest = []
        const { columns } = this.props
        const saved_columns = this.props.ignored_columns[this.props.section]

        columns.forEach(column => {
            if (saved_columns && saved_columns.length && saved_columns.includes(column)) {
                arrSelected.push({ label: column, value: column })
            } else {
                arrTest.push({ label: column, value: column })
            }
        })

        this.setState({
            pdf_variables: this.props.ignored_columns,
            values: arrTest,
            initialState: arrTest,
            selected: arrSelected
        }, function () {
            // console.log('columns', this.state.values)
            // console.log('selected', this.state.selected)
        })
    }

    update (new_values) {
        const ignored_columns = { ...this.props.ignored_columns }
        ignored_columns[this.props.section] = new_values

        /* this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                pdf_variables: ignored_columns
            }
        }), () => this.handleSubmit()) */
    }

    capitalizeFirstLetters (str) {
        return str.toLowerCase().replace(/^\w|\s\w/g, function (letter) {
            return letter.toUpperCase()
        })
    }

    handleChange (selected) {
        const pdf_variables = { ...this.props.ignored_columns }
        const saved_columns = pdf_variables[this.props.section]
        let items_to_remove = false

        if (selected && selected.length) {
            selected.forEach((user) => {
                if (saved_columns && saved_columns.includes(user.value)) {
                    const values = this.state.values.filter(item => item.value !== user.value)

                    this.setState({ values: values })
                    items_to_remove = true
                } else {
                    // alert('not in ignored ' + user.value)
                }
            })

            if (items_to_remove === true) {
                this.handleRemove(selected)
            }

            this.state.selected.forEach((user) => {
                let found = false
                selected.forEach((user2) => {
                    if (user2.value === user.value) {
                        found = true
                    }
                })

                if (!found) {
                    const { values } = this.state
                    values.push({ label: user.value, value: user.value })
                    this.setState({ values: values })
                    saved_columns.push(user.value)
                    // this.update(test)
                }
            })

            this.setState({ selected: selected })
        } else {
            alert('You must have at least one column')
            // this.props.onChange2([])
            // this.setState({ selected: this.state.initialState, values: this.state.initialState })
        }
    }

    handleRemove (selected) {
        const test = []

        const pdf_variables = { ...this.props.ignored_columns }

        selected.map(column => {
            test.push(column.value)
        })

        pdf_variables[this.props.section] = test
        this.props.onChange2(pdf_variables)
    }

    render () {
        const { options, onChangeCallback, ...otherProps } = this.props

        return <Select
            closeMenuOnSelect={false}
            classNamePrefix="Select-multi"
            isMulti
            value={this.state.selected}
            options={this.state.values}
            hideSelectedOptions={false}
            isSearchable={true}
            backspaceRemovesValue={false}
            onChange={this.handleChange}
            {...otherProps}
        />
    }
}
