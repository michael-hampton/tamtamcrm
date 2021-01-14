import React, { Component } from 'react'
import { CirclePicker } from 'react-color'
import { FormGroup, Input, Label, Modal, ModalBody, ModalFooter } from 'reactstrap'
import { translations } from '../utils/_translations'

export default class ColorPickerNew extends Component {
    constructor (props) {
        super(props)
        this.state = {
            show: false,
            color: null
        }

        this.toggle = this.toggle.bind(this)
    }

    handleInput (color, event) {
        console.log('selected colour', color.hex)
        // this.props.onChange(color)
        this.setState({ color: color.hex })
    }

    complete () {
        this.props.onChange(this.state.color)
        this.toggle()
    }

    toggle () {
        this.setState({
            show: !this.state.show,
            errors: []
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return <React.Fragment>
            <div className="d-flex justify-content-between">
                <FormGroup className="mb-3">
                    <Label>{this.props.label || translations.colour}  </Label>
                    <Input value={this.state.color} placeholder="#000000" type="text" onChange={(e) => {
                        this.setState({ color: e.target.value })
                    }}/>
                </FormGroup>

                <div className="d-flex d-inline-flex justify-content-between">
                    <div className="mr-2" style={{
                        width: '60px',
                        height: '20px',
                        backgroundColor: `${this.props.color && this.props.color.length ? this.props.color : '#CCC'}`
                    }}/>
                    <a onClick={(e) => {
                        this.setState({ show: !this.state.show })
                    }}><i className="fa fa-paint-brush" style={{ fontSize: '20px' }}/> </a>
                </div>
            </div>

            <Modal isOpen={this.state.show} toggle={this.toggle} className={this.props.className}>
                <ModalBody className={theme}>

                    <CirclePicker color={this.props.color}
                        onChange={this.handleInput.bind(this)}/>

                    <ModalFooter>
                        <a onClick={this.complete.bind(this)}
                            color="danger">{translations.done}</a>
                        <a onClick={this.toggle} color="secondary">{translations.cancel}</a>
                    </ModalFooter>
                </ModalBody>
            </Modal>
        </React.Fragment>
    }
}
