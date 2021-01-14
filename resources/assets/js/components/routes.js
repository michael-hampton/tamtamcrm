import React from 'react'
// User Management
import UserList from './users/Userlist'
import ProductList from './products/ProductList'
import KanbanNew from './tasks/KanbanNew'
import Calendar from './calendar/Calendars'
import Variables from './settings/Variables'
import Roles from './roles/Roles'
import Invoice from './invoice/Invoice'
import Order from './orders/Order'
import Companies from './companies/Companies'
import Categories from './categories/Categories'
import CaseCategories from './case_categories/Categories'
import Brands from './brands/Brands'
import ProjectList from './projects/ProjectList'
import Leads from './leads/Leads'
import Deals from './deals/DealList'
import TaskList from './tasks/TaskList'
import Customers from './customers/Customers'
import Departments from './departments/Departments'
import ChatPage from './chat/ChatPage'
import Dashboard from './Dashboard'
import Message from './activity/MessageContainer'
import UserProfile from './users/UserProfile'
import TaskStatus from './taskStatus/statusList'
import Permissions from './permissions/Permissions'
import Payments from './payments/Payments'
import PaymentTerms from './paymentTerms/PaymentTerms'
import TaxRates from './TaxRates/TaxRates'
import Credits from './credits/Credits'
import RecurringQuotes from './recurringQuotes/RecurringQuotes'
import Promocodes from './promocodes/Promocodes'
import RecurringInvoices from './recurringInvoices/RecurringInvoices'
import Quotes from './quotes/Quotes'
import Accounts from './settings/Settings'
import Tokens from './tokens/Tokens'
import Subscriptions from './subscriptions/Subscriptions'
import Attributes from './attributes/Attributes'
import ExpenseSettings from './settings/ExpenseSettings'
import CaseSettings from './settings/CaseSettings'
import TaskSettings from './settings/TaskSettings'
import TemplateSettings from './settings/TemplateSettings'
import CustomFieldSettings from './settings/CustomFieldSettings'
import EmailSettings from './settings/EmailSettings'
import PortalSettings from './settings/CustomerPortalSettings'
import Gateways from './gateways/Gateways'
import GatewaySettings from './gateways/GatewaySettings'
import InvoiceSettings from './settings/InvoiceSettings'
import ProductSettings from './settings/ProductSettings'
import NumberSettings from './settings/NumberSettings'
import Groups from './groups/Groups'
import Expenses from './expenses/Expenses'
import ExpenseCategories from './expense_categories/Categories'
import Designs_backup from './designs/Designs'
import Integrations from './settings/IntegrationSettings'
import Notifications from './settings/Notifications'
import Modules from './settings/ModuleSettings'
import Localisation from './settings/LocalisationSettings'
import DeviceSettings from './settings/DeviceSettings'
import WorkflowSettings from './settings/WorkflowSettings'
import TaxSettings from './settings/TaxSettings'
import Cases from './cases/Cases'
import CaseTemplates from './case_templates/CaseTemplates'
import PurchaseOrders from './purchase_orders/PurchaseOrders'
import BankAccounts from './bank_accounts/BankAccountList'
import BankAccountImport from './bank_accounts/Import'
import DesignEditor from './designs/DesignEditor'
import Importer from './import/Importer'

