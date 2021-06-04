import React from 'react'
import IconButton from '@material-ui/core/IconButton'
import CircularProgress from '@material-ui/core/CircularProgress'
import AppTextButton from './AppTextButton'

export default function ActionTextButton (props) {
    if (!props.isVisible) {
        return null
    }

    if (props.isSaving) {
        return <div className="pull-right">
            <IconButton onClick={null}>
                <CircularProgress size={30}
                    style={{ color: !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'white' : '#1976d2' }}/>
            </IconButton>
        </div>
    }

    return <AppTextButton label={props.tooltip} isInHeader={props.isEnabled} onPressed={props.onPressed}/>
}
