import { getEntityIcon, icons } from './utils/_icons'
import { translations } from './utils/_translations'

const modules = JSON.parse(localStorage.getItem('modules'))

let is_admin = false

if (Object.prototype.hasOwnProperty.call(localStorage, 'appState')) {
    const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
    const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
    is_admin = user_account && user_account.length && (parseInt(user_account[0].is_owner) === 1 || parseInt(user_account[0].is_admin) === 1)
}

const items = [
    {
        name: translations.dashboard,
        url: '/dashboard',
        icon: 'fa fa-dashboard'
    },

    {
        icon: 'fa fa-cog',
        name: 'Settings',
        url: '/account-settings'
        /* children: [
            {
                name: translations.account_details,
                url: '/account-settings',
                icon: `fa ${getSettingsIcon('account-settings')}`
            },
            {
                name: translations.payment_terms,
                url: '/payment_terms',
                icon: `fa ${getSettingsIcon('payment_terms')}`
            },
            {
                name: translations.template_settings,
                url: '/template-settings',
                icon: `fa ${getSettingsIcon('template-settings')}`
            },
            {
                name: translations.email_settings,
                url: '/email-settings',
                icon: `fa ${getSettingsIcon('email-settings')}`
            },
            {
                name: translations.online_payments,
                url: '/gateway-settings',
                icon: `fa ${getSettingsIcon('gateway-settings')}`
            },
            {
                name: translations.invoice_settings,
                url: '/invoice-settings',
                icon: `fa ${getSettingsIcon('invoice-settings')}`
            },
            {
                name: translations.product_settings,
                url: '/product-settings',
                icon: `fa ${getSettingsIcon('product-settings')}`
            },
            {
                name: translations.expense_settings,
                url: '/expense-settings',
                icon: `fa ${getSettingsIcon('expense-settings')}`
            },
            {
                name: translations.task_settings,
                url: '/task-settings',
                icon: `fa ${getSettingsIcon('task-settings')}`
            },
            {
                name: translations.number_settings,
                url: '/number-settings',
                icon: `fa ${getSettingsIcon('number-settings')}`
            },
            {
                name: translations.group_settings,
                url: '/group-settings',
                icon: `fa ${getSettingsIcon('group-settings')}`
            },
            // {
            //     name: translations.tax_rates,
            //     url: '/tax-rates',
            //     icon: `fa ${getSettingsIcon('tax-rates')}`
            // },
            {
                name: translations.custom_fields,
                url: '/field-settings',
                icon: `fa ${getSettingsIcon('field-settings')}`
            },
            {
                name: translations.customer_portal,
                url: '/portal-settings',
                icon: `fa ${getSettingsIcon('portal-settings')}`
            },
            {
                name: 'Designs',
                url: '/designs',
                icon: `fa ${getSettingsIcon('designs')}`
            },
            {
                name: translations.integration_settings,
                url: '/integration-settings',
                icon: `fa ${getSettingsIcon('integration-settings')}`
            },
            {
                name: translations.account_management,
                url: '/account-management',
                icon: `fa ${getSettingsIcon('account-management')}`
            },
            {
                name: translations.localisation_settings,
                url: '/localisation-settings',
                icon: `fa ${getSettingsIcon('localisation-settings')}`
            },
            {
                name: translations.workflow_settings,
                url: '/workflow-settings',
                icon: `fa ${getSettingsIcon('workflow-settings')}`
            },
            {
                name: translations.tax_settings,
                url: '/tax-settings',
                icon: `fa ${getSettingsIcon('tax-settings')}`
            },
            {
                name: translations.device_settings,
                url: '/device-settings',
                icon: `fa ${getSettingsIcon('device-settings')}`
            }
            // {
            //     name: 'Subscriptions',
            //     url: '/subscriptions',
            //     icon: 'fa fa-dashboard'
            // },
            // {
            //     name: 'Tokens',
            //     url: '/tokens',
            //     icon: 'fa fa-dashboard'
            // }
        ] */
    }
]

const financial = {
    name: 'Financial',
    icon: 'fa fa-bar-chart',
    children: []
}

if (modules && modules.promocodes) {
    financial.children.push(
        {
            name: translations.promocodes,
            url: '/promocodes',
            icon: `fa ${getEntityIcon('Promocode')}`
        }
    )
}

if (modules && modules.invoices) {
    financial.children.push(
        {
            name: translations.invoices,
            url: '/invoice',
            icon: `fa ${getEntityIcon('Invoice')}`
        }
    )
}

