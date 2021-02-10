import React, { Component } from 'react'
import EditPurchaseOrder from './edit/EditPurchaseOrder'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import PurchaseOrderItem from './PurchaseOrderItem'
import PurchaseOrderFilters from './PurchaseOrderFilters'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import CompanyRepository from '../repositories/CompanyRepository'
import queryString from 'query-string'
import { getDefaultTableFields } from '../presenters/PurchaseOrderPresenter'
import PaginationNew from '../common/PaginationNew'

export default class PurchaseOrders extends Component {
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
            per_page: 5,
            view: {
                ignore: ['user_id', 'next_send_date', 'updated_at', 'use_inclusive_taxes', 'last_sent_date', 'uses_inclusive_taxes', 'line_items', 'next_sent_date', 'first_name', 'last_name', 'design_id', 'status_id', 'custom_surcharge_tax1', 'custom_surcharge_tax2'],
                viewMode: false,
                viewedId: null,
                title: null
            },
            purchase_orders: [],
            cachedData: [],
            companies: [],
            custom_fields: [],
            bulk: [],
            dropdownButtonActions: ['email', 'download', 'clone_quote_to_invoice'],
            filters: {
                status_id: 'active',
                user_id: queryString.parse(this.props.location.search).user_id || '',
                company_id: queryString.parse(this.props.location.search).company_id || '',
                project_id: queryString.parse(this.props.location.search).project_id || '',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            showRestoreButton: false,
            entity_id: queryString.parse(this.props.location.search).entity_id || false,
            entity_type: queryString.parse(this.props.location.search).entity_type || false
        }

        this.updateInvoice = this.updateInvoice.bind(this)
        this.userList = this.userList.bind(this)
        this.filterInvoices = this.filterInvoices.bind(this)
    }

    componentDidMount () {
        this.getCompanies()
        this.getCustomFields()
    }

    updateInvoice (purchase_orders) {
        const cachedData = !this.state.cachedData.length ? purchase_orders : this.state.cachedData
        this.setState({
            purchase_orders: purchase_orders,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(purchase_orders.length / this.state.pageLimit)
            this.onPageChanged({
                invoices: purchase_orders,
                currentPage: this.state.currentPage,
                totalPages: totalPages
            })
        })
    }

    onPageChanged (data) {
        let { purchase_orders, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            purchase_orders = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = purchase_orders.slice(offset, offset + pageLimit)

        this.setState({ currentPage, currentInvoices, totalPages })
    }

    filterInvoices (filters) {
        this.setState({ filters: filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    userList (props) {
        const { pageLimit, custom_fields, companies, currentInvoices, purchase_orders } = this.state
        return <PurchaseOrderItem showCheckboxes={props.showCheckboxes} purchase_orders={currentInvoices}
            show_list={props.show_list}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit}
            companies={companies}
            entities={purchase_orders}
            custom_fields={custom_fields}
            viewId={props.viewId}
            ignoredColumns={props.default_columns} updateInvoice={this.updateInvoice}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
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
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.PurchaseOrder) {
            custom_fields[0] = all_custom_fields.PurchaseOrder
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/PurchaseOrder')
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
        const { purchase_orders, custom_fields, companies, view, filters, error, isOpen, error_message, success_message, show_success, currentInvoices, currentPage, totalPages, pageLimit } = this.state
        const { status_id, company_id, searchText, start_date, end_date, project_id, user_id } = this.state.filters
        const fetchUrl = `/api/purchase_order?search_term=${searchText}&user_id=${user_id}&status=${status_id}&company_id=${company_id}&project_id=${project_id}&start_date=${start_date}&end_date=${end_date}`
        const addButton = companies.length ? <EditPurchaseOrder
            entity_id={this.state.entity_id}
            entity_type={this.state.entity_type}
            custom_fields={custom_fields}
            companies={companies}
            invoice={{}}
            add={true}
            action={this.updateInvoice}
            invoices={purchase_orders}
            modal={true}
        /> : null
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = purchase_orders.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <PurchaseOrderFilters
                                    pageLimit={pageLimit}
                                    cachedData={this.state.cachedData}
                                    updateList={this.onPageChanged.bind(this)}
                                    setFilterOpen={this.setFilterOpen.bind(this)}
                                    purchase_orders={purchase_orders}
                                    companies={companies}
                                    filters={filters} filter={this.filterInvoices}
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
                                    companies={companies}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="PurchaseOrder"
                                    bulk_save_url="/api/purchase_order/bulk"
                                    view={view}
                                    columnMapping={{ status_id: 'STATUS', company_id: 'COMPANY' }}
                                    disableSorting={['id']}
                                    defaultColumn='number'
                                    userList={this.userList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.updateInvoice}
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
