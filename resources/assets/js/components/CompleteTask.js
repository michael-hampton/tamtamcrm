/* eslint-disable no-unused-vars */
import axios from 'axios'
import React, { Component } from 'react'

class CompleteTask extends Component {
    handleMarkTaskAsCompleted (taskId) {
        axios.put(`/api/tasks/complete/${taskId}`).then(response => {
            const filteredArray = this.props.tasks.filter(item => item.id !== parseInt(taskId))
            this.props.action(filteredArray)
        })
    }

    render () {
        return (
            <button
                className='btn btn-primary btn-sm'
                onClick={this.handleMarkTaskAsCompleted.bind(this, this.props.taskId)}
            >
                Mark as completed
            </button>
        )
    }
}

export default CompleteTask
