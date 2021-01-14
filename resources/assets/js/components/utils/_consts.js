import React from 'react'
import { translations } from './_translations'

export const consts = {
    centimeters: 'cm',
    meters: 'mtr',
    inches: 'in',
    milimeters: 'mm',
    ounces: 'oz',
    grams: 'gms',
    pounds: 'lbs',
    foot: 'ft',
    yard: 'yd',
    gateway_mode_live: 'Live',
    gateway_mode_production: 'Production',
    low_priority: 1,
    medium_priority: 2,
    high_priority: 3,
    order_created_subscription: 1,
    order_updated_subscription: 29,
    order_deleted_subscription: 2,
    credit_created_subscription: 3,
    credit_updated_subscription: 30,
    credit_deleted_subscription: 4,
    customer_created_subscription: 5,
    customer_updated_subscription: 31,
    customer_deleted_subscription: 6,
    invoice_created_subscription: 7,
    invoice_updated_subscription: 32,
    invoice_deleted_subscription: 8,
    payment_created_subscription: 9,
    payment_updated_subscription: 33,
    payment_deleted_subscription: 10,
    quote_created_subscription: 11,
    quote_updated_subscription: 34,
    quote_approved_subscription: 45,
    quote_rejected_subscription: 46,
    quote_deleted_subscription: 12,
    lead_created_subscription: 13,
    lead_updated_subscription: 35,
    order_backordered_subscription: 14,
    order_held_subscription: 15,
    deal_created_subscription: 16,
    deal_updated_subscription: 36,
    deal_deleted_subscription: 17,
    project_created_subscription: 18,
    project_updated_subscription: 37,
    project_deleted_subscription: 19,
    task_created_subscription: 20,
    task_updated_subscription: 38,
    task_deleted_subscription: 21,
    purchase_order_created_subscription: 22,
    purchase_order_updated_subscription: 39,
    purchase_order_approved_subscription: 47,
    purchase_order_rejected_subscription: 48,
    purchase_order_deleted_subscription: 23,
    case_created_subscription: 24,
    case_updated_subscription: 40,
    case_deleted_subscription: 25,
    expense_deleted_subscription: 26,
    expense_created_subscription: 27,
    expense_updated_subscription: 41,
    company_created_subscription: 42,
    company_updated_subscription: 43,
    company_deleted_subscription: 44,
    late_invoices_subscription: 28,
    invoice_status_past_due: '-1',
    invoice_status_draft: 1,
    invoice_status_sent: 2,
    invoice_status_paid: 3,
    invoice_status_partial: 4,
    invoice_status_cancelled: 5,
    invoice_status_reversed: 6,
    invoice_status_viewed: 7,
    invoice_status_past_due_text: 'Overdue',
    invoice_status_draft_text: 'Draft',
    invoice_status_sent_text: 'Sent',
    invoice_status_partial_text: 'Partial',
    invoice_status_paid_text: 'Paid',
    invoice_status_cancelled_text: 'Cancelled',
    invoice_status_reversed_text: 'Reversed',
    quote_status_expired: '-1',
    quote_status_draft: 1,
    quote_status_sent: 2,
    quote_status_viewed: 3,
    quote_status_converted: 5,
    quote_status_approved: 4,
    quote_status_rejected: 6,
    quote_status_change_requested: 7,
    purchase_order_status_expired: '-1',
    purchase_order_status_draft: 1,
    purchase_order_status_sent: 2,
    purchase_order_status_viewed: 3,
    purchase_order_status_approved: 4,
    purchase_order_status_rejected: 5,
    purchase_order_status_change_requested: 6,
    quote_status_expired_text: 'Expired',
    recurring_invoice_status_draft: 1,
    recurring_invoice_status_pending: 2,
    recurring_invoice_status_active: 3,
    recurring_invoice_status_stopped: 4,
    recurring_invoice_status_completed: 5,
    recurring_invoice_status_viewed: 6,
    recurring_quote_status_draft: 1,
    recurring_quote_status_pending: 2,
    recurring_quote_status_viewed: 6,
    recurring_quote_status_active: 3,
    recurring_quote_status_stopped: 4,
    recurring_quote_status_completed: 5,
    quote_status_draft_text: 'Draft',
    quote_status_sent_text: 'Sent',
    quote_status_approved_text: 'Approved',
    order_status_draft: 1,
    order_status_sent: 2,
    order_status_complete: 3,
    order_status_approved: 4,
    order_status_held: 5,
    order_status_backorder: 6,
    order_status_partial: 7,
    order_status_cancelled: 8,
    order_status_failed: 9,
    order_status_paid: 10,
    order_status_viewed: 11,
    order_status_draft_text: 'Draft',
    order_status_sent_text: 'Sent',
    order_status_approved_text: 'Approved',
    order_status_complete_text: 'Completed',
    credit_status_draft: 1,
    credit_status_sent: 2,
    credit_status_partial: 3,
    credit_status_applied: 4,
    credit_status_viewed: 5,
    case_status_draft: 1,
    case_status_open: 2,
    case_status_closed: 3,
    case_link_type_product: 1,
    case_link_type_project: 2,
    credit_status_draft_text: 'Draft',
    credit_status_sent_text: 'Sent',
    credit_status_partial_text: 'Partial',
    credit_status_applied_text: 'Applied',
    payment_status_pending: 1,
    payment_status_voided: 2,
    payment_status_failed: 3,
    payment_status_completed: 4,
    payment_status_partial_refund: 5,
    payment_status_refunded: 6,
    payment_status_unapplied: 'unapplied',
    payment_status_pending_text: 'Pending',
    payment_status_voided_text: 'Voided',
    payment_status_failed_text: 'Failed',
    payment_status_completed_text: 'Completed',
    payment_status_partial_refund_text: 'Partially Refunded',
    payment_status_refunded_text: 'Refunded',
    task_status_logged: 1,
    task_status_running: 3000,
    task_status_invoiced: 2000,
    expense_status_logged: 1,
    expense_status_pending: 2,
    expense_status_invoiced: 3,
    expense_status_logged_text: 'Logged',
    expense_status_pending_text: 'Pending',
    expense_status_invoiced_text: 'Invoiced',
    notification_payment_success: 'payment_success',
    notification_payment_refunded: 'payment_refunded',
    notification_lead_success: 'lead_success',
    notification_deal_success: 'deal_success',
    notification_payment_failure: 'payment_failure',
    notification_invoice_sent: 'invoice_sent',
    notification_credit_sent: 'credit_sent',
    notification_quote_sent: 'quote_sent',
    notification_order_sent: 'order_sent',
    notification_purchase_order_sent: 'purchase_order_sent',
    notification_purchase_order_approved: 'purchase_order_approved',
    notification_purchase_order_rejected: 'purchase_order_rejected',
    notification_purchase_order_change_requested: 'purchase_order_change_requested',
    notification_invoice_viewed: 'invoice_viewed',
    notification_quote_viewed: 'quote_viewed',
    notification_credit_viewed: 'credit_viewed',
    notification_quote_approved: 'quote_approved',
    notification_quote_rejected: 'quote_rejected',
    notification_quote_change_requested: 'quote_change_requested',
    notification_order_created: 'order_created',
    notification_order_backordered: 'order_backordered',
    notification_order_held: 'order_held',
    email_design_plain: 'plain',
    email_design_light: 'light',
    email_design_dark: 'dark',
    lock_invoices_off: 'off',
    lock_invoices_sent: 'when_sent',
    lock_invoices_paid: 'when_paid',
    order_charge_point_create: 'on_creation',
    order_charge_point_sent: 'on_send',
    email_design_custom: 'custom',
    reminder_schedule_after_invoice_date: 'after_invoice_date',
    reminder_schedule_before_due_date: 'before_due_date',
    reminder_schedule_after_due_date: 'after_due_date',
    stripe_gateway: '13bb8d58',
    authorize_gateway: '8ab2dce2',
    paypal_gateway: '64bcbdce',
    custom_gateway: '4ntgik8629',
    switch: 'switch',
    text: 'text',
    textarea: 'textarea',
    select: 'select',
    date: 'date',
    currency_pound: 2,
    default_currency: 2,
    line_item_expense: 6,
    line_item_task: 3,
    line_item_product: 1,
    line_item_project: 9
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

export const purchaseOrderStatusColors = {
    [consts.purchase_order_status_draft]: 'secondary',
    [consts.purchase_order_status_sent]: 'primary',
    [consts.purchase_order_status_approved]: 'success',
    [consts.purchase_order_status_rejected]: 'danger',
    [consts.purchase_order_status_change_requested]: 'info',
    [consts.purchase_order_status_viewed]: 'info',
    100: 'danger'
}

export const creditStatuses = {
    [consts.credit_status_draft]: translations.draft,
    [consts.credit_status_sent]: translations.sent,
    [consts.credit_status_partial]: translations.partial,
    [consts.credit_status_applied]: translations.applied,
    [consts.credit_status_viewed]: translations.viewed
}

export const creditStatusColors = {
    [consts.credit_status_draft]: 'secondary',
    [consts.credit_status_sent]: 'primary',
    [consts.credit_status_partial]: 'warning',
    [consts.credit_status_applied]: 'success',
    [consts.credit_status_viewed]: 'info'
}

export const paymentStatuses = {
    [consts.payment_status_pending]: translations.pending,
    [consts.payment_status_voided]: translations.voided,
    [consts.payment_status_failed]: translations.failed,
    [consts.payment_status_completed]: translations.complete,
    [consts.payment_status_partial_refund]: translations.partial_refund,
    [consts.payment_status_refunded]: translations.refunded
}

export const paymentStatusColors = {
    [consts.payment_status_pending]: 'secondary',
    [consts.payment_status_voided]: 'danger',
    [consts.payment_status_failed]: 'danger',
    [consts.payment_status_completed]: 'success',
    [consts.payment_status_partial_refund]: 'dark',
    [consts.payment_status_refunded]: 'danger'
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

export const expenseStatuses = {
    [consts.expense_status_logged]: translations.logged,
    [consts.expense_status_pending]: translations.pending,
    [consts.expense_status_invoiced]: translations.invoiced
}

export const expenseStatusColors = {
    [consts.expense_status_logged]: 'secondary',
    [consts.expense_status_pending]: 'primary',
    [consts.expense_status_invoiced]: 'success'
}

export const recurringInvoiceStatuses = {
    [consts.recurring_invoice_status_draft]: translations.draft,
    [consts.recurring_invoice_status_pending]: translations.pending,
    [consts.recurring_invoice_status_active]: translations.active,
    [consts.recurring_invoice_status_stopped]: translations.stopped,
    [consts.recurring_invoice_status_completed]: translations.complete,
    [consts.recurring_invoice_status_viewed]: translations.viewed
}

export const recurringInvoiceStatusColors = {
    [consts.recurring_invoice_status_draft]: 'secondary',
    [consts.recurring_invoice_status_pending]: 'secondary',
    [consts.recurring_invoice_status_active]: 'primary',
    [consts.recurring_invoice_status_stopped]: 'warning',
    [consts.recurring_invoice_status_completed]: 'success',
    [consts.recurring_invoice_status_viewed]: 'info'
}

export const recurringQuoteStatuses = {
    [consts.recurring_invoice_status_draft]: translations.draft,
    [consts.recurring_invoice_status_pending]: translations.pending,
    [consts.recurring_invoice_status_active]: translations.active,
    [consts.recurring_invoice_status_stopped]: translations.stopped,
    [consts.recurring_invoice_status_completed]: translations.complete,
    [consts.recurring_quote_status_completed]: translations.viewed
}

export const recurringQuoteStatusColors = {
    [consts.recurring_quote_status_draft]: 'secondary',
    [consts.recurring_quote_status_pending]: 'secondary',
    [consts.recurring_quote_status_active]: 'primary',
    [consts.recurring_quote_status_stopped]: 'warning',
    [consts.recurring_quote_status_completed]: 'success',
    [consts.recurring_quote_status_viewed]: 'info'
}

export const caseStatuses = {
    [consts.case_status_draft]: translations.draft,
    [consts.case_status_open]: translations.open,
    [consts.case_status_closed]: translations.closed
}

export const caseStatusColors = {
    [consts.case_status_draft]: 'secondary',
    [consts.case_status_open]: 'primary',
    [consts.case_status_closed]: 'success'
}

export const caseLinkTypes = {
    [consts.case_link_type_product]: translations.product,
    [consts.case_link_type_project]: translations.project
}

export const casePriorityColors = {
    [consts.low_priority]: 'success',
    [consts.medium_priority]: 'warning',
    [consts.high_priority]: 'danger'
}

export const casePriorities = {
    [consts.low_priority]: translations.low,
    [consts.medium_priority]: translations.medium,
    [consts.high_priority]: translations.high
}

export const taskStatuses = {
    [consts.task_status_logged]: translations.logged,
    [consts.task_status_running]: translations.running,
    [consts.task_status_invoiced]: translations.invoiced
}

export const taskStatusColors = {
    [consts.task_status_logged]: 'secondary',
    [consts.task_status_pending]: 'primary',
    [consts.task_status_invoiced]: 'success'
}

export const frequencyOptions = {
    DAILY: 'frequency_daily',
    WEEKLY: 'frequency_weekly',
    FORTNIGHT: 'frequency_two_weeks',
    MONTHLY: 'frequency_monthly',
    TWO_MONTHS: 'frequency_two_months',
    THREE_MONTHS: 'frequency_three_months',
    FOUR_MONTHS: 'frequency_four_months',
    SIX_MONTHS: 'frequency_six_months',
    YEARLY: 'frequency_annually'
}

export const taskTypes = {
    task: 1,
    deal: 2,
    lead: 3
}

export const subscriptions = {
    order_created: 1,
    order_updated: 29,
    order_deleted: 2,
    credit_created: 3,
    credit_updated: 30,
    credit_deleted: 4,
    customer_created: 5,
    customer_updated: 31,
    customer_deleted: 6,
    invoice_created: 7,
    invoice_updated: 32,
    invoice_deleted: 8,
    payment_created: 9,
    payment_updated: 33,
    payment_deleted: 10,
    quote_created: 11,
    quote_updated: 34,
    quote_approved: 45,
    quote_rejected: 46,
    quote_deleted: 12,
    lead_created: 13,
    lead_updated: 35,
    order_backordered: 14,
    order_held: 15,
    deal_created: 16,
    deal_updated: 36,
    deal_deleted: 17,
    project_created: 18,
    project_updated: 37,
    project_deleted: 19,
    task_created: 20,
    task_updated: 38,
    task_deleted: 21,
    purchase_order_created: 22,
    purchase_order_updated: 39,
    purchase_order_approved: 47,
    purchase_order_rejected: 48,
    purchase_order_deleted: 23,
    case_created: 24,
    case_updated: 40,
    case_deleted: 25,
    expense_deleted: 26,
    expense_created: 27,
    expense_updated: 41,
    company_created: 42,
    company_updated: 43,
    company_deleted: 44,
    late_invoices: 28
}
