import React, { Component } from 'react'
import { Col, Row } from 'reactstrap'
import TableSearch from '../common/TableSearch'
import FilterTile from '../common/FilterTile'

export default class TaskStatusFilters extends Component {
    constructor (props) {
        super(props)
        this.state = {
            dropdownButtonActions: ['download'],
            filters: {
                isOpen: false,
                searchText: '',
                start_date: '',
                end_date: ''
            }
        }

        this.filterTaskStatuses = this.filterTaskStatuses.bind(this)
        this.getFilters = this.getFilters.bind(this)
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterTaskStatuses (event) {
        if ('start_date' in event) {
            this.setState(prevState => ({
                filters: {
                    ...prevState.filters,
                    start_date: event.start_date,
                    end_date: event.end_date
                }
            }), () => this.props.filter(this.state.filters))
            return
        }

        const column = event.target.name
        const value = event.target.value

        if (value === 'all') {
            const updatedRowState = this.state.filters.filter(filter => filter.column !== column)
            this.setState({ filters: updatedRowState }, () => this.props.filter(this.state.filters))
            return true
        }

        this.setState(prevState => ({
            filters: {
                ...prevState.filters,
                [column]: value
            }
        }), () => this.props.filter(this.state.filters))
    }

    getFilters () {
        return (
            <Row form>
                <Col md={3}>
                    <TableSearch onChange={(e) => {
                        const value = typeof e.target.value === 'string' ? e.target.value.toLowerCase() : e.target.value
                        const search_results = this.props.cachedData.filter(obj => Object.keys(obj).some(key => obj[key] && obj[key].length ? obj[key].toString().toLowerCase().includes(value) : false))
                        const totalPages = search_results && search_results.length ? Math.ceil(search_results / this.props.pageLimit) : 0
                        this.props.updateList({ invoices: search_results && search_results.length ?  search_results : [], currentPage: 1, totalPages: totalPages })
                    }}/>
                </Col>
            </Row>
        )
    }

    render () {
        const filters = this.getFilters()

        return (<FilterTile setFilterOpen={this.props.setFilterOpen} filters={filters}/>)
    }
}
