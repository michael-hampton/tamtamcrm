import { consts } from '../utils/_consts'
import { getStyle, hexToRgba } from '@coreui/coreui/dist/js/coreui-utilities'
import __formatData, { _filterOverdue, _makeLabels, _removeNullValues } from './_utils'
import moment from 'moment'
import React from 'react'
import DashboardChart from './DashboardChart'

const brandInfo = getStyle('--info')
const brandSuccess = getStyle('--success')
const brandDanger = getStyle('--danger')
const brandWarning = getStyle('--warning')
const brandPrimary = getStyle('--primary')

export default function DashboardPanels (props) {
    const now = new Date()
    const daysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate()

    let currentMoment = moment().startOf('month')
    let endMoment = moment().endOf('month')

    if (props.start_date !== null) {
        currentMoment = moment(props.start_date)
    }

    if (props.end_date !== null) {
        endMoment = moment(props.end_date)
    }

    const start = currentMoment.format('YYYY-MM-DD')
    const end = endMoment.format('YYYY-MM-DD')
    // const currentMoment = moment('2020-02-03')
    // const endMoment = moment('2020-03-17')
    const dates = _makeLabels(currentMoment, endMoment)
    const charts = []
    const modules = JSON.parse(localStorage.getItem('modules'))

    if (modules && modules.invoices) {
        const invoiceChartData = getInvoiceChartData(start, end, dates, props)
        charts.push(invoiceChartData)
    }

    if (modules && modules.orders) {
        const orderChartData = getOrderChartData(start, end, dates, props)
        charts.push(orderChartData)
    }

    if (modules && modules.payments) {
        const paymentChartData = getPaymentChartData(start, end, dates, props)
        charts.push(paymentChartData)
    }

    if (modules && modules.quotes) {
        const quoteChartData = getQuoteChartData(start, end, dates, props)
        charts.push(quoteChartData)
    }

    if (modules && modules.credits) {
        const creditChartData = getCreditChartData(start, end, dates, props)
        charts.push(creditChartData)
    }

    if (modules && modules.tasks) {
        const taskChartData = getTaskChartData(start, end, dates, props)
        charts.push(taskChartData)
    }

    if (modules && modules.expenses) {
        const expenseChartData = getExpenseChartData(start, end, dates, props)
        charts.push(expenseChartData)
    }

    return <DashboardChart doExport={props.doExport} charts={charts} />
}

