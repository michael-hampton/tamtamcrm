import React, { Component } from 'react'
import { CustomInput, Label } from 'reactstrap'
import { consts } from '../utils/_consts'
import { translations } from '../utils/_translations'

export default class Notifications extends Component {
    constructor (props) {
        super(props)
        this.state = {
            roles: [],
            modal: false,
            notifications: [
                {
                    id: 1,
                    value: consts.notification_payment_success,
                    label: translations.payment_successful,
                    isChecked: false
                },
                {
                    id: 1,
                    value: consts.notification_payment_refunded,
                    label: translations.payment_refunded,
                    isChecked: false
                },
                {
                    id: 1,
                    value: consts.notification_lead_success,
                    label: translations.new_lead_created,
                    isChecked: false
                },
                {
                    id: 1,
                    value: consts.notification_deal_success,
                    label: translations.new_deal_created,
                    isChecked: false
                },
                {
                    id: 2,
                    value: consts.notification_payment_failure,
                    label: translations.payment_failure,
                    isChecked: false
                },
                {
                    id: 3,
                    value: consts.notification_invoice_sent,
                    label: translations.invoice_sent,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_credit_sent,
                    label: translations.credit_sent,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_order_sent,
                    label: translations.order_sent,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_purchase_order_sent,
                    label: translations.purchase_order_sent,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_purchase_order_approved,
                    label: translations.purchase_order_approved,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_purchase_order_rejected,
                    label: translations.purchase_order_rejected,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_purchase_order_change_requested,
                    label: translations.purchase_order_change_requested,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_quote_sent,
                    label: translations.quote_sent,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_invoice_viewed,
                    label: translations.invoice_viewed,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_quote_viewed,
                    label: translations.quote_viewed,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_credit_viewed,
                    label: translations.credit_viewed,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_quote_approved,
                    label: translations.quote_approved,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_quote_rejected,
                    label: translations.quote_rejected,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_quote_change_requested,
                    label: translations.quote_change_requested,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_order_created,
                    label: translations.order_created,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_order_backordered,
                    label: translations.order_backordered,
                    isChecked: false
                },
                {
                    id: 4,
                    value: consts.notification_order_held,
                    label: translations.held,
                    isChecked: false
                }
            ]
        }

        this.customInputSwitched = this.customInputSwitched.bind(this)
        this.handleAllChecked = this.handleAllChecked.bind(this)
    }

    componentDidMount () {
        if (this.props.notifications && Object.keys(this.props.notifications).length) {
            this.setState({ notifications: this.props.notifications })
        }
    }

    handleAllChecked (event) {
        const notifications = this.state.notifications
        notifications.forEach(notification => notification.isChecked = event.target.checked)
        this.setState({ notifications: notifications }, () => {
            this.props.onChange(this.state.notifications)
        })
    }

    customInputSwitched (buttonName, e) {
        const checked = e.target.checked
        const notifications = this.state.notifications

        notifications.forEach(notification => {
            if (notification.value === buttonName) {
                notification.isChecked = checked
            }
        })
        this.setState({ notifications: notifications }, () => {
            this.props.onChange(this.state.notifications)
        })
    }

    render () {
        return (<React.Fragment>
            <Label for="exampleCheckbox">Switches <input type="checkbox"
                onClick={this.handleAllChecked}/>Check
                all </Label>
            {this.state.notifications.map((notification, index) => {
                const idName = 'exampleCustomSwitch' + index

                return (
                    <div key={index}>
                        <CustomInput
                            checked={notification.isChecked}
                            type="switch"
                            id={idName}
                            name="customSwitch"
                            label={notification.label}
                            onChange={this.customInputSwitched.bind(this, notification.value)}
                        />
                    </div>
                )
            }
            )}
        </React.Fragment>)
    }
}
