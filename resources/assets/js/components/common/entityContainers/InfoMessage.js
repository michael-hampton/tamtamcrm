import React from 'react'
import { Alert } from 'reactstrap'
import { icons } from '../../utils/_icons'

export default function InfoMessage (props) {
    const bg_color = localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true' ? 'dark dark-info-alert' : 'primary primary-info-alert'
    const text_color = localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true' ? 'text-white' : 'primary'

    return <Alert className={`col-12 mt-2 ${text_color}`} color={bg_color}>
        <i style={{ fontSize: '16px' }} className={`fa ${props.icon ? props.icon : icons.info} mr-4`}/>
        {props.message}
    </Alert>
}
