import React from 'react'
import { ListGroup, Row } from 'reactstrap'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import { translations } from '../../utils/_translations'
import InfoMessage from '../../common/entityContainers/InfoMessage'
import EntityListTile from '../../common/entityContainers/EntityListTile'
import { icons } from '../../utils/_icons'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import PaymentPresenter from '../../presenters/PaymentPresenter'
import Paymentables from './Paymentables'
import FormatDate from '../../common/FormatDate'
import FormatMoney from '../../common/FormatMoney'
import SectionItem from '../../common/entityContainers/SectionItem'

export default function Overview (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    const customer = props.customers.filter(customer => customer.id === parseInt(props.entity.customer_id))

    const companyGateway = props.gateways.length ? props.gateways.filter(gateway => gateway.id === parseInt(props.entity.company_gateway_id)) : []
    let gateway = null

    if (companyGateway.length) {
        const link = props.gatewayModel.getPaymentUrl(companyGateway[0].gateway_key, props.entity.reference_number)
        gateway = <SectionItem link={link}
            icon={icons.credit_card}
            title={`${translations.token} > ${companyGateway[0].name}`}/>
    }

    let user = null

    if (props.entity.assigned_to) {
        const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
        user = <EntityListTile entity={translations.user}
            title={`${assigned_user[0].first_name} ${assigned_user[0].last_name}`}
            icon={icons.user}/>
    }

    const paymentableInvoices = props.model.paymentable_invoices
    const paymentableCredits = props.model.paymentable_credits

    const fields = []

    if (props.entity.custom_value1.length) {
        const label1 = props.model.getCustomFieldLabel('Payment', 'custom_value1')
        fields[label1] = props.model.formatCustomValue(
            'Payment',
            'custom_value1',
            props.entity.custom_value1
        )
    }

    if (props.entity.custom_value2.length) {
        const label2 = props.model.getCustomFieldLabel('Payment', 'custom_value2')
        fields[label2] = props.model.formatCustomValue(
            'Payment',
            'custom_value2',
            props.entity.custom_value2
        )
    }

    if (props.entity.custom_value3.length) {
        const label3 = props.model.getCustomFieldLabel('Payment', 'custom_value3')
        fields[label3] = props.model.formatCustomValue(
            'Payment',
            'custom_value3',
            props.entity.custom_value3
        )
    }

    if (props.entity.custom_value4.length) {
        const label4 = props.model.getCustomFieldLabel('Payment', 'custom_value4')
        fields[label4] = props.model.formatCustomValue(
            'Payment',
            'custom_value4',
            props.entity.custom_value4
        )
    }

    if (props.entity.date.length) {
        fields.date = <FormatDate date={props.entity.date}/>
    }
    if (props.entity.type_id.toString().length) {
        const paymentType = JSON.parse(localStorage.getItem('payment_types')).filter(payment_type => payment_type.id === parseInt(props.entity.type_id))
        if (paymentType.length) {
            fields.payment_type = paymentType[0].name
        }
    }
    if (props.entity.reference_number.length) {
        fields.reference_number = props.entity.reference_number
    }
    if (props.entity.refunded !== 0) {
        fields.refunded = <FormatMoney amount={props.entity.refunded} customers={this.props.customers}/>
    }

    return <React.Fragment>
        <ViewEntityHeader heading_1={translations.amount} value_1={props.entity.amount}
            heading_2={translations.applied} value_2={props.entity.applied}/>

        <PaymentPresenter entity={props.entity} field="status_field"/>

        <Paymentables paymentableInvoices={paymentableInvoices} paymentableCredits={paymentableCredits}/>

        <Row>
            <ListGroup className="col-12 mt-2">
                {gateway}
            </ListGroup>
        </Row>

        {!!props.entity.private_notes.length &&
        <Row>
            <InfoMessage message={props.entity.private_notes}/>
        </Row>
        }

        <Row>
            <EntityListTile entity={translations.customer} title={customer[0].name}
                icon={icons.customer}/>
        </Row>

        {!!user &&
        <Row>
            {user}
        </Row>
        }

        <FieldGrid fields={fields}/>
    </React.Fragment>
}
