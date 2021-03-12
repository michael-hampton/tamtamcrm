import React, { Component } from 'react'
import AddProject from './edit/AddStory'
import DataTable from '../common/DataTable'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import ProjectFilters from './ProjectFilters'
import ProjectItem from './ProjectItem'
import queryString from 'query-string'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import CustomerRepository from '../repositories/CustomerRepository'
import { getDefaultTableFields } from '../presenters/ProjectPresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class ProjectList extends Component {
    constructor (props) {
        super(props)

        this.state = {
            currentPage: 1,
            totalPages: null,
            pageLimit: !localStorage.getItem('number_of_rows') ? Math.ceil(window.innerHeight / 90) : localStorage.getItem('number_of_rows'),
            currentInvoices: [],
            isMobile: window.innerWidth <= 768,
            isOpen: window.innerWidth > 670,
            customers: [],
            projects: [],
            cachedData: [],
            errors: [],
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
                status_id: 'active',
                user_id: queryString.parse(this.props.location.search).user_id || '',
                customer_id: queryString.parse(this.props.location.search).customer_id || '',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            custom_fields: [],
            showRestoreButton: false
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterProjects = this.filterProjects.bind(this)
    }

    componentDidMount () {
        this.getCustomers()
        this.getCustomFields()
    }

    addUserToState (projects, do_filter = false) {
        const should_filter = !this.state.cachedData.length || do_filter === true
        const cachedData = !this.state.cachedData.length ? projects : this.state.cachedData

        if (should_filter) {
            projects = filterStatuses(projects, '', this.state.filters)
        }

        this.setState({
            projects: projects,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(projects / this.props.pageLimit)
            this.onPageChanged({ invoices: projects, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    onPageChanged (data) {
        let { projects, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            projects = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = projects.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    filterProjects (filters) {
        this.setState({ filters: filters })
    }

    userList (props) {
        const { cachedData, custom_fields, customers, currentInvoices } = this.state
        return <ProjectItem showCheckboxes={props.showCheckboxes} projects={currentInvoices} customers={customers}
            show_list={props.show_list} entities={cachedData}
            custom_fields={custom_fields}
            viewId={props.viewId}
            ignoredColumns={props.default_columns} addUserToState={this.addUserToState}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.Project) {
            custom_fields[0] = all_custom_fields.Project
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/Project')
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
        const { cachedData, projects, customers, custom_fields, view, error, isOpen, error_message, success_message, show_success, currentInvoices, currentPage, totalPages, pageLimit } = this.state
        const { start_date, end_date } = this.state.filters
        const fetchUrl = `/api/projects?start_date=${start_date}&end_date=${end_date}`
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = projects.length

        return this.state.customers.length ? (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <ProjectFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.onPageChanged.bind(this)}
                                    setFilterOpen={this.setFilterOpen.bind(this)} customers={customers}
                                    projects={projects}
                                    filters={this.state.filters} filter={this.filterProjects}
                                    saveBulk={this.saveBulk}/>
                                <AddProject customers={customers} projects={cachedData} action={this.addUserToState}
                                    custom_fields={custom_fields}/>
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
                                    customers={customers}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Project"
                                    bulk_save_url="/api/project/bulk"
                                    view={view}
                                    columnMapping={{ customer_id: 'CUSTOMER' }}
                                    disableSorting={['id']}
                                    defaultColumn='name'
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
        ) : null
    }
}