export function getInvoiceChartData (start, end, dates, props) {
    const invoiceActive = __formatData(props.invoices, consts.invoice_status_draft, start, end, 'total', 'status_id')
    const invoiceOutstanding = __formatData(props.invoices, consts.invoice_status_sent, start, end, 'total', 'status_id')
    const invoicePaid = __formatData(props.invoices, consts.invoice_status_paid, start, end, 'total', 'status_id')
    const invoiceCancelled = __formatData(props.invoices, consts.invoice_status_cancelled, start, end, 'total', 'status_id')

    const filterInvoicesByExpiration = _filterOverdue(props.invoices)
    const invoiceOverdue = __formatData(filterInvoicesByExpiration, consts.invoice_status_sent, start, end, 'total', 'status_id')

    const buttons = {}
    const datasets = []

    if (props.dashboard_filters.Invoices.Active === 1) {
        buttons.Active = {
            avg: invoiceActive && Object.keys(invoiceActive).length ? invoiceActive.avg : 0,
            pct: invoiceActive && Object.keys(invoiceActive).length ? invoiceActive.pct : 0,
            value: invoiceActive && Object.keys(invoiceActive).length ? invoiceActive.value : 0
        }

        datasets.push(
            {
                label: 'Active',
                backgroundColor: hexToRgba(brandInfo, 10),
                borderColor: brandInfo,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 2,
                data: invoiceActive && Object.keys(invoiceActive).length ? Object.values(invoiceActive.data) : []
            }
        )
    }

    if (props.dashboard_filters.Invoices.Outstanding === 1) {
        buttons.Outstanding = {
            avg: invoiceOutstanding && Object.keys(invoiceOutstanding).length ? invoiceOutstanding.avg : 0,
            pct: invoiceOutstanding && Object.keys(invoiceOutstanding).length ? invoiceOutstanding.pct : 0,
            value: invoiceOutstanding && Object.keys(invoiceOutstanding).length ? invoiceOutstanding.value : 0
        }

        datasets.push(
            {
                label: 'Outstanding',
                backgroundColor: 'transparent',
                borderColor: brandDanger,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: invoiceOutstanding && Object.keys(invoiceOutstanding).length ? Object.values(invoiceOutstanding.data) : []
            }
        )
    }

    if (props.dashboard_filters.Invoices.Paid === 1) {
        buttons.Paid = {
            avg: invoicePaid && Object.keys(invoicePaid).length ? invoicePaid.avg : 0,
            pct: invoicePaid && Object.keys(invoicePaid).length ? invoicePaid.pct : 0,
            value: invoicePaid && Object.keys(invoicePaid).length ? invoicePaid.value : 0
        }

        datasets.push(
            {
                label: 'Paid',
                backgroundColor: 'transparent',
                borderColor: brandSuccess,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: invoicePaid && Object.keys(invoicePaid).length ? Object.values(invoicePaid.data) : []
            }
        )
    }

    if (props.dashboard_filters.Invoices.Cancelled === 1) {
        buttons.Cancelled = {
            avg: invoiceCancelled && Object.keys(invoiceCancelled).length ? invoiceCancelled.avg : 0,
            pct: invoiceCancelled && Object.keys(invoiceCancelled).length ? invoiceCancelled.pct : 0,
            value: invoiceCancelled && Object.keys(invoiceCancelled).length ? invoiceCancelled.value : 0
        }

        datasets.push(
            {
                label: 'Cancelled',
                backgroundColor: 'transparent',
                borderColor: brandDanger,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: invoiceCancelled && Object.keys(invoiceCancelled).length ? Object.values(invoiceCancelled.data) : []
            }
        )
    }

    if (props.dashboard_filters.Invoices.Overdue === 1) {
        buttons.Overdue = {
            avg: invoiceOverdue && Object.keys(invoiceOverdue).length ? invoiceOverdue.avg : 0,
            pct: invoiceOverdue && Object.keys(invoiceOverdue).length ? invoiceOverdue.pct : 0,
            value: invoiceOverdue && Object.keys(invoiceOverdue).length ? invoiceOverdue.value : 0
        }

        datasets.push(
            {
                label: 'Overdue',
                backgroundColor: 'transparent',
                borderColor: brandDanger,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: invoiceOverdue && Object.keys(invoiceOverdue).length ? Object.values(invoiceOverdue.data) : []
            }
        )
    }

    return {
        name: 'Invoices',
        labels: dates,
        buttons: buttons,
        datasets: datasets
    }
}

