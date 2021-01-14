import React from 'react'
import { Row } from 'reactstrap'

export default function ColorPicker (props) {
    const colors = [
        { value: 'bg-success', label: 'Success', text_color: 'text-light' },
        { value: 'bg-secondary', label: 'Secondary', text_color: 'text-dark' },
        { value: 'bg-primary', label: 'Primary', text_color: 'text-light' },
        { value: 'bg-danger', label: 'Danger', text_color: 'text-light' },
        { value: 'bg-light', label: 'Light', text_color: 'text-dark' },
        { value: 'bg-info', label: 'Info', text_color: 'text-light' },
        { value: 'bg-dark', label: 'Dark', text_color: 'text-light' }
    ]

    return <Row className="pl-4 mb-2">
        <h5>{props.label}</h5>
        <div className="col-4 d-flex justify-content-between align-items-center">
            {colors.map((color, idx) => {
                const selected = color.value === props.value ? 'border border-danger' : ''
                return <span key={idx} style={{ borderWidth: '3px !important' }}
                    data-text={color.text_color} data-name={color.value}
                    onClick={props.handleChange}
                    className={`${color.value} ${color.text_color} p-1 m-1 ${selected}`}>{color.label}</span>
            })}
        </div>
    </Row>
}
