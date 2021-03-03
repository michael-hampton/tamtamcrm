import React, { Component } from 'react'
import axios from 'axios'
import AddUser from './edit/AddUser'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import UserItem from './UserItem'
import UserFilters from './UserFilters'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import { getDefaultTableFields } from '../presenters/UserPresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class UserList extends Component {
    constructor (props) {
        super(props)
        this.state = {
            currentPage: 1,
            totalPages: null,
            pageLimit: !localStorage.getItem('number_of_rows') ? Math.ceil(window.innerHeight / 90) : localStorage.getItem('number_of_rows'),
            currentInvoices: [],
            isMobile: window.innerWidth <= 768,
            isOpen: window.innerWidth > 670,
            users: [],
            cachedData: [],
            departments: [],
            accounts: [],
            custom_fields: [],
            bulk: [],
            dropdownButtonActions: ['download'],
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.success_message,
            view: {
                ignore: [],
                viewMode: false,
                viewedId: null,
                title: null
            },
            filters: {
                start_date: '',
                end_date: '',
                status: 'active',
                role_id: '',
                department_id: '',
                searchText: ''
            },
            showRestoreButton: false
        }

        this.cachedResults = []
        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterUsers = this.filterUsers.bind(this)
        this.getAccounts = this.getAccounts.bind(this)
        this.getDepartments = this.getDepartments.bind(this)
    }

    componentDidMount () {
        this.getDepartments()
        this.getAccounts()
        this.getCustomFields()
    }

    onPageChanged (data) {
        let { users, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            users = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = users.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        console.log('current', currentInvoices)

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    filterUsers (filters) {
        this.setState({ filters: filters })
    }

    renderErrorFor () {

    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.User) {
            custom_fields[0] = all_custom_fields.User
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/User')
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

    getAccounts () {
        axios.get('/api/accounts')
            .then((r) => {
                console.log('accounts', r.data)
                this.setState({
                    accounts: r.data
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            })
    }

    getDepartments () {
        axios.get('/api/departments')
            .then((r) => {
                this.setState({
                    departments: r.data
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            })
    }

    addUserToState (users) {
        const should_filter = !this.state.cachedData.length
        const cachedData = !this.state.cachedData.length ? users : this.state.cachedData

        if (should_filter) {
            users = filterStatuses(users, '', this.state.filters)
        }

        this.setState({
            users: users,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(users.length / this.state.pageLimit)
            this.onPageChanged({ invoices: users, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    userList (props) {
        const { pageLimit, departments, custom_fields, accounts, currentInvoices, users } = this.state
        return <UserItem showCheckboxes={props.showCheckboxes} accounts={accounts} departments={departments}
            show_list={props.show_list} entities={users}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit}
            viewId={props.viewId}
            users={currentInvoices} custom_fields={custom_fields}
            ignoredColumns={props.default_columns} addUserToState={this.addUserToState}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
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
        const { cachedData, users, departments, custom_fields, error, view, filters, isOpen, error_message, success_message, show_success, currentInvoices, currentPage, totalPages, pageLimit } = this.state
        const { start_date, end_date } = this.state.filters
        const fetchUrl = `/api/users?start_date=${start_date}&end_date=${end_date}`
        const addButton = <AddUser add={true} accounts={this.state.accounts} custom_fields={custom_fields}
            departments={departments}
            users={cachedData}
            action={this.addUserToState}/>
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable-large fixed-margin-datatable-large-mobile'
        const total = users.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <UserFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.onPageChanged.bind(this)}
                                    setFilterOpen={this.setFilterOpen.bind(this)} users={users}
                                    departments={departments}
                                    filters={filters} filter={this.filterUsers}
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
                                    default_columns={getDefaultTableFields()}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="User"
                                    bulk_save_url="/api/user/bulk"
                                    view={view}
                                    disableSorting={['id']}
                                    defaultColumn='last_name'
                                    userList={this.userList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.addUserToState}
                                />

                                {total > 0 &&
                                <div className="d-flex flex-row py-4 align-items-center">
                                    <PaginationNew totalRecords={total} pageLimit={parseInt(pageLimit)}
                                        pageNeighbours={1} onPageChanged={this.onPageChanged.bind(this)}/>
                                </div>
                                }
                            </CardBody>
                        </Card>
                    </div>
                </div>
            </Row>
        )
    }
}
