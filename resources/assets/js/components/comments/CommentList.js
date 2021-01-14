/* eslint-disable no-unused-vars */
import React from 'react'
import Comment from './Comment'

export default function CommentList (props) {
    return (
        <div className="commentList">

            {props.comments.length === 0 && !props.loading ? (
                <div className="alert text-center alert-info">
                    Be the first to comment
                </div>
            ) : null}

            {
                props.comments.map((comment, index) => (
                    <Comment key={index} comment={comment}/>
                ))
            }
        </div>
    )
}
