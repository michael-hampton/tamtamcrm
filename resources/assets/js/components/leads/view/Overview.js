import React from 'react'
import { Alert, ListGroup, Row } from 'reactstrap'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import { translations } from '../../utils/_translations'
import { icons } from '../../utils/_icons'
import FormatMoney from '../../common/FormatMoney'
import InfoItem from '../../common/entityContainers/InfoItem'
import EntityListTile from '../../common/entityContainers/EntityListTile'

export default function Overview (props) {
    let user
    let project

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

        <Row>
            <ListGroup className="col-12">
                <InfoItem icon={icons.user}
                    value={`${props.entity.first_name} ${props.entity.last_name}`}
                    title={translations.full_name}/>

                <InfoItem icon={icons.envelope} value={props.entity.email}
                    title={translations.email}/>

                <InfoItem icon={icons.phone} value={props.entity.phone}
                    title={translations.phone_number}/>

                <InfoItem icon={icons.link} value={props.entity.website}
                    title={translations.website}/>

                <InfoItem icon={icons.building} value={props.entity.vat_number}
                    title={translations.vat_number}/>

                <InfoItem icon={icons.list} value={props.entity.number}
                    title={translations.number}/>

                <InfoItem icon={icons.map_marker} value={props.address}
                    title={translations.billing_address}/>

            </ListGroup>
        </Row>
    </React.Fragment>
}
