import React, { Component } from 'react'
import { Card, CardBody, CardHeader, Col, ListGroup, Nav, NavItem, NavLink, Row, TabContent, TabPane } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import InfoItem from '../../common/entityContainers/InfoItem'
import FormatMoney from '../../common/FormatMoney'
import ProductModel from '../../models/ProductModel'
import FileUploads from '../../documents/FileUploads'
import Overview from './Overview'

export default class Product extends Component {
    constructor (props) {
        super(props)

        this.state = {
            activeTab: '1',
            show_success: false
        }

        this.productModel = new ProductModel(this.props.entity)

        // this.triggerAction = this.triggerAction.bind(this)
        this.toggleTab = this.toggleTab.bind(this)
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    render () {
        const variations = this.props.entity.attributes.length ? this.props.entity.attributes.map((attribute, index) => {
            const values = attribute.values.length ? Array.prototype.map.call(attribute.values, function (value) {
                return value.value
            }).join(',') : null

            return <ListGroup key={index} className="col-12 mt-2">
                <InfoItem icon={icons.credit_card}
                    value={<FormatMoney amount={attribute.price}/>} title={translations.price}/>
                <InfoItem icon={icons.credit_card}
                    value={`${attribute.cost}`} title={translations.cost}/>
                <InfoItem icon={icons.list}
                    value={`${attribute.quantity}`} title={translations.quantity}/>
                <InfoItem icon={icons.list}
                    value={`${values}`} title={translations.variations}/>
            </ListGroup>
        }) : null

        return (
            <React.Fragment>
                <Nav tabs className="nav-justified disable-scrollbars">
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '1' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('1')
                            }}
                        >
                            {translations.overview}
                        </NavLink>
                    </NavItem>
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '2' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('2')
                            }}
                        >
                            {translations.variations}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '3' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('3')
                            }}
                        >
                            {translations.documents} ({this.productModel.fileCount})
                        </NavLink>
                    </NavItem>
                </Nav>

                <TabContent activeTab={this.state.activeTab}>
                    <TabPane tabId="1">
                        <Overview entity={this.props.entity}/>
                    </TabPane>

                    <TabPane tabId="2">
                        <Row>
                            {variations}
                        </Row>
                    </TabPane>

                    <TabPane tabId="3">
                        <Row>
                            <Col>
                                <Card>
                                    <CardHeader> {translations.documents} </CardHeader>
                                    <CardBody>
                                        <FileUploads entity_type="Product" entity={this.props.entity}
                                            user_id={this.props.entity.user_id}/>
                                    </CardBody>
                                </Card>
                            </Col>
                        </Row>
                    </TabPane>
                </TabContent>
            </React.Fragment>

        )
    }
}
