import React, { Component } from 'react'
import DataTable from '../common/DataTable'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'
import PlanItem from './PlanItem'
import AddPlan from './edit/AddPlan'
import PlanFilters from './PlanFilters'
import { getDefaultTableFields } from '../presenters/PlanPresenter'

export default class Plans extends Component {
    constructor (props) {
        super(props)

        this.state = {
            currentPage: 1,
            totalPages: null,
            pageLimit: !localStorage.getItem('number_of_rows') ? Math.ceil(window.innerHeight / 90) : localStorage.getItem('number_of_rows'),
            currentInvoices: [],
            isOpen: window.innerWidth > 670,
            plans: [],
            cachedData: [],
            dropdownButtonActions: ['download'],
            filters: {
                status_id: 'active',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            view: {
                ignore: [],
                viewMode: false,
                viewedId: null,
                title: null
            },
            errors: [],
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.success_message,
            showRestoreButton: false
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterPlans = this.filterPlans.bind(this)
        this.handleClose = this.handleClose.bind(this)
    }

    addUserToState (plans, do_filter = false, filters = null) {
        const should_filter = !this.state.cachedData.length || do_filter === true
        const cachedData = !this.state.cachedData.length ? plans : this.state.cachedData

        if (should_filter) {
            plans = filterStatuses(plans, '', this.state.filters)
        }

        this.setState({
            filters: filters !== null ? filters : this.state.filters,
            plans: plans,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(plans.length / this.state.pageLimit)
            this.onPageChanged({ invoices: plans, currentPage: this.state.currentPage, totalPages: totalPages })
            // localStorage.setItem('plans', JSON.stringify(plans))
        })
    }

    onPageChanged (data) {
        let { plans, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            plans = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = plans.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    filterPlans (filters) {
        this.setState({ filters: filters })
    }

    resetFilters () {
        this.props.reset()
    }

    userList (props) {
        const { pageLimit, currentInvoices, cachedData } = this.state
        return <PlanItem showCheckboxes={props.showCheckboxes} plans={currentInvoices}
            show_list={props.show_list} entities={cachedData}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit}
            viewId={props.viewId}
            ignoredColumns={props.default_columns} addUserToState={this.addUserToState}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
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
            plan_types,
            cachedData,
            plans,
            error,
            view,
            filters,
            isOpen,
            error_message,
            success_message,
            show_success,
            currentInvoices,
            currentPage,
            totalPages,
            pageLimit
        } = this.state
        const { start_date, end_date } = this.state.filters
        const fetchUrl = `/api/plans?start_date=${start_date}&end_date=${end_date}`
        const addButton = <AddPlan plans={cachedData} action={this.addUserToState}/>
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = plans.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <PlanFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.addUserToState}
                                    setFilterOpen={this.setFilterOpen.bind(this)} plans={plans}
                                    filters={filters} filter={this.filterPlans}
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
                                    entity_type="Plan"
                                    bulk_save_url="/api/plans/bulk"
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
        )
    }
}
