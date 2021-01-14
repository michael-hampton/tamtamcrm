import React, { Component } from 'react'
import './Switch.css'
import { Col, FormGroup, Label, Row } from 'reactstrap'

class Switch extends Component {
    render () {
        return (
            <React.Fragment>
                <FormGroup>
                    <Row>
                        <Col sm={10}>
                            <div className="form-check">
                                <Label className="form-check-label col-md-3 align-top">{this.props.label}</Label>
                                <input
                                    name={this.props.name}
                                    checked={this.props.isOn}
                                    onChange={this.props.handleToggle}
                                    className="form-check-input react-switch-checkbox"
                                    id={this.props.name}
                                    type="checkbox"
                                />
                                <label
                                    style={{ background: this.props.isOn && '#06D6A0' }}
                                    className="react-switch-label align-bottom"
                                    htmlFor={this.props.name}
                                >
                                    <span className={'react-switch-button'}/>
                                </label>
                            </div>
                        </Col>
                    </Row>
                </FormGroup>
            </React.Fragment>
        )
    }
}

export default Switch
