import React, { Component } from 'react'
import { CustomInput, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class Details extends Component {
    render () {
        return (
            <React.Fragment>
                <FormGroup>
                    <Label for="name">{translations.name} <span className="text-danger">*</span></Label>
                    <Input className={this.props.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="name"
                        value={this.props.brand.name} id="name" placeholder={translations.name}
                        onChange={this.props.handleInput}/>
                    {this.props.renderErrorFor('name')}
                </FormGroup>

                <FormGroup>
                    <Label for="description">{translations.description} </Label>
                    <Input className={this.props.hasErrorFor('description') ? 'is-invalid' : ''} type="textarea"
                        name="description" id="description" rows="5"
                        value={this.props.brand.description}
                        placeholder={translations.description} onChange={this.props.handleInput}/>
                    {this.props.renderErrorFor('description')}
                </FormGroup>

                <FormGroup>
                    <Label>{translations.cover}</Label>
                    <CustomInput onChange={this.props.handleFileChange} type="file" id="cover"
                        name="cover"
                        label="Cover!"/>
                </FormGroup>

                <FormGroup>
                    <Label for="status">{translations.status} </Label>
                    <Input className={this.props.hasErrorFor('status') ? 'is-invalid' : ''} type="select"
                        name="status"
                        id="status"
                        value={this.props.brand.status}
                        onChange={this.props.handleInput}
                    >
                        <option value="0">{translations.disable}</option>
                        <option value="1">{translations.enable}</option>
                    </Input>
                    {this.props.renderErrorFor('status')}
                </FormGroup>
            </React.Fragment>
        )
    }
}
