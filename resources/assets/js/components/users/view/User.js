import React, { Component } from 'react'
import { ListGroup, Row } from 'reactstrap'
import { getEntityIcon, icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import InfoItem from '../../common/entityContainers/InfoItem'
import PlainEntityHeader from '../../common/entityContainers/PlanEntityHeader'
import SectionItem from '../../common/entityContainers/SectionItem'

export default class User extends Component {
    render () {
        const modules = JSON.parse(localStorage.getItem('modules'))
        return (
            <React.Fragment>
                <PlainEntityHeader heading_1={translations.email} value_1={this.props.entity.email}
                    heading_2={translations.phone_number} value_2={this.props.entity.phone}/>

                <Row>
                    <ListGroup className="col-12">
                        <InfoItem icon={icons.user}
                            value={`${this.props.entity.first_name} ${this.props.entity.last_name}`}
                            title={translations.name}/>
                        <InfoItem icon={icons.email} value={this.props.entity.email}
                            title={translations.email}/>
                        <InfoItem icon={icons.phone} value={this.props.entity.phone_number}
                            title={translations.phone_number}/>
                    </ListGroup>
                </Row>

                <ListGroup className="col-12">
                    {modules && modules.invoices &&
                    <SectionItem link={`/#/invoice?user_id=${this.props.entity.id}`}
                        icon={getEntityIcon('Invoice')} title={translations.invoices}/>
                    }

                    {modules && modules.projects &&
                    <SectionItem link={`/#/projects?user_id=${this.props.entity.id}`}
                        icon={getEntityIcon('Project')} title={translations.projects}/>
                    }

                    {modules && modules.credits &&
                    <SectionItem link={`/#/credits?user_id=${this.props.entity.id}`}
                        icon={getEntityIcon('Credit')} title={translations.credits}/>
                    }

                    {modules && modules.quotes &&
                    <SectionItem link={`/#/quotes?user_id=${this.props.entity.id}`}
                        icon={getEntityIcon('Quote')} title={translations.quotes}/>
                    }

                    {modules && modules.recurring_invoices &&
                    <SectionItem link={`/#/recurring-invoices?user_id=${this.props.entity.id}`}
                        icon={getEntityIcon('RecurringInvoice')} title={translations.recurring_invoices}/>
                    }

                    {modules && modules.recurring_quotes &&
                    <SectionItem link={`/#/recurring-quotes?user_id=${this.props.entity.id}`}
                        icon={getEntityIcon('RecurringQuote')} title={translations.recurring_quotes}/>
                    }

                    {modules && modules.tasks &&
                    <SectionItem link={`/#/tasks?user_id=${this.props.entity.id}`} icon={getEntityIcon('Task')}
                        title={translations.tasks}/>
                    }

                    {modules && modules.deals &&
                    <SectionItem link={`/#/deals?user_id=${this.props.entity.id}`} icon={getEntityIcon('Deal')}
                        title={translations.deals}/>
                    }

                    {modules && modules.leads &&
                    <SectionItem link={`/#/leads?user_id=${this.props.entity.id}`} icon={getEntityIcon('Lead')}
                        title={translations.leads}/>
                    }

                    {modules && modules.expenses &&
                    <SectionItem link={`/#/expenses?user_id=${this.props.entity.id}`}
                        icon={getEntityIcon('Expense')} title={translations.expenses}/>
                    }

                    {modules && modules.orders &&
                    <SectionItem link={`/#/orders?user_id=${this.props.entity.id}`} icon={getEntityIcon('Order')}
                        title={translations.orders}/>
                    }

                </ListGroup>

            </React.Fragment>

        )
    }
}
