import React, { Component } from 'react'
import { FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'
import { consts } from '../../utils/_consts'

export default class Details extends Component {
    render () {
        return (
            <React.Fragment>
                <FormGroup>
                    <Label for="name">{translations.subject} <span className="text-danger">*</span></Label>
                    <Input className={this.props.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="name"
                        value={this.props.template.name} id="name" placeholder={translations.name}
                        onChange={this.props.handleInput}/>
                    {this.props.renderErrorFor('name')}
                </FormGroup>

                <FormGroup>
                    <Label for="description">{translations.message} </Label>
                    <Input className={this.props.hasErrorFor('description') ? 'is-invalid' : ''} type="textarea"
                        name="description" id="description" rows="5"
                        value={this.props.template.description}
                        placeholder={translations.description} onChange={this.props.handleInput}/>
                    {this.props.renderErrorFor('description')}
                </FormGroup>

                <FormGroup>
                    <Label for="status">{translations.status} </Label>
                    <Input className={this.props.hasErrorFor('status') ? 'is-invalid' : ''} type="select"
                        name="send_on"
                        id="send_on"
                        value={this.props.template.send_on}
                        onChange={this.props.handleInput}
                    >
                        <option value="">{translations.select_option}</option>
                        <option value={consts.case_status_draft}>{translations.new}</option>
                        <option value={consts.case_status_open}>{translations.open}</option>
                        <option value={consts.case_status_closed}>{translations.closed}</option>
                    </Input>
                    {this.props.renderErrorFor('status')}
                </FormGroup>
            </React.Fragment>
        )
    }
}
