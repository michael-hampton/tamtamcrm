import React, { Component } from 'react'
import Select from 'react-select'

export default class DisplayColumns extends Component {
    constructor (props) {
        super(props)

        this.state = {
            selected: [],
            values: [],
            initialState: [],
            errors: [],
            default_columns: [],
            ignoredColumns: ['settings', 'deleted_at'],
            filters: {
                status: 'active'
            }
        }

        this.handleChange = this.handleChange.bind(this)
    }

    componentDidMount () {
        const arrSelected = []
        const arrTest = []
        const columns = this.props.columns

        if (this.props.default_columns && this.props.default_columns.length) {
            columns.forEach(column => {
                if (this.props.default_columns.includes(column)) {
                    arrSelected.push({ label: column, value: column })
                } else {
                    arrTest.push({ label: column, value: column })
                }
            })
        } else {
            columns.forEach(column => {
                if (!this.props.ignored_columns.includes(column)) {
                    arrSelected.push({ label: column, value: column })
                } else {
                    arrTest.push({ label: column, value: column })
                }
            })
        }

        this.setState({ values: arrTest, initialState: arrTest, selected: arrSelected }, function () {
            console.log('columns', this.state.values)
            console.log('selected', this.state.selected)
        })
    }

    handleChange (selected) {
        if (selected && selected.length) {
            selected.forEach((user) => {
                if (this.props.ignored_columns.includes(user.value)) {
                    const values = this.state.values.filter(item => item.value !== user.value)
                    this.setState({ values: values })
                    const ignored = this.props.ignored_columns.filter(item => item !== user.value)
                    this.props.onChange2(ignored)
                } else {
                    // alert('not in ignored ' + user.value)
                }
            })

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
                    this.props.ignored_columns.push(user.value)
                    this.props.onChange2(this.props.ignored_columns)
                }
            })

            this.setState({ selected: selected })
        } else {
            alert('You must have at least one column')
            // this.props.onChange2([])
            // this.setState({ selected: this.state.initialState, values: this.state.initialState })
        }
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
