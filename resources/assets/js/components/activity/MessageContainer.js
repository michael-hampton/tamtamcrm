import * as React from 'react'
import MessageDialog from './MessageDialog'
import MessageBoard from './MessageBoard'
import axios from 'axios'
import { Card, CardBody, CardHeader, ListGroup } from 'reactstrap'
import Event from './Event'
import { getEntityIcon } from '../utils/_icons'
import InfoItem from '../common/entityContainers/InfoItem'
import FormatDate from '../common/FormatDate'

class MessageContainer extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            isDialogOpen: false,
            mode: '',
            activeMessage: '',
            messages: [],
            notifications: [],
            events: [],
            users: [],
            customers: []
        }
        this.setMode = this.setMode.bind(this)
        this.toggleOpenState = this.toggleOpenState.bind(this)
        this.setActiveMessage = this.setActiveMessage.bind(this)
        this.createMessage = this.createMessage.bind(this)
        this.newMessage = this.newMessage.bind(this)
        this.submitMessage = this.submitMessage.bind(this)
        this.commentOnMessage = this.commentOnMessage.bind(this)
        this.deleteMessage = this.deleteMessage.bind(this)
        this.changeMessage = this.changeMessage.bind(this)
        this.getUsers = this.getUsers.bind(this)
        this.updateEvents = this.updateEvents.bind(this)
    }

    componentDidMount () {
        this.fetchMessages()
        this.getUsers()
    }

    setMode (mode) {
        this.setState({
            mode
        })
        this.toggleOpenState()
    }

    toggleOpenState () {
        this.setState({
            isDialogOpen: !this.state.isDialogOpen
        })
    }

    setActiveMessage (message) {
        this.setState({
            activeMessage: message
        })
    }

    getUsers () {
        axios.get('/api/users')
            .then((r) => {
                this.setState({
                    users: r.data
                })
            })
            .catch((e) => {
                alert(e)
            })
    }

    fetchMessages () {
        axios.get('/api/activity')
            .then((r) => {
                this.setState({
                    messages: r.data.comments,
                    notifications: r.data.notifications,
                    events: r.data.events,
                    users: r.data.users,
                    customers: r.data.customers
                })
            })
            .catch((e) => {
                alert(e)
            })
    }

    createMessage (messageText) {
        const newMessage = {
            comment: messageText,
            parent_id: null,
            user_id: JSON.parse(localStorage.getItem('appState')).user.id
        }
        this.newMessage(newMessage)
    }

    newMessage (newMessage) {
        newMessage.entity = 'App\\Models\\Account'
        newMessage.entity_id = localStorage.getItem('account_id')

        axios.post('/api/comments', newMessage).then(response => {
            this.setState(prevState => ({
                messages: [...prevState.messages, newMessage]
            }), () => {
                this.setActiveMessage('')
            })
        })
            .catch((error) => {
                console.warn(error)
            })
    }

    submitMessage (messageText, mode) {
        if (!messageText) {
            return
        }
        if (mode === 'Create') {
            this.createMessage(messageText)
        } else if (mode === 'Edit') {
            this.changeMessage(messageText)
        } else {
            this.commentOnMessage(messageText)
        }
        this.toggleOpenState()
    }

    deleteMessage (id) {
        axios.delete(`/api/comments/${id}`).then(response => {
            const arrMessages = [...this.state.messages]
            const index = arrMessages.findIndex(message => message.id === id)
            arrMessages.splice(index, 1)
            this.setState({ messages: arrMessages })
        })
            .catch((error) => {
                console.warn(error)
            })
    }

    changeMessage (messageText) {
        const { activeMessage } = this.state
        activeMessage.comment = messageText
        axios.put(`/api/comments/${activeMessage.id}`).then(response => {
            const arrMessages = [...this.state.messages]
            const index = arrMessages.findIndex(message => message.id === activeMessage.id)
            arrMessages[index].comment = messageText
            console.log(arrMessages)
            this.setState({ messages: arrMessages })
        })
            .catch((error) => {
                console.warn(error)
            })
    }

    commentOnMessage (messageText) {
        const { activeMessage } = this.state

        console.log('actve', this.state.activeMessage)

        const messageId = this.state.messages.length
            ? this.state.messages[this.state.messages.length - 1].id + 1
            : 1
        const newMessage = {
            id: messageId,
            comment: messageText,
            parent_id: activeMessage.id,
            user_id: JSON.parse(localStorage.getItem('appState')).user.id
        }
        this.newMessage(newMessage)
    }

    updateEvents (events) {
        this.setState({ events: events })
    }

    formatAMPM (date) {
        var hours = date.getHours()
        var minutes = date.getMinutes()
        var ampm = hours >= 12 ? 'pm' : 'am'
        hours = hours % 12
        hours = hours || 12 // the hour '0' should be '12'
        minutes = minutes < 10 ? '0' + minutes : minutes
        var strTime = hours + ':' + minutes + ' ' + ampm
        return strTime
    }

    render () {
        const {
            messages,
            notifications,
            events,
            isDialogOpen,
            users,
            activeMessage
        } = this.state
        if (this.state.users && this.state.users.length) {
            return (
                <React.Fragment>
                    <MessageDialog
                        setActiveMessage={this.setActiveMessage}
                        mode="Create"
                        message={activeMessage}
                        submitMessage={this.submitMessage}
                        isDialogOpen={isDialogOpen}
                        toggleOpenState={this.toggleOpenState}
                    />

                    {messages && messages.length &&
                    <div className="mb-4">
                        <h2>Messages</h2>
                        <MessageBoard
                            setMode={this.setMode}
                            submitMessage={this.submitMessage}
                            messages={messages}
                            activeUser={true}
                            users={users}
                            deleteMessage={this.deleteMessage}
                            setActiveMessage={this.setActiveMessage}
                        />
                    </div>
                    }

                    {events && events.length
                        ? <div className="mb-4">
                            <h2>Event Invitations</h2>
                            {events.map((event, index) => {
                                return <Event key={index}
                                    action={this.updateEvents}
                                    events={this.state.events}
                                    event={event}
                                />
                            })}
                        </div> : null}

                    {notifications.length ? (
                        <Card>
                            <CardHeader><h2 className="text-center">Notifications</h2>
                            </CardHeader>
                            <CardBody style={{ height: '400px', overflowY: 'auto' }}>
                                <ListGroup className="m-3">
                                    {notifications.map((notification, index) => {
                                        const user = this.state.users.filter(user => user.id === notification.user_id)
                                        const username = user && user.length ? `${user[0].first_name} ${user[0].last_name}` : ''
                                        const message = `${username} - ${notification.data.message}`
                                        return (<React.Fragment key={index}>
                                            <InfoItem icon={getEntityIcon(notification.entity)}
                                                value={message}
                                                title={<FormatDate date={notification.created_at}
                                                    with_time={true}/>}/>
                                        </React.Fragment>)
                                    }
                                    )}
                                </ListGroup>
                            </CardBody>
                        </Card>

                    ) : null}

                </React.Fragment>
            )
        }
        return (<div>Loading</div>)
    }
}

export default MessageContainer
