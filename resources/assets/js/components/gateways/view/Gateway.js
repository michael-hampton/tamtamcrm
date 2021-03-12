import React, { Component } from 'react'
import { ListGroup, Row } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import SectionItem from '../../common/entityContainers/SectionItem'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import FormatMoney from '../../common/FormatMoney'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import PaymentRepository from '../../repositories/PaymentRepository'
import ErrorLog from '../../customers/view/ErrorLog'
import AlertPopup from '../../common/AlertPopup'

export default class Gateway extends Component {
    constructor (props) {
        super(props)

        this.state = {
            payments: [],
            show_alert: false
        }

        this.getPayments = this.getPayments.bind(this)
    }

    componentDidMount () {
        this.getPayments()
    }

    getPayments () {
        const paymentRepository = new PaymentRepository()
        paymentRepository.get().then(response => {
            if (!response) {
                this.setState({ show_alert: true })
                return
            }

            this.setState({ payments: response }, () => {
                console.log('payements', this.state.payments)
            })
        })
    }

    render () {
        const payments = this.state.payments.length ? this.state.payments.filter(payment => payment.company_gateway_id === parseInt(this.props.entity.id)) : []
        const sumValues = payments.length ? payments.map(item => item.amount).reduce((prev, next) => prev + next) : 0

        const allFields = []

        this.props.entity.charges.map((settings, index) => {
            const fields = {}

            if (settings.fee_amount && settings.fee_amount > 0) {
                fields[translations.fee_amount] = <FormatMoney amount={settings.fee_amount}/>
            }

            if (settings.fee_percent && settings.fee_percent > 0) {
                fields[translations.fee_percent] = <FormatMoney amount={settings.fee_percent}/>
            }

            if (settings.fee_cap && settings.fee_cap > 0) {
                fields[translations.fee_cap] = <FormatMoney amount={settings.fee_cap}/>
            }

            if (settings.min_limit && settings.min_limit > 0) {
                fields[translations.min_limit] = <FormatMoney amount={settings.min_limit}/>
            }
            if (settings.max_limit && settings.max_limit > 0) {
                fields[translations.max_limit] = <FormatMoney amount={settings.max_limit}/>
            }

            if (Object.keys(fields).length) {
                allFields[index] = fields
            }
        })

        return (
            <React.Fragment>
                <ViewEntityHeader heading_1={translations.processed} value_1={sumValues}/>

                <Row>
                    <ListGroup className="col-12">
                        <SectionItem link={`/#/customers?group_settings_id=${this.props.entity.id}`}
                            icon={icons.customer} title={translations.customers}/>
                        <SectionItem value={payments.length} link={`/#/payments?gateway_id=${this.props.entity.id}`}
                            icon={icons.credit_card} title={`${translations.payments} ${payments.length}`}/>
                    </ListGroup>
                </Row>

                {allFields.map((field) =>
                    <FieldGrid fields={field}/>
                )}

                <ErrorLog error_logs={this.props.entity.error_logs}/>

                <AlertPopup is_open={this.state.show_alert} message={this.state.error_message} onClose={(e) => {
                    this.setState({ show_alert: false })
                }}/>
            </React.Fragment>

        )
    }
}
