import React from 'react'

export const icons = {
    bank: 'fa-coins',
    lock: 'fa-lock',
    visibility: 'fa-eye',
    visibility_off: 'fa-eye-slash',
    help: 'fa-question',
    view: 'fa-eye',
    refresh: 'fa-gear',
    checkbox: 'fa-check-square',
    checkbox_o: 'fa-check-square-o',
    table: 'fa-table',
    cog: 'fa-cog',
    columns: 'fa-columns',
    tick: 'fa-check',
    download: 'fa-download',
    email: 'fa-envelope-square',
    mark_sent: 'fa-share-square',
    customer: 'fa-user-circle-o',
    contact: 'fa-users',
    approve: 'fa-check-square',
    reject: 'fa-ban',
    clone: 'fa-copy',
    delete: 'fa-trash',
    mark_paid: 'fa-credit-card',
    credit_card: 'fa-credit-card-alt',
    cancel: 'fa-power-off',
    reverse: 'fa-backward',
    archive: 'fa-archive',
    document: 'fa-file',
    products: 'fa-barcode',
    add: 'fa-plus',
    ellipsis: 'fa-ellipsis-h',
    down: 'fa-chevron-down',
    angle_down: 'fa-angle-down',
    angle_up: 'fa-angle-up',
    right: 'fa-chevron-right',
    left: 'fa-chevron-left',
    restore: 'fa-window-restore',
    clear: 'fa-times',
    edit: 'fa-edit',
    refund: 'fa-credit-card',
    phone: 'fa-phone',
    link: 'fa-link',
    building: 'fa-building',
    list: 'fa-list-ol',
    map_marker: 'fa-map-marker',
    envelope: 'fa-envelope',
    book: 'fa-book',
    user: 'fa-user',
    project: 'fa-briefcase',
    task: 'fa-clock-o',
    expense: 'fa-bar-chart-o',
    company: 'fa-building',
    product: 'fa-barcode',
    order: 'fa-shopping-cart',
    calendar: 'fa-calendar',
    percent: 'fa-percent',
    spinner: 'fa-circle-o-notch fa-spin',
    token: 'fa-key',
    pencil: 'fa-pencil',
    google: 'fa-google',
    shield: 'fa-shield',
    globe: 'fa-globe',
    double_right: 'fa-angle-double-right',
    cloud: 'fa-cloud',
    cloud_upload: 'fa-cloud-upload',
    cloud_download: 'fa-cloud-download',
    portal: 'fa-search-plus',
    header: 'fa-header',
    group: 'fa-group',
    pound_sign: 'fa-gbp',
    payment_terms: 'fa-handshake-o',
    pdf: 'fa-file-pdf-o',
    archive_file: 'a-file-archive-o',
    text_file: 'fa-file-text',
    word_file: 'fa-file-word-o',
    excel_file: 'fa-file-excel-o',
    powerpoint_file: 'fa-file-powerpoint-o',
    image_file: 'fa-file-image-o',
    desktop: 'fa-desktop',
    info: 'fa-info-circle',
    industry: 'fa-industry',
    start: 'fa-play',
    stop: 'fa-stop',
    lead: 'fa-chain-broken',
    case: 'fa-chain-broken',
    deal: 'fa-dollar',
    credit: 'fa-undo',
    invoice: 'fa-area-chart',
    promocode: 'fa-badge-percent'
}

export function getEntityIcon (entity) {
    switch (entity) {
        case 'Promocode':
            return icons.promocode
        case 'Deal':
            return icons.deal
        case 'Project':
            return icons.project
        case 'Case':
            return icons.case
        case 'Lead':
            return icons.lead
        case 'Task':
            return icons.task
        case 'User':
            return icons.user
        case 'Customer':
            return icons.group
        case 'Product':
            return icons.product
        case 'Expense':
            return icons.expense
        case 'Payment':
            return icons.credit_card
        case 'Company':
            return icons.company
        case 'Credit':
            return icons.credit
        case 'Quote':
            return icons.payment_terms
        case 'Invoice':
            return icons.invoice
        case 'Order':
            return icons.order
        case 'RecurringInvoice':
        case 'RecurringQuote':
            return icons.restore
        case 'PurchaseOrder':
            return icons.industry
    }
}

export function getFileTypeIcon (type) {
    switch (type) {
        case 'pdf':
            return icons.document
        case 'psd':
            return icons.pdf
        case 'txt':
            return icons.text_file
        case 'doc':
        case 'docx':
            return icons.word_file
        case 'xls':
        case 'xlsx':
            return icons.excel_file
        case 'ppt':
        case 'pptt':
            return icons.powerpoint_file
        case 'png':
        case 'gif':
        case 'jpg':
        case 'jpeg':
            return icons.image_file
        default:
            return null
    }
}

export function getSettingsIcon (section) {
    switch (section) {
        case 'designs':
            return icons.pencil
        case 'integration-settings':
            return icons.google
        case 'account-management':
            return icons.shield
        case 'localisation-settings':
            return icons.globe
        case 'workflow-settings':
            return icons.double_right
        case 'tax-settings':
            return icons.percent
        case 'device-settings':
            return icons.desktop
        case 'portal-settings':
            return icons.cloud
        case 'field-settings':
            return icons.header
        case 'tax-rates':
            return icons.percent
        case 'group-settings':
            return icons.group
        case 'number-settings':
            return icons.list
        case 'product-settings':
            return icons.product
        case 'expense-settings':
            return icons.expense
        case 'task-settings':
            return icons.task
        case 'case-settings':
            return icons.case
        case 'invoice-settings':
            return icons.pound_sign
        case 'gateway-settings':
            return icons.credit_card
        case 'email-settings':
            return icons.envelope
        case 'template-settings':
            return icons.document
        case 'payment_terms':
            return icons.payment_terms
        case 'account-settings':
            return icons.building
        case 'import-settings':
            return icons.cloud_upload
    }
}
