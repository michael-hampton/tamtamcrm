import React from 'react'
import { Button, Col, FormGroup, Input, Label, Row } from 'reactstrap'
import AttributeValueDropdown from '../../common/dropdowns/AttributeValueDropdown'

const VariationInputs = (props) => {
    return (
        props.variations.map((val, idx) => {
            return (
                <div key={idx}>
                    <Row form>

                        <Col md={3}>
                            <FormGroup>
                                <Label for="exampleEmail">Attribute Value</Label>
                                <AttributeValueDropdown
                                    attribute_values={props.attribute_values}
                                    data_id={idx}
                                    attribute_id={props.variations[idx].attribute_id}
                                    attribute_value_id={props.variations[idx].attribute_values}
                                    handleInputChanges={props.onChange}
                                    name="attribute_values"
                                    errors={props.errors}
                                />
                            </FormGroup>
                        </Col>
                        <Col md={2}>
                            <FormGroup>
                                <Label for="examplePassword">Price</Label>
                                <Input type="text"
                                    data-id={idx}
                                    onChange={props.onChange}
                                    value={props.variations[idx].price}
                                    name="price"
                                />
                            </FormGroup>
                        </Col>

                        <Col md={2}>
                            <FormGroup>
                                <Label for="examplePassword">Cost</Label>
                                <Input type="text"
                                    data-id={idx}
                                    onChange={props.onChange}
                                    value={props.variations[idx].cost}
                                    name="cost"
                                />
                            </FormGroup>
                        </Col>

                        <Col md={1}>
                            <FormGroup>
                                <Label for="examplePassword">Quantity</Label>
                                <Input type="text"
                                    data-id={idx}
                                    onChange={props.onChange}
                                    value={props.variations[idx].quantity}
                                    name="quantity"
                                />
                            </FormGroup>
                        </Col>

                        <Col md={1}>
                            <FormGroup check>
                                <Label check>
                                    <Input name="is_default" value={props.variations[idx].is_default}
                                        onChange={props.onChange}
                                        type="checkbox"/>
                                    Is Default
                                </Label>
                            </FormGroup>
                        </Col>
                    </Row>

                    <Button color="danger" onClick={() => props.removeLine(idx)}>
                        Remove
                    </Button>
                </div>
            )
        })
    )
}
export default VariationInputs
