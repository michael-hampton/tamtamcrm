/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
import axios from 'axios'
import AddCustomer from './AddCustomer'
import EditCustomer from './EditCustomer'
import { Button } from 'reactstrap'
import Directory from '../common/Directory'
import Avatar from '../common/Avatar'

export default class Customers extends Component {
    constructor (props) {
        super(props)
        this.state = {
            per_page: 5,
            customers: []
        }

        this.updateCustomers = this.updateCustomers.bind(this)
        this.customerList = this.customerList.bind(this)
    }

    updateCustomers (customers) {
        this.setState({ customers: customers })
    }

    customerList () {
        if (this.state.customers && this.state.customers.length) {
            const list = this.state.customers.map(customer => {
                return (
                    <li className="list-group-item">
                        <div className="row w-100">
                            <div className="col-12 col-sm-6 col-md-3 px-0">
                                {/* <img src="http://demos.themes.guide/bodeo/assets/images/users/m101.jpg" alt="Mike Anamendolla" className="rounded-circle mx-auto d-block img-fluid" /> */}
                                <Avatar lg={true} name={customer.name}/>

                            </div>
                            <div className="col-12 col-sm-6 col-md-9 text-center text-sm-left">
                                <span className="float-right pulse" title="online now">
                                    <EditCustomer
                                        id={customer.id}
                                        action={this.updateCustomers}
                                        customers={this.state.customers}
                                        modal={true}
                                    />

                                    <Button color="danger"
                                        onClick={() => this.deleteCustomer(customer.id)}>Delete</Button>

                                </span>
                                <label className="name lead">{customer.name}</label>
                                <br/>
                                <span className="fa fa-map-marker fa-fw text-muted" data-toggle="tooltip" title=""
                                    data-original-title={this.displayCustomerAddress(customer.address)}/>
                                <span className="text-muted">{this.displayCustomerAddress(customer.address)}</span>
                                <br/>
                                <span className="fa fa-phone fa-fw text-muted" data-toggle="tooltip" title=""
                                    data-original-title={this.displayCustomerPhone(customer.address)}/>
                                <span
                                    className="text-muted small">{this.displayCustomerPhone(customer.address)}</span>
                                <br/>
                                <span className="fa fa-envelope fa-fw text-muted" data-toggle="tooltip"
                                    data-original-title="" title=""/>
                                <span className="text-muted small text-truncate">{customer.email}</span>
                            </div>
                        </div>
                    </li>
                )
            })

            return (
                <div className="col-12 mt-3">
                    <div className="card card-default" id="card_contacts">
                        <div id="contacts" className="panel-collapse collapse show">
                            <ul className="pull-down list-group" id="contact-list">
                                {list}
                            </ul>
                        </div>
                    </div>
                </div>
            )
        } else {
            return <p className="text-center">No Records Found.</p>
        }
    }

    deleteCustomer (id) {
        axios.delete(`/api/customers/${id}`).then(data => {
            const arrCustomers = [...this.state.customers]
            const index = arrCustomers.findIndex(customer => customer.id === id)
            arrCustomers.splice(index, 1)
            this.updateCustomers(arrCustomers)
        })
    }

    displayCustomerAddress (address) {
        if (!address) {
            return ''
        }

        if (address.address_2) {
            return `${address.address_1}, ${address.address_2}, ${address.zip}, ${address.city}`
        }

        return `${address.address_1}, ${address.zip}, ${address.city}`
    }

    displayCustomerPhone (address) {
        if (!address) {
            return (<span/>)
        }

        return (<span key={address.id}>{address.phone}</span>)
    }

    render () {
        const fetchUrl = '/api/customers/'

        return (
            <div className="data-table m-md-3 m-0">

                <AddCustomer
                    action={this.updateCustomers}
                    customers={this.state.customers}
                />

                <Directory
                    userList={this.customerList}
                    fetchUrl={fetchUrl}
                    updateState={this.updateCustomers}
                />
            </div>
        )
    }
}
