import React, { Component } from 'react'
import AddLead from './edit/AddLeadForm'
import DataTable from '../common/DataTable'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import queryString from 'query-string'
import LeadFilters from './LeadFilters'
import LeadItem from './LeadItem'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import UserRepository from '../repositories/UserRepository'
import { getDefaultTableFields } from '../presenters/LeadPresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class Leads extends Component {
    constructor (props) {
        super(props)

        this.state = {
            currentPage: 1,
            totalPages: null,
            pageLimit: !localStorage.getItem('number_of_rows') ? Math.ceil(window.innerHeight / 90) : localStorage.getItem('number_of_rows'),
            currentInvoices: [],
            isMobile: window.innerWidth <= 768,
            isOpen: window.innerWidth > 670,
            leads: [],
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
                customer_id: queryString.parse(this.props.location.search).customer_id || '',
                user_id: queryString.parse(this.props.location.search).user_id || '',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            custom_fields: [],
            showRestoreButton: false
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterLeads = this.filterLeads.bind(this)
    }

    componentDidMount () {
        this.getUsers()
        this.getCustomFields()
    }

    addUserToState (leads) {
        const should_filter = !this.state.cachedData.length
        const cachedData = !this.state.cachedData.length ? leads : this.state.cachedData

        if (should_filter) {
            leads = filterStatuses(leads, '', this.state.filters)
        }

        this.setState({
            leads: leads,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(leads.length / this.state.pageLimit)
            this.onPageChanged({ invoices: leads, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    onPageChanged (data) {
        let { leads, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            leads = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = leads.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    filterLeads (filters) {
        this.setState({ filters: filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    userList (props) {
        const { pageLimit, custom_fields, users, currentInvoices, leads } = this.state
        return <LeadItem showCheckboxes={props.showCheckboxes} leads={currentInvoices} users={users}
            custom_fields={custom_fields}
            show_list={props.show_list} entities={leads}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit}
            viewId={props.viewId}
            ignoredColumns={props.default_columns} addUserToState={this.addUserToState}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.Lead) {
            custom_fields[0] = all_custom_fields.Lead
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/Lead')
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
                alert('error')
            }

            this.setState({ users: response }, () => {
                console.log('users', this.state.users)
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
        const { cachedData, leads, users, custom_fields, view, isOpen, error_message, success_message, show_success, currentInvoices, currentPage, totalPages, pageLimit } = this.state
        const { start_date, end_date } = this.state.filters
        const fetchUrl = `/api/leads?start_date=${start_date}&end_date=${end_date}`
        const { error } = this.state
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = leads.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <LeadFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.onPageChanged.bind(this)}
                                    setFilterOpen={this.setFilterOpen.bind(this)} leads={leads}
                                    filters={this.state.filters} filter={this.filterLeads}
                                    saveBulk={this.saveBulk}/>
                                <AddLead users={users} leads={cachedData} action={this.addUserToState}
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
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Lead"
                                    bulk_save_url="/api/lead/bulk"
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
