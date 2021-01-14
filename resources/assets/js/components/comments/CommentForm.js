/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
import axios from 'axios'

export default class CommentForm extends Component {
    constructor (props) {
        super(props)
        this.state = {
            loading: false,
            error: '',
            comment: ''
        }
        // bind context to methods
        this.onSubmit = this.onSubmit.bind(this)
    }

    /**
     * Handle form input field changes & update the state
     */
    handleFieldChange (event) {
        this.setState({ comment: event.target.value })
    }

    /**
     * Simple validation
     */
    isFormValid () {
        return this.state.comment !== '' && this.state.comment.length >= 3
    }

    /**
     * Form submit handler
     */
    onSubmit (e) {
        // prevent default form submission
        e.preventDefault()
        if (!this.isFormValid()) {
            this.setState({ error: 'All fields are required.' })
            return
        }
        // loading status and clear error
        this.setState({
            error: '',
            loading: true
        })
        // persist the comments on server
        axios.post('/api/comments', {
            comment: this.state.comment,
            entity: this.props.entity_type,
            entity_id: this.props.entity.id,
            user_id: this.props.user_id
        })
            .then((response) => {
                if (response.error) {
                    this.setState({
                        loading: false,
                        error: response.error
                    })
                } else {
                    // add time return from api and push comment to parent state
                    if (response.data && response.data.length) {
                        this.props.addComment(response.data[0])
                    }
                    // clear the message box
                    this.setState({
                        loading: false,
                        comment: ''
                    })
                }
            })
            .catch((error) => {
                console.warn(error)
                this.setState({
                    error: 'Something went wrong while submitting form.',
                    loading: false
                })
            })
    }

    renderError () {
        return this.state.error ? (
            <div className="alert alert-danger">{this.state.error}</div>
        ) : null
    }

    render () {
        return (
            <React.Fragment>
                <form method="post" onSubmit={this.onSubmit}>

                    <div className="form-group">
                        <textarea
                            onChange={this.handleFieldChange.bind(this)}
                            value={this.state.comment}
                            className="form-control"
                            placeholder="Your Comment"
                            name="message"
                            rows="5"
                        />
                    </div>

                    {this.renderError()}

                    <div className="form-group">
                        <button disabled={this.state.loading} className="btn btn-primary pull-right">
                            Comment
                        </button>
                    </div>
                </form>
            </React.Fragment>
        )
    }
}
