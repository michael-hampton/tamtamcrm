import React, { Component } from 'react'
import { Col, FormGroup, Row } from 'reactstrap'
import CategoryDropdown from '../common/dropdowns/CategoryDropdown'
import CompanyDropdown from '../common/dropdowns/CompanyDropdown'
import TableSearch from '../common/TableSearch'
import DateFilter from '../common/DateFilter'
import CsvImporter from '../common/CsvImporter'
import FilterTile from '../common/FilterTile'
import StatusDropdown from '../common/StatusDropdown'
import { filterStatuses } from '../utils/_search'

export default class ProductFilters extends Component {
    constructor (props) {
        super(props)
        this.state = {
            dropdownButtonActions: ['download'],
            isOpen: false,
            filters: {
                status: 'active',
                category_id: '',
                company_id: '',
                searchText: '',
                start_date: '',
                end_date: ''
            }
        }

        this.getFilters = this.getFilters.bind(this)
        this.filterProducts = this.filterProducts.bind(this)
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterProducts (event) {
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
        const { status, searchText, category_id, company_id, start_date, end_date } = this.state.filters

        return (
            <Row form>
                <Col md={2}>
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
                        }} statuses={this.statuses}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <CompanyDropdown
                        company_id={this.props.filters.company_id}
                        handleInputChanges={(e) => {
                            this.setState(prevState => ({
                                filters: {
                                    ...prevState.filters,
                                    [e.target.name]: e.target.value
                                }
                            }), () => {
                                const results = filterStatuses(this.props.cachedData, e.target.value, this.state.filters)
                                this.props.updateList(results || [], false, this.state.filters)
                            })
                        }}
                        companies={this.props.companies}
                        name="company_id"
                    />
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <CategoryDropdown
                        name="category_id"
                        handleInputChanges={(e) => {
                            const name = e.target.name
                            const value = e.target.value
                            this.setState(prevState => ({
                                filters: {
                                    ...prevState.filters,
                                    [name]: value
                                }
                            }), () => {
                                const results = filterStatuses(this.props.cachedData, value, this.state.filters)
                                this.props.updateList(results || [], false, this.state.filters)
                            })
                        }}
                        categories={this.props.categories}
                    />
                </Col>

                <Col sm={12} md={1} className="mt-3 mt-md-0">
                    <CsvImporter filename="products.csv"
                        url={`/api/products?search_term=${searchText}&status=${status}&category_id=${category_id}&company_id=${company_id}&start_date=${start_date}&end_date=${end_date}&page=1&per_page=5000`}/>
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <DateFilter onChange={this.filterProducts}/>
                    </FormGroup>
                </Col>
            </Row>
        )
    }

    render () {
        const filters = this.getFilters()

        return (<FilterTile setFilterOpen={this.props.setFilterOpen} filters={filters}/>)
    }
}
