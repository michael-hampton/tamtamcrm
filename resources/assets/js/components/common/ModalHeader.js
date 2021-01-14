import React from 'react'
import { ModalHeader } from 'reactstrap'

export default function DefaultModalHeader (props) {
    const bg_color = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
        ? 'bg-dark' : 'bg-light'
    const device_settings = Object.prototype.hasOwnProperty.call(localStorage, 'device_settings') ? JSON.parse(localStorage.getItem('device_settings')) : ''
    const header_class = Object.keys(device_settings).length ? `${device_settings.header_background_color} ${device_settings.header_text_color}` : bg_color

    return <ModalHeader className={header_class} toggle={props.toggle}>
        {props.title}
    </ModalHeader>
}
