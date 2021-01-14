import React, { Component } from 'react'
import { Col, FormGroup, Row } from 'reactstrap'
import CustomerDropdown from '../common/dropdowns/CustomerDropdown'
import TableSearch from '../common/TableSearch'
import DateFilter from '../common/DateFilter'
import CsvImporter from '../common/CsvImporter'
import FilterTile from '../common/FilterTile'
import { consts, invoiceStatuses } from '../utils/_consts'
import { translations } from '../utils/_translations'
import StatusDropdown from '../common/StatusDropdown'

export default class InvoiceFilters extends Component {
    constructor (props) {
        super(props)
        this.state = {
            isOpen: false,
            dropdownButtonActions: ['download'],
            filters: {
                status_id: 'Draft',
                customer_id: '',
                searchText: '',
                start_date: '',
                end_date: '',
                project_id: '',
                user_id: '',
                id: ''
            }

        }

        this.getFilters = this.getFilters.bind(this)
        this.filterInvoices = this.filterInvoices.bind(this)

        this.statuses = [
            {
                value: consts.invoice_status_draft,
                label: translations.draft
            },
            {
                value: consts.invoice_status_sent,
                label: translations.sent
            },
            // {
            //     value: consts.invoice_status_draft,
            //     label: translations.viewed
            // },
            {
                value: consts.invoice_status_partial,
                label: translations.partial
            },
            {
                value: consts.invoice_status_paid,
                label: translations.paid
            },
            {
                value: consts.invoice_status_cancelled,
                label: translations.cancelled
            },
            {
                value: consts.invoice_status_reversed,
                label: translations.reversed
            }
        ]
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    filterInvoices (event) {
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
        const { status_id, customer_id, searchText, start_date, end_date, project_id, user_id } = this.state.filters
        return (
            <Row form>
                <Col md={3}>
                    <TableSearch onChange={this.filterInvoices}/>
                </Col>

                <Col md={3}>
                    <CustomerDropdown
                        customer={this.props.filters.customer_id}
                        handleInputChanges={this.filterInvoices}
                        customers={this.props.customers}
                        name="customer_id"
                    />
                </Col>

                <Col sm={12} md={2} className="mt-3 mt-md-0">
                    <FormGroup>
                        <StatusDropdown filterStatus={this.filterInvoices} statuses={this.statuses}/>
                    </FormGroup>
                </Col>

                <Col md={3}>
                    <FormGroup>
                        <DateFilter onChange={this.filterInvoices}/>
                    </FormGroup>
                </Col>

                <Col md={1}>
                    <CsvImporter statuses={invoiceStatuses} customers={this.props.customers} filename="invoices.csv"
                        url={`/api/invoice?search_term=${searchText}&status=${status_id}&customer_id=${customer_id}&project_id=${project_id}&user_id=${user_id}&start_date=${start_date}&end_date=${end_date}&page=1&per_page=5000`}/>
                </Col>
            </Row>
        )
    }

    render () {
        const filters = this.getFilters()

        return (<FilterTile setFilterOpen={this.props.setFilterOpen} filters={filters}/>)
    }
}
