import React, { Component } from 'react'
import axios from 'axios'
import AddTaskStatus from './edit/AddTaskStatus'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import TaskStatusFilters from './TaskStatusFilters'
import TaskStatusItem from './TaskStatusItem'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import { getDefaultTableFields } from '../presenters/TaskStatusPresenter'
import PaginationNew from '../common/PaginationNew'

export default class Categories extends Component {
    constructor (props) {
        super(props)

        this.state = {
            currentPage: 1,
            totalPages: null,
            pageLimit: !localStorage.getItem('number_of_rows') ? Math.ceil(window.innerHeight / 90) : localStorage.getItem('number_of_rows'),
            currentInvoices: [],
            isOpen: window.innerWidth > 670,
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.success_message,
            dropdownButtonActions: ['download'],
            statuses: [],
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
                end_date: ''
            }
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterCategories = this.filterCategories.bind(this)
        this.getCustomers = this.getCustomers.bind(this)
    }

    componentDidMount () {
        this.getCustomers()
    }

    addUserToState (statuses) {
        const cachedData = !this.state.cachedData.length ? statuses : this.state.cachedData
        this.setState({
            statuses: statuses,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(statuses.length / this.state.pageLimit)
            this.onPageChanged({ invoices: statuses, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    onPageChanged (data) {
        let { statuses, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            statuses = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = statuses.slice(offset, offset + pageLimit)

        this.setState({ currentPage, currentInvoices, totalPages })
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

    filterCategories (filters) {
        this.setState({ filters: filters })
    }

    resetFilters () {
        this.props.reset()
    }

    userList (props) {
        const { pageLimit, customers, currentInvoices, statuses } = this.state
        return <TaskStatusItem showCheckboxes={props.showCheckboxes} customers={customers} statuses={currentInvoices}
            show_list={props.show_list} entities={statuses}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit}
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
        const { searchText, status, start_date, end_date } = this.state.filters
        const { view, statuses, customers, error, isOpen, error_message, success_message, show_success, currentInvoices, currentPage, totalPages, pageLimit } = this.state
        const fetchUrl = `/api/taskStatus?search_term=${searchText}&status=${status}&start_date=${start_date}&end_date=${end_date} `
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = statuses.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <TaskStatusFilters
                                    pageLimit={pageLimit}
                                    cachedData={this.state.cachedData}
                                    updateList={this.onPageChanged.bind(this)}
                                    setFilterOpen={this.setFilterOpen.bind(this)}
                                    statuses={statuses}
                                    customers={customers}
                                    filters={this.state.filters} filter={this.filterCategories}
                                    saveBulk={this.saveBulk}/>

                                <AddTaskStatus
                                    customers={customers}
                                    statuses={statuses}
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
                                    columnMapping={{ customer_id: 'CUSTOMER' }}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="TaskStatus"
                                    bulk_save_url="/api/expense-statuses/bulk"
                                    view={view}
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