if (modules && modules.orders) {
    financial.children.push(
        {
            name: translations.orders,
            url: '/orders',
            icon: `fa ${getEntityIcon('Order')}`
        }
    )
}

if (modules && modules.quotes) {
    financial.children.push(
        {
            name: translations.quotes,
            url: '/quotes',
            icon: `fa ${getEntityIcon('Quote')}`
        }
    )
}

if (modules && modules.credits) {
    financial.children.push(
        {
            name: translations.credits,
            url: '/credits',
            icon: `fa ${getEntityIcon('Credit')}`
        }
    )
}

if (modules && modules.recurringInvoices) {
    financial.children.push(
        {
            name: translations.recurring_invoices,
            url: '/recurring-invoices',
            icon: `fa ${getEntityIcon('RecurringInvoice')}`
        }
    )
}

if (modules && modules.recurringQuotes) {
    financial.children.push(
        {
            name: translations.recurring_quotes,
            url: '/recurring-quotes',
            icon: `fa ${getEntityIcon('RecurringQuote')}`
        }
    )
}

if (modules && modules.payments) {
    items.push(
        {
            name: 'Payments',
            url: '/payments',
            icon: `fa ${getEntityIcon('Payment')}`
        }
    )
}

if (modules && modules.expenses) {
    items.push(
        {
            name: 'Expenses',
            url: '/expenses',
            icon: `fa ${getEntityIcon('Expense')}`
        }
    )
}

items.push(financial)

const tasks = {
    name: 'CRM',
    icon: `fa ${getEntityIcon('Task')}`,
    children: []
}

if (modules && modules.leads) {
    tasks.children.push({
        name: 'Leads',
        url: '/leads',
        icon: `fa ${getEntityIcon('Lead')}`
    })
}

if (modules && modules.cases) {
    tasks.children.push({
        name: 'Cases',
        url: '/cases',
        icon: `fa ${getEntityIcon('Case')}`
    })
}

if (modules && modules.projects) {
    tasks.children.push({
        name: 'Projects',
        url: 'projects',
        icon: `fa ${getEntityIcon('Project')}`
    })
}

if (modules && modules.tasks) {
    tasks.children.push({
        name: 'Tasks',
        url: 'tasks',
        icon: `fa ${getEntityIcon('Task')}`
    })

    tasks.children.push(
        {
            name: 'Task Statuses',
            url: '/statuses',
            icon: 'fa fa-building'
        }
    )
}

if (modules && modules.deals) {
    tasks.children.push(
        {
            name: 'Deals',
            url: '/deals',
            icon: `fa ${getEntityIcon('Deal')}`
        }
    )
}

items.push(tasks)

const users = {
    name: 'Users',
    icon: `fa ${icons.user}`,
    children: [
        {
            name: 'Employees',
            url: '/users',
            icon: `fa ${getEntityIcon('User')}`
        },

        {
            name: 'Departments',
            url: '/departments',
            icon: 'fa fa-sitemap'
        },

        {
            name: 'Roles',
            url: '/roles',
            icon: 'fa fa-chain-broken'
        },

        {
            name: 'Permissions',
            url: '/permissions',
            icon: 'fa fa-list-alt'
        }
    ]
}

if (is_admin) {
    items.push(users)
}

if (modules && modules.companies) {
    items.push(
        {
            name: translations.companies,
            url: '/companies',
            icon: `fa ${getEntityIcon('Company')}`
        }
    )
}

if (modules && modules.purchase_orders) {
    items.push(
        {
            name: translations.purchase_orders,
            url: '/purchase_orders',
            icon: `fa ${getEntityIcon('Company')}`
        }
    )
}

const products = {
    name: translations.products,
    icon: 'fa fa-barcode',
    children: [
        {
            name: translations.products,
            url: '/products',
            icon: `fa ${getEntityIcon('Product')}`
        },
        {
            name: 'Categories',
            url: '/categories',
            icon: 'fa fa-building'
        },
        {
            name: 'Brands',
            url: '/brands',
            icon: 'fa fa-building'
        },
        {
            name: 'Attributes',
            url: '/attributes',
            icon: 'fa fa-building'
        }
    ]
}

if (modules && modules.products) {
    items.push(products)
}

if (modules && modules.events) {
    items.push({
        name: 'Calendar',
        url: '/calendar',
        icon: 'fa fa-calendar'
    })
}

items.push({
    name: translations.customers,
    url: '/customers',
    icon: 'fa fa-address-book-o'
})

// items.push(
//     {
//         name: 'Chat',
//         url: '/chat',
//         icon: 'fa fa-chain-broken'
//     }
// )

export default {
    items: items
}
