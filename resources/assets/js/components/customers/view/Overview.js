import React from 'react'
import { ListGroup, Row } from 'reactstrap'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import { translations } from '../../utils/_translations'
import InfoMessage from '../../common/entityContainers/InfoMessage'
import { getEntityIcon, icons } from '../../utils/_icons'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import SectionItem from '../../common/entityContainers/SectionItem'
import EntityListTile from '../../common/entityContainers/EntityListTile'

export default function Overview (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''
    const modules = JSON.parse(localStorage.getItem('modules'))

    let user = null

    if (props.entity.assigned_to) {
        const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(props.entity.assigned_to))
        user = <EntityListTile entity={translations.user}
            title={`${assigned_user[0].first_name} ${assigned_user[0].last_name}`}
            icon={icons.user}/>
    }

    const fields = []

    if (props.model.hasLanguage && props.model.languageId !== parseInt(props.settings.language_id)) {
        fields.language =
            JSON.parse(localStorage.getItem('languages')).filter(language => language.id === props.model.languageId)[0].name
    }

    if (props.model.hasCurrency && props.model.currencyId !== parseInt(props.settings.currency_id)) {
        fields.currency =
            JSON.parse(localStorage.getItem('currencies')).filter(currency => currency.id === props.model.currencyId)[0].name
    }

    if (props.entity.custom_value1.length) {
        const label1 = props.model.getCustomFieldLabel('Customer', 'custom_value1')
        fields[label1] = props.model.formatCustomValue(
            'Customer',
            'custom_value1',
            props.entity.custom_value1
        )
    }

    if (props.entity.custom_value2.length) {
        const label2 = props.model.getCustomFieldLabel('Customer', 'custom_value2')
        fields[label2] = props.model.formatCustomValue(
            'Customer',
            'custom_value2',
            props.entity.custom_value2
        )
    }

    if (props.entity.custom_value3.length) {
        const label3 = props.model.getCustomFieldLabel('Customer', 'custom_value3')
        fields[label3] = props.model.formatCustomValue(
            'Customer',
            'custom_value3',
            props.entity.custom_value3
        )
    }

    if (props.entity.custom_value4.length) {
        const label4 = props.model.getCustomFieldLabel('Customer', 'custom_value4')
        fields[label4] = props.model.formatCustomValue(
            'Customer',
            'custom_value4',
            props.entity.custom_value4
        )
    }

    return <React.Fragment>
        <ViewEntityHeader heading_1={translations.paid_to_date} value_1={props.entity.paid_to_date}
            heading_2={translations.balance} value_2={props.entity.balance}/>

        {!!props.entity.private_notes.length &&
        <Row>
            <InfoMessage icon={icons.lock} message={props.entity.private_notes}/>
        </Row>
        }

        {!!props.entity.public_notes.length &&
        <Row>
            <InfoMessage message={props.entity.public_notes}/>
        </Row>
        }

        <Row>
            {user}
        </Row>

        <FieldGrid fields={fields}/>

        <Row>
            <ListGroup className="col-12 mb-2">
                {props.gateway_tokens}
            </ListGroup>

            <ListGroup className="col-12">
                {modules && modules.invoices &&
                <SectionItem link={`/#/invoice?customer_id=${props.entity.id}`}
                    icon={getEntityIcon('Invoice')} title={translations.invoices}/>
                }

                {modules && modules.projects &&
                <SectionItem link={`/#/projects?customer_id=${props.entity.id}`}
                    icon={getEntityIcon('Project')} title={translations.projects}/>
                }

                {modules && modules.credits &&
                <SectionItem link={`/#/credits?customer_id=${props.entity.id}`}
                    icon={getEntityIcon('Credit')} title={translations.credits}/>
                }

                {modules && modules.quotes &&
                <SectionItem link={`/#/quotes?customer_id=${props.entity.id}`}
                    icon={getEntityIcon('Quote')} title={translations.quotes}/>
                }

                {modules && modules.recurring_invoices &&
                <SectionItem link={`/#/recurring-invoices?customer_id=${props.entity.id}`}
                    icon={getEntityIcon('RecurringInvoice')} title={translations.recurring_invoices}/>
                }

                {modules && modules.recurring_quotes &&
                <SectionItem link={`/#/recurring-quotes?customer_id=${props.entity.id}`}
                    icon={getEntityIcon('RecurringQuote')} title={translations.recurring_quotes}/>
                }

                {modules && modules.payments &&
                <SectionItem link={`/#/payments?customer_id=${props.entity.id}`}
                    icon={getEntityIcon('Payment')} title={translations.payments}/>
                }

                {modules && modules.tasks &&
                <SectionItem link={`/#/tasks?customer_id=${props.entity.id}`} icon={getEntityIcon('Task')}
                    title={translations.tasks}/>
                }

                {modules && modules.deals &&
                <SectionItem link={`/#/deals?customer_id=${props.entity.id}`} icon={getEntityIcon('Deal')}
                    title={translations.deals}/>
                }

                {modules && modules.leads &&
                <SectionItem link={`/#/leads?customer_id=${props.entity.id}`} icon={getEntityIcon('Lead')}
                    title={translations.leads}/>
                }

                {modules && modules.expenses &&
                <SectionItem link={`/#/expenses?customer_id=${props.entity.id}`}
                    icon={getEntityIcon('Expense')} title={translations.expenses}/>
                }

                {modules && modules.orders &&
                <SectionItem link={`/#/orders?customer_id=${props.entity.id}`} icon={getEntityIcon('Order')}
                    title={translations.orders}/>
                }

            </ListGroup>
        </Row>
    </React.Fragment>
}
