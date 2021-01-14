import React, { Component } from 'react'
import { Alert } from 'reactstrap'

export default class ErrorMessage extends Component {
    render () {
        return (
            <Alert color="danger">
                {this.props.message}
            </Alert>
        )
    }
}