export function getQuoteChartData (start, end, dates, props) {
    const quoteActive = __formatData(props.quotes, consts.quote_status_draft, start, end, 'total', 'status_id')
    const quoteApproved = __formatData(props.quotes, consts.quote_status_approved, start, end, 'total', 'status_id')
    const quoteUnapproved = __formatData(props.quotes, consts.quote_status_sent, start, end, 'total', 'status_id')

    const filterQuotesByExpiration = props.quotes && props.quotes.length ? _filterOverdue(props.quotes) : []
    const quoteOverdue = __formatData(filterQuotesByExpiration, consts.quote_status_sent, start, end, 'total', 'status_id')

    const buttons = {}
    const datasets = []

    if (props.dashboard_filters.Quotes.Active === 1) {
        buttons.Active = {
            avg: quoteActive && Object.keys(quoteActive).length ? quoteActive.avg : 0,
            pct: quoteActive && Object.keys(quoteActive).length ? quoteActive.pct : 0,
            value: quoteActive && Object.keys(quoteActive).length ? quoteActive.value : 0
        }

        datasets.push(
            {
                label: 'Active',
                backgroundColor: hexToRgba(brandInfo, 10),
                borderColor: brandInfo,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 2,
                data: quoteActive && Object.keys(quoteActive).length ? Object.values(quoteActive.data) : []
            }
        )
    }

    if (props.dashboard_filters.Quotes.Approved === 1) {
        buttons.Approved = {
            avg: quoteApproved && Object.keys(quoteApproved).length ? quoteActive.avg : 0,
            pct: quoteApproved && Object.keys(quoteApproved).length ? quoteActive.pct : 0,
            value: quoteApproved && Object.keys(quoteApproved).length ? quoteActive.value : 0
        }

        datasets.push(
            {
                label: 'Approved',
                backgroundColor: 'transparent',
                borderColor: brandSuccess,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: quoteApproved && Object.keys(quoteApproved).length ? Object.values(quoteApproved.data) : []
            }
        )
    }

    if (props.dashboard_filters.Quotes.Unapproved === 1) {
        buttons.Unapproved = {
            avg: quoteUnapproved && Object.keys(quoteUnapproved).length ? quoteActive.avg : 0,
            pct: quoteUnapproved && Object.keys(quoteUnapproved).length ? quoteActive.pct : 0,
            value: quoteUnapproved && Object.keys(quoteUnapproved).length ? quoteActive.value : 0
        }

        datasets.push(
            {
                label: 'Unapproved',
                backgroundColor: 'transparent',
                borderColor: brandDanger,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 2,
                data: quoteUnapproved && Object.keys(quoteUnapproved).length ? Object.values(quoteUnapproved.data) : []
            }
        )
    }

    if (props.dashboard_filters.Quotes.Overdue === 1) {
        buttons.Overdue = {
            avg: quoteOverdue && Object.keys(quoteOverdue).length ? quoteOverdue.avg : 0,
            pct: quoteOverdue && Object.keys(quoteOverdue).length ? quoteOverdue.pct : 0,
            value: quoteOverdue && Object.keys(quoteOverdue).length ? quoteOverdue.value : 0
        }

        datasets.push(
            {
                label: 'Overdue',
                backgroundColor: 'transparent',
                borderColor: brandDanger,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: quoteOverdue && Object.keys(quoteOverdue).length ? Object.values(quoteOverdue.data) : []
            }
        )
    }

    return {
        name: 'Quotes',
        labels: dates,
        buttons: buttons,
        datasets: datasets
    }
}

