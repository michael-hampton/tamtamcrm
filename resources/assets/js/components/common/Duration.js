import React from 'react'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../utils/_translations'

export default function Duration (props) {
    let hours, minutes
    const options = []

    for (var i = 0; i <= 120; i += 15) {
        if (i === 0) {
            options.push(<option value="">{translations.change_duration}</option>)
            continue
        }

        hours = Math.floor(i / 60)
        minutes = i % 60
        let formatted_minutes = minutes

        if (minutes < 10) {
            formatted_minutes = '0' + minutes // adding leading zero
        }

        if (hours > 0) {
            minutes += 60 * hours
            console.log('minutes', minutes)
        }

        options.push(<option value={minutes}>{hours + ':' + formatted_minutes}</option>)
    }

    return <FormGroup>
        <Input value={props.value} onChange={props.onChange} type="select">
            {options}
        </Input>
    </FormGroup>
}
