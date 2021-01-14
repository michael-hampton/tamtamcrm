import React, { Component } from 'react'
import { ListGroup, Row } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import InfoItem from '../../common/entityContainers/InfoItem'
import FormatDate from '../../common/FormatDate'
import FormatMoney from '../../common/FormatMoney'

export default class Promocode extends Component {
    render () {
        return (
            <React.Fragment>
                <ViewEntityHeader heading_1={translations.amount} value_1={this.props.entity.reward}
                    heading_2={translations.amount_type} value_2={this.props.entity.amount_type}/>

                <Row>
                    <ListGroup className="col-12">
                        <InfoItem icon={icons.credit_card} value={<FormatMoney amount={this.props.entity.reward}/>}
                            title={translations.amount}/>
                        <InfoItem icon={icons.link} value={this.props.entity.code}
                            title={translations.code}/>
                        <InfoItem icon={icons.building} value={this.props.entity.description}
                            title={translations.description}/>
                        <InfoItem icon={icons.percent} value={this.props.entity.amount_type}
                            title={translations.amount_type}/>
                        <InfoItem icon={icons.list} value={this.props.entity.quantity}
                            title={translations.quantity}/>
                        <InfoItem icon={icons.calendar} value={<FormatDate date={this.props.entity.expires_at}/>}
                            title={translations.expiry_date}/>
                    </ListGroup>
                </Row>

            </React.Fragment>

        )
    }
}
