import React from 'react'
import { Button, Col, FormGroup, Input, Label, Row } from 'reactstrap'

const AttributeValueInputs = (props) => {
    return (
        props.values.map((val, idx) => {
            return (
                <div key={idx}>
                    <Row form>

                        <Col md={8}>
                            <FormGroup>
                                <Label for="examplePassword">Value</Label>
                                <Input type="text"
                                    data-id={idx}
                                    onChange={props.onChange}
                                    value={props.values[idx].value}
                                    name="value"
                                />
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
export default AttributeValueInputs
