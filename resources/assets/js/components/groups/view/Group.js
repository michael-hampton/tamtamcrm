import React, { Component } from 'react'
import { ListGroup, Row } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import SectionItem from '../../common/entityContainers/SectionItem'
import BottomNavigationButtons from '../../common/BottomNavigationButtons'
import EditGroup from '../edit/EditGroup'

export default class Group extends Component {
    constructor (props) {
        super(props)
        this.state = {
            show_settings: false
        }
    }

    render () {
        return (
            <React.Fragment>
                {!this.state.settings
                    ? <Row>
                        <ListGroup className="col-12">
                            <SectionItem link={`/#/customers?group_settings_id=${this.props.entity.id}`}
                                icon={icons.customer} title={translations.customers}/>

                        </ListGroup>
                    </Row>
                    : <Row>
                        <a style={{ marginLeft: '12px' }} onClick={(e) => {
                            this.setState({ settings: false })
                        }}>{translations.back}</a>
                        <EditGroup group={this.props.entity}/>
                    </Row>
                }

                <BottomNavigationButtons button1_click={(e) => {
                    this.setState({ settings: true })
                }}
                button1={{ label: translations.settings }}
                button2_click={(e) => {
                    e.preventDefault()
                    window.location.href = `/#/gateway-settings?group_id=${this.props.entity.id}`
                }}
                button2={{ label: translations.gateways }}/>

            </React.Fragment>

        )
    }
}
