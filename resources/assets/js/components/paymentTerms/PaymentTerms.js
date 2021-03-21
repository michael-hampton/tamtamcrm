import React, { Component } from 'react'
import axios from 'axios'
import AddPaymentTerm from './edit/AddPaymentTerm'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import PaymentTermFilters from './PaymentTermFilters'
import PaymentTermItem from './PaymentTermItem'
import { translations } from '../utils/_translations'
import Snackbar from '@material-ui/core/Snackbar'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'

export default class PaymentTerms extends Component {
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
            paymentTerms: [],
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
        this.filterPaymentTerms = this.filterPaymentTerms.bind(this)
    }

    addUserToState (paymentTerms, do_filter = false, filters = null) {
        const should_filter = !this.state.cachedData.length || do_filter === true
        const cachedData = !this.state.cachedData.length ? paymentTerms : this.state.cachedData

        if (should_filter) {
            paymentTerms = filterStatuses(paymentTerms, '', this.state.filters)
        }

        this.setState({
            filters: filters !== null ? filters : this.state.filters,
            paymentTerms: paymentTerms,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(paymentTerms.length / this.state.pageLimit)
            this.onPageChanged({
                invoices: paymentTerms,
                currentPage: this.state.currentPage,
                totalPages: totalPages
            })
        })
    }

    onPageChanged (data) {
        let { paymentTerms, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            paymentTerms = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = paymentTerms.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    filterPaymentTerms (filters) {
        this.setState({ filters: filters })
    }

    resetFilters () {
        this.props.reset()
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    userList (props) {
        const { pageLimit, currentInvoices, cachedData } = this.state
        return <PaymentTermItem showCheckboxes={props.showCheckboxes} paymentTerms={currentInvoices}
            show_list={props.show_list} entities={cachedData}
            onPageChanged={this.onPageChanged.bind(this)}
            pageLimit={pageLimit}
            ignoredColumns={props.default_columns} addUserToState={this.addUserToState}
            toggleViewedEntity={props.toggleViewedEntity}
            viewId={props.viewId}
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
        const { start_date, end_date } = this.state.filters
        const { cachedData, view, paymentTerms, error, isOpen, error_message, success_message, show_success, currentInvoices, currentPage, totalPages, pageLimit } = this.state
        const fetchUrl = `/api/payment_terms?start_date=${start_date}&end_date=${end_date} `
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = paymentTerms.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <PaymentTermFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.addUserToState}
                                    setFilterOpen={this.setFilterOpen.bind(this)}
                                    paymentTerms={paymentTerms}
                                    filters={this.state.filters} filter={this.filterPaymentTerms}
                                    saveBulk={this.saveBulk}/>

                                <AddPaymentTerm
                                    payment_terms={cachedData}
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

                                    default_columns={['name']}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Group"
                                    bulk_save_url="/api/payment_terms/bulk"
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
