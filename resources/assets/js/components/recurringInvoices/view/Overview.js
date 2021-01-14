import React from 'react'
import { ListGroup, ListGroupItem, ListGroupItemHeading, ListGroupItemText, Row } from 'reactstrap'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import { translations } from '../../utils/_translations'
import InfoMessage from '../../common/entityContainers/InfoMessage'
import EntityListTile from '../../common/entityContainers/EntityListTile'
import { icons } from '../../utils/_icons'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import FormatMoney from '../../common/FormatMoney'
import LineItem from '../../common/entityContainers/LineItem'
import TotalsBox from '../../common/entityContainers/TotalsBox'
import RecurringInvoicePresenter from '../../presenters/RecurringInvoicePresenter'
import FormatDate from '../../common/FormatDate'
import { frequencyOptions } from '../../utils/_consts'

export default function Overview (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    const customer = props.customers.filter(customer => customer.id === parseInt(props.entity.customer_id))
    let user = null

    if (props.entity.assigned_to) {
        const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
        user = <EntityListTile entity={translations.user}
            title={`${assigned_user[0].first_name} ${assigned_user[0].last_name}`}
            icon={icons.user}/>
    }

    const fields = []

    if (props.entity.custom_value1.length) {
        const label1 = props.model.getCustomFieldLabel('RecurringInvoice', 'custom_value1')
        fields[label1] = props.model.formatCustomValue(
            'RecurringInvoice',
            'custom_value1',
            props.entity.custom_value1
        )
    }

    if (props.entity.custom_value2.length) {
        const label2 = props.model.getCustomFieldLabel('RecurringInvoice', 'custom_value2')
        fields[label2] = props.model.formatCustomValue(
            'RecurringInvoice',
            'custom_value2',
            props.entity.custom_value2
        )
    }

    if (props.entity.custom_value3.length) {
        const label3 = props.model.getCustomFieldLabel('RecurringInvoice', 'custom_value3')
        fields[label3] = props.model.formatCustomValue(
            'RecurringInvoice',
            'custom_value3',
            props.entity.custom_value3
        )
    }

    if (props.entity.custom_value4.length) {
        const label4 = props.model.getCustomFieldLabel('RecurringInvoice', 'custom_value4')
        fields[label4] = props.model.formatCustomValue(
            'RecurringInvoice',
            'custom_value4',
            props.entity.custom_value4
        )
    }

    fields.date = <FormatDate date={props.entity.date}/>
    fields.due_date = <FormatDate date={props.entity.due_date}/>

    if (props.entity.po_number && props.entity.po_number.length) {
        fields.po_number = props.entity.po_number
    }

    if (props.entity.discount_total && props.entity.discount_total.toString().length) {
        fields.discount = <FormatMoney customers={props.customers}
            amount={props.entity.discount_total}/>
    }

    if (props.entity.frequency && props.entity.frequency.toString().length) {
        fields.frequency = translations[frequencyOptions[props.entity.frequency]]
    }

    if (props.entity.start_date && props.entity.start_date.length) {
        fields.start_date = <FormatDate date={props.entity.start_date}/>
    }

    if (props.entity.expiry_date && props.entity.expiry_date.length) {
        fields.expiry_date = <FormatDate date={props.entity.expiry_date}/>
    }

    if (props.entity.date_to_send && props.entity.date_to_send.length) {
        fields.date_to_send = <FormatDate date={props.entity.date_to_send}/>
    }

    if (props.entity.number_of_occurrances && props.entity.number_of_occurrances.length) {
        fields.cycles_remaining = props.entity.cycles_remaining
    }

    if (props.entity.is_never_ending) {
        fields.cycles_remaining = translations.never_ending
    }

    fields.grace_period = props.entity.grace_period > 0 ? props.entity.grace_period : translations.payment_term
    fields.auto_billing_enabled = props.entity.auto_billing_enabled === true ? translations.yes : translations.no

    let stats = null

    if (props.invoices && props.invoices.length) {
        stats = props.model.recurringInvoiceStatsForInvoice(props.entity.id, props.invoices)
    }

    return <React.Fragment>
        <ViewEntityHeader heading_1={translations.total} value_1={props.entity.total}
            heading_2={translations.balance} value_2={props.entity.balance}/>

        <RecurringInvoicePresenter entity={props.entity} field="status_field"/>

        {!!stats &&
        <h4>{translations.invoices} - {stats}</h4>
        }

        {props.invoices && props.invoices.length &&
        <Row>
            <ListGroup className="col-12 mt-4">
                {props.invoices.map((invoice, index) => (
                    <a key={index} href={`/#/invoice?number=${invoice.number}`}>
                        <ListGroupItem className={listClass}>
                            <ListGroupItemHeading
                                className="">
                                <i className={`fa ${icons.pound_sign} mr-4`}/>{invoice.number}
                            </ListGroupItemHeading>

                            <ListGroupItemText>
                                <FormatMoney amount={invoice.total}/> - {invoice.date}
                            </ListGroupItemText>
                        </ListGroupItem>
                    </a>
                ))}
            </ListGroup>
        </Row>
        }

        {!!props.entity.private_notes.length &&
        <Row>
            <InfoMessage icon={icons.lock} message={props.entity.private_notes}/>
        </Row>
        }

        {!!props.entity.public_notes.length &&
        <Row>
            <InfoMessage message={props.entity.public_notes}/>
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

        <Row>
            <ListGroup className="col-12 mt-4">
                {props.entity.line_items.map((line_item, index) => (
                    <LineItem customers={props.customers} key={index} line_item={line_item}/>
                ))}
            </ListGroup>
        </Row>

        <Row className="justify-content-end">
            <TotalsBox customers={props.customers} entity={props.entity}/>
        </Row>
    </React.Fragment>
}
