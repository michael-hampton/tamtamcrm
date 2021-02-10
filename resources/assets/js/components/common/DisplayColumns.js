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
            // console.log('columns', this.state.values)
            console.log('selected', this.state.selected)
        })
    }

    handleChange (selected) {
        console.log('selected', selected)

        let ignored = this.props.default_columns || []

        if (selected && selected.length) {
            const values = selected.map((value, index) => {
                return value.value
            })

            if (ignored && values) {
                ignored = ignored.filter(item => values.includes(item))
            }

            const ignored2 = values.filter(item => !ignored.includes(item))

            if (ignored2 && ignored2.length) {
                ignored = ignored.concat(ignored2)
            }

            // console.log('ignored', ignored)
            // console.log('ignored2', ignored2)
        }

        this.props.onChange2(ignored)
        const arrSelected = []
        const arrTest = []

        this.setState({selected:selected})

        /*if (ignored && ignored.length) {
            this.props.columns.forEach(column => {
                if (ignored.includes(column)) {
                    arrSelected.push({ label: column, value: column })
                } else {
                    arrTest.push({ label: column, value: column })
                }
            })
        }

        console.log('selected', arrSelected)

        this.setState({ values: arrTest, initialState: arrTest, selected: arrSelected }, function () {
            // console.log('columns', this.state.values)
            // console.log('selected', this.state.selected)
        }) */
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
