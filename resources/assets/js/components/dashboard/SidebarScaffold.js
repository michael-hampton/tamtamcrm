import TaskModel from '../models/TaskModel'
import { _filterOverdue, _getLast30Days, _groupByStatus } from './_utils'
import InvoiceItem from '../invoice/InvoiceItem'
import { getDefaultTableFields as defaultInvoiceFields } from '../presenters/InvoicePresenter'
import React from 'react'
import { translations } from '../utils/_translations'
import TaskItem from '../tasks/TaskItem'
import { getDefaultTableFields as defaultTaskFields } from '../presenters/TaskPresenter'
import ExpenseItem from '../expenses/ExpenseItem'
import { getDefaultTableFields as defaultExpenseFields } from '../presenters/ExpensePresenter'
import QuoteItem from '../quotes/QuoteItem'
import { getDefaultTableFields as defaultQuoteFields } from '../presenters/QuotePresenter'
import OrderItem from '../orders/OrderItem'
import { getDefaultTableFields as defaultOrderFields } from '../presenters/OrderPresenter'
import PaymentItem from '../payments/PaymentItem'
import { getDefaultTableFields as defaultPaymentFields } from '../presenters/PaymentPresenter'
import CreditItem from '../credits/CreditItem'
import { getDefaultTableFields as defaultCreditFields } from '../presenters/CreditPresenter'
import { Card, CardBody, CardHeader, ListGroup, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'

export default function SidebarScaffold (props) {
    const modules = JSON.parse(localStorage.getItem('modules'))

    return <>
        {!props.isMobile &&
        <Nav className="tabs-justify disable-scrollbars" tabs>
            {modules && modules.invoices &&
            <NavItem>
                <NavLink
                    className={props.activeTab2 === '1' ? 'active' : ''}
                    onClick={() => {
                        props.toggleTab2('1')
                    }}>
                    {translations.invoices}
                </NavLink>
            </NavItem>
            }

            {modules && modules.orders &&
            <NavItem>
                <NavLink
                    className={props.activeTab2 === '2' ? 'active' : ''}
                    onClick={() => {
                        props.toggleTab2('2')
                    }}>
                    {translations.orders}
                </NavLink>
            </NavItem>
            }

            {modules && modules.payments &&
            <NavItem>
                <NavLink
                    className={props.activeTab2 === '3' ? 'active' : ''}
                    onClick={() => {
                        props.toggleTab2('3')
                    }}>
                    {translations.payments}
                </NavLink>
            </NavItem>
            }

            {modules && modules.quotes &&
            <NavItem>
                <NavLink
                    className={props.activeTab2 === '4' ? 'active' : ''}
                    onClick={() => {
                        props.toggleTab2('4')
                    }}>
                    {translations.quotes}
                </NavLink>
            </NavItem>
            }

            {modules && modules.credits &&
            <NavItem>
                <NavLink
                    className={props.activeTab2 === '5' ? 'active' : ''}
                    onClick={() => {
                        props.toggleTab2('5')
                    }}>
                    {translations.credits}
                </NavLink>
            </NavItem>
            }

            {modules && modules.tasks &&
            <NavItem>
                <NavLink
                    className={props.activeTab2 === '6' ? 'active' : ''}
                    onClick={() => {
                        props.toggleTab2('6')
                    }}>
                    {translations.tasks}
                </NavLink>
            </NavItem>
            }
            {modules && modules.expenses &&
            <NavItem>
                <NavLink
                    className={props.activeTab2 === '7' ? 'active' : ''}
                    onClick={() => {
                        props.toggleTab2('7')
                    }}>
                    {translations.expenses}
                </NavLink>
            </NavItem>
            }
        </Nav>
        }

        <TabContent activeTab={props.activeTab2}>
            <TabPane tabId="1">
                <InvoiceSidebar invoices={props.invoices} customers={props.customers}
                    toggleViewedEntity={props.toggleViewedEntity} addUserToState={props.addUserToState}
                    viewId={props.viewId}/>
            </TabPane>

            <TabPane tabId="2">
                <OrderSidebar orders={props.orders} customers={props.customers}
                    toggleViewedEntity={props.toggleViewedEntity} addUserToState={props.addUserToState}
                    viewId={props.viewId}/>
            </TabPane>

            <TabPane tabId="3">
                <PaymentSidebar payments={props.payments} customers={props.customers}
                    toggleViewedEntity={props.toggleViewedEntity} addUserToState={props.addUserToState}
                    viewId={props.viewId}/>
            </TabPane>

            <TabPane tabId="4">
                <QuoteSidebar quotes={props.quotes} customers={props.customers}
                    toggleViewedEntity={props.toggleViewedEntity} addUserToState={props.addUserToState}
                    viewId={props.viewId}/>
            </TabPane>

            <TabPane tabId="5">
                <CreditSidebar credits={props.credits} customers={props.customers}
                    toggleViewedEntity={props.toggleViewedEntity} addUserToState={props.addUserToState}
                    viewId={props.viewId}/>
            </TabPane>

            <TabPane tabId="6">
                <TaskSidebar tasks={props.tasks} customers={props.customers}
                    toggleViewedEntity={props.toggleViewedEntity} addUserToState={props.addUserToState}
                    viewId={props.viewId}/>
            </TabPane>
            <TabPane tabId="7">
                <ExpenseSidebar expenses={props.expenses} customers={props.customers}
                    toggleViewedEntity={props.toggleViewedEntity} addUserToState={props.addUserToState}
                    viewId={props.viewId}/>
            </TabPane>
        </TabContent>
    </>
}

export function QuoteSidebar (props) {
    /** ************************************************** Quotes ****************************************************/
    // expired
    const filterQuotesByExpiration = props.quotes && props.quotes.length ? _filterOverdue(props.quotes) : []
    const arrOverdueQuotes = filterQuotesByExpiration.length ? _groupByStatus(filterQuotesByExpiration, 2, 'status_id') : []

    // last 30 days
    const filterQuotesLast30Days = props.quotes && props.quotes.length ? _getLast30Days(props.quotes) : []
    const arrRecentQuotes = filterQuotesLast30Days.length ? _groupByStatus(filterQuotesLast30Days, 1, 'status_id') : []

    const overdue_quotes = props.customers.length && arrOverdueQuotes.length
        ? <QuoteItem ignoredColumns={defaultQuoteFields()} showCheckboxes={false} updateInvoice={(entities) => {
            props.addUserToState('quotes', entities)
        }} quotes={arrOverdueQuotes} force_mobile={true} show_list={true} users={[]}
        custom_fields={[]} customers={props.customers}
        viewId={props.viewId}
        toggleViewedEntity={(id, title = null, edit = null) => {
            props.toggleViewedEntity('Quote', props.quotes, id, title, edit)
        }}
        bulk={[]}
        onChangeBulk={null}/> : null

    const recent_quotes = props.customers.length && arrRecentQuotes.length
        ? <QuoteItem ignoredColumns={defaultQuoteFields()} showCheckboxes={false} updateInvoice={(entities) => {
            props.addUserToState('quotes', entities)
        }} quotes={arrRecentQuotes} force_mobile={true} show_list={true} users={[]}
        custom_fields={[]} customers={props.customers}
        viewId={props.viewId}
        toggleViewedEntity={(id, title = null, edit = null) => {
            props.toggleViewedEntity('Quote', props.quotes, id, title, edit)
        }}
        bulk={[]}
        onChangeBulk={null}/> : null

    return <DashboardSidebar list1_title={translations.overdue_quotes} list1={overdue_quotes}
        list1_count={arrOverdueQuotes.length ? arrOverdueQuotes.length : ''}
        list2_title={translations.recent_quotes}
        list2={recent_quotes}
        list2_count={arrRecentQuotes.length ? arrRecentQuotes.length : ''}
    />
}

export function CreditSidebar (props) {
    /** ************************************************** Credits ****************************************************/
    // expired
    const filterCreditsByExpiration = props.credits && props.credits.length ? _filterOverdue(props.credits) : []
    const arrOverdueCredits = filterCreditsByExpiration.length ? _groupByStatus(filterCreditsByExpiration, 2, 'status_id') : []

    // last 30 days
    const filterCreditsLast30Days = props.credits && props.credits.length ? _getLast30Days(props.credits) : []
    const arrRecentCredits = filterCreditsLast30Days.length ? _groupByStatus(filterCreditsLast30Days, 1, 'status_id') : []

    const overdue_credits = props.customers.length && arrOverdueCredits.length
        ? <CreditItem ignoredColumns={defaultCreditFields()} showCheckboxes={false} updateInvoice={(entities) => {
            props.addUserToState('quotes', entities)
        }} credits={arrOverdueCredits} force_mobile={true} show_list={true} users={[]}
        custom_fields={[]} customers={props.customers}
        viewId={props.viewId}
        toggleViewedEntity={(id, title = null, edit = null) => {
            props.toggleViewedEntity('Credit', props.credits, id, title, edit)
        }}
        bulk={[]}
        onChangeBulk={null}/> : null

    const recent_credits = props.customers.length && arrRecentCredits.length
        ? <CreditItem ignoredColumns={defaultCreditFields()} showCheckboxes={false} updateInvoice={(entities) => {
            props.addUserToState('credits', entities)
        }} credits={arrRecentCredits} force_mobile={true} show_list={true} users={[]}
        custom_fields={[]} customers={props.customers}
        viewId={props.viewId}
        toggleViewedEntity={(id, title = null, edit = null) => {
            props.toggleViewedEntity('Credit', props.credits, id, title, edit)
        }}
        bulk={[]}
        onChangeBulk={null}/> : null

    return <DashboardSidebar list1_title={translations.overdue_credits} list1={overdue_credits}
        list1_count={arrOverdueCredits.length ? arrOverdueCredits.length : ''}
        list2_title={translations.recent_credits}
        list2={recent_credits}
        list2_count={arrRecentCredits.length ? arrRecentCredits.length : ''}
    />
}

export function InvoiceSidebar (props) {
    /** ************************************************** Invoice ****************************************************/
    // expired
    const filterInvociesByExpiration = props.invoices && props.invoices.length ? _filterOverdue(props.invoices) : []
    const arrOverdueInvoices = filterInvociesByExpiration.length ? _groupByStatus(filterInvociesByExpiration, 2, 'status_id') : []

    // last 30 days
    const filterInvoicesLast30Days = props.invoices && props.invoices.length ? _getLast30Days(props.invoices) : []
    const arrRecentInvoices = filterInvoicesLast30Days.length ? _groupByStatus(filterInvoicesLast30Days, 1, 'status_id') : []

    const overdue_invoices = props.customers.length && arrOverdueInvoices.length
        ? <InvoiceItem ignoredColumns={defaultInvoiceFields()} showCheckboxes={false}
            updateInvoice={(entities) => {
                props.addUserToState('invoices', entities)
            }} invoices={arrOverdueInvoices} force_mobile={true} show_list={true} users={[]}
            custom_fields={[]} customers={props.customers}
            viewId={props.viewId}
            toggleViewedEntity={(id, title = null, edit = null) => {
                props.toggleViewedEntity('Invoice', props.invoices, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/>
        : null

    const recent_invoices = props.customers.length && arrRecentInvoices.length
        ? <InvoiceItem ignoredColumns={defaultInvoiceFields()} showCheckboxes={false}
            updateInvoice={(entities) => {
                props.addUserToState('invoices', entities)
            }} invoices={arrRecentInvoices} force_mobile={true} show_list={true} users={[]}
            custom_fields={[]} customers={props.customers}
            viewId={props.viewId}
            toggleViewedEntity={(id, title = null, edit = null) => {
                props.toggleViewedEntity('Invoice', props.invoices, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

    return <DashboardSidebar list1_title={translations.overdue_invoices} list1={overdue_invoices}
        list1_count={arrOverdueInvoices.length ? arrOverdueInvoices.length : ''}
        list2_title={translations.recent_invoices}
        list2={recent_invoices}
        list2_count={arrRecentInvoices.length ? arrRecentInvoices.length : ''}
    />
}

export function TaskSidebar (props) {
    /** ************************************************** Tasks ****************************************************/
    // last 30 days
    const filterTasksLast30Days = props.tasks.length ? _getLast30Days(props.tasks) : []
    const arrRecentTasks = filterTasksLast30Days.length ? filterTasksLast30Days.filter((item) => {
        const taskModel = new TaskModel(item)
        return !item.deleted_at && !taskModel.isRunning
    }) : []

    // TODO - Running tasks
    const arrRunningTasks = props.tasks.length ? props.tasks.filter((item) => {
        const taskModel = new TaskModel(item)
        return !item.deleted_at && taskModel.isRunning
    }) : []

    const recent_tasks = props.customers.length && arrRecentTasks.length
        ? <TaskItem ignoredColumns={defaultTaskFields()} showCheckboxes={false} force_mobile={true}
            action={(entities) => {
                props.addUserToState('tasks', entities)
            }} tasks={arrRecentTasks} show_list={true}
            users={JSON.parse(localStorage.getItem('users'))}
            custom_fields={[]} customers={props.customers}
            viewId={props.viewId}
            toggleViewedEntity={(id, title = null, edit = null) => {
                props.toggleViewedEntity('Task', props.tasks, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

    const running_tasks = props.customers.length && arrRunningTasks.length
        ? <TaskItem ignoredColumns={defaultTaskFields()} showCheckboxes={false} action={(entities) => {
            props.addUserToState('tasks', entities)
        }} tasks={arrRunningTasks} force_mobile={true} show_list={true}
        users={JSON.parse(localStorage.getItem('users'))}
        custom_fields={[]} customers={props.customers}
        viewId={props.viewId}
        toggleViewedEntity={(id, title = null, edit = null) => {
            props.toggleViewedEntity('Task', props.tasks, id, title, edit)
        }}
        bulk={[]}
        onChangeBulk={null}/> : null

    return <DashboardSidebar list1_title={translations.recent_tasks} list1={recent_tasks}
        list1_count={arrRecentTasks.length ? arrRecentTasks.length : ''}
        list2_title={translations.running_tasks}
        list2={running_tasks}
        list2_count={arrRunningTasks.length ? arrRunningTasks.length : ''}
    />
}

export function ExpenseSidebar (props) {
    /** ************************************************** Expenses ****************************************************/
    // last 30 days
    const arrRecentExpenses = props.expenses.length ? _getLast30Days(props.expenses) : []

    const recent_expenses = props.customers.length && arrRecentExpenses.length
        ? <ExpenseItem ignoredColumns={defaultExpenseFields()} showCheckboxes={false}
            updateExpenses={(entities) => {
                props.addUserToState('expenses', entities)
            }} expenses={arrRecentExpenses} force_mobile={true} show_list={true} users={[]}
            custom_fields={[]} customers={props.customers}
            viewId={props.viewId}
            toggleViewedEntity={(id, title = null, edit = null) => {
                props.toggleViewedEntity('Expense', props.expenses, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

    return <DashboardSidebar list1_title={translations.recent_expenses} list1={recent_expenses}
        list1_count={arrRecentExpenses.length ? arrRecentExpenses.length : ''}
    />
}

export function PaymentSidebar (props) {
    /** ************************************************** Payments ****************************************************/
    // last 30 days
    const filterPaymentsLast30Days = props.payments && props.payments.length ? _getLast30Days(props.payments) : []
    const arrRecentPayments = filterPaymentsLast30Days.length ? _groupByStatus(filterPaymentsLast30Days, 4, 'status_id') : []

    const recent_payments = props.customers.length && arrRecentPayments.length
        ? <PaymentItem ignoredColumns={defaultPaymentFields()} showCheckboxes={false}
            updateCustomers={(entities) => {
                props.addUserToState('payments', entities)
            }} payments={arrRecentPayments} force_mobile={true} credits={props.credits}
            invoices={props.invoices} show_list={true}
            users={[]}
            custom_fields={[]} customers={props.customers}
            viewId={props.viewId}
            toggleViewedEntity={(id, title = null, edit = null) => {
                props.toggleViewedEntity('Payment', props.payments, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

    return <DashboardSidebar list1_title={translations.recent_payments} list1={recent_payments}
        list1_count={arrRecentPayments.length ? arrRecentPayments.length : ''}
    />
}

export function OrderSidebar (props) {
    /** ************************************************** Orders ****************************************************/
    // expired
    const filterOrdersByExpiration = props.orders && props.orders.length ? _filterOverdue(props.orders) : []
    const arrOverdueOrders = filterOrdersByExpiration.length ? _groupByStatus(filterOrdersByExpiration, 2, 'status_id') : []

    // last 30 days
    const filterOrdersLast30Days = props.orders && props.orders.length ? _getLast30Days(props.orders) : []
    const arrRecentOrders = filterOrdersLast30Days.length ? _groupByStatus(filterOrdersLast30Days, 1, 'status_id') : []

    const overdue_orders = props.customers.length && arrOverdueOrders.length
        ? <OrderItem ignoredColumns={defaultOrderFields()} showCheckboxes={false} updateOrder={(entities) => {
            props.addUserToState('orders', entities)
        }} orders={arrOverdueOrders} force_mobile={true} show_list={true} users={[]}
        custom_fields={[]} customers={props.customers}
        viewId={props.viewId}
        toggleViewedEntity={(id, title = null, edit = null) => {
            props.toggleViewedEntity('Order', props.orders, id, title, edit)
        }}
        bulk={[]}
        onChangeBulk={null}/> : null

    const recent_orders = props.customers.length && arrRecentOrders.length
        ? <OrderItem ignoredColumns={defaultOrderFields()} showCheckboxes={false} updateOrder={(entities) => {
            props.addUserToState('orders', entities)
        }} orders={arrRecentOrders} force_mobile={true} show_list={true} users={[]}
        custom_fields={[]} customers={props.customers}
        viewId={props.viewId}
        toggleViewedEntity={(id, title = null, edit = null) => {
            props.toggleViewedEntity('Order', props.orders, id, title, edit)
        }}
        bulk={[]}
        onChangeBulk={null}/> : null

    return <DashboardSidebar list1_title={translations.recent_orders} list1={recent_orders}
        list1_count={arrRecentOrders.length ? arrRecentOrders.length : ''}
        list2_title={translations.overdue_orders}
        list2={overdue_orders}
        list2_count={arrOverdueOrders.length ? arrOverdueOrders.length : ''}
    />
}

export function DashboardSidebar (props) {
    return <>
        <Card>
            <CardHeader>{props.list1_title} {props.list1_count}</CardHeader>
            <CardBody style={{ height: '220px', overflowY: 'auto' }}>
                <ListGroup>
                    {props.list1}
                </ListGroup>
            </CardBody>
        </Card>

        {Object.prototype.hasOwnProperty.call(props, 'list2') &&
        <Card>
            <CardHeader>{props.list2_title} {props.list2_count}</CardHeader>
            <CardBody style={{ height: '220px', overflowY: 'auto' }}>
                <ListGroup>
                    {props.list2}
                </ListGroup>
            </CardBody>
        </Card>
        }
    </>
}
