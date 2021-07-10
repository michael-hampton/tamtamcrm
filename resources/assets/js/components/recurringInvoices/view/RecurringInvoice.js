import React, { Component } from 'react'
import FileUploads from '../../documents/FileUploads'
import {
    Alert,
    Card,
    CardBody,
    CardHeader,
    Col,
    Nav,
    NavItem,
    NavLink,
    Row,
    Spinner,
    TabContent,
    TabPane
} from 'reactstrap'
import { translations } from '../../utils/_translations'
import RecurringInvoiceModel from '../../models/RecurringInvoiceModel'
import BottomNavigationButtons from '../../common/BottomNavigationButtons'
import Audit from '../../common/Audit'
import ViewContacts from '../../common/entityContainers/ViewContacts'
import ViewSchedule from '../../common/entityContainers/ViewSchedule'
import Overview from './Overview'
import InvoiceRepository from '../../repositories/InvoiceRepository'
import AlertPopup from '../../common/AlertPopup'

export default class RecurringInvoice extends Component {
    constructor (props) {
        super(props)
        this.state = {
            invoices: [],
            entity: this.props.entity,
            activeTab: '1',
            obj_url: null,
            show_success: false,
            show_alert: false,
            file_count: this.props.entity.files.length || 0,
            audits: []
        }

        this.invoiceModel = new RecurringInvoiceModel(this.state.entity)
        this.toggleTab = this.toggleTab.bind(this)
        this.loadPdf = this.loadPdf.bind(this)
        this.triggerAction = this.triggerAction.bind(this)
        this.refresh = this.refresh.bind(this)
        this.getInvoices = this.getInvoices.bind(this)
    }

    componentDidMount () {
        this.getInvoices()
    }

    refresh (entity) {
        this.invoiceModel = new RecurringInvoiceModel(entity)
        this.setState({ entity: entity })
    }

    getAudits () {
        const invoiceRepository = new InvoiceRepository()
        invoiceRepository.audits('RecurringInvoice', this.props.entity.id).then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.unexpected_error })
                return
            }

            this.setState({ audits: response }, () => {
                console.log('audits', this.state.audits)
            })
        })
    }

    getInvoices () {
        const invoiceRepository = new InvoiceRepository()
        invoiceRepository.get().then(response => {
            if (!response) {
                this.setState({ show_alert: true })
                return
            }

            this.setState({ invoices: response }, () => {
                console.log('allInvoices', this.state.allInvoices)
            })
        })
    }

    triggerAction (action) {
        this.invoiceModel.completeAction(this.state.entity, action).then(response => {
            this.setState({ show_success: true }, () => {
                this.props.updateState(response, this.refresh)
            })

            setTimeout(
                function () {
                    this.setState({ show_success: false })
                }
                    .bind(this),
                2000
            )
        })
    }

    loadPdf () {
        this.invoiceModel.loadPdf().then(url => {
            console.log('url', url)
            this.setState({ obj_url: url }, () => URL.revokeObjectURL(url))
        })
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab }, () => {
                if (tab === '5' && !this.state.audits.length) {
                    this.getAudits()
                }

                if (this.state.activeTab === '6') {
                    this.loadPdf()
                }
            })
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
                            {translations.details}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '2' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('2')
                            }}
                        >
                            {translations.schedule}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '3' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('3')
                            }}
                        >
                            {translations.contacts}
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '4' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('4')
                            }}
                        >
                            {translations.documents} ({this.state.file_count})
                        </NavLink>
                    </NavItem>

                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '5' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('5')
                            }}
                        >
                            {translations.history}
                        </NavLink>
                    </NavItem>
                </Nav>
                <TabContent activeTab={this.state.activeTab}>
                    <TabPane tabId="1">
                        <Overview model={this.invoiceModel} invoices={this.invoiceModel.invoices}
                            entity={this.state.entity}
                            customers={this.props.customers}/>
                    </TabPane>

                    <TabPane tabId="2">
                        <Row>
                            <Col>
                                <ViewSchedule recurringDates={this.state.entity.schedule} entity={this.invoiceModel}
                                    customers={this.props.customers}/>
                            </Col>
                        </Row>
                    </TabPane>

                    <TabPane tabId="3">
                        <Row>
                            <Col>
                                <ViewContacts entity={this.invoiceModel} customers={this.props.customers}/>
                            </Col>
                        </Row>
                    </TabPane>

                    <TabPane tabId="4">
                        <Row>
                            <Col>
                                <Card>
                                    <CardHeader> {translations.documents} </CardHeader>
                                    <CardBody>
                                        <FileUploads updateCount={(count) => {
                                            this.setState({ file_count: count })
                                        }} entity_type="RecurringInvoice" entity={this.state.entity}
                                        user_id={this.state.entity.user_id}/>
                                    </CardBody>
                                </Card>
                            </Col>
                        </Row>
                    </TabPane>

                    <TabPane tabId="5">
                        <Row>
                            <Col>
                                {this.state.audits.length ? <Audit entity="Invoice" audits={this.state.audits}/>
                                    : <Spinner style={{
                                        width: '3rem',
                                        height: '3rem'
                                    }}/>}
                            </Col>
                        </Row>
                    </TabPane>

                    <TabPane tabId="6">
                        <Row>
                            <Col>
                                <Card>
                                    <CardHeader> {translations.pdf} </CardHeader>
                                    <CardBody>
                                        <iframe style={{ width: '400px', height: '400px' }}
                                            className="embed-responsive-item" id="viewer" src={this.state.obj_url}/>
                                    </CardBody>
                                </Card>
                            </Col>
                        </Row>
                    </TabPane>
                </TabContent>

                {this.state.show_success &&
                <Alert color="primary">
                    {translations.action_completed}
                </Alert>
                }

                <BottomNavigationButtons button1_click={(e) => this.toggleTab('6')}
                    button1={{ label: translations.view_pdf }}
                    button2_click={(e) => this.triggerAction(this.invoiceModel.isActive ? 'stop_recurring' : 'start_recurring')}
                    button2={{ label: this.invoiceModel.isActive ? translations.stop : translations.start }}/>

                <AlertPopup is_open={this.state.show_alert} message={this.state.error_message} onClose={(e) => {
                    this.setState({ show_alert: false })
                }}/>
            </React.Fragment>

        )
    }
}
