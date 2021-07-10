import React from 'react'
import { Row } from 'reactstrap'
import { translations } from '../../utils/_translations'
import EntityListTile from '../../common/entityContainers/EntityListTile'
import { icons } from '../../utils/_icons'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import FormatMoney from '../../common/FormatMoney'

export default function Overview (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    let user = null

    if (props.entity.assigned_to) {
        const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
        user = <EntityListTile entity={translations.user}
            title={`${assigned_user[0].first_name} ${assigned_user[0].last_name}`}
            icon={icons.user}/>
    }

    const fields = []

    if (props.entity.price) {
        fields.price = <FormatMoney amount={props.entity.price}/>
    }

    if (props.entity.trial_period.length) {
        fields.trial_period = props.entity.trial_period
    }

    if (props.entity.active_subscribers_limit) {
        fields.active_subscribers_limit = props.entity.active_subscribers_limit
    }

    return <React.Fragment>
        {/* <ViewEntityHeader heading_1={translations.amount} value_1={props.entity.amount} */}
        {/*    heading_2={translations.applied} value_2={props.entity.applied}/> */}

        {!!user &&
        <Row>
            {user}
        </Row>
        }

        <FieldGrid fields={fields}/>
    </React.Fragment>
}
