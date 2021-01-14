import React, { Component } from 'react'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader, UncontrolledTooltip } from 'reactstrap'
import { icons } from '../utils/_icons'
import { translations } from '../utils/_translations'

export default class LearnMoreModal extends Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false
        }

        this.toggle = this.toggle.bind(this)
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <UncontrolledTooltip placement="right" target="UncontrolledTooltipExample">
                    {translations.learn_more}
                </UncontrolledTooltip>

                <span id="UncontrolledTooltipExample" onClick={this.toggle}><i
                    className={`fa ${icons.help}`}/>{translations.learn_more}
                </span>

                <Modal centered={true} backdrop="static" isOpen={this.state.modal} toggle={this.toggle}
                    className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>{translations.restore.toUpperCase()}</ModalHeader>
                    <ModalBody className={theme}>
                        {this.props.content}
                    </ModalBody>
                    <ModalFooter>
                        <Button onClick={this.toggle} color="secondary">{translations.close}</Button>
                    </ModalFooter>
                </Modal>
            </React.Fragment>
        )
    }
}

export class LearnMoreUrl extends Component {
    render () {
        return (
            <React.Fragment>
                <UncontrolledTooltip placement="right" target="UncontrolledTooltipExample">
                    {translations.learn_more}
                </UncontrolledTooltip>

                <span id="UncontrolledTooltipExample" onClick={(e) => {
                    e.preventDefault()
                    window.location.href = this.props.url
                }}
                > <i className={`fa ${icons.help}`}/></span>
            </React.Fragment>
        )
    }
}
