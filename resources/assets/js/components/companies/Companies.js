import React, { Component } from 'react'
import AddCompany from './edit/AddCompany'
import DataTable from '../common/DataTable'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import CompanyFilters from './CompanyFilters'
import CompanyItem from './CompanyItem'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import UserRepository from '../repositories/UserRepository'
import { getDefaultTableFields } from '../presenters/CompanyPresenter'
import PaginationNew from '../common/PaginationNew'

export default class Companies extends Component {
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
            brands: [],
            bulk: [],
            cachedData: [],
            errors: [],
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.success_message,
            dropdownButtonActions: ['download'],
            error: '',
            view: {
                ignore: ['assigned_to', 'country_id', 'currency_id', 'industry_id', 'user_id'],
                viewMode: false,
                viewedId: null,
                title: null
            },
            filters: {
                status_id: 'active',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            custom_fields: [],
            showRestoreButton: false
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterCompanies = this.filterCompanies.bind(this)
    }

    componentDidMount () {
        this.getUsers()
        this.getCustomFields()
    }

    addUserToState (brands) {
        const cachedData = !this.state.cachedData.length ? brands : this.state.cachedData
        this.setState({ brands: brands, cachedData: cachedData }, () => {
            const totalPages = Math.ceil(brands.length / this.state.pageLimit)
            this.onPageChanged({ invoices: brands, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    onPageChanged (data) {
        let { brands, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            brands = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = brands.slice(offset, offset + pageLimit)

        this.setState({ currentPage, currentInvoices, totalPages })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    filterCompanies (filters) {
        this.setState({ filters: filters })
    }

    userList (props) {
        const { pageLimit, custom_fields, users, currentInvoices, brands } = this.state
        return <CompanyItem showCheckboxes={props.showCheckboxes} brands={currentInvoices} users={users}
            show_list={props.show_list} entities={brands}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit}
            custom_fields={custom_fields}
            ignoredColumns={props.default_columns} addUserToState={this.addUserToState}
            toggleViewedEntity={props.toggleViewedEntity}
            viewId={props.viewId}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (custom_fields.Company) {
            custom_fields[0] = custom_fields.Company
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/Company')
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
        const { custom_fields, users, error, view, brands, isOpen, error_message, success_message, show_success, currentInvoices, currentPage, totalPages, pageLimit } = this.state
        const { searchText, status_id, start_date, end_date } = this.state.filters
        const fetchUrl = `/api/companies?search_term=${searchText}&status=${status_id}&start_date=${start_date}&end_date=${end_date}`
        const addButton = users.length
            ? <AddCompany brands={brands} users={users} action={this.addUserToState}
                custom_fields={custom_fields}/> : null
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = brands.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <CompanyFilters
                                    pageLimit={pageLimit}
                                    cachedData={this.state.cachedData}
                                    updateList={this.onPageChanged.bind(this)}
                                    setFilterOpen={this.setFilterOpen.bind(this)} brands={brands}
                                    filters={this.state.filters} filter={this.filterCompanies}
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
                                    entity_type="Company"
                                    bulk_save_url="/api/company/bulk"
                                    view={view}
                                    disableSorting={['id']}
                                    defaultColumn='name'
                                    ignore={this.state.ignoredColumns}
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
