/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
import { Badge } from 'reactstrap'
import Avatar from '../common/Avatar'

class Friend extends Component {
    render () {
        const { customer_id, name, avatar, message, when, toRespond, seen } = this.props.friend
        const active = this.props.selected_friend === customer_id
        return (
            <div className={`chat_list ${active === true ? 'active_chat' : ''}`}
                onClick={() => this.props.loadMessages(customer_id)}>
                <div className="chat_people">
                    <div className="chat_img">
                        {/* <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil"> */}
                        <Avatar name={name}/>
                    </div>

                    {seen ? (
                        <span className="text-muted float-right">
                            <i className="fa fa-check" aria-hidden="true"/>
                        </span>
                    ) : toRespond ? (
                        <Badge color="danger" className="float-right">
                            {toRespond}
                        </Badge>
                    ) : (
                        <span className="text-muted float-right">
                            <i icon="reply" aria-hidden="true"/>
                        </span>
                    )}

                    <div className="chat_ib">
                        <h5>{name} <span className="chat_date">{when ? this.props.formatDate(when) : ''}</span></h5>
                        <p>{message}</p>
                    </div>
                </div>
            </div>
        )
    }
}

export default Friend
