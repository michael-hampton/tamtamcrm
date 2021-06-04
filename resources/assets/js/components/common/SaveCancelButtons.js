import { translations } from '../utils/_translations'
import React from 'react'
import Button from '@material-ui/core/Button'
import AppTextButton from './buttons/AppTextButton'
import ActionTextButton from './buttons/ActionTextButton'

export default function SaveCancelButtons (props) {
    return <div>
        {!!props.handleCancel && <AppTextButton label={props.cancelLabel ?? translations.cancel} isInHeader={props.isHeader && (props.isEnabled || props.isCancelEnabled)} onPressed={props.isEnabled || props.isCancelEnabled ? () => {
            props.onCancelPressed()
        } : null} />}

        <ActionTextButton tooltip={props.saveLabel ?? translations.save} isVisible={true} isSaving={props.isSaving} isHeader={props.isHeader} isEnabled={props.isEnabled} onPressed={props.isEnabled ? () => {
            props.onSavePressed()
        } : null} />
    </div>
}
