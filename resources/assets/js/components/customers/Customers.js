import React, { Component } from 'react'
import axios from 'axios'
import AddCustomer from './edit/AddCustomer'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import CustomerFilters from './CustomerFilters'
import CustomerItem from './CustomerItem'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import queryString from 'query-string'
import CompanyRepository from '../repositories/CompanyRepository'
import { getDefaultTableFields } from '../presenters/CustomerPresenter'

export default class Customers extends Component {
    constructor (props) {
        super(props)
        this.state = {
            isMobile: window.innerWidth <= 768,
            isOpen: window.innerWidth > 670,
            per_page: 5,
            view: {
                viewMode: false,
                viewedId: null,
                title: null
            },
            customers: [],
            cachedData: [],
            companies: [],
            bulk: [],
            dropdownButtonActions: ['download'],
            filters: {
                status: 'active',
                company_id: '',
                group_settings_id: queryString.parse(this.props.location.search).group_settings_id || '',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            custom_fields: [],
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.success_message,
            showRestoreButton: false
        }

        this.updateCustomers = this.updateCustomers.bind(this)
        this.customerList = this.customerList.bind(this)
        this.getCompanies = this.getCompanies.bind(this)
        this.filterCustomers = this.filterCustomers.bind(this)
    }

    componentDidMount () {
        this.getCompanies()
        this.getCustomFields()
    }

    updateCustomers (customers) {
        const cachedData = !this.state.cachedData.length ? customers : this.state.cachedData
        this.setState({
            customers: customers,
            cachedData: cachedData
        })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    getCompanies () {
        const companyRepository = new CompanyRepository()
        companyRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ companies: response }, () => {
                console.log('companies', this.state.companies)
            })
        })
    }

    getCustomFields () {
        axios.get('api/accounts/fields/Customer')
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
            })
    }

    filterCustomers (filters) {
        this.setState({ filters: filters })
    }

    customerList (props) {
        const { customers, custom_fields } = this.state
        return <CustomerItem viewId={props.viewId} showCheckboxes={props.showCheckboxes} customers={customers}
            show_list={props.show_list}
            custom_fields={custom_fields}
            ignoredColumns={props.ignoredColumns} updateCustomers={this.updateCustomers}
            deleteCustomer={this.deleteCustomer} toggleViewedEntity={props.toggleViewedEntity}
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
        const { searchText, status, company_id, group_settings_id, start_date, end_date } = this.state.filters
        const { custom_fields, customers, companies, error, view, filters, isOpen, error_message, success_message, show_success } = this.state
        const fetchUrl = `/api/customers?search_term=${searchText}&status=${status}&company_id=${company_id}&group_settings_id=${group_settings_id}&start_date=${start_date}&end_date=${end_date}`
        const addButton = companies.length ? <AddCustomer
            custom_fields={custom_fields}
            action={this.updateCustomers}
            customers={customers}
            companies={companies}
        /> : null
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable-large fixed-margin-datatable-large-mobile'

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <CustomerFilters setFilterOpen={this.setFilterOpen.bind(this)} companies={companies}
                                    customers={customers}
                                    filters={filters} filter={this.filterCustomers}
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
                                    default_columns={getDefaultTableFields()}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Customer"
                                    bulk_save_url="/api/customer/bulk"
                                    view={view}
                                    disableSorting={['id']}
                                    defaultColumn='name'
                                    userList={this.customerList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.updateCustomers}
                                />
                            </CardBody>
                        </Card>
                    </div>
                </div>
            </Row>
        )
    }
}
