import React, { Component } from 'react'
import axios from 'axios'
import AddCase from './edit/AddCase'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import CaseFilters from './CaseFilters'
import CaseItem from './CaseItem'
import queryString from 'query-string'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import { getDefaultTableFields } from '../presenters/CasePresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class Cases extends Component {
    constructor (props) {
        super(props)

        this.state = {
            currentPage: 1,
            totalPages: null,
            pageLimit: !localStorage.getItem('number_of_rows') ? Math.ceil(window.innerHeight / 90) : localStorage.getItem('number_of_rows'),
            currentInvoices: [],
            isMobile: window.innerWidth <= 768,
            isOpen: window.innerWidth > 670,
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.success_message,
            dropdownButtonActions: ['download'],
            customers: [],
            cases: [],
            cachedData: [],
            view: {
                ignore: [],
                viewMode: false,
                viewedId: null,
                title: null
            },
            errors: [],
            filters: {
                searchText: '',
                status: 'active',
                start_date: '',
                end_date: '',
                customer_id: queryString.parse(this.props.location.search).customer_id || '',
                category_id: queryString.parse(this.props.location.search).category_id || '',
                priority_id: queryString.parse(this.props.location.search).priority_id || ''
            }
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterCases = this.filterCases.bind(this)
        this.getCustomers = this.getCustomers.bind(this)
    }

    componentDidMount () {
        this.getCustomers()
    }

    onPageChanged (data) {
        let { cases, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            cases = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = cases.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    addUserToState (cases, do_filter = false, filters = null) {
        const should_filter = !this.state.cachedData.length || do_filter === true
        const cachedData = !this.state.cachedData.length ? cases : this.state.cachedData

        if (should_filter) {
            cases = filterStatuses(cases, '', this.state.filters)
        }

        this.setState({
            filters: filters !== null ? filters : this.state.filters,
            cases: cases,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(cases.length / this.state.pageLimit)
            this.onPageChanged({ invoices: cases, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    getCustomers () {
        axios.get('/api/customers')
            .then((r) => {
                this.setState({
                    customers: r.data
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            })
    }

    filterCases (filters) {
        this.setState({ filters: filters })
    }

    resetFilters () {
        this.props.reset()
    }

    userList (props) {
        const { pageLimit, customers, currentInvoices, cachedData } = this.state
        return <CaseItem showCheckboxes={props.showCheckboxes} customers={customers} cases={currentInvoices}
            show_list={props.show_list}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit} entities={cachedData}
            viewId={props.viewId}
            ignoredColumns={props.default_columns} addUserToState={this.addUserToState}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
    }

    getUsers () {
        axios.get('api/users')
            .then((r) => {
                this.setState({
                    users: r.data
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            })
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    setError (message = null) {
        this.setState({ error: true, error_message: message === null ? translations.unexpected_error : message })
    }

    setSuccess (message = null) {
        this.setState({
            show_success: true,
            success_message: message === null ? translations.success_message : message
        })
    }

    render () {
        const { start_date, end_date } = this.state.filters
        const {
            cachedData,
            view,
            cases,
            customers,
            error,
            isOpen,
            error_message,
            success_message,
            show_success,
            currentInvoices,
            pageLimit
        } = this.state
        const fetchUrl = `/api/cases?start_date=${start_date}&end_date=${end_date}`
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable-large fixed-margin-datatable-large-mobile'
        const total = cases.length

        return customers.length ? (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <CaseFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.addUserToState}
                                    setFilterOpen={this.setFilterOpen.bind(this)} cases={cases}
                                    customers={customers}
                                    filters={this.state.filters} filter={this.filterCases}
                                    saveBulk={this.saveBulk}/>

                                <AddCase
                                    customers={customers}
                                    cases={cachedData}
                                    action={this.addUserToState}
                                />
                            </CardBody>
                        </Card>
                    </div>

                    {error &&
                        <Snackbar open={error} autoHideDuration={3000} onClose={this.handleClose.bind(this)}>
                            <Alert severity="danger">
                                {error_message}
                            </Alert>
                        </Snackbar>
                    }

                    {show_success &&
                        <Snackbar open={show_success} autoHideDuration={3000} onClose={this.handleClose.bind(this)}>
                            <Alert severity="success">
                                {success_message}
                            </Alert>
                        </Snackbar>
                    }

                    <div className={margin_class}>
                        <Card>
                            <CardBody>
                                <DataTable

                                    pageLimit={pageLimit}
                                    onPageChanged={this.onPageChanged.bind(this)}
                                    currentData={currentInvoices}
                                    hide_pagination={true}

                                    default_columns={getDefaultTableFields()}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    customers={this.state.customers}
                                    columnMapping={{
                                        customer_id: 'CUSTOMER',
                                        priority_id: 'PRIORITY',
                                        status_id: 'STATUS'
                                    }}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Case"
                                    bulk_save_url="/api/cases/bulk"
                                    view={view}
                                    userList={this.userList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.addUserToState}
                                />

                                {total > 0 &&
                                    <div className="d-flex flex-row py-4 align-items-center">
                                        <PaginationNew totalRecords={total} pageLimit={parseInt(pageLimit)}
                                            pageNeighbours={1}
                                            onPageChanged={this.onPageChanged.bind(this)}/>
                                    </div>
                                }
                            </CardBody>
                        </Card>
                    </div>
                </div>
            </Row>
        ) : null
    }
}
