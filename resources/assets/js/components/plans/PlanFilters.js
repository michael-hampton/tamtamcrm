import React, { Component } from 'react'
import { Col, FormGroup, Row, Button } from 'reactstrap'
import TableSearch from '../common/TableSearch'
import FilterTile from '../common/FilterTile'
import DateFilter from '../common/DateFilter'
import CsvImporter from '../common/CsvImporter'
import StatusDropdown from '../common/StatusDropdown'
import { filterStatuses } from '../utils/_search'
import { translations } from "../utils/_translations";

export default class PlanFilters extends Component {
    constructor (props) {
        super(props)
        this.state = {

            dropdownButtonActions: ['download'],

            filters: {
                start_date: '',
                end_date: '',
                status_id: 'active',
                role_id: '',
                department_id: '',
                searchText: ''
            }
        }

        this.getFilters = this.getFilters.bind(this)
        this.filterPlans = this.filterPlans.bind(this)
    }

    filterPlans (event) {
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

        const column = event.target.id
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

        return true
    }

    getFilters () {
        const { searchText, status_id, start_date, end_date } = this.props.filters

        return (
            <Row form>
                <Col md={3}>
                    <TableSearch onChange={(e) => {
                        const value = typeof e.target.value === 'string' ? e.target.value.toLowerCase() : e.target.value
                        const search_results = this.props.cachedData.filter(obj => Object.keys(obj).some(key => obj[key] && obj[key].length ? obj[key].toString().toLowerCase().includes(value) : false))
                        this.props.updateList(search_results || [], false, this.state.filters)
                    }}/>
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <StatusDropdown filterStatus={(e) => {
                            this.setState(prevState => ({
                                filters: {
                                    ...prevState.filters,
                                    [e.target.id]: e.target.value
                                }
                            }), () => {
                                const results = filterStatuses(this.props.cachedData, e.target.value, this.state.filters)
                                this.props.updateList(results || [], false, this.state.filters)
                            })
                        }}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={1} className="mt-3 mt-md-0">
                    <CsvImporter filename="plans.csv"
                        url={`/api/plans?search_term=${searchText}&status=${status_id}&start_date=${start_date}&end_date=${end_date}&page=1&per_page=5000`}/>
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <FormGroup>
                        <DateFilter onChange={this.filterPlans}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={1} className="mt-3 mt-md-0">
                    <Button color="primary" onClick={() => {
                        location.href = '/#/plan-subscriptions'
                    }}>{translations.subscriptions}</Button>
                </Col>
            </Row>
        )
    }

    render () {
        const filters = this.getFilters()

        return (<FilterTile setFilterOpen={this.props.setFilterOpen} filters={filters}/>)
    }
}
