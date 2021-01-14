import * as React from 'react'
import { Button, Card, CardBody, CardFooter, CardHeader } from 'reactstrap'
import Avatar from '../common/Avatar'
import TimeAgo from 'react-timeago/lib'

class MessageCard extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            expanded: false,
            messageText: ''
        }

        this.handleExpandClick = this.handleExpandClick.bind(this)
        this.buildComment = this.buildComment.bind(this)
        this.handleKeyPress = this.handleKeyPress.bind(this)
        this.handleCommentChange = this.handleCommentChange.bind(this)
    }

    handleExpandClick () {
        this.setState(state => ({ expanded: !state.expanded }))
    }

    handleCommentChange (event) {
        this.setState({
            messageText: event.target.value
        })
    }

    handleKeyPress (event) {
        const { messageText } = this.state

        const {
            currentMessage,
            setActiveMessage,
            submitMessage,
            setMode
        } = this.props

        if (event.key === 'Enter') {
            setActiveMessage(currentMessage)
            setMode('Comment')

            setTimeout(() => {
                submitMessage(messageText, 'Comment')
                this.setState({ messageText: '' })
            }, 2000)
        }
    }

    formatDate (dateString) {
        // const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June',
            'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'
        ]
        const d = new Date(dateString)
        // const dayName = days[d.getDay()]
        const monthName = monthNames[d.getMonth()]
        // const formattedDate = `${dayName} ${d.getDate()} ${monthName} ${d.getFullYear()}`
        // return formattedDate

        return (
            <ul className="media-date text-uppercase reviews list-inline">
                <li className="dd">{d.getDate()}</li>
                <li className="mm">{monthName}</li>
                <li className="aaaa">{d.getFullYear()}</li>
            </ul>
        )
    }

    buildComment (message) {
        const { users } = this.props

        const author = users.find(
            (user) => user.id === message.user_id
        )
        const firstName = author ? author.first_name : 'Michael'
        const lastName = author ? author.last_name : 'Hampton'

        return (
            <li className="comment">
                <a className="pull-left" href="#">
                    <Avatar className="mr-2" name={`${firstName}  ${lastName}`} large={false}/>
                    {/* <img className="avatar" src="https://bootdey.com/img/Content/user_1.jpg" */}
                    {/*     alt="avatar"/> */}
                </a>
                <div className="comment-body">
                    <div className="comment-heading">
                        <h4 className="user">{`${firstName}  ${lastName}`}</h4>
                        <h5 className="time"><TimeAgo date={message.created_at}/></h5>
                    </div>

                    <p> {message.comment}</p></div>
            </li>
        )
    }

    render () {
        const {
            activeUser,
            users,
            currentMessage,
            messages,
            deleteMessage
        } = this.props
        const author = users.find(
            (user) => user.id === currentMessage.user_id
        )
        const firstName2 = author ? author.first_name : 'Michael'
        const lastName2 = author ? author.last_name : 'Hampton'
        const childMessages = messages.filter(
            (message) => message.parent_id === currentMessage.id
        )

        return (
            <div className="col-12">
                <Card>
                    <CardHeader>
                        <div className="pull-left image">
                            <Avatar className="mr-2" name={`${firstName2}  ${lastName2}`} large={false}/>
                            {/* <img src="https://bootdey.com/img/Content/user_1.jpg" */}
                            {/*     className="img-circle avatar" alt="user profile image"/> */}
                        </div>

                        <div className="pull-left meta">
                            <div className="title h5">
                                <a href="#">
                                    <b> {`${firstName2}  ${lastName2}`} </b>
                                </a>
                                made a post.
                            </div>
                            <h6 className="text-muted time"><TimeAgo date={currentMessage.created_at}/></h6></div>
                    </CardHeader>

                    <CardBody>
                        <div className="post-description">
                            <p>{currentMessage.comment}</p>
                            <div className="stats">
                                <a href="#" className="btn btn-default stat-item">
                                    <i className="fa fa-thumbs-up icon"/>2
                                </a>

                                <a href="#" className="btn btn-default stat-item">
                                    <i className="fa fa-share icon"/>12
                                </a>

                                {activeUser && currentMessage.author === activeUser.id ? (
                                    <Button className="ml-2" color="danger"
                                        aria-label="Delete message"
                                        onClick={() => deleteMessage(currentMessage.id)}
                                    >Delete</Button>
                                ) : null}
                            </div>
                        </div>
                    </CardBody>

                    <CardFooter>
                        <div className="input-group">
                            <input onChange={this.handleCommentChange} value={this.state.messageText}
                                onKeyPress={this.handleKeyPress} className="form-control" placeholder="Add a comment"
                                type="text"/>

                            <span
                                className="input-group-addon"> <a
                                    href="#"><i className="fa fa-edit"/></a> </span></div>
                    </CardFooter>

                    <ul className="comments-list">

                        {childMessages.length ? childMessages.map((message) => {
                            return this.buildComment(message)
                        })
                            : null}
                    </ul>
                </Card>
            </div>
        )
    }
}

export default MessageCard
