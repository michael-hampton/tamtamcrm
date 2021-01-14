import React from 'react'
import { Button, ModalFooter } from 'reactstrap'
import { translations } from '../utils/_translations'
import { icons } from '../utils/_icons'

export default function DefaultModalFooter (props) {
    const bg_color = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
        ? 'bg-dark' : 'bg-light'
    const device_settings = Object.prototype.hasOwnProperty.call(localStorage, 'device_settings') ? JSON.parse(localStorage.getItem('device_settings')) : ''
    const footer_class = Object.keys(device_settings).length ? `${device_settings.footer_background_color} ${device_settings.footer_text_color}` : bg_color

    return <ModalFooter className={footer_class}>
        {props.show_success &&
        <Button color="success" onClick={props.saveData}>{translations.save}</Button>
        }
        <Button color="secondary" onClick={props.toggle}>{translations.close}</Button>

        {props.loading &&
        <span style={{ fontSize: '36px' }} className={`fa ${icons.spinner}`}/>
        }

        {props.extra_button && props.extra_button}
    </ModalFooter>
}
