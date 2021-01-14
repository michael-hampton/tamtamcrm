import React, { Component } from 'react'
import { FormGroup, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'
import ProductDropdown from '../../common/dropdowns/ProductDropdown'
import ProjectDropdown from '../../common/dropdowns/ProjectDropdown'

export default class Links extends Component {
    render () {
        return (
            <React.Fragment>
                <FormGroup>
                    <Label for="message">{translations.project}<span className="text-danger">*</span></Label>
                    <ProjectDropdown
                        renderErrorFor={this.renderErrorFor}
                        name="link_project_value"
                        handleInputChanges={this.props.handleInput}
                        project={this.props.case.link_project_value}
                    />
                </FormGroup>

                <FormGroup>
                    <Label for="message">{translations.product}<span className="text-danger">*</span></Label>
                    <ProductDropdown
                        renderErrorFor={this.renderErrorFor}
                        name="link_product_value"
                        handleInputChanges={this.props.handleInput}
                        product={this.props.case.link_product_value}
                        products={this.props.products}
                    />
                </FormGroup>
            </React.Fragment>
        )
    }
}
