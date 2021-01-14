/* eslint-disable no-unused-vars */
import axios from 'axios'
import React, { Component } from 'react'

class CompleteProject extends Component {
    constructor (props) {
        super(props)
        this.handleMarkProjectAsCompleted = this.handleMarkProjectAsCompleted.bind(this)
    }

    handleMarkProjectAsCompleted () {
        axios.put(`/api/projects/${this.props.projectId}`)
            .then(response => window.location.href = '/dashboard')
    }

    render () {
        return (
            <button
                className='btn btn-primary btn-sm'
                onClick={this.handleMarkProjectAsCompleted}
            >
                Mark as completed
            </button>
        )
    }
}

export default CompleteProject