export function getCreditChartData (start, end, dates, props) {
    const creditActive = __formatData(props.credits, consts.credit_status_draft, start, end, 'total', 'status_id')
    const creditCompleted = __formatData(props.credits, consts.credit_status_applied, start, end, 'total', 'status_id')
    const creditSent = __formatData(props.credits, consts.credit_status_sent, start, end, 'total', 'status_id')

    const filterCreditsByExpiration = _filterOverdue(props.credits)
    const creditOverdue = __formatData(filterCreditsByExpiration, consts.credit_status_sent, start, end, 'total', 'status_id')

    const buttons = {}
    const datasets = []

    if (props.dashboard_filters.Credits.Active === 1) {
        buttons.Active = {
            avg: creditActive && Object.keys(creditActive).length ? creditActive.avg : 0,
            pct: creditActive && Object.keys(creditActive).length ? creditActive.pct : 0,
            value: creditActive && Object.keys(creditActive).length ? creditActive.value : 0
        }

        datasets.push(
            {
                label: 'Active',
                backgroundColor: hexToRgba(brandInfo, 10),
                borderColor: brandInfo,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 2,
                data: creditActive && Object.keys(creditActive).length ? Object.values(creditActive.data) : []
            }
        )
    }

    if (props.dashboard_filters.Credits.Completed === 1) {
        buttons.Completed = {
            avg: creditCompleted && Object.keys(creditCompleted).length ? creditCompleted.avg : 0,
            pct: creditCompleted && Object.keys(creditCompleted).length ? creditCompleted.pct : 0,
            value: creditCompleted && Object.keys(creditCompleted).length ? creditCompleted.value : 0
        }

        datasets.push(
            {
                label: 'Completed',
                backgroundColor: 'transparent',
                borderColor: brandSuccess,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: creditCompleted && Object.keys(creditCompleted).length ? Object.values(creditCompleted.data) : []
            }
        )
    }

    if (props.dashboard_filters.Credits.Sent === 1) {
        buttons.Sent = {
            avg: creditSent && Object.keys(creditSent).length ? creditSent.avg : 0,
            pct: creditSent && Object.keys(creditSent).length ? creditSent.pct : 0,
            value: creditSent && Object.keys(creditSent).length ? creditSent.value : 0
        }

        datasets.push(
            {
                label: 'Sent',
                backgroundColor: 'transparent',
                borderColor: brandWarning,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 2,
                data: creditSent && Object.keys(creditSent).length ? Object.values(creditSent.data) : []
            }
        )
    }

    if (props.dashboard_filters.Credits.Overdue === 1) {
        buttons.Overdue = {
            avg: creditOverdue && Object.keys(creditOverdue).length ? creditOverdue.avg : 0,
            pct: creditOverdue && Object.keys(creditOverdue).length ? creditOverdue.pct : 0,
            value: creditOverdue && Object.keys(creditOverdue).length ? creditOverdue.value : 0
        }

        datasets.push(
            {
                label: 'Overdue',
                backgroundColor: 'transparent',
                borderColor: brandDanger,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: creditOverdue && Object.keys(creditOverdue).length ? Object.values(creditOverdue.data) : []
            }
        )
    }

    return {
        name: 'Credits',
        labels: dates,
        buttons: buttons,
        datasets: datasets
    }
}

