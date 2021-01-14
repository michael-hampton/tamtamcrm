import * as React from 'react'
import { Card, CardBody } from 'reactstrap'

class MessageDialog extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            messageText: this.props.message ? this.props.message.comment : ''
        }
        this.onChange = this.onChange.bind(this)
    }

    onChange (e) {
        const value = e.target.value
        this.props.setActiveMessage(value)
        this.setState({
            messageText: value
        })
    }

    render () {
        const { mode, submitMessage } = this.props
        const { messageText } = this.state
        return (

            <Card>
                <CardBody>
                    <form className="form-horizontal" role="form">
                        <h4>What's New</h4>
                        <div className="form-group p-2">
                            <textarea value={this.props.message} className="form-control"
                                placeholder="Update your status"
                                onChange={this.onChange}/>
                        </div>
                        <button onClick={() => submitMessage(messageText, mode)}
                            className="btn btn-primary pull-right"
                            type="button">Post
                        </button>
                        <ul className="list-inline">
                            <li><a href=""><i className="glyphicon glyphicon-upload"/></a></li>
                            <li><a href=""><i className="glyphicon glyphicon-camera"/></a></li>
                            <li><a href=""><i className="glyphicon glyphicon-map-marker"/></a></li>
                        </ul>
                    </form>
                </CardBody>
            </Card>

        )
    }
}

export default MessageDialog
