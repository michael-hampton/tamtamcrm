import * as React from 'react'
import MessageCard from './MessageCard'

class MessageBoard extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            messageText: ''
        }
    }

    render () {
        const { messages, activeUser, deleteMessage, submitMessage, setMode, setActiveMessage, users } = this.props
        const content = messages.length ? messages.map((message) => (
            !message.parent_id ? (
                <MessageCard
                    setMode={setMode}
                    currentMessage={message}
                    messages={messages}
                    submitMessage={submitMessage}
                    deleteMessage={deleteMessage}
                    activeUser={activeUser}
                    users={users}
                    setActiveMessage={setActiveMessage}
                />
            ) : null
        )) : ''
        return content ? <div style={{ height: '400px', overflowY: 'auto' }}>
            {content}
        </div> : null
    }
}

export default MessageBoard
