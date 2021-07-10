import { consts } from './_consts'

export const taskStatusColors = {
    [consts.task_status_logged]: 'secondary',
    [consts.task_status_pending]: 'primary',
    [consts.task_status_invoiced]: 'success'
}

export const casePriorityColors = {
    [consts.low_priority]: 'success',
    [consts.medium_priority]: 'warning',
    [consts.high_priority]: 'danger'
}

export const caseStatusColors = {
    [consts.case_status_draft]: 'secondary',
    [consts.case_status_open]: 'primary',
    [consts.case_status_closed]: 'success'
}

export const recurringQuoteStatusColors = {
    [consts.recurring_quote_status_draft]: 'secondary',
    [consts.recurring_quote_status_pending]: 'secondary',
    [consts.recurring_quote_status_active]: 'primary',
    [consts.recurring_quote_status_stopped]: 'warning',
    [consts.recurring_quote_status_completed]: 'success',
    [consts.recurring_quote_status_viewed]: 'info'
}

export const recurringInvoiceStatusColors = {
    [consts.recurring_invoice_status_draft]: 'secondary',
    [consts.recurring_invoice_status_pending]: 'secondary',
    [consts.recurring_invoice_status_active]: 'primary',
    [consts.recurring_invoice_status_stopped]: 'warning',
    [consts.recurring_invoice_status_completed]: 'success',
    [consts.recurring_invoice_status_viewed]: 'info'
}

export const expenseStatusColors = {
    [consts.expense_status_logged]: 'secondary',
    [consts.expense_status_pending]: 'primary',
    [consts.expense_status_invoiced]: 'success'
}

export const orderStatusColors = {
    [consts.order_status_draft]: 'secondary',
    [consts.order_status_sent]: 'primary',
    [consts.order_status_complete]: 'success',
    [consts.order_status_paid]: 'success',
    [consts.order_status_approved]: 'success',
    [consts.order_status_backorder]: 'warning',
    [consts.order_status_held]: 'warning',
    [consts.order_status_cancelled]: 'danger',
    [consts.order_status_viewed]: 'info',
    '-1': 'danger'
}

export const paymentStatusColors = {
    [consts.payment_status_pending]: 'secondary',
    [consts.payment_status_voided]: 'danger',
    [consts.payment_status_failed]: 'danger',
    [consts.payment_status_completed]: 'success',
    [consts.payment_status_partial_refund]: 'dark',
    [consts.payment_status_refunded]: 'danger',
    [consts.payment_status_partially_unapplied]: 'secondary',
    [consts.payment_status_partially_unapplied]: 'info'
}

export const creditStatusColors = {
    [consts.credit_status_draft]: 'secondary',
    [consts.credit_status_sent]: 'primary',
    [consts.credit_status_partial]: 'warning',
    [consts.credit_status_applied]: 'success',
    [consts.credit_status_viewed]: 'info'
}

export const purchaseOrderStatusColors = {
    [consts.purchase_order_status_draft]: 'secondary',
    [consts.purchase_order_status_sent]: 'primary',
    [consts.purchase_order_status_approved]: 'success',
    [consts.purchase_order_status_rejected]: 'danger',
    [consts.purchase_order_status_change_requested]: 'info',
    [consts.purchase_order_status_viewed]: 'info',
    100: 'danger'
}

export const quoteStatusColors = {
    [consts.quote_status_draft]: 'secondary',
    [consts.quote_status_sent]: 'primary',
    [consts.quote_status_approved]: 'success',
    [consts.quote_status_rejected]: 'danger',
    [consts.quote_status_change_requested]: 'info',
    [consts.quote_status_converted]: 'success',
    [consts.quote_status_viewed]: 'info',
    100: 'danger'
}

export const invoiceStatusColors = {
    [consts.invoice_status_draft]: 'secondary',
    [consts.invoice_status_sent]: 'primary',
    [consts.invoice_status_paid]: 'success',
    [consts.invoice_status_partial]: 'warning',
    [consts.invoice_status_draft_text]: 'danger',
    [consts.invoice_status_reversed]: 'danger',
    [consts.invoice_status_cancelled]: 'danger',
    [consts.invoice_status_viewed]: 'info',
    100: 'danger'
}
