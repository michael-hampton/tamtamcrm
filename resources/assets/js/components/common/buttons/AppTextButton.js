import React from 'react'
import { Button, Container, withStyles } from '@material-ui/core'
import IconButton from '@material-ui/core/IconButton'
import CircularProgress from '@material-ui/core/CircularProgress'

export default function AppTextButton (props) {
    let primaryColor = null

    if (props.color != null) {
        primaryColor = props.color
    } else if (!props.isInHeader) {
        primaryColor = '#CCC'
    } else if (!Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')) {
        primaryColor = '#FFF'
    } else {
        primaryColor = '#000'
    }

    return <Button onClick={props.onPressed} disabled={!props.isInHeader}
        style={{ color: primaryColor, textTransform: 'none' }}>{props.label}</Button>
}
