import React, { Component } from 'react'
import { Card, CardBody, CardHeader, Col, Nav, NavItem, NavLink, Row, TabContent, TabPane } from 'reactstrap'
import { translations } from '../../utils/_translations'
import PaymentModel from '../../models/PaymentModel'
import CompanyModel from '../../models/CompanyModel'
import Overview from './Overview'
import Details from './Details'
import FileUploads from '../../documents/FileUploads'
import BottomNavigationButtons from '../../common/BottomNavigationButtons'

export default class Company extends Component {
    constructor (props) {
        super(props)

        this.state = {
            entity: this.props.entity,
            activeTab: '1',
            show_success: false
        }

        this.companyModel = new CompanyModel(this.state.entity)
        this.triggerAction = this.triggerAction.bind(this)
        this.toggleTab = this.toggleTab.bind(this)
    }

    triggerAction (action) {
        if (action === 'newExpense') {
            location.href = `/#/expenses?entity_type=company&entity_id=${this.state.entity.id}`
        }

        const paymentModel = new PaymentModel(null, this.state.entity)
        paymentModel.completeAction(this.state.entity, action)
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    render () {
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
                            {translations.details}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '3' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('3')
                            }}
                        >
                            {translations.documents} ({this.companyModel.fileCount})
                        </NavLink>
                    </NavItem>
                </Nav>

                <TabContent activeTab={this.state.activeTab}>
                    <TabPane tabId="1">
                        <Overview model={this.companyModel} entity={this.state.entity}/>
                    </TabPane>

                    <TabPane tabId="2">
                        <Details entity={this.state.entity}/>
                    </TabPane>

                    <TabPane tabId="3">
                        <Row>
                            <Col>
                                <Card>
                                    <CardHeader>{translations.documents}</CardHeader>
                                    <CardBody>
                                        <FileUploads entity_type="Company" entity={this.state.entity}
                                            user_id={this.state.entity.user_id}/>
                                    </CardBody>
                                </Card>
                            </Col>
                        </Row>
                    </TabPane>
                </TabContent>

                <BottomNavigationButtons button1_click={(e) => this.triggerAction('archive')}
                    button1={{ label: translations.archive }}
                    button2_click={(e) => this.triggerAction('newExpense')}
                    button2={{ label: translations.new_expense }}/>

            </React.Fragment>
        )
    }
}
