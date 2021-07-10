import React, { Component } from 'react'
import axios from 'axios'
import AddProduct from './edit/AddProduct'
import DataTable from '../common/DataTable'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import ProductItem from './ProductItem'
import ProductFilters from './ProductFilters'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import CompanyRepository from '../repositories/CompanyRepository'
import { getDefaultTableFields } from '../presenters/ProductPresenter'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class ProductList extends Component {
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
                ignore: [],
                viewMode: false,
                viewedId: null,
                title: null
            },
            products: [],
            companies: [],
            cachedData: [],
            categories: [],
            custom_fields: [],
            dropdownButtonActions: ['download'],
            bulk: [],
            filters: {
                status: 'active',
                category_id: '',
                company_id: '',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            showRestoreButton: false
        }

        this.addProductToState = this.addProductToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterProducts = this.filterProducts.bind(this)
        this.getCompanies = this.getCompanies.bind(this)
    }

    componentDidMount () {
        this.getCompanies()
        this.getCategories()
        this.getCustomFields()
    }

    onPageChanged (data) {
        let { products, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            products = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = products.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    addProductToState (products, do_filter = false, filters = null) {
        const should_filter = !this.state.cachedData.length || do_filter === true
        const cachedData = !this.state.cachedData.length ? products : this.state.cachedData

        if (should_filter) {
            products = filterStatuses(products, '', this.state.filters)
        }

        this.setState({
            filters: filters !== null ? filters : this.state.filters,
            products: products,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(products.length / this.state.pageLimit)
            this.onPageChanged({ invoices: products, currentPage: this.state.currentPage, totalPages: totalPages })
        })
    }

    filterProducts (filters) {
        this.setState({ filters: filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    getCompanies () {
        const companyRepository = new CompanyRepository()
        companyRepository.get().then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ companies: response }, () => {
                console.log('companies', this.state.companies)
            })
        })
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.Product) {
            custom_fields[0] = all_custom_fields.Product
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/Product')
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

    getCategories () {
        axios.get('/api/categories')
            .then((r) => {
                this.setState({
                    categories: r.data
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            })
    }

    userList (props) {
        const { pageLimit, custom_fields, companies, categories, currentInvoices, cachedData } = this.state

        return <ProductItem showCheckboxes={props.showCheckboxes} products={currentInvoices} categories={categories}
            show_list={props.show_list} entities={cachedData}
            onPageChanged={this.addProductToState}
            pageLimit={pageLimit}
            viewId={props.viewId}
            companies={companies} custom_fields={custom_fields}
            ignoredColumns={props.default_columns} addProductToState={this.addProductToState}
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
        const { cachedData, products, custom_fields, companies, categories, view, filters, error, isOpen, error_message, success_message, show_success, currentInvoices, pageLimit } = this.state
        const { start_date, end_date } = this.state.filters
        const fetchUrl = `/api/products?start_date=${start_date}&end_date=${end_date}`
        const addButton = companies.length && categories.length ? <AddProduct
            custom_fields={custom_fields}
            companies={companies}
            categories={categories}
            products={cachedData}
            action={this.addProductToState}
        /> : null
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable-large fixed-margin-datatable-large-mobile'
        const total = products.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <ProductFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.onPageChanged.bind(this)}
                                    setFilterOpen={this.setFilterOpen.bind(this)} companies={companies}
                                    products={products}
                                    filters={filters} filter={this.filterProducts}
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
                                    entity_type="Product"
                                    bulk_save_url="/api/product/bulk"
                                    view={view}
                                    disableSorting={['id']}
                                    defaultColumn='name'
                                    userList={this.userList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.addProductToState}
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