// https://github.com/ReactTraining/react-router/tree/master/packages/react-router-config
const routes = [
    {
        path: '/bank_accounts/import',
        name: 'Import',
        component: BankAccountImport
    },
    {
        path: '/import-settings',
        name: 'Import',
        component: Importer
    },
    {
        path: '/bank_accounts',
        name: 'Bank Accounts',
        component: BankAccounts
    },
    {
        path: '/customers',
        name: 'Customers',
        component: Customers
    },
    {
        path: '/payments',
        name: 'Payments',
        component: Payments
    },
    {
        path: '/payment_terms',
        name: 'Payment Terms',
        component: PaymentTerms
    },
    {
        path: '/users',
        exact: true,
        name: 'Users',
        component: UserList
    },
    {
        path: '/promocodes',
        exact: true,
        name: 'Promocodes',
        component: Promocodes
    },
    {
        path: '/products',
        name: 'Products',
        component: ProductList
    },
    {
        path: '/kanban',
        name: 'Tasks',
        component: KanbanNew
    },
    {
        path: '/calendar',
        exact: true,
        name: 'Calendar',
        component: Calendar
    },
    {
        path: '/roles',
        name: 'Roles',
        component: Roles
    },
    {
        path: '/invoice',
        name: 'Invoice',
        component: Invoice
    },
    {
        path: '/orders',
        name: 'Orders',
        component: Order
    },
    {
        path: '/companies',
        name: 'Companies',
        component: Companies
    },
    {
        path: '/categories',
        name: 'Categories',
        component: Categories
    },
    {
        path: '/brands',
        name: 'Brands',
        component: Brands
    },
    {
        path: '/departments',
        name: 'Departments',
        component: Departments
    },
    {
        path: '/chat',
        name: 'Chat',
        component: ChatPage
    },
    {
        path: '/activity',
        name: 'Activity',
        component: Message
    },
    {
        path: '/statuses',
        name: 'Task Statuses',
        component: TaskStatus
    },
    {
        path: '/tax-rates',
        name: 'Tax Rates',
        component: TaxRates
    },
    {
        path: '/expenses',
        name: 'Expenses',
        component: Expenses
    },
    {
        path: '/expense_categories',
        name: 'Expense Categories',
        component: ExpenseCategories
    },
    {
        path: '/purchase_orders',
        name: 'Purchase Orders',
        component: PurchaseOrders
    },
    {
        path: '/permissions',
        name: 'Permissions',
        component: Permissions
    },
    {
        path: '/credits',
        name: 'Credits',
        component: Credits
    },
    {
        path: '/quotes',
        name: 'Quotes',
        component: Quotes
    },
    {
        path: '/account-settings/:add?',
        name: 'Accounts',
        component: Accounts
    },
    {
        path: '/portal-settings',
        name: 'Portal Settings',
        component: PortalSettings
    },
    {
        path: '/email-settings',
        name: 'Templates',
        component: EmailSettings
    },
    {
        path: '/expense-settings',
        name: 'Expense Settings',
        component: ExpenseSettings
    },
    {
        path: '/case-settings',
        name: 'Case Settings',
        component: CaseSettings
    },
    {
        path: '/task-settings',
        name: 'Task Settings',
        component: TaskSettings
    },
    {
        path: '/gateways',
        name: 'Gateways',
        component: Gateways
    },
    {
        path: '/gateway-settings',
        name: 'Gateway Settings',
        component: GatewaySettings
    },
    {
        path: '/invoice-settings',
        name: 'Invoice Settings',
        component: InvoiceSettings
    },
    {
        path: '/product-settings',
        name: 'Product Settings',
        component: ProductSettings
    },
    {
        path: '/template-settings',
        name: 'Email Settings',
        component: TemplateSettings
    },
    {
        path: '/email-settings',
        name: 'Email Settings',
        component: EmailSettings
    },
    {
        path: '/number-settings',
        name: 'Number Settings',
        component: NumberSettings
    },
    {
        path: '/group-settings',
        name: 'Group Settings',
        component: Groups
    },
    {
        path: '/subscriptions',
        name: 'Subscriptions',
        component: Subscriptions
    },
    {
        path: '/attributes',
        name: 'Attributes',
        component: Attributes
    },
    {
        path: '/tokens',
        name: 'Tokens',
        component: Tokens
    },
    {
        path: '/workflow-settings',
        name: 'Workflow Settings',
        component: WorkflowSettings
    },
    {
        path: '/tax-settings',
        name: 'Tax Settings',
        component: TaxSettings
    },
    {
        path: '/variables',
        name: 'Variables',
        component: Variables
    },
    {
        path: '/device-settings',
        name: 'Device Settings',
        component: DeviceSettings
    },
    {
        path: '/field-settings',
        name: 'Field Settings',
        component: CustomFieldSettings
    },
    {
        path: '/designs',
        name: 'Designs',
        component: Designs_backup
    },
    {
        path: '/design-editor',
        name: 'Design Editor',
        component: DesignEditor
    },
    {
        path: '/integration-settings',
        name: 'Integrations',
        component: Integrations
    },
    {
        path: '/notifications',
        name: 'Notifications',
        component: Notifications
    },
    {
        path: '/localisation-settings',
        name: 'Localisation',
        component: Localisation
    },
    {
        path: '/account-management',
        name: 'Modules',
        component: Modules
    },
    {
        path: '/recurring-quotes',
        name: 'Recurring Quotes',
        component: RecurringQuotes
    },
    {
        path: '/recurring-invoices',
        name: 'Recurring Invoices',
        component: RecurringInvoices
    },
    {
        path: '/tasks',
        exact: true,
        name: 'Task List',
        component: TaskList
    },
    {
        path: '/projects',
        exact: true,
        name: 'Project List',
        component: ProjectList
    },
    {
        path: '/leads',
        exact: true,
        name: 'Leads List',
        component: Leads
    },
    {
        path: '/deals',
        exact: true,
        name: 'Deals List',
        component: Deals
    },
    {
        path: '/cases',
        exact: true,
        name: 'Cases List',
        component: Cases
    },
    {
        path: '/case_categories',
        exact: true,
        name: 'Cases List',
        component: CaseCategories
    },
    {
        path: '/case_templates',
        exact: true,
        name: 'Case Templates',
        component: CaseTemplates
    },
    {
        path: '/users/:username',
        exact: true,
        name: 'User Details',
        component: UserProfile
    },
    {
        path: '/',
        name: 'Dashboard',
        component: Dashboard
    }
    // {path: '/base/list-groups', name: 'List Group', component: ListGroups},
    // {path: '/base/navbars', name: 'Navbars', component: Navbars},
    // {path: '/base/navs', name: 'Navs', component: Navs},
    // {path: '/base/paginations', name: 'Paginations', component: Paginations},
    // {path: '/base/popovers', name: 'Popovers', component: Popovers},
    // {path: '/base/progress-bar', name: 'Progress Bar', component: ProgressBar},
    // {path: '/base/tooltips', name: 'Tooltips', component: Tooltips},
    // {path: '/buttons', exact: true, name: 'Buttons', component: Buttons},
    // {path: '/buttons/buttons', name: 'Buttons', component: Buttons},
    // {path: '/buttons/button-dropdowns', name: 'Button Dropdowns', component: ButtonDropdowns},
    // {path: '/buttons/button-groups', name: 'Button Group', component: ButtonGroups},
    // {path: '/buttons/brand-buttons', name: 'Brand Buttons', component: BrandButtons},
    // {path: '/icons', exact: true, name: 'Icons', component: CoreUIIcons},
    // {path: '/icons/coreui-icons', name: 'CoreUI Icons', component: CoreUIIcons},
    // {path: '/icons/flags', name: 'Flags', component: Flags},
    // {path: '/icons/font-awesome', name: 'Font Awesome', component: FontAwesome},
    // {path: '/icons/simple-line-icons', name: 'Simple Line Icons', component: SimpleLineIcons},
    // {path: '/notifications', exact: true, name: 'Notifications', component: Alerts},
    // {path: '/notifications/alerts', name: 'Alerts', component: Alerts},
    // {path: '/notifications/badges', name: 'Badges', component: Badges},
    // {path: '/notifications/modals', name: 'Modals', component: Modals},
    // {path: '/widgets', name: 'Widgets', component: Widgets},
    // {path: '/charts', name: 'Charts', component: Charts},
    // {path: '/users', exact: true, name: 'Users', component: Users},
    // {path: '/features', exact: true, name: 'Features', component: Features},
    // {path: '/organisations', exact: true, name: 'Organisations', component: Organisations},
    // {path: '/organisation-units', exact: true, name: 'Organisation Units', component: OrganisationUnits},
    // {path: '/roles', exact: true, name: 'Roles', component: Roles},
    // {path: '/applications', exact: true, name: 'Applications', component: Applications}
    // { path: '/users/:id', exact: true, name: 'User Details', component: User },
]

export default routes
