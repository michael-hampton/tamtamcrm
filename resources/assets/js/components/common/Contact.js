import React, { Component } from 'react'
import ContactInputs from './ContactInputs'
import { Button, Form } from 'reactstrap'

export default class Contact extends Component {
    constructor (props) {
        super(props)
        this.state = {
            contacts: this.props.contacts && this.props.contacts.length ? this.props.contacts : [{
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                password: ''
            }]
        }

        this.handleChange = this.handleChange.bind(this)
        this.addContact = this.addContact.bind(this)
        this.removeContact = this.removeContact.bind(this)
    }

    handleChange (e) {
        const contacts = [...this.state.contacts]
        contacts[e.target.dataset.id][e.target.dataset.field] = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({ contacts }, () => this.props.onChange(this.state.contacts))
    }

    addContact (e) {
        this.setState((prevState) => ({
            contacts: [...prevState.contacts, {
                is_primary: false,
                first_name: '',
                last_name: '',
                email: '',
                phone: '',
                password: ''
            }]
        }), () => this.props.onChange(this.state.contacts))
    }

    removeContact (idx) {
        alert(idx)

        this.setState({
            contacts: this.state.contacts.filter(function (contact, sidx) {
                return sidx !== idx
            })
        }, () => this.props.onChange(this.state.contacts))
    }

    render () {
        const { contacts } = this.state

        return contacts.length ? (
            <Form>
                <ContactInputs handleChange={this.handleChange} contacts={contacts} removeContact={this.removeContact}/>
                <Button color="primary" size="lg" block onClick={this.addContact}>Add new contact</Button>
            </Form>
        ) : null
    }
}
