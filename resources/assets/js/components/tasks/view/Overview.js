import React from 'react'
import { ListGroup, Row } from 'reactstrap'
import { translations } from '../../utils/_translations'
import InfoMessage from '../../common/entityContainers/InfoMessage'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import PlainEntityHeader from '../../common/entityContainers/PlanEntityHeader'
import FormatMoney from '../../common/FormatMoney'
import EntityListTile from '../../common/entityContainers/EntityListTile'
import { icons } from '../../utils/_icons'
import TaskPresenter from '../../presenters/TaskPresenter'
import FormatDate from '../../common/FormatDate'
import { frequencyOptions } from '../../utils/_consts'
import TaskTimeItem from '../../common/entityContainers/TaskTimeItem'

export default function Overview (props) {
    const customer = props.customers.filter(customer => customer.id === parseInt(props.entity.customer_id))
    let user
    let project
    let invoice = null

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

    if (props.entity.invoice_id && props.entity.invoice) {
        invoice = <EntityListTile entity={translations.invoice}
            title={`${props.entity.invoice.number} ${props.entity.invoice.total}`}
            icon={icons.user}/>
    }

    const fields = []

    if (props.entity.status_name.length) {
        fields.status = props.entity.status_name
    }

    if (props.entity.description.length) {
        fields.description = props.entity.description
    }

    if (props.entity.start_date.length) {
        fields.start_date = props.entity.start_date
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

    const recurring = []

    if (props.entity.is_recurring === true) {
        if (props.entity.recurring_start_date.length) {
            recurring.start_date = <FormatDate date={props.entity.recurring_start_date}/>
        }

        if (props.entity.recurring_end_date.length) {
            recurring.end_date = <FormatDate date={props.entity.recurring_end_date}/>
        }

        if (props.entity.recurring_due_date.length) {
            recurring.due_date = <FormatDate date={props.entity.recurring_due_date}/>
        }

        if (props.entity.recurring_frequency.toString().length) {
            fields.frequency = translations[frequencyOptions[props.entity.frequency]]
        }
    }

    const time_display = props.model.formatTime(props.lastTime)
    const last_timer = props.entity.timers[props.entity.timers.length - 1]

    const task_times = props.entity.timers && props.entity.timers.length ? props.entity.timers.map((timer, index) => {
        const lastTime = timer.id === last_timer.id ? time_display : null
        return <TaskTimeItem lastTime={lastTime} key={index} taskTime={timer}/>
    }) : null

    return <React.Fragment>
        <PlainEntityHeader heading_1={translations.duration} value_1={props.totalDuration}
            heading_2={translations.amount}
            value_2={<FormatMoney amount={props.calculatedAmount} customers={props.customers}/>}/>

        <TaskPresenter entity={props.entity} field="status_field"/>

        {!!props.entity.name.length &&
        <Row>
            <InfoMessage message={props.entity.name}/>
        </Row>
        }

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

        {!!project &&
        <Row>
            {project}
        </Row>
        }

        {!!invoice &&
        <Row>
            {invoice}
        </Row>
        }

        <FieldGrid fields={fields}/>

        {!!Object.keys(recurring).length &&
        <div>
            <h5>{translations.recurring}</h5>
            <FieldGrid fields={recurring}/>
        </div>
        }

        <Row>
            <ListGroup className="col-12">
                {task_times}
            </ListGroup>
        </Row>
    </React.Fragment>
}
