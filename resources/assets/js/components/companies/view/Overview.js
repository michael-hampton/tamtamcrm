import React from 'react'
import { ListGroup, Row } from 'reactstrap'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import { translations } from '../../utils/_translations'
import InfoMessage from '../../common/entityContainers/InfoMessage'
import { icons } from '../../utils/_icons'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import SectionItem from '../../common/entityContainers/SectionItem'
import EntityListTile from '../../common/entityContainers/EntityListTile'

export default function Overview (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    const fields = []

    if (props.model.hasCurrency) {
        fields.currency =
            JSON.parse(localStorage.getItem('currencies')).filter(currency => currency.id === props.model.currencyId)[0].name
    }

    if (props.entity.custom_value1.length) {
        const label1 = props.model.getCustomFieldLabel('Company', 'custom_value1')
        fields[label1] = props.model.formatCustomValue(
            'Company',
            'custom_value1',
            props.entity.custom_value1
        )
    }

    if (props.entity.custom_value2.length) {
        const label2 = props.model.getCustomFieldLabel('Company', 'custom_value2')
        fields[label2] = props.model.formatCustomValue(
            'Company',
            'custom_value2',
            props.entity.custom_value2
        )
    }

    if (props.entity.custom_value3.length) {
        const label3 = props.model.getCustomFieldLabel('Company', 'custom_value3')
        fields[label3] = props.model.formatCustomValue(
            'Company',
            'custom_value3',
            props.entity.custom_value3
        )
    }

    if (props.entity.custom_value4.length) {
        const label4 = props.model.getCustomFieldLabel('Company', 'custom_value4')
        fields[label4] = props.model.formatCustomValue(
            'Company',
            'custom_value4',
            props.entity.custom_value4
        )
    }

    let user = null

    if (props.entity.assigned_to) {
        const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
        user = <EntityListTile entity={translations.user}
            title={`${assigned_user[0].first_name} ${assigned_user[0].last_name}`}
            icon={icons.user}/>
    }

    return <React.Fragment>
        <ViewEntityHeader heading_1={translations.paid_to_date} value_1={props.entity.paid_to_date}
            heading_2={translations.balance} value_2={props.entity.balance}/>

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
            {user}
        </Row>

        <FieldGrid fields={fields}/>

        <Row>
            <ListGroup className="col-12">
                <SectionItem link={`/#/expenses?company_id=${props.entity.id}`}
                    icon={icons.expense} title={translations.expenses}/>
            </ListGroup>
        </Row>
    </React.Fragment>
}
