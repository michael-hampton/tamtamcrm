import React, { Component } from 'react'
import {
    Button,
    ButtonGroup,
    ButtonToolbar,
    Card,
    CardBody,
    CardFooter,
    CardHeader,
    CardTitle,
    Col,
    Input,
    ListGroup,
    Modal,
    ModalBody,
    ModalFooter,
    ModalHeader,
    Nav,
    NavItem,
    NavLink,
    Progress,
    Row,
    TabContent,
    TabPane
} from 'reactstrap'
import { CardModule } from './common/Card.jsx'
import ReactEcharts from 'echarts-for-react'
import axios from 'axios'
import MessageContainer from './activity/MessageContainer'
import Line from 'react-chartjs-2'
import moment from 'moment'
import { CustomTooltips } from '@coreui/coreui-plugin-chartjs-custom-tooltips'
import { getStyle, hexToRgba } from '@coreui/coreui/dist/js/coreui-utilities'
import MonthPicker from './common/MonthPicker'
import { icons } from './utils/_icons'
import FormatMoney from './common/FormatMoney'
import { consts } from './utils/_consts'
import { translations } from './utils/_translations'
import SettingsWizard from './settings/settings_wizard/SettingsWizard'
import ViewEntity from './common/ViewEntity'
import TaskItem from './tasks/TaskItem'
import ExpenseItem from './expenses/ExpenseItem'
import PaymentItem from './payments/PaymentItem'
import QuoteItem from './quotes/QuoteItem'
import OrderItem from './orders/OrderItem'
import TaskModel from './models/TaskModel'
import InvoiceItem from './invoice/InvoiceItem'

const brandPrimary = getStyle('--primary')
const brandSuccess = getStyle('--success')
const brandInfo = getStyle('--info')
const brandWarning = getStyle('--warning')
const brandDanger = getStyle('--danger')

function calculateAverage (array) {
    if (!array.length) {
        return 0
    }

    return Math.round(array.reduce((a, b) => (a + b)) / array.length * 100 + Number.EPSILON) / 100
}

function calculateTotals (array) {
    return array.reduce((a, b) => a + b, 0)
}

function getAverages (array) {
    const avg = calculateAverage(array)

    console.log('array', array)

    const totals = calculateTotals(array)
    const pct = calculatePercentage(avg, totals)

    return {
        avg: Math.round(avg),
        value: totals,
        pct: Math.round(pct)
    }
}

function calculatePercentage (number1, number2) {
    if (number1 <= 0 || number2 <= 0) {
        return 0
    }

    return Math.floor((number1 / number2) * 100)
}

function filterOverdue (array) {
    const today = new Date()
    return array.filter((item) => {
        return new Date(item.due_date) < today
    })
}

function getLast30Days (array) {
    const last_date = new Date()
    last_date.setDate(last_date.getDate() - 30)

    return array.filter((item) => {
        return new Date(item.created_at) > last_date && !item.deleted_at
    })
}

function filterByDate (startDate, endDate, array) {
    startDate = new Date(startDate)
    endDate = new Date(endDate)

    // return matches for date range
    return array.filter(function (a) {
        const date = new Date(a.created_at)
        return date >= startDate && date <= endDate
    })
}

function removeNullValues (array, column) {
    return array.filter(e => e[column] !== null && e[column] !== '')
}

function orderByDate (array, dir) {
    return array.sort(function (a, b) {
        const dateA = new Date(a.created_at)
        const dateB = new Date(b.created_at)

        return dir === 'asc' ? dateA - dateB : dateB - dateA // sort by
        // date
        // ascending
    })
}

function groupByStatus (array, status, compare_column) {
    return array.filter(e => e[compare_column] === status)
}

function groupDataByDate (array, column, status, compare_column) {
    return array.reduce(function (m, d) {
        if (status !== null && d[compare_column] !== status) {
            return m
        }

        const date = moment(d.created_at).format('DD')

        if (!m[date]) {
            m[date] = parseFloat(d[column])
            return m
        }
        m[date] += parseFloat(d[column])
        return m
    }, {})
}

function formatData (myData, status, startDate, endDate, column, compare_column, doGrouping = true, dir = 'asc') {
    if (!myData.length) {
        return null
    }

    // sort by date
    myData = orderByDate(myData, dir)

    // get data for specified date range
    let filteredData = filterByDate(startDate, endDate, myData)

    if (!filteredData.length) {
        return null
    }

    // Calculate the sums and group data (while tracking count)
    if (doGrouping) {
        filteredData = groupDataByDate(filteredData, column, status, compare_column)
    } else {
        filteredData = groupByStatus(myData, status, compare_column)
    }

    const avgs = Object.keys(filteredData).length ? getAverages(Object.values(filteredData)) : {
        avg: 0,
        value: 0,
        pct: 0
    }

    return { ...{ data: filteredData }, ...avgs }
}

function makeLabels (currentMoment, endMoment) {
    const dates = []
    while (currentMoment.isBefore(endMoment, 'day')) {
        currentMoment.add(1, 'days')
        dates.push(currentMoment.format('DD'))
    }

    return dates
}

