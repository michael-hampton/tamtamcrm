import React from 'react'
import { FormGroup, Input, Label } from 'reactstrap'
import CustomerDropdown from '../../common/dropdowns/CustomerDropdown'
import { translations } from '../../utils/_translations'
import UserDropdown from '../../common/dropdowns/UserDropdown'
import Datepicker from '../../common/Datepicker'
import ColorPickerNew from '../../common/ColorPickerNew'

export default function Details (props) {
    return (<React.Fragment>
        <FormGroup>
            <Label for="name">{translations.name}(*):</Label>
            <Input className={props.hasErrorFor('description') ? 'is-invalid' : ''} type="text"
                name="name" onChange={props.handleInput} value={props.project.name}/>
            {props.renderErrorFor('name')}
        </FormGroup>

        <FormGroup>
            <Label for="description">{translations.description}(*):</Label>
            <Input className={props.hasErrorFor('description') ? 'is-invalid' : ''} type="textarea"
                value={props.project.description} name="description"
                onChange={props.handleInput}/>
            {props.renderErrorFor('description')}
        </FormGroup>

        {!!props.is_new &&
            <FormGroup>
                <Label for="description">{translations.customer}(*):</Label>
                <CustomerDropdown
                    customer={props.project.customer_id}
                    errors={props.errors}
                    renderErrorFor={props.renderErrorFor}
                    handleInputChanges={props.handleInput}
                    customers={props.customers}
                />
            </FormGroup>
        }

        <FormGroup>
            <Label for="postcode">{translations.assigned_user}:</Label>
            <UserDropdown
                user_id={props.project.assigned_to}
                name="assigned_to"
                errors={props.errors}
                handleInputChanges={props.handleInput}
            />
        </FormGroup>

        <FormGroup>
            <Label for="due_date">{translations.due_date}(*):</Label>
            <Datepicker name="due_date" date={props.project.due_date}
                handleInput={props.handleInput}
                className={props.hasErrorFor('due_date') ? 'form-control is-invalid' : 'form-control'}/>
            {props.renderErrorFor('due_date')}
        </FormGroup>

        <FormGroup>
            <Label for="postcode">{translations.budgeted_hours}:</Label>
            <Input
                type='number'
                name="budgeted_hours"
                value={props.project.budgeted_hours}
                errors={props.errors}
                onChange={props.handleInput}
            />
        </FormGroup>

        <FormGroup>
            <Label for="postcode">{translations.task_rate}:</Label>
            <Input
                type='number'
                name="task_rate"
                value={props.project.task_rate}
                errors={props.errors}
                onChange={props.handleInput}
            />
        </FormGroup>

        <ColorPickerNew color={props.project.column_color} onChange={(color) => {
            const e = {}
            e.target = {
                name: 'column_color',
                value: color
            }

            props.handleInput(e)
        }}/>

        <FormGroup>
            <Label for="public_notes">{translations.public_notes}:</Label>
            <Input
                value={props.project.public_notes}
                type='textarea'
                name="public_notes"
                errors={props.errors}
                onChange={props.handleInput}
            />
        </FormGroup>

        <FormGroup>
            <Label for="private_notes">{translations.private_notes}:</Label>
            <Input
                value={props.project.private_notes}
                type='textarea'
                name="private_notes"
                errors={props.errors}
                onChange={props.handleInput}
            />
        </FormGroup>

    </React.Fragment>
    )
}
