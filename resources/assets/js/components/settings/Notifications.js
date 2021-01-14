import React, { Component } from 'react'
import { Form, FormGroup } from 'reactstrap'

class Notifications extends Component {
    constructor (props) {
        super(props)
        this.state = {
            notifications: []
        }

        this.setNotifications = this.setNotifications.bind(this)
    }

    setNotifications (notifications) {
        this.setState({ notifications: notifications })
    }

    render () {
        return (
            <div>
                <p>Start editing to see some magic happen :)</p>
                <Form>
                    <FormGroup>
                        <Notifications onChange={this.setNotifications}/>
                    </FormGroup>
                </Form>
            </div>
        )
    }
}

export default Notifications
