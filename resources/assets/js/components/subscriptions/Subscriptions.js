import React, { Component } from 'react'
import axios from 'axios'
import AddSubscription from './edit/AddSubscription'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import SubscriptionFilters from './SubscriptionFilters'
import SubscriptionItem from './SubscriptionItem'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import { getDefaultTableFields } from '../presenters/TokenPresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class Subscriptions extends Component {
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
            subscriptions: [],
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
        this.filterSubscriptions = this.filterSubscriptions.bind(this)
    }

    addUserToState (subscriptions) {
        const should_filter = !this.state.cachedData.length
        const cachedData = !this.state.cachedData.length ? subscriptions : this.state.cachedData

        if (should_filter) {
            subscriptions = filterStatuses(subscriptions, '', this.state.filters)
        }

        this.setState({
            subscriptions: subscriptions,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(subscriptions.length / this.state.pageLimit)
            this.onPageChanged({
                invoices: subscriptions,
                currentPage: this.state.currentPage,
                totalPages: totalPages
            })
        })
    }

    filterSubscriptions (filters) {
        this.setState({ filters: filters })
    }

    resetFilters () {
        this.props.reset()
    }

    onPageChanged (data) {
        let { subscriptions, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            subscriptions = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = subscriptions.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    userList (props) {
        const { pageLimit, currentInvoices, subscriptions } = this.state
        return <SubscriptionItem showCheckboxes={props.showCheckboxes} subscriptions={currentInvoices}
            show_list={props.show_list} entities={subscriptions}
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

    handleClose () {
        this.setState({ error: '', show_success: false })
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
        const { cachedData, view, subscriptions, error, isOpen, error_message, success_message, show_success, currentInvoices, currentPage, totalPages, pageLimit } = this.state
        const fetchUrl = `/api/subscriptions?start_date=${start_date}&end_date=${end_date} `
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = subscriptions.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <SubscriptionFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.onPageChanged.bind(this)}
                                    setFilterOpen={this.setFilterOpen.bind(this)}
                                    subscriptions={subscriptions}
                                    updateIgnoredColumns={this.updateIgnoredColumns}
                                    filters={this.state.filters} filter={this.filterSubscriptions}
                                    saveBulk={this.saveBulk}
                                    ignoredColumns={this.state.ignoredColumns}/>

                                <AddSubscription
                                    subscriptions={cachedData}
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
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Subscription"
                                    bulk_save_url="/api/subscriptions/bulk"
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
