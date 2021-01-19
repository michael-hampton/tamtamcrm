import { translations } from './_translations'
import { consts } from './_consts'

export const taskStatuses = {
    [consts.task_status_logged]: translations.logged,
    [consts.task_status_running]: translations.running,
    [consts.task_status_invoiced]: translations.invoiced
}

export const caseStatuses = {
    [consts.case_status_draft]: translations.draft,
    [consts.case_status_open]: translations.open,
    [consts.case_status_closed]: translations.closed
}

export const recurringQuoteStatuses = {
    [consts.recurring_invoice_status_draft]: translations.draft,
    [consts.recurring_invoice_status_pending]: translations.pending,
    [consts.recurring_invoice_status_active]: translations.active,
    [consts.recurring_invoice_status_stopped]: translations.stopped,
    [consts.recurring_invoice_status_completed]: translations.complete,
    [consts.recurring_quote_status_completed]: translations.viewed
}

export const recurringInvoiceStatuses = {
    [consts.recurring_invoice_status_draft]: translations.draft,
    [consts.recurring_invoice_status_pending]: translations.pending,
    [consts.recurring_invoice_status_active]: translations.active,
    [consts.recurring_invoice_status_stopped]: translations.stopped,
    [consts.recurring_invoice_status_completed]: translations.complete,
    [consts.recurring_invoice_status_viewed]: translations.viewed
}

export const expenseStatuses = {
    [consts.expense_status_logged]: translations.logged,
    [consts.expense_status_pending]: translations.pending,
    [consts.expense_status_invoiced]: translations.invoiced
}

export const orderStatuses = {
    [consts.order_status_draft]: translations.pending,
    [consts.order_status_sent]: translations.sent,
    [consts.order_status_complete]: translations.complete,
    [consts.order_status_paid]: translations.paid,
    [consts.order_status_approved]: translations.dispatched,
    [consts.order_status_backorder]: translations.backordered,
    [consts.order_status_held]: translations.held,
    [consts.order_status_cancelled]: translations.cancelled,
    [consts.order_status_viewed]: translations.viewed,
    '-1': 'Expired'
}

export const paymentStatuses = {
    [consts.payment_status_pending]: translations.pending,
    [consts.payment_status_voided]: translations.voided,
    [consts.payment_status_failed]: translations.failed,
    [consts.payment_status_completed]: translations.complete,
    [consts.payment_status_partial_refund]: translations.partial_refund,
    [consts.payment_status_refunded]: translations.refunded
}

export const creditStatuses = {
    [consts.credit_status_draft]: translations.draft,
    [consts.credit_status_sent]: translations.sent,
    [consts.credit_status_partial]: translations.partial,
    [consts.credit_status_applied]: translations.applied,
    [consts.credit_status_viewed]: translations.viewed
}

export const purchaseOrderStatuses = {
    [consts.purchase_order_status_draft]: translations.draft,
    [consts.purchase_order_status_sent]: translations.sent,
    [consts.purchase_order_status_approved]: translations.status_approved,
    [consts.purchase_order_status_rejected]: translations.status_rejected,
    [consts.purchase_order_status_change_requested]: translations.status_approved,
    [consts.quote_status_invoiced]: translations.invoiced,
    [consts.quote_status_on_order]: translations.on_order,
    [consts.purchase_order_status_viewed]: translations.viewed,
    100: translations.expired
}

export const quoteStatuses = {
    [consts.quote_status_draft]: translations.draft,
    [consts.quote_status_sent]: translations.sent,
    [consts.quote_status_approved]: translations.status_approved,
    [consts.quote_status_rejected]: translations.status_rejected,
    [consts.quote_status_change_requested]: translations.status_change_requested,
    [consts.quote_status_converted]: translations.converted,
    [consts.quote_status_viewed]: translations.viewed,
    100: translations.expired
}

export const invoiceStatuses = {
    [consts.invoice_status_draft]: translations.draft,
    [consts.invoice_status_sent]: translations.sent,
    [consts.invoice_status_paid]: translations.paid,
    [consts.invoice_status_partial]: translations.partial,
    [consts.invoice_status_cancelled]: translations.cancelled,
    100: translations.overdue,
    [consts.invoice_status_reversed]: translations.reversed,
    [consts.invoice_status_viewed]: translations.viewed
}
