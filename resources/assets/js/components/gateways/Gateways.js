import React, { Component } from 'react'
import AddGateway from './edit/AddGateway'
import { Alert, Card, CardBody, Row } from 'reactstrap'
import DataTable from '../common/DataTable'
import GatewayFilters from './GatewayFilters'
import GatewayItem from './GatewayItem'
import Snackbar from '@material-ui/core/Snackbar'
import { translations } from '../utils/_translations'
import queryString from 'query-string'
import GatewayModel from '../models/GatewayModel'
import axios from 'axios'
import CustomerModel from '../models/CustomerModel'
import GroupModel from '../models/GroupModel'
import AccountModel from '../models/AccountModel'

export default class Gateways extends Component {
    constructor (props) {
        super(props)

        this.gatewayModel = new GatewayModel()

        this.state = {
            gateway_ids: this.gatewayModel.gateway_ids.split(','),
            customer_id: queryString.parse(this.props.location.search).customer_id || '',
            group_id: queryString.parse(this.props.location.search).group_id || '',
            isOpen: window.innerWidth > 670,
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.success_message,
            dropdownButtonActions: ['download'],
            gateways: [],
            cachedData: [],
            view: {
                ignore: [],
                viewMode: false,
                viewedId: null,
                title: null
            },
            errors: [],
            ignoredColumns: ['gateway', 'id', 'config', 'fees_and_limits', 'account_id', 'user_id', 'updated_at', 'status', 'deleted_at', 'created_at', 'show_billing_address', 'show_shipping_address', 'require_cvv', 'accepted_credit_cards', 'update_details'],
            filters: {
                searchText: '',
                status: 'active',
                start_date: '',
                end_date: ''
            }
        }

        this.account_id = JSON.parse(localStorage.getItem('appState')).user.account_id

        this.addUserToState = this.addUserToState.bind(this)
        this.userList = this.userList.bind(this)
        this.filterGateways = this.filterGateways.bind(this)
        this.setList = this.setList.bind(this)
        this.removeFromList = this.removeFromList.bind(this)
        this.save = this.save.bind(this)
        this.loadCustomer = this.loadCustomer.bind(this)
        this.loadGroup = this.loadGroup.bind(this)
        this.loadAccount = this.loadAccount.bind(this)
    }

    componentDidMount () {
        if (this.state.customer_id.length) {
            this.loadCustomer()
        } else if (this.state.group_id.length) {
            this.loadGroup()
        } else {
            this.loadAccount()
        }
    }

    loadCustomer () {
        axios.get(`/api/customers/${this.state.customer_id}`)
            .then((r) => {
                console.log('data', r.data)
                this.model = new CustomerModel(r.data)
                this.setState({ gateway_ids: this.model.gateways })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            })
    }

    loadGroup () {
        axios.get(`/api/group/${this.state.group_id}`)
            .then((r) => {
                console.log('data', r.data)
                this.model = new GroupModel(r.data)
                this.setState({ gateway_ids: this.model.gateways })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            })
    }

    loadAccount () {
        axios.get(`/api/accounts/${this.account_id}`)
            .then((r) => {
                this.model = new AccountModel(r.data)
                this.setState({ gateway_ids: this.model.gateways })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            })
    }

    addUserToState (gateways) {
        const cachedData = !this.state.cachedData.length ? gateways : this.state.cachedData
        this.setState({
            gateways: gateways,
            cachedData: cachedData
        })
    }

    save () {
        this.model.saveSettings().then(response => {
            if (!response) {
                this.setState({
                    showErrorMessage: true,
                    loading: false,
                    errors: this.model.errors,
                    message: this.model.error_message
                })
            }
        })
    }

    filterGateways (filters) {
        this.setState({ filters: filters })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    resetFilters () {
        this.props.reset()
    }

    userList (props) {
        const { gateways, customer_id, group_id, gateway_ids } = this.state

        return <GatewayItem removeFromList={this.removeFromList}
            isFiltered={this.state.customer_id.length || this.state.group_id.length}
            setList={this.setList}
            gateway_ids={gateway_ids}
            showCheckboxes={props.showCheckboxes}
            gateways={gateways}
            viewId={props.viewId}
            customer_id={customer_id}
            group_id={group_id}
            ignoredColumns={props.ignoredColumns} addUserToState={this.addUserToState}
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

    arraysEqual (arr1, arr2) {
        if (arr1.length !== arr2.length) {
            return false
        }
        for (var i = arr1.length; i--;) {
            if (arr1[i] != arr2[i]) {
                return false
            }
        }

        return true
    }

    removeFromList (gateway, archive = false) {
        const gateway_ids = this.model.removeGateway(gateway)

        this.setState({ gateway_ids: gateway_ids }, () => {
            setTimeout(() => {
                this.save()
            }, 2000)
        })
    }

    setList (list) {
        const ids = []
        list.map(gateway => {
            ids.push(gateway.id)
        })

        const has_changed = this.arraysEqual(ids, this.state.gateway_ids)

        this.setState({ gateway_ids: ids }, () => {
            if (has_changed) {
                return
            }

            this.model.gateway_ids = ids
            console.log('ids', ids)

            setTimeout(() => {
                this.save()
            }, 2000)
        })
    }

    render () {
        const { searchText, error } = this.state.filters
        const { view, gateways, isOpen, customer_id, group_id, error_message, success_message, show_success } = this.state
        const fetchUrl = `/api/company_gateways?search_term=${searchText} `
        const margin_class = isOpen === false || (Object.prototype.hasOwnProperty.call(localStorage, 'datatable_collapsed') && localStorage.getItem('datatable_collapsed') === true)
            ? 'fixed-margin-datatable-collapsed'
            : 'fixed-margin-datatable fixed-margin-datatable-mobile'

        return (
            <Row>
                <div className="col-12">
                    <div className="topbar">
                        <Card>
                            <CardBody>
                                <GatewayFilters setFilterOpen={this.setFilterOpen.bind(this)} gateways={gateways}
                                    updateIgnoredColumns={this.updateIgnoredColumns}
                                    filters={this.state.filters} filter={this.filterGateways}
                                    saveBulk={this.saveBulk} ignoredColumns={this.state.ignoredColumns}/>

                                <AddGateway
                                    customer_id={customer_id}
                                    group_id={group_id}
                                    gateways={gateways}
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
                        {customer_id &&
                        <Alert color="info">
                            {translations.filtered_by_customer}
                        </Alert>
                        }

                        <Card>
                            <CardBody>
                                <DataTable
                                    hide_table={true}
                                    setSuccess={this.setSuccess.bind(this)}
                                    setError={this.setError.bind(this)}
                                    columnMapping={{ customer_id: 'CUSTOMER' }}
                                    dropdownButtonActions={this.state.dropdownButtonActions}
                                    entity_type="Gateway"
                                    bulk_save_url="/api/gateways/bulk"
                                    view={view}
                                    ignore={this.state.ignoredColumns}
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
