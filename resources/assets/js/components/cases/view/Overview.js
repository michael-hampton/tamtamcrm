import React from 'react'
import { Row } from 'reactstrap'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import { translations } from '../../utils/_translations'
import CasePresenter from '../../presenters/CasePresenter'
import InfoMessage from '../../common/entityContainers/InfoMessage'
import EntityListTile from '../../common/entityContainers/EntityListTile'
import { icons } from '../../utils/_icons'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import FormatDate from '../../common/FormatDate'

export default function Overview (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    const fields = []

    if (props.entity.custom_value1.length) {
        const label1 = props.model.getCustomFieldLabel('Case', 'custom_value1')
        fields[label1] = props.model.formatCustomValue(
            'Case',
            'custom_value1',
            props.entity.custom_value1
        )
    }

    if (props.entity.custom_value2.length) {
        const label2 = props.model.getCustomFieldLabel('Case', 'custom_value2')
        fields[label2] = props.model.formatCustomValue(
            'Case',
            'custom_value2',
            props.entity.custom_value2
        )
    }

    if (props.entity.custom_value3.length) {
        const label3 = props.model.getCustomFieldLabel('Case', 'custom_value3')
        fields[label3] = props.model.formatCustomValue(
            'Case',
            'custom_value3',
            props.entity.custom_value3
        )
    }

    if (props.entity.custom_value4.length) {
        const label4 = props.model.getCustomFieldLabel('Case', 'custom_value4')
        fields[label4] = props.model.formatCustomValue(
            'Case',
            'custom_value4',
            props.entity.custom_value4
        )
    }

    fields.date = <FormatDate date={props.entity.created_at}/>
    fields.due_date = <FormatDate date={props.entity.due_date}/>

    if (props.entity.subject && props.entity.subject.length) {
        fields.subject = props.entity.subject
    }

    if (props.entity.priority && props.entity.priority.toString().length) {
        fields.priority = this.props.priority
    }

    if (props.entity.category && Object.keys(props.entity.category).length) {
        fields.category = props.entity.category.name
    }

    let user = null

    if (props.entity.assigned_to) {
        const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
        user = <EntityListTile entity={translations.user}
            title={`${assigned_user[0].first_name} ${assigned_user[0].last_name}`}
            icon={icons.user}/>
    }

    const customer = props.customers.filter(customer => customer.id === parseInt(props.entity.customer_id))

    return <React.Fragment>
        <ViewEntityHeader heading_1={translations.amount} value_1={props.entity.amount}
            heading_2={translations.converted} value_2={0}/>

        <CasePresenter entity={props.entity} field="status_field"/>

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
