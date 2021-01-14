import React from 'react'
import { ListGroup, ListGroupItem, ListGroupItemHeading, ListGroupItemText, Row } from 'reactstrap'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import { translations } from '../../utils/_translations'
import InfoMessage from '../../common/entityContainers/InfoMessage'
import EntityListTile from '../../common/entityContainers/EntityListTile'
import { getEntityIcon, icons } from '../../utils/_icons'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import InvoicePresenter from '../../presenters/InvoicePresenter'
import FormatMoney from '../../common/FormatMoney'
import LineItem from '../../common/entityContainers/LineItem'
import TotalsBox from '../../common/entityContainers/TotalsBox'
import FormatDate from '../../common/FormatDate'

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

    const tax_total = props.model.calculateTaxes(false)

    const fields = []

    if (props.entity.custom_value1.length) {
        const label1 = props.model.getCustomFieldLabel('Invoice', 'custom_value1')
        fields[label1] = props.model.formatCustomValue(
            'Invoice',
            'custom_value1',
            props.entity.custom_value1
        )
    }

    if (props.entity.custom_value2.length) {
        const label2 = props.model.getCustomFieldLabel('Invoice', 'custom_value2')
        fields[label2] = props.model.formatCustomValue(
            'Invoice',
            'custom_value2',
            props.entity.custom_value2
        )
    }

    if (props.entity.custom_value3.length) {
        const label3 = props.model.getCustomFieldLabel('Invoice', 'custom_value3')
        fields[label3] = props.model.formatCustomValue(
            'Invoice',
            'custom_value3',
            props.entity.custom_value3
        )
    }

    if (props.entity.custom_value4.length) {
        const label4 = props.model.getCustomFieldLabel('Invoice', 'custom_value4')
        fields[label4] = props.model.formatCustomValue(
            'Invoice',
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

    if (props.entity.balance > 0) {
        fields.balance_due = <FormatMoney customers={props.customers} amount={props.entity.balance}/>
    }

    if (props.entity.partial > 0) {
        fields.partial_due = <FormatMoney customers={props.customers} amount={props.entity.partial}/>
    }

    let recurring = null

    if (props.entity.recurring) {
        recurring = <EntityListTile entity={translations.recurring_invoice}
            title={`${props.entity.recurring.number}`}
            icon={getEntityIcon('RecurringInvoice')}/>
    }

    return <React.Fragment>
        <ViewEntityHeader heading_1={translations.total} value_1={props.entity.total}
            heading_2={translations.balance} value_2={props.entity.balance}/>

        <InvoicePresenter entity={props.entity} field="status_field"/>

        {!!props.entity.paymentables.length &&
        <Row>
            <ListGroup className="col-12 mt-4">
                {props.entity.paymentables.map((line_item, index) => (
                    <a key={index} href={`/#/payments?number=${line_item.number}`}>
                        <ListGroupItem className={listClass}>
                            <ListGroupItemHeading
                                className="">
                                <i className={`fa ${icons.credit_card} mr-4`}/>{line_item.number}
                            </ListGroupItemHeading>

                            <ListGroupItemText>
                                <FormatMoney amount={line_item.amount}/> - {line_item.date}
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

        {!!recurring &&
        <Row>
            {recurring}
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
