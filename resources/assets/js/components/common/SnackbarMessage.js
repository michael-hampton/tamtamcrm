import React from 'react'
import { Alert } from 'reactstrap'
import Snackbar from '@material-ui/core/Snackbar'

export default function SnackbarMessage (props) {
    return <Snackbar open={props.open} autoHideDuration={3000} onClose={props.onClose}>
        <Alert severity={props.severity}>
            {props.message}
        </Alert>
    </Snackbar>
}