export function getOrderChartData (start, end, dates, props) {
    const orderHeld = __formatData(props.orders, consts.order_status_held, start, end, 'total', 'status_id')
    const orderDraft = __formatData(props.orders, consts.order_status_draft, start, end, 'total', 'status_id')
    const orderBackordered = __formatData(props.orders, consts.order_status_backorder, start, end, 'total', 'status_id')
    const orderCancelled = __formatData(props.orders, consts.order_status_cancelled, start, end, 'total', 'status_id')
    const orderSent = __formatData(props.orders, consts.order_status_sent, start, end, 'total', 'status_id')
    const orderCompleted = __formatData(props.orders, consts.order_status_complete, start, end, 'total', 'status_id')

    const filterOrdersByExpiration = _filterOverdue(props.orders)
    const orderOverdue = __formatData(filterOrdersByExpiration, 1, start, end, 'total', 'status_id')

    const buttons = {}
    const datasets = []

    if (props.dashboard_filters.Orders.Draft === 1) {
        buttons.Draft = {
            avg: orderDraft && Object.keys(orderDraft).length ? orderDraft.avg : 0,
            pct: orderDraft && Object.keys(orderDraft).length ? orderDraft.pct : 0,
            value: orderDraft && Object.keys(orderDraft).length ? orderDraft.value : 0
        }

        datasets.push(
            {
                label: 'Draft',
                backgroundColor: hexToRgba(brandInfo, 10),
                borderColor: brandInfo,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 2,
                data: orderDraft && Object.keys(orderDraft).length ? Object.values(orderDraft.data) : []
            }
        )
    }

    if (props.dashboard_filters.Orders.Held === 1 && orderHeld && orderHeld.value && orderHeld.value > 0) {
        buttons.Held = {
            avg: orderHeld && Object.keys(orderHeld).length ? orderHeld.avg : 0,
            pct: orderHeld && Object.keys(orderHeld).length ? orderHeld.pct : 0,
            value: orderHeld && Object.keys(orderHeld).length ? orderHeld.value : 0
        }

        datasets.push(
            {
                label: 'Held',
                backgroundColor: 'transparent',
                borderColor: brandWarning,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: orderHeld && Object.keys(orderHeld).length ? Object.values(orderHeld.data) : []
            }
        )
    }

    if (props.dashboard_filters.Orders.Backordered === 1 && orderBackordered && orderBackordered.value && orderBackordered.value > 0) {
        buttons.Backordered = {
            avg: orderBackordered && Object.keys(orderBackordered).length ? orderBackordered.avg : 0,
            pct: orderBackordered && Object.keys(orderBackordered).length ? orderBackordered.pct : 0,
            value: orderBackordered && Object.keys(orderBackordered).length ? orderBackordered.value : 0
        }

        datasets.push(
            {
                label: 'Backordered',
                backgroundColor: 'transparent',
                borderColor: brandWarning,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: orderBackordered && Object.keys(orderBackordered).length ? Object.values(orderBackordered.data) : []
            }
        )
    }

    if (props.dashboard_filters.Orders.Cancelled === 1 && orderCancelled && orderCancelled.value && orderCancelled.value > 0) {
        buttons.Cancelled = {
            avg: orderCancelled && Object.keys(orderCancelled).length ? orderCancelled.avg : 0,
            pct: orderCancelled && Object.keys(orderCancelled).length ? orderCancelled.pct : 0,
            value: orderCancelled && Object.keys(orderCancelled).length ? orderCancelled.value : 0
        }

        datasets.push(
            {
                label: 'Cancelled',
                backgroundColor: 'transparent',
                borderColor: brandDanger,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: orderCancelled && Object.keys(orderCancelled).length ? Object.values(orderCancelled.data) : []
            }
        )
    }

    if (props.dashboard_filters.Orders.Completed === 1) {
        buttons.Completed = {
            avg: orderCompleted && Object.keys(orderCompleted).length ? orderCompleted.avg : 0,
            pct: orderCompleted && Object.keys(orderCompleted).length ? orderCompleted.pct : 0,
            value: orderCompleted && Object.keys(orderCompleted).length ? orderCompleted.value : 0
        }

        datasets.push(
            {
                label: 'Completed',
                backgroundColor: 'transparent',
                borderColor: brandSuccess,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: orderCompleted && Object.keys(orderCompleted).length ? Object.values(orderCompleted.data) : []
            }
        )
    }

    if (props.dashboard_filters.Orders.Sent === 1) {
        buttons.Sent = {
            avg: orderSent && Object.keys(orderSent).length ? orderSent.avg : 0,
            pct: orderSent && Object.keys(orderSent).length ? orderSent.pct : 0,
            value: orderSent && Object.keys(orderSent).length ? orderSent.value : 0
        }

        datasets.push(
            {
                label: 'Sent',
                backgroundColor: 'transparent',
                borderColor: brandSuccess,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: orderSent && Object.keys(orderSent).length ? Object.values(orderSent.data) : []
            }
        )
    }

    if (props.dashboard_filters.Orders.Overdue === 1 && orderOverdue && orderOverdue.value && orderOverdue.value > 0) {
        buttons.Overdue = {
            avg: orderOverdue && Object.keys(orderOverdue).length ? orderOverdue.avg : 0,
            pct: orderOverdue && Object.keys(orderOverdue).length ? orderOverdue.pct : 0,
            value: orderOverdue && Object.keys(orderOverdue).length ? orderOverdue.value : 0
        }

        datasets.push(
            {
                label: 'Overdue',
                backgroundColor: 'transparent',
                borderColor: brandDanger,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: orderOverdue && Object.keys(orderOverdue).length ? Object.values(orderOverdue.data) : []
            }
        )
    }

    return {
        name: 'Orders',
        labels: dates,
        buttons: buttons,
        datasets: datasets
    }
}

