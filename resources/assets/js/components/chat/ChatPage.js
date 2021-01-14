/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
import Friend from './Friend'
import ChatMessage from './ChatMessage'
import ChatInput from './ChatInput'
import axios from 'axios'

class ChatPage extends Component {
    constructor (props) {
        super(props)
        this.sendMessage = this.sendMessage.bind(this)
        this.loadMessages = this.loadMessages.bind(this)
        this.state = {
            userID: JSON.parse(localStorage.getItem('appState')).user.id,
            friends: [],
            messages: [],
            chatActive: false,
            customer_id: 0
        }
    }

    componentDidMount () {
        this.getCustomers()
    }

    loadMessages (customer_id) {
        axios.get(`/api/messages/${customer_id}`)
            .then((r) => {
                this.setState({
                    customer_id: customer_id,
                    messages: r.data,
                    chatActive: true
                })
            })
            .catch((e) => {
                alert(e)
            })
    }

    getCustomers () {
        axios.get('/api/messages/customers')
            .then((r) => {
                this.setState({
                    friends: r.data
                })
            })
            .catch((e) => {
                alert(e)
            })
    }

    sendMessage (message) {
        axios.post('/api/messages', message)
            .then((r) => {
                // for now this will let us know things work.  `console` will give us a
                // warning though
                this.setState(prevState => ({
                    messages: [...prevState.messages, message]
                }))
            })
            .catch((e) => {
                alert(e)
            })
    }

    formatDate (dateString) {
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June',
            'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'
        ]
        const d = new Date(dateString)
        const dayName = days[d.getDay()]
        const monthName = monthNames[d.getMonth()]
        const hours = d.getHours()
        const minutes = d.getMinutes()
        const formattedDate = `${d.getDate()} ${monthName}`
        return formattedDate
    }

    render () {
        return (
            <React.Fragment>
                <h3 className=" text-center">Messaging</h3>
                <div className="messaging">
                    <div className="inbox_msg">
                        <div className="inbox_people">
                            <div className="headind_srch">
                                <div className="recent_heading">
                                    <h4>Recent</h4>
                                </div>
                                <div className="srch_bar">
                                    <div className="stylish-input-group">
                                        <input type="text" className="search-bar" placeholder="Search"/>
                                        <span className="input-group-addon">
                                            <button type="button"> <i className="fa fa-search"
                                                aria-hidden="true"/> </button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div className="inbox_chat">
                                {this.state.friends.map(friend => (
                                    <Friend
                                        formatDate={this.formatDate}
                                        selected_friend={this.state.customer_id}
                                        loadMessages={this.loadMessages}
                                        key={friend.name}
                                        friend={friend}/>
                                ))}

                            </div>
                        </div>

                        <div className="mesgs">
                            <div className="msg_history">
                                {this.state.messages.map(message => (
                                    <ChatMessage
                                        formatDate={this.formatDate}
                                        key={message.author + message.when}
                                        message={message}
                                    />
                                ))}
                            </div>

                            <ChatInput
                                customer_id={this.state.customer_id}
                                display={this.state.chatActive}
                                userID={this.state.userID}
                                sendMessage={this.sendMessage}
                            />

                        </div>
                    </div>
                </div>
            </React.Fragment>
        )
    }
}

export default ChatPage
