import React, { Component } from 'react'

export default class TableSort extends Component {
    constructor (props) {
        super(props)
        this.state = {}
    }

    tableHeads () {
        if (this.props.columns && this.props.columns.length) {
            if (this.props.default_columns && this.props.default_columns.length) {
                return this.props.columns.filter(column => {
                    return (!this.props.ignore || !this.props.ignore.length) || (this.props.ignore.length && this.props.default_columns.includes(column))
                }).map(column => {
                    const sortedClass = (this.props.disableSorting && this.props.disableSorting.includes(column)) ? ('') : ((column === this.props.sorted_column) ? (`th-sm sorting_${this.props.order}`) : ('sorting_asc_disabled'))
                    return <th className={`table-head ${sortedClass}`} key={column}
                        onClick={() => this.sortByColumn(column)}>
                        {this.columnHead(column)}
                    </th>
                })
            }

            if (!this.props.default_columns) {
                return this.props.columns.filter(column => {
                    return (!this.props.ignore || !this.props.ignore.length) || (this.props.ignore.length && !this.props.ignore.includes(column))
                }).map(column => {
                    const sortedClass = (this.props.disableSorting && this.props.disableSorting.includes(column)) ? ('') : ((column === this.props.sorted_column) ? (`th-sm sorting_${this.props.order}`) : ('sorting_asc_disabled'))
                    return <th className={`table-head ${sortedClass}`} key={column}
                        onClick={() => this.sortByColumn(column)}>
                        {this.columnHead(column)}
                    </th>
                })
            }
        }
    }

    columnHead (value) {
        const formattedValue = this.props.columnMapping && this.props.columnMapping[value] !== undefined ? this.props.columnMapping[value] : value.split('_').join(' ').toUpperCase()
        return formattedValue
    }

    sortByColumn (column) {
        if (column === this.props.sorted_column) {
            this.props.order === 'asc' ? this.props.fetchEntities(1, 'desc') : this.props.fetchEntities(1, 'asc')
        } else {
            this.props.fetchEntities(1, 'asc', column)
        }
    }

    render () {
        const tableHeads = this.tableHeads()

        return (<thead>
            <tr>
                <th/>
                {tableHeads}
            </tr>
        </thead>)
    }
}
