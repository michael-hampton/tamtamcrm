/* eslint-disable no-unused-vars */
import React, { Component } from 'react'

class ChatInput extends Component {
    constructor (props) {
        super(props)
        this.state = {
            message: ''
        }
        this.onSubmit = this.onSubmit.bind(this)
    }

    onSubmit (e) {
        e.preventDefault()
        const messageObj = {
            author: JSON.parse(localStorage.getItem('appState')).user.id,
            user_id: this.props.userID,
            message: this.state.message,
            when: new Date(),
            customer_id: this.props.customer_id
        }
        this.props.sendMessage(messageObj)
        this.setState({ message: '' })
    }

    handleInputChanges (e) {
        e.preventDefault()
        this.setState({ message: e.currentTarget.value })
    }

    render () {
        const inputClass = `write_msg ${(this.props.display ? 'd-block' : 'd-none')}`

        return (
            <form onSubmit={this.onSubmit}>
                <div className="type_msg">
                    <div className="input_msg_write">
                        <input type="text" className={inputClass} placeholder="Type a message"
                            onChange={this.handleInputChanges.bind(this)}
                            value={this.state.message}/>
                        <button className="msg_send_btn" type="submit">
                            <i className="fa fa-paper-plane-o" aria-hidden="true"/>
                        </button>
                    </div>
                </div>
            </form>
        )
    }
}

export default ChatInput