export function getTaskChartData (start, end, dates, props) {
    const taskInvoices = _removeNullValues(props.invoices, 'task_id')
    const taskInvoiced = __formatData(taskInvoices, null, start, end, 'total', 'status_id')

    const today = new Date()
    const filterTasksByExpiration = props.tasks.filter((item) => {
        return new Date(item.due_date) > today
    })

    const taskOverdue = __formatData(filterTasksByExpiration, 1, start, end, 'valued_at', 'status_id')

    /* const taskLogged = Object.values(__formatData(props.tasks, 1, currentMoment, endMoment, 'total', 'status_id'))

    const taskPaid = Object.values(__formatData(props.tasks, 3, currentMoment, endMoment, 'total', 'status_id')) */

    const buttons = {}
    const datasets = []

    // TODO Check key name
    if (props.dashboard_filters.Tasks.Invoiced === 1) {
        buttons.Active = {
            avg: taskInvoiced && Object.keys(taskInvoiced).length ? taskInvoiced.avg : 0,
            pct: taskInvoiced && Object.keys(taskInvoiced).length ? taskInvoiced.pct : 0,
            value: taskInvoiced && Object.keys(taskInvoiced).length ? taskInvoiced.value : 0
        }

        datasets.push(
            {
                label: 'Invoiced',
                backgroundColor: 'transparent',
                borderColor: brandWarning,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: taskInvoiced && Object.keys(taskInvoiced).length ? Object.values(taskInvoiced.data) : []
            }
        )
    }

    if (props.dashboard_filters.Tasks.Overdue === 1) {
        buttons.Overdue = {
            avg: taskOverdue && Object.keys(taskOverdue).length ? taskOverdue.avg : 0,
            pct: taskOverdue && Object.keys(taskOverdue).length ? taskOverdue.pct : 0,
            value: taskOverdue && Object.keys(taskOverdue).length ? taskOverdue.value : 0
        }

        datasets.push(
            {
                label: 'Overdue',
                backgroundColor: 'transparent',
                borderColor: brandDanger,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: taskOverdue && Object.keys(taskOverdue).length ? Object.values(taskOverdue.data) : []
            }
        )
    }

    return {
        name: 'Tasks',
        labels: dates,
        buttons: buttons,
        datasets: datasets
    }
}

export function getPaymentChartData (start, end, dates, props) {
    const paymentActive = __formatData(props.payments, consts.payment_status_pending, start, end, 'amount', 'status_id')
    const paymentRefunded = __formatData(props.payments, consts.payment_status_refunded, start, end, 'refunded', 'status_id')
    const paymentCompleted = __formatData(props.payments, consts.payment_status_completed, start, end, 'amount', 'status_id')

    const buttons = {}
    const datasets = []

    if (props.dashboard_filters.Payments.Active === 1) {
        buttons.Active = {
            avg: paymentActive && Object.keys(paymentActive).length ? paymentActive.avg : 0,
            pct: paymentActive && Object.keys(paymentActive).length ? paymentActive.pct : 0,
            value: paymentActive && Object.keys(paymentActive).length ? paymentActive.value : 0
        }

        datasets.push(
            {
                label: 'Active',
                backgroundColor: 'transparent',
                borderColor: brandInfo,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 2,
                data: paymentActive && Object.keys(paymentActive).length ? Object.values(paymentActive.data) : []
            }
        )
    }

    if (props.dashboard_filters.Payments.Refunded === 1) {
        buttons.Refunded = {
            avg: paymentRefunded && Object.keys(paymentRefunded).length ? paymentRefunded.avg : 0,
            pct: paymentRefunded && Object.keys(paymentRefunded).length ? paymentRefunded.pct : 0,
            value: paymentRefunded && Object.keys(paymentRefunded).length ? paymentRefunded.value : 0
        }

        datasets.push(
            {
                label: 'Refunded',
                backgroundColor: 'transparent',
                borderColor: brandDanger,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: paymentRefunded && Object.keys(paymentRefunded).length ? Object.values(paymentRefunded.data) : []
            }
        )
    }

    if (props.dashboard_filters.Payments.Completed === 1) {
        buttons.Completed = {
            avg: paymentCompleted && Object.keys(paymentCompleted).length ? paymentCompleted.avg : 0,
            pct: paymentCompleted && Object.keys(paymentCompleted).length ? paymentCompleted.pct : 0,
            value: paymentCompleted && Object.keys(paymentCompleted).length ? paymentCompleted.value : 0
        }

        datasets.push(
            {
                label: 'Completed',
                backgroundColor: 'transparent',
                borderColor: brandSuccess,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: paymentCompleted && Object.keys(paymentCompleted).length ? Object.values(paymentCompleted.data) : []
            }
        )
    }

    return {
        name: 'Payments',
        labels: dates,
        buttons: buttons,
        datasets: datasets
    }
}

