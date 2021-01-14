import React from 'react'
import { Alert, Row } from 'reactstrap'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import { translations } from '../../utils/_translations'
import EntityListTile from '../../common/entityContainers/EntityListTile'
import { icons } from '../../utils/_icons'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import FormatMoney from '../../common/FormatMoney'

export default function Overview (props) {
    const customer = props.customers.filter(customer => customer.id === parseInt(props.entity.customer_id))

    let user = null
    let project = null

    if (props.entity.assigned_to) {
        const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
        user = <EntityListTile entity={translations.user}
            title={`${assigned_user[0].first_name} ${assigned_user[0].last_name}`}
            icon={icons.user}/>
    }

    if (props.entity.project_id && props.entity.project) {
        project = <EntityListTile entity={translations.project}
            title={`${props.entity.project.number} ${props.entity.project.name}`}
            icon={icons.user}/>
    }

    const fields = []

    if (props.entity.status_name && props.entity.status_name.length) {
        fields.status = props.entity.status_name
    }

    if (props.entity.due_date.length) {
        fields.due_date = props.entity.due_date
    }

    if (props.entity.custom_value1.length) {
        const label1 = props.model.getCustomFieldLabel('Task', 'custom_value1')
        fields[label1] = props.model.formatCustomValue(
            'Task',
            'custom_value1',
            props.entity.custom_value1
        )
    }

    if (props.entity.custom_value2.length) {
        const label2 = props.model.getCustomFieldLabel('Task', 'custom_value2')
        fields[label2] = props.model.formatCustomValue(
            'Task',
            'custom_value2',
            props.entity.custom_value2
        )
    }

    if (props.entity.custom_value3.length) {
        const label3 = props.model.getCustomFieldLabel('Task', 'custom_value3')
        fields[label3] = props.model.formatCustomValue(
            'Task',
            'custom_value3',
            props.entity.custom_value3
        )
    }

    if (props.entity.custom_value4.length) {
        const label4 = props.model.getCustomFieldLabel('Task', 'custom_value4')
        fields[label4] = props.model.formatCustomValue(
            'Task',
            'custom_value4',
            props.entity.custom_value4
        )
    }

    return <React.Fragment>
        <ViewEntityHeader heading_1={translations.valued_at}
            value_1={<FormatMoney amount={props.entity.valued_at}/>}/>

        {props.entity.name.length &&
        <Alert color="dark col-12 mt-2">
            {props.entity.name}
        </Alert>
        }

        {props.entity.private_notes.length &&
        <Alert color="dark col-12 mt-2">
            {props.entity.private_notes}
        </Alert>
        }

        {props.entity.public_notes.length &&
        <Alert color="dark col-12 mt-2">
            {props.entity.public_notes}
        </Alert>
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

        {!!project &&
        <Row>
            {project}
        </Row>
        }

        <FieldGrid fields={fields}/>
    </React.Fragment>
}