function objectToCSVRow (dataObject, headers, isHeader = false) {
    const dataArray = []
    for (const o in dataObject) {
        if (!isHeader && !headers.includes(o)) {
            continue
        }

        if (typeof dataObject[o] === 'boolean') {
            dataObject[o] = dataObject[o] === true ? 'Yes' : 'No'
        }

        const innerValue = dataObject[o] === null ? '' : dataObject[o].toString()

        let result = innerValue.replace(/"/g, '""')
        result = '"' + result + '"'
        dataArray.push(result)
    }
    return dataArray.join(',') + '\r\n'
}

const mainChartOpts = {
    tooltips: {
        enabled: false,
        custom: CustomTooltips,
        intersect: true,
        mode: 'index',
        position: 'nearest',
        callbacks: {
            labelColor: function (tooltipItem, chart) {
                return { backgroundColor: chart.data.datasets[tooltipItem.datasetIndex].borderColor }
            }
        }
    },
    maintainAspectRatio: false,
    legend: {
        display: false
    },
    scales: {
        xAxes: [
            {
                gridLines: {
                    drawOnChartArea: false
                }
            }],
        yAxes: [
            {
                ticks: {
                    beginAtZero: true,
                    maxTicksLimit: 5,
                    stepSize: Math.ceil(250 / 5),
                    max: 250
                }
            }]
    },
    elements: {
        point: {
            radius: 0,
            hitRadius: 10,
            hoverRadius: 4,
            hoverBorderWidth: 3
        }
    }
}

export default class Dashboard extends Component {
    constructor (props) {
        super(props)
        this.getOption = this.getOption.bind(this)
        this.state = {
            dashboard_minimized: localStorage.getItem('dashboard_minimized') || false,
            sources: [],
            customers: [],
            modal: false,
            modal2: false,
            dashboard_filters: {
                Invoices: {
                    Active: 1,
                    Outstanding: 1,
                    Cancelled: 1,
                    Sent: 1,
                    Overdue: 1,
                    Paid: 1
                },

                Orders: {
                    Draft: 1,
                    Backordered: 1,
                    Sent: 1,
                    Held: 1,
                    Cancelled: 1,
                    Overdue: 1,
                    Completed: 1
                },
                Expenses: {
                    Logged: 1,
                    Invoiced: 1,
                    Pending: 1,
                    Paid: 1
                },
                Credits: {
                    Active: 1,
                    Completed: 1,
                    Sent: 1,
                    Overdue: 1
                },
                Tasks: {
                    Invoiced: 1,
                    Overdue: 1
                },
                Quotes: {
                    Sent: 1,
                    Overdue: 1,
                    Approved: 1,
                    Unapproved: 1,
                    Active: 1
                },
                Payments: {
                    Completed: 1,
                    Active: 1,
                    Refunded: 1
                }
            },
            leadCounts: [],
            start_date: new Date(moment().subtract(1, 'months').format('YYYY-MM-DD hh:mm')),
            end_date: new Date(),
            totalBudget: 0,
            totalEarnt: 0,
            leadsToday: 0,
            newDeals: 0,
            newCustomers: 0,
            deals: [],
            invoices: [],
            quotes: [],
            payments: [],
            expenses: [],
            tasks: [],
            orders: [],
            credits: [],
            activeTab: '1',
            activeTab2: window.innerWidth <= 768 ? '' : '3',
            isMobile: window.innerWidth <= 768,
            view: {
                ignore: [],
                viewMode: false,
                viewedId: null,
                title: null
            },
            viewId: null,
            ignoredColumns: ['is_deleted', 'viewed', 'tax_rate', 'tax_rate_name', 'tax_2', 'tax_3', 'tax_rate_name_2', 'tax_rate_name_3', 'date_to_send', 'recurring_invoice_id', 'recurring', 'currency_id', 'exchange_rate', 'account_id', 'assigned_to', 'gateway_percentage', 'gateway_fee', 'files', 'audits', 'paymentables', 'customer_name', 'emails', 'transaction_fee', 'transaction_fee_tax', 'shipping_cost', 'shipping_cost_tax', 'design_id', 'invitations', 'id', 'user_id', 'status', 'company_id', 'custom_value1', 'custom_value2', 'custom_value3', 'custom_value4', 'updated_at', 'deleted_at', 'created_at', 'public_notes', 'private_notes', 'terms', 'footer', 'last_send_date', 'line_items', 'next_send_date', 'last_sent_date', 'first_name', 'last_name', 'tax_total', 'discount_total', 'sub_total', 'task_rate',
                'timers',
                'public_notes',
                'private_notes',
                'deleted_at',
                'users',
                'customer',
                'contributors',
                'users',
                'comments',
                'is_completed',
                'task_status_id',
                'reference_number',
                'transaction_id',
                'tax_rate',
                'tax_rate_name',
                'tax_2', 'tax_3',
                'tax_rate_name_2',
                'tax_rate_name_3',
                'project_id',
                'category',
                'files',
                'customer_name',
                'user_id',
                'company_id',
                'invoice_currency_id',
                'converted_amount',
                'exchange_rate',
                'deleted_at',
                'recurring_expense_id',
                'currency_id',
                'type_id',
                'invoice_id',
                'assigned_to',
                'bank_id',
                'expense_category_id',
                'create_invoice',
                'include_documents'

            ]
        }

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.settings = user_account[0].account.settings

        this.toggle = this.toggle.bind(this)
        this.toggleTab2 = this.toggleTab2.bind(this)
        this.getChartData = this.getChartData.bind(this)
        this.doExport = this.doExport.bind(this)
        this.setDates = this.setDates.bind(this)
        this.onRadioBtnClick = this.onRadioBtnClick.bind(this)
        this.fetchData = this.fetchData.bind(this)
        this.toggleDashboardFilter = this.toggleDashboardFilter.bind(this)
        this.toggleModal = this.toggleModal.bind(this)
        this.toggleModal2 = this.toggleModal2.bind(this)
        this.getCustomer = this.getCustomer.bind(this)
        this.handleWindowSizeChange = this.handleWindowSizeChange.bind(this)
        this.addUserToState = this.addUserToState.bind(this)
        this.toggleViewedEntity = this.toggleViewedEntity.bind(this)
    }

    componentDidMount () {
        this.fetchData()

        if (!this.settings.name.length) {
            setTimeout(
                function () {
                    this.setState({ modal2: true })
                }
                    .bind(this),
                3000
            )
        }

        // window.setInterval(() => {
        //     this.fetchData()
        // }, 5000)
    }

    componentWillMount () {
        window.addEventListener('resize', this.handleWindowSizeChange)
    }

    componentWillUnmount () {
        window.removeEventListener('resize', this.handleWindowSizeChange)
    }

    handleWindowSizeChange () {
        const selected_tab1 = window.innerWidth <= 768 ? this.state.activeTab : '1'
        const selected_tab2 = window.innerWidth <= 768 ? '' : '3'
        this.setState({ isMobile: window.innerWidth <= 768, activeTab: selected_tab1, activeTab2: selected_tab2 })
    }

    addUserToState (entity_name, entities) {
        this.setState({ [entity_name]: entities })
    }

    toggleViewedEntity (entity, entities, id, title = null, edit = null) {
        if (this.state.view.viewMode === true) {
            this.setState({
                view: {
                    ...this.state.view,
                    viewMode: false,
                    viewedId: null,
                    entity: null,
                    entities: []
                }
            }, () => console.log('view', this.state.view))

            return
        }

        this.setState({
            view: {
                ...this.state.view,
                viewMode: !this.state.view.viewMode,
                viewedId: id,
                edit: edit,
                title: title,
                entity: entity,
                entities: entities
            }
        }, () => console.log('view', this.state.view))
    }

    toggleDashboardFilter (e) {
        const dashboard_filters = this.state.dashboard_filters

        console.log('dashboard filters', dashboard_filters, e.target.dataset.entity)

        dashboard_filters[e.target.dataset.entity][e.target.dataset.action] = e.target.checked === true ? 1 : 0
        this.setState({ dashboard_filters: dashboard_filters }, () => {
            console.log('dashboard filters', this.state.dashboard_filters)
        })
    }

    toggleModal () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    toggleModal2 () {
        this.setState({
            modal2: !this.state.modal2,
            errors: []
        })
    }

    fetchData () {
        axios.get('/api/dashboard')
            .then((r) => {
                if (r.data) {
                    this.setState(
                        {
                            sources: r.data.sources,
                            leadCounts: r.data.leadCounts,
                            totalBudget: r.data.totalBudget,
                            totalEarnt: r.data.totalEarnt,
                            leadsToday: r.data.leadsToday,
                            newDeals: r.data.newDeals,
                            newCustomers: r.data.newCustomers,
                            invoices: r.data.invoices,
                            quotes: r.data.quotes,
                            payments: r.data.payments,
                            expenses: r.data.expenses,
                            tasks: r.data.tasks,
                            orders: r.data.orders,
                            credits: r.data.credits,
                            customers: r.data.customers
                        }
                    )
                }
            })
            .catch((e) => {
                console.warn(e)
            })
    }

    setDates (date) {
        this.setState(date)
    }

    getArrayToExport (entity, radioSelected) {
        let currentMoment = moment().startOf('month')
        let endMoment = moment().endOf('month')

        if (this.state.start_date !== null) {
            currentMoment = moment(this.state.start_date)
        }

        if (this.state.end_date !== null) {
            endMoment = moment(this.state.end_date)
        }

        const start = currentMoment.format('YYYY-MM-DD')
        const end = endMoment.format('YYYY-MM-DD')

        let array = []

        switch (entity) {
            case 'Tasks':
                switch (radioSelected) {
                    case 'Invoiced':
                        // array = formatData(myData, 1, start, end, 'amount', 'status', false)
                        break

                    case 'Overdue': {
                        const today = new Date()
                        const filterTasksByExpiration = this.state.tasks.filter((item) => {
                            return new Date(item.due_date) > today
                        })

                        array = formatData(filterTasksByExpiration, 1, start, end, 'valued_at', 'status_id')
                    }

                        break
                }
                break

            case 'Invoices':
                switch (radioSelected) {
                    case 'Active':
                        array = formatData(this.state.invoices, consts.invoice_status_draft, start, end, 'amount', 'status', false)
                        break

                    case 'Outstanding':
                        array = formatData(this.state.invoices, consts.invoice_status_sent, start, end, 'amount', 'status', false)
                        break
                    case 'Paid':
                        array = formatData(this.state.invoices, consts.invoice_status_paid, start, end, 'amount', 'status', false)
                        break

                    case 'Cancelled':
                        array = formatData(this.state.invoices, consts.invoice_status_cancelled, start, end, 'amount', 'status', false)
                        break

                    case 'Overdue': {
                        const filterInvoicesByExpiration = filterOverdue(this.state.invoices)

                        array = formatData(filterInvoicesByExpiration, consts.invoice_status_sent, start, end, 'total', 'status_id')
                    }

                        break
                }
                break

            case 'Expenses':
                switch (radioSelected) {
                    case 'Logged':
                        array = formatData(this.state.expenses, consts.expense_status_logged, start, end, 'amount', 'status', false)
                        break
                    case 'Pending':
                        array = formatData(this.state.expenses, consts.expense_status_pending, start, end, 'amount', 'status', false)
                        break

                    case 'Invoiced':
                        array = formatData(this.state.expenses, consts.expense_status_invoiced, start, end, 'amount', 'status', false)
                        break

                    case 'Paid':
                        array = formatData(this.state.expenses, consts.expense_status_invoiced, start, end, 'amount', 'status', false)
                        break
                }

                break

            case 'Payments':
                switch (radioSelected) {
                    case 'Active':
                        array = formatData(this.state.payments, consts.payment_status_pending, start, end, 'amount', 'status', false)
                        break
                    case 'Refunded':
                        array = formatData(this.state.payments, consts.payment_status_refunded, start, end, 'amount', 'status', false)
                        break
                    case 'Completed':
                        array = formatData(this.state.payments, consts.payment_status_completed, start, end, 'amount', 'status', false)
                        break
                }
                break

            case 'Quotes':
                switch (radioSelected) {
                    case 'Active':
                        array = formatData(this.state.quotes, consts.quote_status_draft, start, end, 'amount', 'status', false)
                        break

                    case 'Approved':
                        array = formatData(this.state.quotes, consts.quote_status_approved, start, end, 'amount', 'status', false)
                        break

                    case 'Unapproved':
                        array = formatData(this.state.quotes, consts.quote_status_sent, start, end, 'amount', 'status', false)
                        break

                    case 'Overdue': {
                        const filterQuotesByExpiration = filterOverdue(this.state.quotes)

                        array = formatData(filterQuotesByExpiration, consts.quote_status_sent, start, end, 'total', 'status_id')
                    }

                        break
                }
                break

            case 'Credits':
                switch (radioSelected) {
                    case 'Active':
                        array = formatData(this.state.credits, consts.credit_status_draft, start, end, 'amount', 'status', false)
                        break

                    case 'Completed':
                        array = formatData(this.state.credits, consts.credit_status_applied, start, end, 'amount', 'status', false)
                        break

                    case 'Sent':
                        array = formatData(this.state.credits, consts.credit_status_sent, start, end, 'amount', 'status', false)
                        break

                    case 'Overdue': {
                        const filterCreditsByExpiration = filterOverdue(this.state.credits)

                        array = formatData(filterCreditsByExpiration, consts.credit_status_sent, start, end, 'total', 'status_id')
                        // array = formatData(this.state.credits, 2, start, end, 'amount', 'status', false)
                    }

                        break
                }
                break

            case 'Orders':
                switch (radioSelected) {
                    case 'Draft':
                        array = formatData(this.state.orders, consts.order_status_draft, start, end, 'amount', 'status', false)
                        break

                    case 'Held':
                        array = formatData(this.state.orders, consts.order_status_held, start, end, 'amount', 'status', false)
                        break

                    case 'Backordered':
                        array = formatData(this.state.orders, consts.order_status_backorder, start, end, 'amount', 'status', false)
                        break

                    case 'Cancelled':
                        array = formatData(this.state.orders, consts.order_status_cancelled, start, end, 'amount', 'status', false)
                        break

                    case 'Sent':
                        array = formatData(this.state.orders, consts.order_status_sent, start, end, 'amount', 'status', false)
                        break

                    case 'Completed':
                        array = formatData(this.state.orders, consts.order_status_complete, start, end, 'amount', 'status', false)
                        break

                    case 'Overdue': {
                        const filterOrdersByExpiration = filterOverdue(this.state.orders)

                        array = formatData(filterOrdersByExpiration, consts.order_status_draft, start, end, 'total', 'status_id')
                        // array = formatData(this.state.orders, 3, start, end, 'amount', 'status', false)
                    }

                        break
                }
        }

        return array
    }

    getInvoiceChartData (start, end, dates) {
        const invoiceActive = formatData(this.state.invoices, consts.invoice_status_draft, start, end, 'total', 'status_id')
        const invoiceOutstanding = formatData(this.state.invoices, consts.invoice_status_sent, start, end, 'total', 'status_id')
        const invoicePaid = formatData(this.state.invoices, consts.invoice_status_paid, start, end, 'total', 'status_id')
        const invoiceCancelled = formatData(this.state.invoices, consts.invoice_status_cancelled, start, end, 'total', 'status_id')

        const filterInvoicesByExpiration = filterOverdue(this.state.invoices)
        const invoiceOverdue = formatData(filterInvoicesByExpiration, consts.invoice_status_sent, start, end, 'total', 'status_id')

        const buttons = {}
        const datasets = []

        if (this.state.dashboard_filters.Invoices.Active === 1) {
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

        if (this.state.dashboard_filters.Invoices.Outstanding === 1) {
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

        if (this.state.dashboard_filters.Invoices.Paid === 1) {
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

        if (this.state.dashboard_filters.Invoices.Cancelled === 1) {
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

        if (this.state.dashboard_filters.Invoices.Overdue === 1) {
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

        const invoices = {
            name: 'Invoices',
            labels: dates,
            buttons: buttons,
            datasets: datasets
        }

        return invoices
    }

    getQuoteChartData (start, end, dates) {
        const quoteActive = formatData(this.state.quotes, consts.quote_status_draft, start, end, 'total', 'status_id')
        const quoteApproved = formatData(this.state.quotes, consts.quote_status_approved, start, end, 'total', 'status_id')
        const quoteUnapproved = formatData(this.state.quotes, consts.quote_status_sent, start, end, 'total', 'status_id')

        const filterQuotesByExpiration = filterOverdue(this.state.quotes)
        const quoteOverdue = formatData(filterQuotesByExpiration, consts.quote_status_sent, start, end, 'total', 'status_id')

        const buttons = {}
        const datasets = []

        if (this.state.dashboard_filters.Quotes.Active === 1) {
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

        if (this.state.dashboard_filters.Quotes.Approved === 1) {
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

        if (this.state.dashboard_filters.Quotes.Unapproved === 1) {
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

        if (this.state.dashboard_filters.Quotes.Overdue === 1) {
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

        const quotes = {
            name: 'Quotes',
            labels: dates,
            buttons: buttons,
            datasets: datasets
        }

        return quotes
    }

    getCreditChartData (start, end, dates) {
        const creditActive = formatData(this.state.credits, consts.credit_status_draft, start, end, 'total', 'status_id')
        const creditCompleted = formatData(this.state.credits, consts.credit_status_applied, start, end, 'total', 'status_id')
        const creditSent = formatData(this.state.credits, consts.credit_status_sent, start, end, 'total', 'status_id')

        const filterCreditsByExpiration = filterOverdue(this.state.credits)
        const creditOverdue = formatData(filterCreditsByExpiration, consts.credit_status_sent, start, end, 'total', 'status_id')

        const buttons = {}
        const datasets = []

        if (this.state.dashboard_filters.Credits.Active === 1) {
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

        if (this.state.dashboard_filters.Credits.Completed === 1) {
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

        if (this.state.dashboard_filters.Credits.Sent === 1) {
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

        if (this.state.dashboard_filters.Credits.Overdue === 1) {
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

        const credits = {
            name: 'Credits',
            labels: dates,
            buttons: buttons,
            datasets: datasets
        }

        return credits
    }

    getOrderChartData (start, end, dates) {
        const orderHeld = formatData(this.state.orders, consts.order_status_held, start, end, 'total', 'status_id')
        const orderDraft = formatData(this.state.orders, consts.order_status_draft, start, end, 'total', 'status_id')
        const orderBackordered = formatData(this.state.orders, consts.order_status_backorder, start, end, 'total', 'status_id')
        const orderCancelled = formatData(this.state.orders, consts.order_status_cancelled, start, end, 'total', 'status_id')
        const orderSent = formatData(this.state.orders, consts.order_status_sent, start, end, 'total', 'status_id')
        const orderCompleted = formatData(this.state.orders, consts.order_status_complete, start, end, 'total', 'status_id')

        const filterOrdersByExpiration = filterOverdue(this.state.orders)
        const orderOverdue = formatData(filterOrdersByExpiration, 1, start, end, 'total', 'status_id')

        const buttons = {}
        const datasets = []

        if (this.state.dashboard_filters.Orders.Draft === 1) {
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

        if (this.state.dashboard_filters.Orders.Held === 1) {
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

        if (this.state.dashboard_filters.Orders.Backordered === 1) {
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

        if (this.state.dashboard_filters.Orders.Cancelled === 1) {
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

        if (this.state.dashboard_filters.Orders.Completed === 1) {
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

        if (this.state.dashboard_filters.Orders.Sent === 1) {
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

        if (this.state.dashboard_filters.Orders.Overdue === 1) {
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

        const orders = {
            name: 'Orders',
            labels: dates,
            buttons: buttons,
            datasets: datasets
        }

        return orders
    }

    getTaskChartData (start, end, dates) {
        const taskInvoices = removeNullValues(this.state.invoices, 'task_id')
        const taskInvoiced = formatData(taskInvoices, null, start, end, 'total', 'status_id')

        const today = new Date()
        const filterTasksByExpiration = this.state.tasks.filter((item) => {
            return new Date(item.due_date) > today
        })

        const taskOverdue = formatData(filterTasksByExpiration, 1, start, end, 'valued_at', 'status_id')

        /* const taskLogged = Object.values(formatData(this.state.tasks, 1, currentMoment, endMoment, 'total', 'status_id'))

        const taskPaid = Object.values(formatData(this.state.tasks, 3, currentMoment, endMoment, 'total', 'status_id')) */

        const buttons = {}
        const datasets = []

        // TODO Check key name
        if (this.state.dashboard_filters.Tasks.Invoiced === 1) {
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

        if (this.state.dashboard_filters.Tasks.Overdue === 1) {
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

        const tasks = {
            name: 'Tasks',
            labels: dates,
            buttons: buttons,
            datasets: datasets
        }

        return tasks
    }

    getPaymentChartData (start, end, dates) {
        const paymentActive = formatData(this.state.payments, consts.payment_status_pending, start, end, 'amount', 'status_id')
        const paymentRefunded = formatData(this.state.payments, consts.payment_status_refunded, start, end, 'refunded', 'status_id')
        const paymentCompleted = formatData(this.state.payments, consts.payment_status_completed, start, end, 'amount', 'status_id')

        const buttons = {}
        const datasets = []

        if (this.state.dashboard_filters.Payments.Active === 1) {
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

        if (this.state.dashboard_filters.Payments.Refunded === 1) {
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

        if (this.state.dashboard_filters.Payments.Completed === 1) {
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

        const payments = {
            name: 'Payments',
            labels: dates,
            buttons: buttons,
            datasets: datasets
        }

        return payments
    }

    getExpenseChartData (start, end, dates) {
        const expenseInvoices = removeNullValues(this.state.invoices, 'expense_id')

        const expenseLogged = formatData(this.state.expenses, consts.expense_status_logged, start, end, 'amount', 'status_id')
        const expensePending = formatData(this.state.expenses, consts.expense_status_pending, start, end, 'amount', 'status_id')
        const expenseInvoiced = formatData(expenseInvoices, consts.expense_status_invoiced, start, end, 'amount', 'status_id')
        const expensePaid = formatData(this.state.expenses, consts.expense_status_invoiced, start, end, 'amount', 'status_id')

        const buttons = {}
        const datasets = []

        if (this.state.dashboard_filters.Expenses.Logged === 1) {
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

        if (this.state.dashboard_filters.Expenses.Pending === 1) {
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

        if (this.state.dashboard_filters.Expenses.Invoiced === 1) {
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

        if (this.state.dashboard_filters.Expenses.Paid === 1) {
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

        const expenses = {
            name: 'Expenses',
            labels: dates,
            buttons: buttons,
            datasets: datasets
        }

        return expenses
    }

    getChartData () {
        var now = new Date()
        const daysInMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0).getDate()

        let currentMoment = moment().startOf('month')
        let endMoment = moment().endOf('month')

        if (this.state.start_date !== null) {
            currentMoment = moment(this.state.start_date)
        }

        if (this.state.end_date !== null) {
            endMoment = moment(this.state.end_date)
        }

        const start = currentMoment.format('YYYY-MM-DD')
        const end = endMoment.format('YYYY-MM-DD')
        // const currentMoment = moment('2020-02-03')
        // const endMoment = moment('2020-03-17')
        const dates = makeLabels(currentMoment, endMoment)
        const charts = []
        const modules = JSON.parse(localStorage.getItem('modules'))

        if (modules && modules.invoices) {
            const invoiceChartData = this.getInvoiceChartData(start, end, dates)
            charts.push(invoiceChartData)
        }

        if (modules && modules.orders) {
            const orderChartData = this.getOrderChartData(start, end, dates)
            charts.push(orderChartData)
        }

        if (modules && modules.payments) {
            const paymentChartData = this.getPaymentChartData(start, end, dates)
            charts.push(paymentChartData)
        }

        if (modules && modules.quotes) {
            const quoteChartData = this.getQuoteChartData(start, end, dates)
            charts.push(quoteChartData)
        }

        if (modules && modules.credits) {
            const creditChartData = this.getCreditChartData(start, end, dates)
            charts.push(creditChartData)
        }

        if (modules && modules.tasks) {
            const taskChartData = this.getTaskChartData(start, end, dates)
            charts.push(taskChartData)
        }

        if (modules && modules.expenses) {
            const expenseChartData = this.getExpenseChartData(start, end, dates)
            charts.push(expenseChartData)
        }

        return charts
    }

    getPieOptions () {
        return {
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b} : {c} ({d}%)'
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['Website', 'Personal Contact', 'Email', 'Other', 'Call']
            },
            series: [
                {
                    name: 'Sources',
                    type: 'pie',
                    radius: '55%',
                    center: ['50%', '60%'],
                    data: this.state.sources,
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(0, 0, 0, 0.5)'
                        }
                    }
                }
            ]
        }
    }

    getOption () {
        return {
            backgroundColor: '#1b1b1b',
            tooltip: {
                trigger: 'item',
                formatter: '{a} <br/>{b}: {c}%'
            },
            legend: {
                orient: 'horizontal',
                x: 'left',
                y: 0,
                data: ['Opened', 'Lost', 'Demo', 'Contacted', 'Won', 'No Show']
            },
            // Add Custom Colors
            color: ['#0FB365', '#1EC481', '#28D094', '#48D7A4', '#94E8CA', '#BFF1DF'],
            // Enable drag recalculate
            calculable: true,
            toolbox: {
                show: true,
                feature: {
                    mark: { show: true },
                    restore: { show: true },
                    saveAsImage: { show: true }
                }
            },
            series: [
                {
                    name: 'Deals',
                    type: 'funnel',
                    funnelAlign: 'left',
                    x: '25%',
                    x2: '25%',
                    y: '17.5%',
                    width: '50%',
                    height: '80%',
                    data: this.state.leadCounts
                }
            ]
        }
    }

    toggle (tab) {
        if (this.state.activeTab !== tab) {
            if (this.state.isMobile) {
                this.setState({ activeTab: tab, activeTab2: '' })
            } else {
                this.setState({ activeTab: tab })
            }
        }
    }

    toggleTab2 (tab) {
        if (this.state.activeTab2 !== tab) {
            if (this.state.isMobile) {
                this.setState({ activeTab2: tab, activeTab: '' })
            } else {
                this.setState({ activeTab2: tab })
            }
        }
    }

    doExport () {
        const array = this.getArrayToExport(this.state.entity, this.state.radioSelected)

        if (array[0] && Object.keys(array[0]).length) {
            // const colNames = Object.keys(response.data.data[0]);
            const colNames = Object.keys(array[0])

            let csvContent = 'data:text/csv;charset=utf-8,'
            csvContent += objectToCSVRow(colNames, colNames, true)

            array.forEach((item) => {
                csvContent += objectToCSVRow(item, colNames)
            })

            console.log('csv data', csvContent)

            const encodedUri = encodeURI(csvContent)
            const link = document.createElement('a')
            link.setAttribute('href', encodedUri)
            link.setAttribute('download', `${this.state.entity}-${this.state.radioSelected}.csv`)
            document.body.appendChild(link)
            link.click()
            document.body.removeChild(link)
        }
    }

    onRadioBtnClick (radioSelected, entity) {
        this.setState({
            radioSelected: radioSelected,
            entity: entity
        })
    }

    buildCheckboxes (entity) {
        return Object.keys(this.state.dashboard_filters[entity]).map((action, index) => {
            const checked = this.state.dashboard_filters[entity][action] === 1
            return (
                <li className="list-group-item-dark list-group-item d-flex justify-content-between align-items-center">
                    <Input checked={checked} onClick={this.toggleDashboardFilter} data-entity={entity}
                        data-action={action}
                        type="checkbox"/>
                    <span>{action}</span>
                </li>
            )
        })
    }

    getCustomer (customer_id) {
        const customer = this.state.customers.filter(customer => customer.id === customer_id)
        return customer[0].name
    }

    render () {
        const dashboard_minimized = this.state.dashboard_minimized
        const dashboardFilterEntities = Object.keys(this.state.dashboard_filters)

        const dashboardBody = dashboardFilterEntities.map((entity, index) => {
            return (
                <Card key={index} className="mr-2 p-0 col-12 col-md-3">
                    <CardHeader>{entity}</CardHeader>
                    <CardBody>
                        <ul className="list-group">
                            {this.buildCheckboxes(entity)}
                        </ul>
                    </CardBody>
                </Card>
            )
        })

        const onEvents = {
            click: this.onChartClick,
            legendselectchanged: this.onChartLegendselectchanged
        }

        const charts = this.state.invoices.length ? this.getChartData().map((entry, index) => {
            const buttons = Object.keys(entry.buttons).map((key, value) => {
                return <Button key={value}
                    color="outline-secondary"
                    onClick={() => this.onRadioBtnClick(key, entry.name)}
                    active={this.state.radioSelected === key}>{key} <FormatMoney
                        amount={entry.buttons[key].value}/></Button>
            })

            const footerButtons = Object.keys(entry.buttons).map((key, value) => {
                return <Col key={value} sm={12} md
                    className="mb-sm-2 mb-0">
                    <div
                        className="text-muted">{key}
                    </div>
                    <strong>Avg {entry.buttons[key].avg}
                        ({entry.buttons[key].pct}%)</strong>
                    <Progress
                        className="progress-xs mt-2"
                        color="warning" value={entry.buttons[key].pct}/>
                </Col>
            })

            return (<Row key={index}>
                <Col>
                    <Card>
                        <CardBody>
                            <Row>
                                <Col sm="5">
                                    <CardTitle
                                        className="mb-0"><h3>{entry.name}</h3></CardTitle>
                                    <h5> {`${moment(this.state.start_date).format('Do MMMM YYYY')} - ${moment(this.state.end_date).format('Do MMMM YYYY')}`}
                                    </h5>
                                </Col>
                                <Col sm="7"
                                    className="d-none d-sm-inline-block">
                                    <Button color="primary" onClick={this.doExport()}
                                        className="float-right"><i
                                            className="icon-cloud-download"/></Button>
                                    <ButtonToolbar
                                        className="float-right"
                                        aria-label="Toolbar with button groups">
                                        <ButtonGroup className="mr-3"
                                            aria-label="First group">
                                            {buttons}
                                        </ButtonGroup>
                                    </ButtonToolbar>
                                </Col>
                            </Row>
                            <div className="chart-wrapper"
                                style={{
                                    height: 300 + 'px',
                                    marginTop: 40 + 'px'
                                }}>
                                <Line data={entry}
                                    options={mainChartOpts}
                                    height={300}/>
                            </div>
                        </CardBody>
                        <CardFooter>
                            <Row className="text-center">
                                {footerButtons}
                            </Row>
                        </CardFooter>
                    </Card>
                </Col>
            </Row>)
        }) : null

        let leads = ''
        // expired
        const filterQuotesByExpiration = filterOverdue(this.state.quotes)
        const arrOverdueQuotes = filterQuotesByExpiration.length ? groupByStatus(filterQuotesByExpiration, 2, 'status_id') : []

        const filterOrdersByExpiration = filterOverdue(this.state.orders)
        const arrOverdueOrders = filterOrdersByExpiration.length ? groupByStatus(filterOrdersByExpiration, 2, 'status_id') : []

        const filterInvociesByExpiration = this.state.invoices.length ? filterOverdue(this.state.invoices) : []
        const arrOverdueInvoices = filterInvociesByExpiration.length ? groupByStatus(filterInvociesByExpiration, 2, 'status_id') : []

        // last 30 days
        const filterQuotesLast30Days = getLast30Days(this.state.quotes)
        const arrRecentQuotes = filterQuotesLast30Days.length ? groupByStatus(filterQuotesLast30Days, 1, 'status_id') : []

        const filterOrdersLast30Days = getLast30Days(this.state.orders)
        const arrRecentOrders = filterOrdersLast30Days.length ? groupByStatus(filterOrdersLast30Days, 1, 'status_id') : []

        const filterPaymentsLast30Days = getLast30Days(this.state.payments)
        const arrRecentPayments = filterPaymentsLast30Days.length ? groupByStatus(filterPaymentsLast30Days, 4, 'status_id') : []

        const arrRecentExpenses = this.state.expenses.length ? getLast30Days(this.state.expenses) : []

        const filterTasksLast30Days = this.state.tasks.length ? getLast30Days(this.state.tasks) : []
        const arrRecentTasks = filterTasksLast30Days.length ? filterTasksLast30Days.filter((item) => {
            const taskModel = new TaskModel(item)
            return !item.deleted_at && !taskModel.isRunning
        }) : []

        // TODO - Running tasks
        const arrRunningTasks = this.state.tasks.length ? this.state.tasks.filter((item) => {
            const taskModel = new TaskModel(item)
            return !item.deleted_at && taskModel.isRunning
        }) : []

        const filterInvoicesLast30Days = getLast30Days(this.state.invoices)
        const arrRecentInvoices = filterInvoicesLast30Days.length ? groupByStatus(filterInvoicesLast30Days, 1, 'status_id') : []

        const overdue_invoices = this.state.customers.length && arrOverdueInvoices.length
            ? <InvoiceItem showCheckboxes={false} updateInvoice={(entities) => {
                this.addUserToState('invoices', entities)
            }} invoices={arrOverdueInvoices} force_mobile={true} show_list={true} users={[]}
            custom_fields={[]} customers={this.state.customers}
            viewId={this.state.viewId}
            ignoredColumns={this.state.ignoredColumns}
            toggleViewedEntity={(id, title = null, edit = null) => {
                this.toggleViewedEntity('Invoice', this.state.invoices, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/>
            : null

        const recent_invoices = this.state.customers.length && arrRecentInvoices.length
            ? <InvoiceItem showCheckboxes={false} updateInvoice={(entities) => {
                this.addUserToState('invoices', entities)
            }} invoices={arrRecentInvoices} force_mobile={true} show_list={true} users={[]}
            custom_fields={[]} customers={this.state.customers}
            viewId={this.state.viewId}
            ignoredColumns={this.state.ignoredColumns}
            toggleViewedEntity={(id, title = null, edit = null) => {
                this.toggleViewedEntity('Invoice', this.state.invoices, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

        const recent_tasks = this.state.customers.length && arrRecentTasks.length
            ? <TaskItem showCheckboxes={false} force_mobile={true} action={(entities) => {
                this.addUserToState('tasks', entities)
            }} tasks={arrRecentTasks} show_list={true} users={JSON.parse(localStorage.getItem('users'))}
            custom_fields={[]} customers={this.state.customers}
            viewId={this.state.viewId}
            ignoredColumns={this.state.ignoredColumns}
            toggleViewedEntity={(id, title = null, edit = null) => {
                this.toggleViewedEntity('Task', this.state.tasks, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

        const running_tasks = this.state.customers.length && arrRunningTasks.length
            ? <TaskItem showCheckboxes={false} action={(entities) => {
                this.addUserToState('tasks', entities)
            }} tasks={arrRunningTasks} force_mobile={true} show_list={true}
            users={JSON.parse(localStorage.getItem('users'))}
            custom_fields={[]} customers={this.state.customers}
            viewId={this.state.viewId}
            ignoredColumns={this.state.ignoredColumns}
            toggleViewedEntity={(id, title = null, edit = null) => {
                this.toggleViewedEntity('Task', this.state.tasks, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

        const recent_expenses = this.state.customers.length && arrRecentExpenses.length
            ? <ExpenseItem showCheckboxes={false} updateExpenses={(entities) => {
                this.addUserToState('expenses', entities)
            }} expenses={arrRecentExpenses} force_mobile={true} show_list={true} users={[]}
            custom_fields={[]} customers={this.state.customers}
            viewId={this.state.viewId}
            ignoredColumns={this.state.ignoredColumns}
            toggleViewedEntity={(id, title = null, edit = null) => {
                this.toggleViewedEntity('Expense', this.state.expenses, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

        const overdue_quotes = this.state.customers.length && arrOverdueQuotes.length
            ? <QuoteItem showCheckboxes={false} updateInvoice={(entities) => {
                this.addUserToState('quotes', entities)
            }} quotes={arrOverdueQuotes} force_mobile={true} show_list={true} users={[]}
            custom_fields={[]} customers={this.state.customers}
            viewId={this.state.viewId}
            ignoredColumns={this.state.ignoredColumns}
            toggleViewedEntity={(id, title = null, edit = null) => {
                this.toggleViewedEntity('Quote', this.state.quotes, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

        const recent_quotes = this.state.customers.length && arrRecentQuotes.length
            ? <QuoteItem showCheckboxes={false} updateInvoice={(entities) => {
                this.addUserToState('quotes', entities)
            }} quotes={arrRecentQuotes} force_mobile={true} show_list={true} users={[]}
            custom_fields={[]} customers={this.state.customers}
            viewId={this.state.viewId}
            ignoredColumns={this.state.ignoredColumns}
            toggleViewedEntity={(id, title = null, edit = null) => {
                this.toggleViewedEntity('Quote', this.state.quotes, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

        const overdue_orders = this.state.customers.length && arrOverdueOrders.length
            ? <OrderItem showCheckboxes={false} updateOrder={(entities) => {
                this.addUserToState('orders', entities)
            }} orders={arrOverdueOrders} force_mobile={true} show_list={true} users={[]}
            custom_fields={[]} customers={this.state.customers}
            viewId={this.state.viewId}
            ignoredColumns={this.state.ignoredColumns}
            toggleViewedEntity={(id, title = null, edit = null) => {
                this.toggleViewedEntity('Order', this.state.orders, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

        const recent_orders = this.state.customers.length && arrRecentOrders.length
            ? <OrderItem showCheckboxes={false} updateOrder={(entities) => {
                this.addUserToState('orders', entities)
            }} orders={arrRecentOrders} force_mobile={true} show_list={true} users={[]}
            custom_fields={[]} customers={this.state.customers}
            viewId={this.state.viewId}
            ignoredColumns={this.state.ignoredColumns}
            toggleViewedEntity={(id, title = null, edit = null) => {
                this.toggleViewedEntity('Order', this.state.orders, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

        const recent_payments = this.state.customers.length && arrRecentPayments.length
            ? <PaymentItem showCheckboxes={false} updateCustomers={(entities) => {
                this.addUserToState('payments', entities)
            }} payments={arrRecentPayments} force_mobile={true} credits={this.state.credits}
            invoices={this.state.invoices} show_list={true}
            users={[]}
            custom_fields={[]} customers={this.state.customers}
            viewId={this.state.viewId}
            ignoredColumns={this.state.ignoredColumns}
            toggleViewedEntity={(id, title = null, edit = null) => {
                this.toggleViewedEntity('Payment', this.state.payments, id, title, edit)
            }}
            bulk={[]}
            onChangeBulk={null}/> : null

        const modules = JSON.parse(localStorage.getItem('modules'))

        if (this.state.deals.length) {
            let count = 1

            leads = this.state.deals.map((lead, index) => {
                return (
                    <React.Fragment key={index}>
                        <div key={index} className="media mt-1">
                            <div className="media-left pr-2">
                                <img className="media-object avatar avatar-md rounded-circle"
                                    src={`/files/avatar${count++}.png`} alt="Generic placeholder image"/>
                            </div>
                            <div className="media-body">
                                <p className="text-bold-600 m-0">{lead.title.substring(0, 40)} <span
                                    className="float-right badge badge-success">{lead.status_name}</span></p>
                                <p className="font-small-2 text-muted m-0">{lead.valued_at}<i
                                    className="ft-calendar pl-1"/>{lead.due_date}</p>
                            </div>
                        </div>
                    </React.Fragment>
                )
            })
        }

        return <React.Fragment>
            <Row>
                <div style={{ position: 'absolute', right: '20px', zIndex: '99999' }}>
                    {!dashboard_minimized &&
                    <span style={{ fontSize: '28px' }} className="pull-right" onClick={() => {
                        localStorage.setItem('dashboard_minimized', true)
                        this.setState({ dashboard_minimized: true })
                    }}>-</span>
                    }

                    {!!dashboard_minimized &&
                    <span style={{ fontSize: '28px' }} className="pull-right" onClick={() => {
                        localStorage.setItem('dashboard_minimized', false)
                        this.setState({ dashboard_minimized: false })
                    }}>+</span>
                    }
                </div>

                <Col className="dashboard-content-wrapper" lg={dashboard_minimized ? 12 : 7}>
                    <div className={`topbar pl-0 dashboard-tabs ${dashboard_minimized ? 'dashboard-tabs-full' : ''}`}>
                        <Card>
                            <CardBody className="pb-0">
                                <Nav
                                    className="tabs-justify disable-scrollbars nav-fill setting-tabs disable-scrollbars"
                                    tabs>
                                    <NavItem>
                                        <NavLink
                                            className={this.state.activeTab === '1' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggle('1')
                                            }}>
                                            {translations.overview}
                                        </NavLink>
                                    </NavItem>
                                    <NavItem>
                                        <NavLink
                                            className={this.state.activeTab === '2' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggle('2')
                                            }}>
                                            {translations.activity}
                                        </NavLink>
                                    </NavItem>

                                    {this.state.isMobile && modules && modules.invoices &&
                                    <NavItem>
                                        <NavLink
                                            className={this.state.activeTab2 === '3' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggleTab2('3')
                                            }}>
                                            {translations.invoices}
                                        </NavLink>
                                    </NavItem>
                                    }

                                    {this.state.isMobile && modules && modules.quotes &&
                                    <NavItem>
                                        <NavLink
                                            className={this.state.activeTab2 === '4' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggleTab2('4')
                                            }}>
                                            {translations.quotes}
                                        </NavLink>
                                    </NavItem>
                                    }

                                    {this.state.isMobile && modules && modules.payments &&
                                    <NavItem>
                                        <NavLink
                                            className={this.state.activeTab2 === '5' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggleTab2('5')
                                            }}>
                                            {translations.payments}
                                        </NavLink>
                                    </NavItem>
                                    }

                                    {this.state.isMobile && this.state.isMobile && modules && modules.orders &&
                                    <NavItem>
                                        <NavLink
                                            className={this.state.activeTab2 === '6' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggleTab2('6')
                                            }}>
                                            {translations.orders}
                                        </NavLink>
                                    </NavItem>
                                    }
                                </Nav>

                                <Row>
                                    <Col className="d-flex justify-content-between align-items-center">
                                        <i className={`ml-4 mt-2 fa ${icons.left}`}/>
                                        <i className={`mt-2 fa ${icons.right}`}/>
                                        <MonthPicker start_year={moment(this.state.start_date).format('YYYY')}
                                            start_month={moment(this.state.start_date).format('M')}
                                            end_year={moment(this.state.end_date).format('YYYY')}
                                            end_month={moment(this.state.end_date).format('M')}
                                            onChange={this.setDates}/>
                                    </Col>
                                </Row>
                            </CardBody>
                        </Card>
                    </div>

                    <TabContent className="dashboard-tabs-margin" activeTab={this.state.activeTab}>
                        <TabPane className="pr-0" tabId="1">
                            <Row>
                                <Col className="pl-0" md={6}>
                                    <CardModule
                                        body={true}
                                        content={
                                            <div>
                                                <div className="media">
                                                    <div className="media-body text-left">
                                                        <h3 className="success">{this.state.leadsToday}</h3>
                                                        <span>Today's Leads</span>
                                                    </div>
                                                    <div className="media-right media-middle">
                                                        <i className="ft-award success font-large-2 float-right"/>
                                                    </div>
                                                </div>

                                                <div className="progress mt-1 mb-0" style={{ height: '7px' }}>
                                                    <div className="progress-bar bg-success" role="progressbar"
                                                        style={{ width: '80%' }} aria-valuenow="80" aria-valuemin="0"
                                                        aria-valuemax="100"/>
                                                </div>
                                            </div>
                                        }
                                    />

                                    <CardModule
                                        body={true}
                                        content={
                                            <div>
                                                <div className="media">
                                                    <div className="media-body text-left">
                                                        <h3 className="deep-orange">{this.state.newDeals}</h3>
                                                        <span>New Deal</span>
                                                    </div>
                                                    <div className="media-right media-middle">
                                                        <i className="ft-package deep-orange font-large-2 float-right"/>
                                                    </div>
                                                </div>

                                                <div className="progress mt-1 mb-0" style={{ height: '7px' }}>
                                                    <div className="progress-bar bg-deep-orange" role="progressbar"
                                                        style={{ width: '35%' }} aria-valuenow="35" aria-valuemin="0"
                                                        aria-valuemax="100"/>
                                                </div>
                                            </div>
                                        }
                                    />

                                    <CardModule
                                        body={true}
                                        content={
                                            <div>
                                                <div className="media">
                                                    <div className="media-body text-left">
                                                        <h3 className="info">{this.state.newCustomers}</h3>
                                                        <span>New Customers</span>
                                                    </div>
                                                    <div className="media-right media-middle">
                                                        <i className="ft-users info font-large-2 float-right"/>
                                                    </div>
                                                </div>

                                                <div className="progress mt-1 mb-0" style={{ height: '7px' }}>
                                                    <div className="progress-bar bg-success" role="progressbar"
                                                        style={{ width: '35%' }} aria-valuenow="35" aria-valuemin="0"
                                                        aria-valuemax="100"/>
                                                </div>
                                            </div>
                                        }
                                    />
                                </Col>
                                <Col className="pl-0" md={6}>
                                    <CardModule
                                        body={true}
                                        hCenter={true}
                                        header={
                                            <React.Fragment>
                                                <span className="success darken-1">Total Budget</span>
                                                <h3 className="font-large-2 grey darken-1 text-bold-200">{this.state.totalBudget}</h3>
                                            </React.Fragment>
                                        }
                                        content={
                                            <React.Fragment>
                                                <input type="text" value="75"
                                                    className="knob hide-value responsive angle-offset"
                                                    data-angleOffset="0" data-thickness=".15"
                                                    data-linecap="round" data-width="150"
                                                    data-height="150" data-inputColor="#e1e1e1"
                                                    data-readOnly="true" data-fgColor="#37BC9B"
                                                    data-knob-icon="ft-trending-up"/>

                                                <ul className="list-inline clearfix mt-2 mb-0">
                                                    <li className="border-right-grey border-right-lighten-2 pr-2">
                                                        <h2 className="grey darken-1 text-bold-400">75%</h2>
                                                        <span className="success">Completed</span>
                                                    </li>
                                                    <li className="pl-2">
                                                        <h2 className="grey darken-1 text-bold-400">25%</h2>
                                                        <span className="danger">Remaining</span>
                                                    </li>
                                                </ul>
                                            </React.Fragment>
                                        }
                                    />
                                </Col>
                                {/* <Col md={6}> */}
                                {/*    <CardModule */}
                                {/*        body={false} */}
                                {/*        content={ */}
                                {/*            <div className="earning-chart position-relative"> */}
                                {/*                <div className="chart-title position-absolute mt-2 ml-2"> */}
                                {/*                    <h1 className="font-large-2 grey darken-1 text-bold-200">{this.state.totalEarnt}</h1> */}
                                {/*                    <span className="text-muted">Total Earning</span> */}
                                {/*                </div> */}
                                {/*                <div className="chartjs height-400"> */}
                                {/*                    <canvas id="earning-chart" className="height-400 block"/> */}
                                {/*                </div> */}
                                {/*                <div */}
                                {/*                    className="chart-stats position-absolute position-bottom-0 position-right-0 mb-2 mr-3"> */}
                                {/*                    <a href="#" className="btn bg-info mr-1 white">Statistics <i */}
                                {/*                        className="ft-bar-chart"/></a> <span */}
                                {/*                        className="text-muted">for the <a */}
                                {/*                            href="#">last year.</a></span> */}
                                {/*                </div> */}
                                {/*            </div> */}
                                {/*        } */}
                                {/*    /> */}
                                {/* </Col> */}
                            </Row>

                            {/* <Row className="match-height"> */}
                            {/*    <Col className="col-xl-6" lg={12}> */}
                            {/*        <CardModule */}
                            {/*            body={true} */}
                            {/*            header={ */}
                            {/*                <React.Fragment> */}
                            {/*                    <h4 className="card-title">Deals Funnel <span */}
                            {/*                        className="text-muted text-bold-400">This Month</span></h4> */}
                            {/*                    <a className="heading-elements-toggle"><i */}
                            {/*                        className="ft-more-horizontal font-medium-3"/></a> */}
                            {/*                    <div className="heading-elements"> */}
                            {/*                        <ul className="list-inline mb-0"> */}
                            {/*                            <li><a data-action="reload"><i className="ft-rotate-cw"/></a> */}
                            {/*                            </li> */}
                            {/*                        </ul> */}
                            {/*                    </div> */}
                            {/*                </React.Fragment> */}
                            {/*            } */}
                            {/*            content={ */}
                            {/*                <ReactEcharts option={this.getOption()}/> */}
                            {/*            } */}
                            {/*        /> */}

                            {/*    </Col> */}
                            {/*    <Col className="col-xl-6" lg={12}> */}
                            {/*        <CardModule */}
                            {/*            cardHeight='410px' */}
                            {/*            body={true} */}
                            {/*            header={ */}
                            {/*                <React.Fragment> */}
                            {/*                    <h4 className="card-title">Deals <span className="text-muted text-bold-400">- Won 5</span> */}
                            {/*                    </h4> */}
                            {/*                    <a className="heading-elements-toggle"><i */}
                            {/*                        className="ft-more-horizontal font-medium-3"/></a> */}
                            {/*                    <div className="heading-elements"> */}
                            {/*                        <ul className="list-inline mb-0"> */}
                            {/*                            <li><a data-action="reload"><i className="ft-rotate-cw"/></a> */}
                            {/*                            </li> */}
                            {/*                        </ul> */}
                            {/*                    </div> */}
                            {/*                </React.Fragment> */}
                            {/*            } */}
                            {/*            content={ */}
                            {/*                <div style={{ */}
                            {/*                    height: '300px', */}
                            {/*                    overflowY: 'auto' */}
                            {/*                }} id="deals-list-scroll" */}
                            {/*                className="card-body height-350 position-relative ps-container ps-theme-default" */}
                            {/*                data-ps-id="6205b797-6d0d-611f-25fd-16195eadda29"> */}
                            {/*                    {leads} */}
                            {/*                </div> */}
                            {/*            } */}
                            {/*        /> */}
                            {/*    </Col> */}
                            {/* </Row> */}

                            <Row className="match-height">
                                {/* <Col className="col-xl-8" lg={12}> */}
                                {/*    <StatsCard/> */}
                                {/* </Col> */}

                                <Col md={12}>
                                    <CardModule
                                        body={true}
                                        header={
                                            <React.Fragment>
                                                <h4 className="card-title">Sources <span
                                                    className="text-muted text-bold-400">This Month</span></h4>
                                                <a className="heading-elements-toggle"><i
                                                    className="ft-more-horizontal font-medium-3"/></a>
                                                <div className="heading-elements">
                                                    <ul className="list-inline mb-0">
                                                        <li><a data-action="reload"><i className="ft-rotate-cw"/></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </React.Fragment>
                                        }
                                        content={
                                            <ReactEcharts
                                                option={this.getPieOptions()}
                                                style={{ height: 150 }}
                                                onChartReady={this.onChartReady}
                                                onEvents={onEvents}
                                            />
                                        }
                                    />
                                </Col>
                            </Row>

                            <Row>
                                <Button color="danger" onClick={this.toggleModal}>Configure Dashboard</Button>
                            </Row>

                        </TabPane>

                        <TabPane tabId="2">
                            <MessageContainer/>
                        </TabPane>
                    </TabContent>
                </Col>

                <Col className={`dashboard-tabs-right ${dashboard_minimized ? 'd-none' : ''}`} lg={5}>

                    <Card className="dashboard-border">
                        <CardBody>
                            {!this.state.isMobile &&
                            <Nav className="tabs-justify disable-scrollbars" tabs>
                                {modules && modules.invoices &&
                                <NavItem>
                                    <NavLink
                                        className={this.state.activeTab2 === '3' ? 'active' : ''}
                                        onClick={() => {
                                            this.toggleTab2('3')
                                        }}>
                                        {translations.invoices}
                                    </NavLink>
                                </NavItem>
                                }

                                {modules && modules.quotes &&
                                <NavItem>
                                    <NavLink
                                        className={this.state.activeTab2 === '4' ? 'active' : ''}
                                        onClick={() => {
                                            this.toggleTab2('4')
                                        }}>
                                        {translations.quotes}
                                    </NavLink>
                                </NavItem>
                                }

                                {modules && modules.payments &&
                                <NavItem>
                                    <NavLink
                                        className={this.state.activeTab2 === '5' ? 'active' : ''}
                                        onClick={() => {
                                            this.toggleTab2('5')
                                        }}>
                                        {translations.payments}
                                    </NavLink>
                                </NavItem>
                                }

                                {modules && modules.orders &&
                                <NavItem>
                                    <NavLink
                                        className={this.state.activeTab2 === '6' ? 'active' : ''}
                                        onClick={() => {
                                            this.toggleTab2('6')
                                        }}>
                                        {translations.orders}
                                    </NavLink>
                                </NavItem>
                                }
                                {modules && modules.tasks &&
                                <NavItem>
                                    <NavLink
                                        className={this.state.activeTab2 === '7' ? 'active' : ''}
                                        onClick={() => {
                                            this.toggleTab2('7')
                                        }}>
                                        {translations.tasks}
                                    </NavLink>
                                </NavItem>
                                }
                                {modules && modules.expenses &&
                                <NavItem>
                                    <NavLink
                                        className={this.state.activeTab2 === '8' ? 'active' : ''}
                                        onClick={() => {
                                            this.toggleTab2('8')
                                        }}>
                                        {translations.expenses}
                                    </NavLink>
                                </NavItem>
                                }
                            </Nav>
                            }

                            <TabContent activeTab={this.state.activeTab2}>
                                <TabPane tabId="3">
                                    <Card>
                                        <CardHeader>{translations.overdue_invoices} {arrOverdueInvoices.length ? arrOverdueInvoices.length : ''}</CardHeader>
                                        <CardBody style={{ height: '285px', overflowY: 'auto' }}>
                                            <ListGroup>
                                                {overdue_invoices}
                                            </ListGroup>
                                        </CardBody>
                                    </Card>

                                    <Card>
                                        <CardHeader>{translations.recent_invoices} {arrRecentInvoices.length ? arrRecentInvoices.length : ''}</CardHeader>
                                        <CardBody style={{ height: '285px', overflowY: 'auto' }}>
                                            <ListGroup>
                                                {recent_invoices}
                                            </ListGroup>
                                        </CardBody>
                                    </Card>
                                </TabPane>

                                <TabPane tabId="4">
                                    <Card>
                                        <CardHeader>{translations.overdue_quotes} {arrOverdueQuotes.length ? arrOverdueQuotes.length : ''}</CardHeader>
                                        <CardBody style={{ height: '285px', overflowY: 'auto' }}>
                                            <ListGroup>
                                                {overdue_quotes}
                                            </ListGroup>
                                        </CardBody>
                                    </Card>

                                    <Card>
                                        <CardHeader>{translations.recent_quotes} {arrRecentQuotes.length ? arrRecentQuotes.length : ''}</CardHeader>
                                        <CardBody style={{ height: '285px', overflowY: 'auto' }}>
                                            <ListGroup>
                                                {recent_quotes}
                                            </ListGroup>
                                        </CardBody>
                                    </Card>
                                </TabPane>

                                <TabPane tabId="5">
                                    <Card>
                                        <CardHeader>{translations.recent_payments} {arrRecentPayments.length ? arrRecentPayments.length : ''}</CardHeader>
                                        <CardBody style={{ height: '285px', overflowY: 'auto' }}>
                                            <ListGroup>
                                                {recent_payments}
                                            </ListGroup>
                                        </CardBody>
                                    </Card>
                                </TabPane>

                                <TabPane tabId="6">
                                    <Card>
                                        <CardHeader>{translations.overdue_orders} {arrOverdueOrders.length ? arrOverdueOrders.length : ''}</CardHeader>
                                        <CardBody style={{ height: '285px', overflowY: 'auto' }}>
                                            <ListGroup>
                                                {overdue_orders}
                                            </ListGroup>
                                        </CardBody>
                                    </Card>

                                    <Card>
                                        <CardHeader>{translations.recent_orders} {arrRecentOrders.length ? arrRecentOrders.length : ''}</CardHeader>
                                        <CardBody style={{ height: '285px', overflowY: 'auto' }}>
                                            <ListGroup>
                                                {recent_orders}
                                            </ListGroup>
                                        </CardBody>
                                    </Card>
                                </TabPane>
                                <TabPane tabId="7">
                                    <Card>
                                        <CardHeader>{translations.recent_tasks} {arrRecentTasks.length ? arrRecentTasks.length : ''}</CardHeader>
                                        <CardBody style={{ height: '285px', overflowY: 'auto' }}>
                                            <ListGroup>
                                                {recent_tasks}
                                            </ListGroup>
                                        </CardBody>
                                    </Card>

                                    <Card>
                                        <CardHeader>{translations.running_tasks} {arrRunningTasks.length ? arrRunningTasks.length : ''}</CardHeader>
                                        <CardBody style={{ height: '285px', overflowY: 'auto' }}>
                                            <ListGroup>
                                                {running_tasks}
                                            </ListGroup>
                                        </CardBody>
                                    </Card>
                                </TabPane>
                                <TabPane tabId="8">
                                    <Card>
                                        <CardHeader>{translations.recent_expenses} {arrRecentExpenses.length ? arrRecentExpenses.length : ''}</CardHeader>
                                        <CardBody style={{ height: '285px', overflowY: 'auto' }}>
                                            <ListGroup>
                                                {recent_expenses}
                                            </ListGroup>
                                        </CardBody>
                                    </Card>
                                </TabPane>
                            </TabContent>
                        </CardBody>
                    </Card>
                </Col>
            </Row>

            <Row className={this.state.activeTab === '1' ? 'd-block z-index-high' : 'd-none'}>
                <Col sm={12}>
                    {charts}
                </Col>
            </Row>

            <Modal size="lg" isOpen={this.state.modal} toggle={this.toggleModal}>
                <ModalHeader toggle={this.toggleModal}>Configure Dashboard</ModalHeader>
                <ModalBody>
                    {dashboardBody}
                </ModalBody>
                <ModalFooter>
                    <Button color="secondary" onClick={this.toggleModal}>Close</Button>
                </ModalFooter>
            </Modal>

            <Modal isOpen={this.state.modal2} toggle={this.toggleModal2}>
                <ModalHeader toggle={this.toggleModal2}>Configure Dashboard</ModalHeader>
                <ModalBody>
                    <SettingsWizard/>
                </ModalBody>
                <ModalFooter>
                    <Button color="secondary" onClick={this.toggleModal}>Close</Button>
                </ModalFooter>
            </Modal>

            {this.state.view && <ViewEntity
                updateState={this.updateState}
                toggle={this.toggleViewedEntity}
                title={this.state.view.title}
                viewed={this.state.view.viewMode}
                edit={this.state.view.edit}
                companies={[]}
                customers={this.state.customers && this.state.customers.length ? this.state.customers : []}
                entities={this.state.view.entities}
                entity={this.state.view.viewedId}
                entity_type={this.state.view.entity}
            />}
        </React.Fragment>
    }
}
