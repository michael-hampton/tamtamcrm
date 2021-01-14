import React from 'react'
import { CustomInput } from 'reactstrap'

export default function SwitchWithIcon (props) {
    return (
        <a href="#"
            className="border-0 mt-2 mb-2 list-group-item-dark list-group-item list-group-item-action flex-column align-items-start">
            <div className="d-flex w-100 justify-content-between">
                <h5 className="mb-1">
                    <i style={{ fontSize: '24px', marginRight: '20px' }}
                        className={`fa ${props.icon}`}/>
                    {props.label}
                </h5>
                <CustomInput
                    checked={props.checked}
                    type="switch"
                    id={props.name}
                    name={props.name}
                    label=""
                    onChange={props.handleInput}/>
            </div>

            <h6 id="passwordHelpBlock" className="form-text text-muted">
                {props.help_text}
            </h6>
        </a>
    )
}
