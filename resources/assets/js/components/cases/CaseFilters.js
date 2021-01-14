import React, { Component } from 'react'
import { Col, FormGroup, Row } from 'reactstrap'
import TableSearch from '../common/TableSearch'
import DateFilter from '../common/DateFilter'
import FilterTile from '../common/FilterTile'
import CustomerDropdown from '../common/dropdowns/CustomerDropdown'
import CaseCategoryDropdown from '../common/dropdowns/CaseCategoryDropdown'
import CasePriorityDropdown from '../common/dropdowns/CasePriorityDropdown'
import StatusDropdown from '../common/StatusDropdown'
import { caseLinkTypes, casePriorities, caseStatuses, consts } from '../utils/_consts'
import { translations } from '../utils/_translations'
import CsvImporter from '../common/CsvImporter'

export default class CaseFilters extends Component {
    constructor (props) {
        super(props)
        this.state = {
            isOpen: false,
            dropdownButtonActions: ['download'],
            filters: {
                searchText: '',
                status: 'active',
                start_date: '',
                end_date: '',
                customer_id: '',
                category_id: '',
                priority_id: ''
            }
        }

        this.statuses = [
            {
                value: consts.case_status_draft,
                label: translations.draft
            },
            {
                value: consts.case_status_open,
                label: translations.open
            },
            {
                value: consts.case_status_closed,
                label: translations.closed
            }
        ]

        this.filterCases = this.filterCases.bind(this)
        this.getFilters = this.getFilters.bind(this)
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterCases (event) {
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
                    <TableSearch onChange={this.filterCases}/>
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <CustomerDropdown
                        customer={this.props.filters.customer_id}
                        handleInputChanges={this.filterCases}
                        customers={this.props.customers}
                        name="customer_id"
                    />
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <StatusDropdown name="status" filterStatus={this.filterCases} statuses={this.statuses}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <FormGroup>
                        <CasePriorityDropdown
                            name="priority_id"
                            priority={this.props.filters.priority_id}
                            renderErrorFor={this.renderErrorFor}
                            handleInputChanges={this.filterCases}
                        />
                    </FormGroup>
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <FormGroup>
                        <CaseCategoryDropdown
                            name="category_id"
                            category={this.props.filters.category_id}
                            renderErrorFor={this.renderErrorFor}
                            handleInputChanges={this.filterCases}
                        />
                    </FormGroup>
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <DateFilter onChange={this.filterCases}/>
                    </FormGroup>
                </Col>

                <Col md={1}>
                    <CsvImporter links={caseLinkTypes} priorities={casePriorities} statuses={caseStatuses}
                        customers={this.props.customers} filename="cases.csv"
                        url={`/api/cases?status=${this.state.filters.status_id}&customer_id=${this.state.filters.customer_id}&category_id=${this.state.filters.category_id}&priority_id=${this.state.filters.priority_id}&start_date=${this.state.filters.start_date}&end_date=${this.state.filters.end_date}&page=1&per_page=5000`}/>
                </Col>
            </Row>
        )
    }

    render () {
        const filters = this.getFilters()

        return (<FilterTile setFilterOpen={this.props.setFilterOpen} filters={filters}/>)
    }
}
