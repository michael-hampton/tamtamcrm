import axios from 'axios'
import React from 'react'
import { Form, FormGroup, Label } from 'reactstrap'

class ViewCustomer extends React.Component {
    constructor (props) {
        super(props)
        console.log('task', this.props.task)
        this.state = {
            modal: false,
            id: this.props.task.customer_id,
            customer: {},
            errors: []
        }
    }

    componentDidMount () {
        axios.get(`/api/customers/${this.state.id}`).then(data => {
            const customerData = { ...data.data, ...data.data.addresses[0] }
            delete customerData.addresses
            this.setState({ customer: customerData })
        })
    }

    render () {
        alert(this.state.customer.name)
        return (
            <div>
                <Form>
                    <FormGroup>
                        <Label column sm="4"> Name </Label>
                        <span>{this.state.customer.name}</span>
                    </FormGroup>

                    <FormGroup>
                        <Label column sm="4"> Phone </Label>
                        <span>{this.state.customer.phone} </span>
                    </FormGroup>

                    <FormGroup>
                        <Label column sm="4"> Address 1 </Label>
                        <span>{this.state.customer.address_1} </span>
                    </FormGroup>

                    <FormGroup>
                        <Label column sm="4"> Address 2 </Label>
                        <span>{this.state.customer.address_2} </span>
                    </FormGroup>

                    <FormGroup>
                        <Label column sm="4"> Postcode </Label>
                        <span>{this.state.customer.zip} </span>
                    </FormGroup>

                    <FormGroup>
                        <Label column sm="4"> City </Label>
                        <span>{this.state.customer.city} </span>
                    </FormGroup>

                    <FormGroup>
                        <Label column sm="4"> Company Name </Label>
                        <span>{this.state.customer.company_name} </span>
                    </FormGroup>
                </Form>
            </div>
        )
    }
}

export default ViewCustomer
