import React from 'react'
import { ListGroup, ListGroupItem, ListGroupItemHeading, ListGroupItemText, Row } from 'reactstrap'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import { translations } from '../../utils/_translations'
import InfoMessage from '../../common/entityContainers/InfoMessage'
import EntityListTile from '../../common/entityContainers/EntityListTile'
import { icons } from '../../utils/_icons'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import FormatDate from '../../common/FormatDate'
import FormatMoney from '../../common/FormatMoney'

export default function Overview (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    let user = null
    let invoices = []

    if (props.invoices.length) {
        invoices = props.invoices.filter(invoice => invoice.plan_subscription_id === parseInt(props.entity.id))
    }

    if (props.entity.assigned_to) {
        const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
        user = <EntityListTile entity={translations.user}
            title={`${assigned_user[0].first_name} ${assigned_user[0].last_name}`}
            icon={icons.user}/>
    }

    const fields = []

    if (props.entity.starts_at.length) {
        fields.starts_at = <FormatDate date={props.entity.starts_at}/>
    }

    if (props.entity.ends_at.length) {
        fields.ends_at = <FormatDate date={props.entity.ends_at}/>
    }

    if (props.entity.trial_ends_at.length) {
        fields.trial_ends_at = <FormatDate date={props.entity.trial_ends_at}/>
    }

    if (props.entity.due_date.length) {
        fields.due_date = <FormatDate date={props.entity.due_date}/>
    }

    return <React.Fragment>
        {/* <ViewEntityHeader heading_1={translations.amount} value_1={props.entity.amount} */}
        {/*    heading_2={translations.applied} value_2={props.entity.applied}/> */}

        {invoices.length &&
        <Row>
            <ListGroup className="col-12 mt-4">
                {invoices.map((invoice, index) => (
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

        {!!user &&
        <Row>
            {user}
        </Row>
        }

        <FieldGrid fields={fields}/>
    </React.Fragment>
}
