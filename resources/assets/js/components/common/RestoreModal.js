import React, { Component } from 'react'
import { Button, DropdownItem, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import axios from 'axios'
import { icons } from '../utils/_icons'
import { translations } from '../utils/_translations'

export default class RestoreModal extends Component {
    constructor (props) {
        super(props)
        this.state = {
            roles: [],
            modal: false
        }

        this.toggle = this.toggle.bind(this)
        this.restoreFunction = this.restoreFunction.bind(this)
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    restoreFunction () {
        const self = this
        axios.post(this.props.url)
            .then(function (response) {
                const arrEntities = [...self.props.entities]
                const index = self.props.entities.findIndex(entity => entity.id === self.props.id)
                arrEntities.splice(index, 1)
                self.props.updateState(arrEntities)
            })
            .catch(function (error) {
                alert(error)
                console.log(error)
            })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <DropdownItem onClick={this.toggle}><i className={`fa ${icons.restore}`}/>{translations.restore}
                </DropdownItem>

                <Modal centered={true} backdrop="static" isOpen={this.state.modal} toggle={this.toggle}
                    className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>{translations.restore.toUpperCase()}</ModalHeader>
                    <ModalBody className={theme}>
                        {translations.restore_message}
                    </ModalBody>
                    <ModalFooter>
                        <Button onClick={() => this.restoreFunction(this.props.id)}
                            color="danger">{translations.yes}</Button>{' '}
                        <Button onClick={this.toggle} color="secondary">{translations.no}</Button>
                    </ModalFooter>
                </Modal>
            </React.Fragment>
        )
    }
}
