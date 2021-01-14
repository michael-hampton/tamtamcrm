import React, { Component } from 'react'
import {
    Button,
    ListGroup,
    ListGroupItem,
    Modal,
    ModalBody,
    ModalFooter,
    ModalHeader,
    UncontrolledTooltip
} from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'

export default class AboutModal extends Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            check: false,
            errors: [],
            showSuccessMessage: false,
            showErrorMessage: false,
            message: '',
            health_check: {}
        }

        this.toggle = this.toggle.bind(this)
        this.sendMessage = this.sendMessage.bind(this)
        this.healthCheck = this.healthCheck.bind(this)
        this.handleChange = this.handleChange.bind(this)
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    getData () {
        axios.get('/api/support/messages/send')
            .then(function (response) {

            })
            .catch(function (error) {
                alert(error)
                console.log(error)
            })
    }

    healthCheck () {
        axios.get('/api/health_check')
            .then((r) => {
                this.setState({
                    health_check: r.data
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    error: e
                })
            })
    }

    sendMessage () {
        axios.post('/api/support/messages/send', { message: this.state.message, send_logs: this.state.check })
            .then(function (response) {

            })
            .catch(function (error) {
                alert(error)
                console.log(error)
            })
    }

    handleChange (e) {
        this.setState({
            [e.target.name]: e.target.value
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        const color = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
            ? '#fff' : '#000'
        const listClass = localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true' ? 'list-group-item-dark' : ''

        return (
            <React.Fragment>
                <UncontrolledTooltip placement="right" target="contactTooltip">
                    {translations.about}
                </UncontrolledTooltip>

                <i id="contactTooltip" onClick={this.toggle}
                    style={{
                        marginLeft: '12px',
                        marginRight: 'auto',
                        color: color,
                        fontSize: '20px',
                        cursor: 'pointer'
                    }}
                    className="fa fa-question-circle"/>

                <Modal centered={true} backdrop="static" isOpen={this.state.modal} toggle={this.toggle}
                    className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>TamTam CRM</ModalHeader>
                    <ModalBody className={theme}>
                        <div className="text-center">
                            <p>{translations.about_message}</p>
                            <p>{translations.about_link}</p>
                            <p>https://michael-hampton.github.io/tamtam</p>
                        </div>

                        {Object.keys(this.state.health_check).length &&
                        <div className="col-8">
                            <ListGroup className="mt-2 mb-2">
                                {Object.keys(this.state.health_check).map((index) => {
                                    const icon = this.state.health_check[index] === true ? 'text-success fa-check' : 'text-danger fa-times-circle'
                                    return <ListGroupItem
                                        className={`${listClass} d-flex justify-content-between align-items-center`}>{index}
                                        <i style={{ fontSize: '20px' }} className={`fa ${icon}`}/></ListGroupItem>
                                })}
                            </ListGroup>
                        </div>

                        }

                        <Button color="primary" block onClick={this.healthCheck}>
                            {translations.health_check}
                        </Button>
                    </ModalBody>
                    <ModalFooter>
                        <Button onClick={this.toggle} color="secondary">Cancel</Button>
                        <Button onClick={this.sendMessage}
                            color="primary">Upgrade</Button>{' '}
                    </ModalFooter>
                </Modal>
            </React.Fragment>
        )
    }
}
