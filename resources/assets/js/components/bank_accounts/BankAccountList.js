import React, { Component } from 'react'
import axios from 'axios'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import PaginationNew from '../common/PaginationNew'
import { filterStatuses } from '../utils/_search'
import BankAccountItem from './BankAccountItem'
import BankAccountFilters from './BankAccountFilters'
import AddBankAccount from './edit/AddBankAccount'
import { getDefaultTableFields } from '../presenters/BankAccountPresenter'

export default class Tokens extends Component {
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
            bank_accounts: [],
            users: [],
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
        this.filterTokens = this.filterTokens.bind(this)
        this.getUsers = this.getUsers.bind(this)
    }

    componentDidMount () {
        this.getUsers()
    }

    addUserToState (bank_accounts) {
        const should_filter = !this.state.cachedData.length
        const cachedData = !this.state.cachedData.length ? bank_accounts : this.state.cachedData

        if (should_filter) {
            bank_accounts = filterStatuses(bank_accounts, '', this.state.filters)
        }

        this.setState({
            bank_accounts: bank_accounts,
            cachedData: cachedData
        }, () => {
            const totalPages = Math.ceil(bank_accounts.length / this.state.pageLimit)
            this.onPageChanged({
                invoices: bank_accounts,
                currentPage: this.state.currentPage,
                totalPages: totalPages
            })
        })
    }

    onPageChanged (data) {
        let { bank_accounts, pageLimit } = this.state
        const { currentPage, totalPages } = data

        if (data.invoices) {
            bank_accounts = data.invoices
        }

        const offset = (currentPage - 1) * pageLimit
        const currentInvoices = bank_accounts.slice(offset, offset + pageLimit)
        const filters = data.filters ? data.filters : this.state.filters

        this.setState({ currentPage, currentInvoices, totalPages, filters })
    }

    filterTokens (filters) {
        this.setState({ filters: filters })
    }

    resetFilters () {
        this.props.reset()
    }

    userList (props) {
        const { pageLimit, users, currentInvoices, bank_accounts } = this.state
        return <BankAccountItem showCheckboxes={props.showCheckboxes} bank_accounts={currentInvoices} users={users}
            viewId={props.viewId} entities={bank_accounts}
            pageLimit={pageLimit}
            show_list={props.show_list}
            onPageChanged={this.onPageChanged.bind(this)}
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
        const { cachedData, view, bank_accounts, error, isOpen, error_message, success_message, show_success, currentInvoices, currentPage, totalPages, pageLimit } = this.state
        const fetchUrl = `/api/bank_accounts?start_date=${start_date}&end_date=${end_date} `
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'
        const total = bank_accounts.length

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <BankAccountFilters
                                    pageLimit={pageLimit}
                                    cachedData={cachedData}
                                    updateList={this.onPageChanged.bind(this)}
                                    setFilterOpen={this.setFilterOpen.bind(this)} bank_accounts={bank_accounts}
                                    updateIgnoredColumns={this.updateIgnoredColumns}
                                    filters={this.state.filters} filter={this.filterTokens}
                                    saveBulk={this.saveBulk} ignoredColumns={this.state.ignoredColumns}/>

                                <AddBankAccount
                                    bank_accounts={bank_accounts}
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
                                    currentData={cachedData}
                                    hide_pagination={true}

                                    default_columns={getDefaultTableFields()}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="BankAccount"
                                    bulk_save_url="/api/bank_accounts/bulk"
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