export function getExpenseChartData (start, end, dates, props) {
    const expenseInvoices = _removeNullValues(props.invoices, 'expense_id')

    const expenseLogged = __formatData(props.expenses, consts.expense_status_logged, start, end, 'amount', 'status_id')
    const expensePending = __formatData(props.expenses, consts.expense_status_pending, start, end, 'amount', 'status_id')
    const expenseInvoiced = __formatData(expenseInvoices, consts.expense_status_invoiced, start, end, 'amount', 'status_id')
    const expensePaid = __formatData(props.expenses, consts.expense_status_invoiced, start, end, 'amount', 'status_id')

    const buttons = {}
    const datasets = []

    if (props.dashboard_filters.Expenses.Logged === 1) {
        buttons.Logged = {
            avg: expenseLogged && Object.keys(expenseLogged).length ? expenseLogged.avg : 0,
            pct: expenseLogged && Object.keys(expenseLogged).length ? expenseLogged.pct : 0,
            value: expenseLogged && Object.keys(expenseLogged).length ? expenseLogged.value : 0
        }

        datasets.push(
            {
                label: 'Logged',
                backgroundColor: hexToRgba(brandInfo, 10),
                borderColor: brandInfo,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 2,
                data: expenseLogged && Object.keys(expenseLogged).length ? Object.values(expenseLogged.data) : []
            }
        )
    }

    if (props.dashboard_filters.Expenses.Pending === 1) {
        buttons.Pending = {
            avg: expensePending && Object.keys(expensePending).length ? expensePending.avg : 0,
            pct: expensePending && Object.keys(expensePending).length ? expensePending.pct : 0,
            value: expensePending && Object.keys(expensePending).length ? expensePending.value : 0
        }

        datasets.push(
            {
                label: 'Pending',
                backgroundColor: 'transparent',
                borderColor: brandPrimary,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 1,
                borderDash: [8, 5],
                data: expensePending && Object.keys(expensePending).length ? Object.values(expensePending.data) : []
            }
        )
    }

    if (props.dashboard_filters.Expenses.Invoiced === 1) {
        buttons.Invoiced = {
            avg: expenseInvoiced && Object.keys(expenseInvoiced).length ? expenseInvoiced.avg : 0,
            pct: expenseInvoiced && Object.keys(expenseInvoiced).length ? expenseInvoiced.pct : 0,
            value: expenseInvoiced && Object.keys(expenseInvoiced).length ? expenseInvoiced.value : 0
        }

        datasets.push(
            {
                label: 'Invoiced',
                backgroundColor: 'transparent',
                borderColor: brandWarning,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 2,
                data: expenseInvoiced && Object.keys(expenseInvoiced).length ? Object.values(expenseInvoiced.data) : []
            }
        )
    }

    if (props.dashboard_filters.Expenses.Paid === 1) {
        buttons.Paid = {
            avg: expenseLogged && Object.keys(expenseLogged).length ? expensePaid.avg : 0,
            pct: expenseLogged && Object.keys(expenseLogged).length ? expensePaid.pct : 0,
            value: expenseLogged && Object.keys(expenseLogged).length ? expensePaid.value : 0
        }

        datasets.push(
            {
                label: 'Paid',
                backgroundColor: 'transparent',
                borderColor: brandSuccess,
                pointHoverBackgroundColor: '#fff',
                borderWidth: 2,
                data: expensePaid && Object.keys(expensePaid).length ? Object.values(expensePaid.data) : []
            }
        )
    }

    return {
        name: 'Expenses',
        labels: dates,
        buttons: buttons,
        datasets: datasets
    }
}
