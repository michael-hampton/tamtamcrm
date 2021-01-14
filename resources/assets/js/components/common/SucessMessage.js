import React, { Component } from 'react'
import { Alert } from 'reactstrap'

export default class SuccessMessage extends Component {
    render () {
        return (
            <Alert color="success">
                {this.props.message}
            </Alert>
        )
    }
}
