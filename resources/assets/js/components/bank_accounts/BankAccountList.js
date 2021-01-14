import React, { Component } from 'react'
import AddBankAccount from './edit/AddBankAccount'
import DataTable from '../common/DataTable'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import BankAccountFilters from './BankAccountFilters'
import BankAccountItem from './BankAccountItem'
import queryString from 'query-string'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import BankRepository from '../repositories/BankRepository'

export default class BankAccountList extends Component {
    constructor (props) {
        super(props)

        this.state = {
            isOpen: window.innerWidth > 670,
            banks: [],
            bank_accounts: [],
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
                bank_id: queryString.parse(this.props.location.search).bank_id || '',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            custom_fields: [],
            ignoredColumns: [
                'id',
                'bank_id',
                'created_at',
                'deleted_at',
                'updated_at',
                'is_deleted',
                'archived_at',
                'account_id',
                'custom_value1',
                'custom_value2',
                'custom_value3',
                'custom_value4',
                'user_id',
                'assigned_to',
                'private_notes',
                'public_notes'
            ],
            showRestoreButton: false
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterBankAccounts = this.filterBankAccounts.bind(this)
        this.getBanks = this.getBanks.bind(this)
    }

    componentDidMount () {
        this.getBanks()
        // this.getCustomFields()
    }

    addUserToState (bank_accounts) {
        const cachedData = !this.state.cachedData.length ? bank_accounts : this.state.cachedData
        this.setState({
            bank_accounts: bank_accounts,
            cachedData: cachedData
        })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    filterBankAccounts (filters) {
        this.setState({ filters: filters })
    }

    userList (props) {
        const { bank_accounts, custom_fields, banks } = this.state
        return <BankAccountItem showCheckboxes={props.showCheckboxes} bank_accounts={bank_accounts}
            banks={banks}
            custom_fields={custom_fields}
            viewId={props.viewId}
            ignoredColumns={props.ignoredColumns} addUserToState={this.addUserToState}
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

    getBanks () {
        const bankRepository = new BankRepository()
        bankRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ banks: response }, () => {
                console.log('banks', this.state.banks)
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
        const { bank_accounts, custom_fields, ignoredColumns, view, error, isOpen, error_message, success_message, show_success, banks } = this.state
        const { status_id, searchText, start_date, end_date, user_id, bank_id } = this.state.filters
        const fetchUrl = `/api/bank_accounts?search_term=${searchText}&user_id=${user_id}&status=${status_id}&start_date=${start_date}&end_date=${end_date}&bank_id=${bank_id}`
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <BankAccountFilters setFilterOpen={this.setFilterOpen.bind(this)}
                                    bank_accounts={bank_accounts}
                                    updateIgnoredColumns={this.updateIgnoredColumns}
                                    filters={this.state.filters} filter={this.filterBankAccounts}
                                    saveBulk={this.saveBulk}
                                    ignoredColumns={this.state.ignoredColumns}/>
                                <AddBankAccount banks={banks} bank_accounts={bank_accounts} action={this.addUserToState}
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
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="BankAccount"
                                    bulk_save_url="/api/bank_accounts/bulk"
                                    view={view}
                                    disableSorting={['id']}
                                    defaultColumn='name'
                                    ignore={ignoredColumns}
                                    userList={this.userList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.addUserToState}
                                />
                            </CardBody>
                        </Card>
                    </div>
                </div>
            </Row>
        )
    }
}
