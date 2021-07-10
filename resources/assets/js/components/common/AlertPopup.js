import React from 'react'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import { translations } from '../utils/_translations'

export default function AlertPopup (props) {
    const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
    return (
        <Modal centered={true} backdrop="static" isOpen={props.is_open}
            className={props.className}>
            <ModalHeader>{translations.alert}</ModalHeader>
            <ModalBody className={theme}>
                {props.message}
            </ModalBody>
            <ModalFooter>
                <Button onClick={(e) => {
                    if (props.onClose) {
                        props.onClose()
                    }
                }} color="secondary">{translations.close}</Button>
            </ModalFooter>
        </Modal>
    )
}
