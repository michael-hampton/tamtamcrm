import React, { Component } from 'react'
import { ListGroup, Row } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import InfoItem from '../../common/entityContainers/InfoItem'

export default class Subscription extends Component {
    render () {
        return (
            <React.Fragment>
                <Row>
                    <ListGroup className="col-12">
                        <InfoItem icon={icons.building} value={this.props.entity.name}
                            title={translations.name}/>

                        <InfoItem icon={icons.link} value={this.props.entity.target_url}
                            title={translations.target_url}/>
                    </ListGroup>
                </Row>
            </React.Fragment>

        )
    }
}
