import React, { Component } from 'react'
import { CustomInput, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class Details extends Component {
    constructor (props) {
        super(props)

        this.buildParentOptions = this.buildParentOptions.bind(this)
    }

    buildParentOptions () {
        let categoryList
        if (!this.props.categories.length) {
            categoryList = <option value="">Loading...</option>
        } else {
            categoryList = this.props.categories.map((category, index) => (
                <option key={index} value={category.id}>{category.name}</option>
            ))
        }

        return (
            <FormGroup>
                <Label for="gender">Parent:</Label>
                <Input className={this.props.hasErrorFor('parent') ? 'is-invalid' : ''}
                    type="select"
                    name="parent"
                    onChange={this.props.handleInput.bind(this)}>
                    <option value="">Select Parent</option>
                    {categoryList}
                </Input>
                {this.props.renderErrorFor('parent')}
            </FormGroup>
        )
    }

    render () {
        const parentDropdown = this.buildParentOptions()
        return (
            <React.Fragment>
                <FormGroup>
                    <Label for="name">{translations.name} <span className="text-danger">*</span></Label>
                    <Input className={this.props.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="name"
                        value={this.props.category.name}
                        id="name" placeholder={translations.name} onChange={this.props.handleInput}/>
                    {this.props.renderErrorFor('name')}
                </FormGroup>

                <FormGroup>
                    <Label for="description">{translations.description} </Label>
                    <Input className={this.props.hasErrorFor('description') ? 'is-invalid' : ''} type="textarea"
                        name="description" id="description" rows="5"
                        value={this.props.category.description}
                        placeholder={translations.description} onChange={this.props.handleInput}/>
                    {this.props.renderErrorFor('description')}
                </FormGroup>

                {parentDropdown}

                <FormGroup>
                    <Label>{translations.cover}</Label>
                    <CustomInput onChange={this.props.handleFileChange} type="file" id="cover"
                        name="cover"
                        label="Cover!"/>
                </FormGroup>

                <FormGroup>
                    <Label for="status">{translations.status} </Label>
                    <Input className={this.props.hasErrorFor('status') ? 'is-invalid' : ''} type="select"
                        value={this.props.category.status}
                        name="status"
                        id="status"
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
