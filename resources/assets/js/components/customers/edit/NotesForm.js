import React from 'react'
import { FormGroup, Input, Label } from 'reactstrap'
import IndustryDropdown from '../../common/dropdowns/IndustryDropdown'

export default function NotesForm (props) {
    const hasErrorFor = (field) => {
        return props.errors && !!props.errors[field]
    }

    const renderErrorFor = (field) => {
        if (hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    return (
        <div>
            <FormGroup>
                <Label for="size_id">Size</Label>
                <Input className={hasErrorFor('size_id') ? 'is-invalid' : ''}
                    type="select"
                    id="size_id"
                    value={props.customer.size_id}
                    onChange={props.onChange}
                    name="size_id">
                    <option value="">Size</option>
                    <option value="1">test</option>
                </Input>
                {renderErrorFor('size_id')}
            </FormGroup>

            <FormGroup>
                <Label for="industry_id">Industry</Label>
                <IndustryDropdown
                    industry_id={props.customer.industry_id}
                    handleInputChanges={props.onChange}
                />
                {renderErrorFor('industry_id')}
            </FormGroup>
        </div>

    )
}
