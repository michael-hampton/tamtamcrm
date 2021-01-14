import React, { Component } from 'react'
import { ListGroup, Row } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import InfoItem from '../../common/entityContainers/InfoItem'

export default class TaxRate extends Component {
    render () {
        return (
            <React.Fragment>
                <Row>
                    <ListGroup className="col-12">
                        <InfoItem icon={icons.building} value={this.props.entity.name}
                            title={translations.name}/>

                        <InfoItem icon={icons.percent} value={this.props.entity.rate}
                            title={translations.amount}/>
                    </ListGroup>
                </Row>
            </React.Fragment>

        )
    }
}
