import React from 'react'
import { FormGroup, Input, Label } from 'reactstrap'
import BankDropdown from '../../common/dropdowns/BankDropdown'
import { translations } from '../../utils/_translations'
import UserDropdown from '../../common/dropdowns/UserDropdown'

export default function Details (props) {
    return (<React.Fragment>
        <FormGroup>
            <Label for="name">{translations.name}(*):</Label>
            <Input className={props.hasErrorFor('description') ? 'is-invalid' : ''} type="text"
                name="name" onChange={props.handleInput} value={props.bank_account.name}/>
            {props.renderErrorFor('name')}
        </FormGroup>

        <FormGroup>
            <Label for="description">{translations.description}(*):</Label>
            <Input className={props.hasErrorFor('description') ? 'is-invalid' : ''} type="textarea"
                value={props.bank_account.description} name="description"
                onChange={props.handleInput}/>
            {props.renderErrorFor('description')}
        </FormGroup>

        <FormGroup>
            <Label for="postcode">{translations.bank}:</Label>
            <BankDropdown
                bank_id={props.bank_account.bank_id}
                banks={props.banks}
                name="bank_id"
                errors={props.errors}
                handleInputChanges={props.handleInput}
            />
        </FormGroup>

        <FormGroup>
            <Label for="postcode">{translations.assigned_user}:</Label>
            <UserDropdown
                user_id={props.bank_account.assigned_to}
                name="assigned_to"
                errors={props.errors}
                handleInputChanges={props.handleInput}
            />
        </FormGroup>

        <FormGroup>
            <Label for="postcode">{translations.username}:</Label>
            <Input
                type='text'
                name="username"
                value={props.bank_account.username}
                errors={props.errors}
                onChange={props.handleInput}
            />
        </FormGroup>

        <FormGroup>
            <Label for="postcode">{translations.password}:</Label>
            <Input
                type='password'
                name="password"
                value={props.bank_account.password}
                errors={props.errors}
                onChange={props.handleInput}
            />
        </FormGroup>

        <FormGroup>
            <Label for="public_notes">{translations.public_notes}:</Label>
            <Input
                value={props.bank_account.public_notes}
                type='textarea'
                name="public_notes"
                errors={props.errors}
                onChange={props.handleInput}
            />
        </FormGroup>

        <FormGroup>
            <Label for="private_notes">{translations.private_notes}:</Label>
            <Input
                value={props.bank_account.private_notes}
                type='textarea'
                name="private_notes"
                errors={props.errors}
                onChange={props.handleInput}
            />
        </FormGroup>

    </React.Fragment>
    )
}
