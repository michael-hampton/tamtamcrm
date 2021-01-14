import React from 'react'
import { ListGroup, Row } from 'reactstrap'
import { translations } from '../../utils/_translations'
import InfoMessage from '../../common/entityContainers/InfoMessage'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import PlainEntityHeader from '../../common/entityContainers/PlanEntityHeader'
import EntityListTile from '../../common/entityContainers/EntityListTile'
import { icons } from '../../utils/_icons'
import formatDuration from '../../utils/_formatting'
import SectionItem from '../../common/entityContainers/SectionItem'
import FormatDate from '../../common/FormatDate'
import FormatMoney from '../../common/FormatMoney'

export default function Overview (props) {
    const modules = JSON.parse(localStorage.getItem('modules'))

    const customer = props.customers.filter(customer => customer.id === parseInt(props.entity.customer_id))
    let user = null

    if (props.entity.assigned_to) {
        const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
        user = <EntityListTile entity={translations.user}
            title={`${assigned_user[0].first_name} ${assigned_user[0].last_name}`}
            icon={icons.user}/>
    }

    const fields = []
    fields.due_date = <FormatDate date={props.entity.due_date}/>
    fields.task_rate = <FormatMoney amount={props.entity.task_rate} customers={props.customers}/>

    const total = props.model.taskDurationForProject()

    if (props.entity.custom_value1.length) {
        const label1 = props.model.getCustomFieldLabel('Project', 'custom_value1')
        fields[label1] = props.model.formatCustomValue(
            'Project',
            'custom_value1',
            props.entity.custom_value1
        )
    }

    if (props.entity.custom_value2.length) {
        const label2 = props.model.getCustomFieldLabel('Project', 'custom_value2')
        fields[label2] = props.model.formatCustomValue(
            'Project',
            'custom_value2',
            props.entity.custom_value2
        )
    }

    if (props.entity.custom_value3.length) {
        const label3 = props.model.getCustomFieldLabel('Project', 'custom_value3')
        fields[label3] = props.model.formatCustomValue(
            'Project',
            'custom_value3',
            props.entity.custom_value3
        )
    }

    if (props.entity.custom_value4.length) {
        const label4 = props.model.getCustomFieldLabel('Project', 'custom_value4')
        fields[label4] = props.model.formatCustomValue(
            'Project',
            'custom_value4',
            props.entity.custom_value4
        )
    }

    return <React.Fragment>
        <PlainEntityHeader heading_1={translations.total} value_1={formatDuration(total)}
            heading_2={translations.budgeted} value_2={props.entity.budgeted_hours}/>

        {!!props.entity.name.length &&
        <Row>
            <InfoMessage message={props.entity.name}/>
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

        {!!props.invoice &&
        <Row>
            {props.invoice}
        </Row>
        }

        <Row>
            <ListGroup className="col-12 mt-2 mb-2">
                {!!props.entity.tasks && !!props.entity.tasks.length && props.entity.tasks.map((task, index) => (
                    <EntityListTile key={index} entity={translations.task} title={task.name}
                        icon={icons.task}/>
                ))}
            </ListGroup>
        </Row>

        {modules && modules.invoices &&
        <SectionItem link={`/#/invoice?project_id=${props.entity.id}`}
            icon={icons.document} title={translations.invoices}/>
        }

        {modules && modules.tasks &&
        <SectionItem link={`/#/tasks?project_id=${props.entity.id}`}
            icon={icons.document} title={translations.tasks}/>
        }

        {modules && modules.credits &&
        <SectionItem link={`/#/credits?project_id=${props.entity.id}`}
            icon={icons.document} title={translations.credits}/>
        }

        {modules && modules.quotes &&
        <SectionItem link={`/#/quotes?project_id=${props.entity.id}`}
            icon={icons.document} title={translations.quotes}/>
        }

        {modules && modules.recurring_invoices &&
        <SectionItem link={`/#/recurring-invoices?project_id=${props.entity.id}`}
            icon={icons.document} title={translations.recurring_invoices}/>
        }

        {modules && modules.recurring_quotes &&
        <SectionItem link={`/#/recurring-quotes?project_id=${props.entity.id}`}
            icon={icons.document} title={translations.recurring_quotes}/>
        }

        <FieldGrid fields={fields}/>

    </React.Fragment>
}
