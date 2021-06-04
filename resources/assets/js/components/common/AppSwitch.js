import React from 'react'
import Switch from '@material-ui/core/Switch'
import FormGroup from '@material-ui/core/FormGroup'
import FormControlLabel from '@material-ui/core/FormControlLabel'
import FormControl from '@material-ui/core/FormControl'

export default function AppSwitch (props) {
    return (
        <FormControl component="fieldset">
            <FormGroup aria-label="position" row>
                <FormControlLabel
                    value="start"
                    control={<Switch
                        checked={props.isOn}
                        onChange={props.handleToggle}
                        name={props.name}
                    />}
                    label={props.label}
                    labelPlacement="start"
                />
            </FormGroup>
        </FormControl>
    )
}
