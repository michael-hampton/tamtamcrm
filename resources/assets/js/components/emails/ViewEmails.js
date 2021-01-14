import React, { Component } from 'react'
import { ListGroup, ListGroupItem, ListGroupItemHeading, ListGroupItemText } from 'reactstrap'
import { translations } from '../utils/_translations'

export default class Emails extends Component {
    constructor (props) {
        super(props)

        this.handleCheck = this.handleCheck.bind(this)
    }

    handleCheck (email) {
        this.props.handleSettingsChange(this.props.template_type, email.body)
    }

    render () {
        return this.props.emails && this.props.emails.length ? this.props.emails.map(email => {
            const active = parseInt(this.props.active_id) === email.id ? 'active' : ''
            return <ListGroup key={email.id}>
                <ListGroupItem color="dark" onClick={() => this.handleCheck(email)} className={active}>
                    <ListGroupItemHeading>{email.subject}</ListGroupItemHeading>
                    <ListGroupItemText>
                            Sent: {email.sent_at}
                    </ListGroupItemText>
                </ListGroupItem>
            </ListGroup>
        })
            : <div className="text-center">{translations.no_history}</div>
    }
}
