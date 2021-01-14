import React from 'react'
import { Button, FormGroup, Input, Label } from 'reactstrap'

const FeatureInputs = (props) => {
    return (
        props.features.map((val, idx) => {
            return (
                <div key={idx}>
                    <FormGroup>
                        <Label for="examplePassword">Feature {(idx + 1)}</Label>
                        <Input type="text"
                            data-id={idx}
                            onChange={props.onChange}
                            value={props.features[idx].description}
                            name="description"
                        />
                    </FormGroup>

                    <Button color="danger" onClick={() => props.removeLine(idx)}>
                        Remove
                    </Button>
                </div>
            )
        })
    )
}
export default FeatureInputs
