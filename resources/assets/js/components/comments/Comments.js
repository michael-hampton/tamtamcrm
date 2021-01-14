/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
import CommentForm from './CommentForm'
import CommentList from './CommentList'
import axios from 'axios'

export default class Comments extends Component {
    constructor (props) {
        super(props)
        this.state = {
            comments: [],
            loading: false
        }
        this.addComment = this.addComment.bind(this)
        this.loadComments = this.loadComments.bind(this)
    }

    componentDidMount () {
        this.loadComments()
    }

    loadComments () {
        this.setState({ loading: true })

        // get all the comments
        axios.get(`/api/comments/${this.props.entity_type}/${this.props.entity.id}`)
            .then((r) => {
                this.setState({
                    comments: r.data,
                    loading: false
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false
                })
            })
    }

    /**
     * Add new comment
     * @param {Object} comment
     */
    addComment (comment) {
        this.setState({
            comments: [comment, ...this.state.comments]
        })
    }

    render () {
        return (
            <div className="row bootstrap snippets">
                <div className="col-12">
                    <div className="comment-wrapper">
                        <div className="panel panel-info">
                            <div className="panel-heading">
                                Comment panel
                            </div>

                            <div className="panel-body">
                                {<CommentForm
                                    addComment={this.addComment}
                                    user_id={this.props.user_id}
                                    entity_type={this.props.entity_type}
                                    entity={this.props.entity}
                                />}

                                <div className="clearfix"/>
                                <hr/>
                                <ul className="media-list">
                                    {<CommentList
                                        loading={this.state.loading}
                                        comments={this.state.comments}
                                    />}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}
