import React, { Component } from 'react'
import { Alert, Card, CardBody, CardHeader, Col, Nav, NavItem, NavLink, Row, TabContent, TabPane } from 'reactstrap'
import ExpenseModel from '../../models/ExpenseModel'
import { translations } from '../../utils/_translations'
import FileUploads from '../../documents/FileUploads'
import BottomNavigationButtons from '../../common/BottomNavigationButtons'
import CompanyModel from '../../models/CompanyModel'
import InvoiceModel from '../../models/InvoiceModel'
import EntityListTile from '../../common/entityContainers/EntityListTile'
import { icons } from '../../utils/_icons'
import Overview from './Overview'
import InvoiceRepository from '../../repositories/InvoiceRepository'
import ExpenseRepository from '../../repositories/ExpenseRepository'

export default class Expense extends Component {
    constructor (props) {
        super(props)
        this.state = {
            entity: this.props.entity,
            categories: [],
            activeTab: '1',
            obj_url: null,
            show_success: false
        }

        this.expenseModel = new ExpenseModel(this.state.entity)
        this.companyModel = new CompanyModel()
        this.invoiceModel = new InvoiceModel()
        this.toggleTab = this.toggleTab.bind(this)
        this.triggerAction = this.triggerAction.bind(this)
        this.refresh = this.refresh.bind(this)
    }

    componentDidMount () {
        this.getCategories()
    }

    refresh (entity) {
        this.expenseModel = new ExpenseModel(entity)
        this.setState({ entity: entity })
    }

    getCategories () {
        const expenseRepo = new ExpenseRepository()
        expenseRepo.getCategories().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ categories: response }, () => {
                console.log('categories', this.state.categories)
            })
        })
    }

    getCompanies () {
        this.companyModel.getCompanies().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ companies: response }, () => {
                console.log('companies', this.state.companies)
            })
        })
    }

    getInvoices () {
        const invoiceRepository = new InvoiceRepository()
        invoiceRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ invoices: response }, () => {
                console.log('invoices', this.state.invoices)
            })
        })
    }

    triggerAction (action) {
        this.expenseModel.completeAction(this.state.entity, action).then(response => {
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

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab }, () => {
                if (this.state.activeTab === '3') {
                    this.loadPdf()
                }
            })
        }
    }

    render () {
        const button1_action = !this.state.entity.invoice_id ? (e) => location.href = '/#/invoice?entity_type=expense&entity_id=' + this.state.entity.id : (e) => location.href = '/#/invoice?id=' + this.state.entity.invoice_id
        const button1_label = !this.state.entity.invoice_id ? translations.new_invoice : translations.view_invoice

        let user = null

        if (this.state.entity.assigned_to) {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(this.state.entity.assigned_to))
            user = <EntityListTile entity={translations.user}
                title={`${assigned_user[0].first_name} ${assigned_user[0].last_name}`}
                icon={icons.user}/>
        }

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
                            {translations.documents} ({this.expenseModel.fileCount})
                        </NavLink>
                    </NavItem>
                </Nav>
                <TabContent activeTab={this.state.activeTab}>
                    <TabPane tabId="1">
                        <Overview categories={this.state.categories} model={this.expenseModel}
                            user={user}
                            entity={this.state.entity}
                            customers={this.props.customers}/>
                    </TabPane>

                    <TabPane tabId="2">
                        <Row>
                            <Col>
                                <Card>
                                    <CardHeader>{translations.documents}</CardHeader>
                                    <CardBody>
                                        <FileUploads entity_type="Expense" entity={this.state.entity}
                                            user_id={this.state.entity.user_id}/>
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

                <BottomNavigationButtons button1_click={button1_action}
                    button1={{ label: button1_label }}
                    button2_click={(e) => this.triggerAction('clone_to_expense', true)}
                    button2={{ label: translations.clone_expense }}/>

            </React.Fragment>

        )
    }
}
