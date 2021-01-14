import React, { Component } from 'react'
import DataTable from '../common/DataTable'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DealFilters from './DealFilters'
import DealItem from './DealItem'
import AddDeal from './edit/AddDeal'
import queryString from 'query-string'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import CustomerRepository from '../repositories/CustomerRepository'
import UserRepository from '../repositories/UserRepository'
import { getDefaultTableFields } from '../presenters/DealPresenter'

export default class DealList extends Component {
    constructor (props) {
        super(props)

        this.state = {
            isMobile: window.innerWidth <= 768,
            isOpen: window.innerWidth > 670,
            dropdownButtonActions: ['download'],
            deals: [],
            users: [],
            customers: [],
            errors: [],
            kanban: false,
            view: {
                ignore: [],
                viewMode: false,
                viewedId: null,
                title: null
            },
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.success_message,
            filters: {
                // project_id: queryString.parse(this.props.location.search).project_id || '',
                status_id: 'active',
                task_status_id: '',
                customer_id: queryString.parse(this.props.location.search).customer_id || '',
                user_id: queryString.parse(this.props.location.search).user_id || '',
                // task_type: '',
                searchText: '',
                start_date: '',
                end_date: ''
            },
            custom_fields: [],
            showRestoreButton: false
        }

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterDeals = this.filterDeals.bind(this)
        this.getCustomers = this.getCustomers.bind(this)
        this.getUsers = this.getUsers.bind(this)
    }

    componentDidMount () {
        this.getUsers()
        this.getCustomers()
        this.getCustomFields()
    }

    addUserToState (deals) {
        this.setState({ deals: deals })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    filterDeals (filters) {
        this.setState({ filters: filters })

        return true
    }

    userList (props) {
        const { deals, custom_fields, users, customers } = this.state

        return <DealItem showCheckboxes={props.showCheckboxes} action={this.addUserToState} deals={deals} users={users}
            show_list={props.show_list}
            custom_fields={custom_fields} customers={customers}
            viewId={props.viewId}
            ignoredColumns={props.ignoredColumns} addUserToState={this.addUserToState}
            toggleViewedEntity={props.toggleViewedEntity}
            bulk={props.bulk}
            onChangeBulk={props.onChangeBulk}/>
    }

    getCustomFields () {
        const all_custom_fields = JSON.parse(localStorage.getItem('custom_fields'))
        const custom_fields = []

        if (all_custom_fields.Deal) {
            custom_fields[0] = all_custom_fields.Deal
        }

        this.setState({
            custom_fields: custom_fields
        })

        /* axios.get('api/accounts/fields/Deal')
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

    getCustomers () {
        const customerRepository = new CustomerRepository()
        customerRepository.get().then(response => {
            if (!response) {
                alert('error')
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
        const { deals, users, customers, custom_fields, isOpen, error_message, success_message, show_success } = this.state
        const { task_status, customer_id, user_id, searchText, start_date, end_date } = this.state.filters
        const fetchUrl = `/api/deals?search_term=${searchText}&task_status=${task_status}&customer_id=${customer_id}&user_id=${user_id}&start_date=${start_date}&end_date=${end_date}`
        const { error, view } = this.state

        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable-large fixed-margin-datatable-large-mobile'

        const addButton = customers.length && users.length ? <AddDeal
            custom_fields={custom_fields}
            modal={true}
            status={1}
            customers={customers}
            users={users}
            action={this.addUserToState}
            deals={deals}
        /> : null

        return customers.length ? (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <DealFilters setFilterOpen={this.setFilterOpen.bind(this)} customers={customers}
                                    users={users}
                                    deals={deals}
                                    filters={this.state.filters} filter={this.filterDeals}
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
                                    customers={customers}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Deal"
                                    bulk_save_url="/api/deals/bulk"
                                    view={view}
                                    disableSorting={['id']}
                                    defaultColumn='name'
                                    userList={this.userList}
                                    fetchUrl={fetchUrl}
                                    updateState={this.addUserToState}
                                />
                            </CardBody>
                        </Card>
                    </div>
                </div>
            </Row>
        ) : null
    }
}
