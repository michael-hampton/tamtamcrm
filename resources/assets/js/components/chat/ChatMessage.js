/* eslint-disable no-unused-vars */
import React, { Component } from 'react'

class ChatMessage extends Component {
    outgoingMessage (message, formattedDate) {
        return (
            <div className="outgoing_msg">
                <div className="sent_msg">
                    <p>{message}</p>
                    <span className="time_date">{formattedDate}</span></div>
            </div>
        )
    }

    incomingMessage (author, message, formattedDate) {
        return (
            <div className="incoming_msg">
                <div className="incoming_msg_img"><img src="https://ptetutorials.com/images/user-profile.png"
                    alt={author}/></div>
                <div className="received_msg">
                    <div className="received_withd_msg">
                        <p>{message}</p>
                        <span className="time_date">{formattedDate}</span></div>
                </div>
            </div>
        )
    }

    render () {
        const { author, avatar, when, message } = this.props.message
        const formattedDate = this.props.formatDate(when)
        const currentUser = JSON.parse(localStorage.getItem('appState')).user.id

        console.log('author', author)

        return parseInt(author) === currentUser || author.toLowerCase() === 'michael hampton' ? this.outgoingMessage(message, formattedDate) : this.incomingMessage(author, message, formattedDate)
    }
}

export default ChatMessage
