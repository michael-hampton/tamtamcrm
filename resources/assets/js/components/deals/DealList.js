import React, { Component } from 'react'
import DataTable from '../common/DataTable'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DealFilters from './DealFilters'
import DealItem from './DealItem'
import AddDeal from './edit/AddDeal'
import queryString from 'query-string'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import CustomerRepository from '../repositories/CustomerRepository'
import UserRepository from '../repositories/UserRepository'
import { getDefaultTableFields } from '../presenters/DealPresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class DealList extends Component {
    constructor (props) {
        super(props)

        this.state = {
            currentPage: 1,
            totalPages: null,
            pageLimit: !localStorage.getItem('number_of_rows') ? Math.ceil(window.innerHeight / 90) : localStorage.getItem('number_of_rows'),
            currentInvoices: [],
            isMobile: window.innerWidth <= 768,
            isOpen: window.innerWidth > 670,
            dropdownButtonActions: ['download'],
            cachedData: [],
            deals: [],
            users: [],
            customers: [],
            errors: [],
            kanban: false,
            view: {
                ignore: [],
                viewMode: false,
                viewedId: null,
                title: null
            },
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.success_message,
            filters: {
                // project_id: queryString.parse(this.props.location.search).project_id || '',
                status_id: 'active',
                task_status_id: '',
                customer_id: queryString.parse(this.props.location.search).customer_id || '',
                user_id: queryString.parse(this.props.location.search).user_id || '',
                // task_type: '',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            custom_fields: [],
            showRestoreButton: false
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterDeals = this.filterDeals.bind(this)
        this.getCustomers = this.getCustomers.bind(this)
        this.getUsers = this.getUsers.bind(this)
    }

    componentDidMount () {
        this.getUsers()
        this.getCustomers()
        this.getCustomFields()
    }

    addUserToState (deals, do_filter = false, filters = null) {
        const should_filter = !this.state.cachedData.length || do_filter === true
        const cachedData = !this.state.cachedData.length ? deals : this.state.cachedData

        if (should_filter) {
            deals = filterStatuses(deals, '', this.state.filters)
        }

        this.setState({
            filters: filters !== null ? filters : this.state.filters,
            deals: deals,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(deals.length / this.state.pageLimit)
            this.onPageChanged({ invoices: deals, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    onPageChanged (data) {
        let { deals, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            deals = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = deals.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    filterDeals (filters) {
        this.setState({ filters: filters })

        return true
    }

    userList (props) {
        const { pageLimit, custom_fields, users, customers, currentInvoices, cachedData } = this.state

        return <DealItem showCheckboxes={props.showCheckboxes} action={this.addUserToState} deals={currentInvoices}
            users={users}
            show_list={props.show_list} entities={cachedData}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit}
            custom_fields={custom_fields} customers={customers}
            viewId={props.viewId}
            ignoredColumns={props.default_columns} addUserToState={this.addUserToState}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.Deal) {
            custom_fields[0] = all_custom_fields.Deal
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/Deal')
            .then((r) => {
                this.setState({
                    custom_fields: r.data.fields
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            }) */
    }

    getUsers () {
        const userRepository = new UserRepository()
        userRepository.get().then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ users: response }, () => {
                console.log('users', this.state.users)
            })
        })
    }

    getCustomers () {
        const customerRepository = new CustomerRepository()
        customerRepository.get().then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ customers: response }, () => {
                console.log('customers', this.state.customers)
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
        const {
            cachedData,
            deals,
            users,
            customers,
            custom_fields,
            isOpen,
            error_message,
            success_message,
            show_success,
            currentInvoices,
            pageLimit
        } = this.state
        const { start_date, end_date } = this.state.filters
        const fetchUrl = `/api/deals?start_date=${start_date}&end_date=${end_date}`
        const { error, view } = this.state

        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable-large fixed-margin-datatable-large-mobile'

        const addButton = customers.length && users.length ? <AddDeal
            custom_fields={custom_fields}
            modal={true}
            status={1}
            customers={customers}
            users={users}
            action={this.addUserToState}
            deals={cachedData}
        /> : null

        const total = deals.length

        return customers.length ? (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <DealFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.addUserToState}
                                    setFilterOpen={this.setFilterOpen.bind(this)} customers={customers}
                                    users={users}
                                    deals={deals}
                                    filters={this.state.filters} filter={this.filterDeals}
                                    saveBulk={this.saveBulk}/>
                                {addButton}
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
                                    columnMapping={{ customer_id: 'CUSTOMER', status_name: 'Status' }}
                                    default_columns={getDefaultTableFields()}
                                    customers={customers}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Deal"
                                    bulk_save_url="/api/deals/bulk"
                                    view={view}
                                    disableSorting={['id']}
                                    defaultColumn='name'
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
