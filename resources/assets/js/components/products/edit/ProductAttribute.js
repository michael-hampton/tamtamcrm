import React from 'react'
import { Col, FormGroup, Input, Label, Row } from 'reactstrap'
import { translations } from '../../utils/_translations'
import { consts } from '../../utils/_consts'

class ProductAttribute extends React.Component {
    constructor (props) {
        super(props)

        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
    }

    hasErrorFor (field) {
        return !!this.props.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    render () {
        return (
            <React.Fragment>
                <Row form>
                    <Col md={3}>
                        <FormGroup>
                            <Label for="length">{translations.length}:</Label>
                            <Input value={this.props.product.length}
                                className={this.hasErrorFor('length') ? 'is-invalid' : ''}
                                type="text"
                                name="length" onChange={this.props.handleInput}/>
                            {this.renderErrorFor('length')}
                        </FormGroup>
                    </Col>

                    <Col md={3}>
                        <FormGroup>
                            <Label for="width">{translations.width}:</Label>
                            <Input className={this.hasErrorFor('width') ? 'is-invalid' : ''}
                                value={this.props.product.width}
                                type="text"
                                name="width"
                                onChange={this.props.handleInput}/>
                            {this.renderErrorFor('width')}
                        </FormGroup>
                    </Col>

                    <Col md={3}>
                        <FormGroup>
                            <Label for="height">{translations.height}:</Label>
                            <Input className={this.hasErrorFor('height') ? 'is-invalid' : ''}
                                type="text"
                                name="height"
                                value={this.props.product.height}
                                onChange={this.props.handleInput}/>
                            {this.renderErrorFor('height')}
                        </FormGroup>
                    </Col>

                    <Col md={3}>
                        <FormGroup>
                            <Label for="distance_unit">{translations.distance_unit}:</Label>
                            <Input className={this.hasErrorFor('distance_unit') ? 'is-invalid' : ''}
                                type="select"
                                name="distance_unit"
                                value={this.props.product.distance_unit}
                                onChange={this.props.handleInput}>
                                <option value="">{translations.select_option}</option>
                                <option value={consts.centimeters}>{translations.centimeters}</option>
                                <option value={consts.meters}>{translations.meters}</option>
                                <option value={consts.inches}>{translations.inches}</option>
                                <option value={consts.milimeters}>{translations.milimeters}</option>
                                <option value={consts.foot}>{translations.foot}</option>
                                <option value={consts.yard}>{translations.yard}</option>
                            </Input>
                            {this.renderErrorFor('distance_unit')}
                        </FormGroup>
                    </Col>
                </Row>

                <Row form>
                    <Col md={3}>
                        <FormGroup>
                            <Label for="weight">{translations.weight}:</Label>
                            <Input className={this.hasErrorFor('weight') ? 'is-invalid' : ''}
                                type="text"
                                name="weight"
                                value={this.props.product.weight}
                                onChange={this.props.handleInput}/>
                            {this.renderErrorFor('weight')}
                        </FormGroup>
                    </Col>

                    <Col md={3}>
                        <FormGroup>
                            <Label for="mass_unit">{translations.mass_unit}:</Label>
                            <Input className={this.hasErrorFor('mass_unit') ? 'is-invalid' : ''}
                                type="select"
                                name="mass_unit"
                                value={this.props.product.mass_unit}
                                onChange={this.props.handleInput}>
                                <option value="">{translations.select_option}</option>
                                <option value={consts.ounces}>{translations.ounces}</option>
                                <option value={consts.grams}>{translations.grams}</option>
                                <option value={consts.pounds}>{translations.pounds}</option>
                            </Input>
                            {this.renderErrorFor('mass_unit')}
                        </FormGroup>
                    </Col>
                </Row>
            </React.Fragment>
        )
    }
}

export default ProductAttribute
