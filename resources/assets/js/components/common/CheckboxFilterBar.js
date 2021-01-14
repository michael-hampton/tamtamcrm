import React, { Component } from 'react'
import { FormGroup, Input, Label, UncontrolledTooltip } from 'reactstrap'
import { translations } from '../utils/_translations'

export default class CheckboxFilterBar extends Component {
    render () {
        return (
            <React.Fragment>
                <UncontrolledTooltip placement="right" target="selectAll">
                    {translations.select_all}
                </UncontrolledTooltip>

                <div style={{ fontSize: '16px' }} className="d-flex justify-content-between align-items-center">
                    <FormGroup check>
                        <Label check>
                            <Input style={{ height: '16px', width: '16px' }} checked={this.props.isChecked}
                                id="selectAll" onClick={this.props.checkAll} type="checkbox" name="radio1"/>
                            {translations.select_all}
                        </Label>
                    </FormGroup>

                    <span>{this.props.count} {translations.selected}</span>

                    <a onClick={this.props.cancel}>{translations.cancel}</a>
                </div>
            </React.Fragment>
        )
    }
}
