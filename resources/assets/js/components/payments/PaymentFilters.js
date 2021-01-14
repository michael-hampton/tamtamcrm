import React, { Component } from 'react'
import { Col, FormGroup, Row } from 'reactstrap'
import CustomerDropdown from '../common/dropdowns/CustomerDropdown'
import TableSearch from '../common/TableSearch'
import DateFilter from '../common/DateFilter'
import CsvImporter from '../common/CsvImporter'
import FilterTile from '../common/FilterTile'
import { translations } from '../utils/_translations'
import { consts, paymentStatuses } from '../utils/_consts'
import StatusDropdown from '../common/StatusDropdown'

export default class PaymentFilters extends Component {
    constructor (props) {
        super(props)
        this.state = {
            isOpen: false,
            dropdownButtonActions: ['download'],
            filters: {
                status_id: 'active',
                customer_id: '',
                searchText: '',
                start_date: '',
                end_date: '',
                gateway_id: ''
            }
        }

        this.statuses = [
            {
                value: consts.payment_status_pending,
                label: translations.pending
            },
            {
                value: consts.payment_status_refunded,
                label: translations.refunded
            },
            {
                value: consts.payment_status_completed,
                label: translations.complete
            },
            {
                value: consts.payment_status_unapplied,
                label: translations.unapplied
            }
        ]

        this.getFilters = this.getFilters.bind(this)
        this.filterPayments = this.filterPayments.bind(this)
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterPayments (event) {
        if ('start_date' in event) {
            this.setState(prevState => ({
                filters: {
                    ...prevState.filters,
                    start_date: event.start_date,
                    end_date: event.end_date
                }
            }))
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
        const { status_id, searchText, customer_id, gateway_id, start_date, end_date } = this.state.filters
        return (
            <Row form>
                <Col md={3}>
                    <TableSearch onChange={this.filterPayments}/>
                </Col>

                <Col sm={12} md={3} className="mt-3 mt-md-0">
                    <CustomerDropdown
                        handleInputChanges={this.filterPayments}
                        customer={this.state.filters.customer_id}
                        customers={this.props.customers}
                        name="customer_id"
                    />
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <StatusDropdown filterStatus={this.filterPayments} statuses={this.statuses}/>
                    </FormGroup>
                </Col>

                <Col sm={12} md={1} className="mt-3 mt-md-0">
                    <CsvImporter statuses={paymentStatuses} customers={this.props.customers} filename="payments.csv"
                        url={`/api/payments?search_term=${searchText}&status=${status_id}&customer_id=${customer_id}&gateway_id=${gateway_id}&start_date=${start_date}&end_date=${end_date}&page=1&per_page=5000`}/>
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <DateFilter onChange={this.filterPayments}/>
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
