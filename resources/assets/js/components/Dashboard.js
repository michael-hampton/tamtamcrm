import React, { Component } from 'react'
import {
    Button,
    Card,
    CardBody,
    CardHeader,
    Col,
    Input,
    Modal,
    ModalBody,
    ModalFooter,
    ModalHeader,
    Nav,
    NavItem,
    NavLink,
    Row,
    TabContent,
    TabPane
} from 'reactstrap'
import axios from 'axios'
import MessageContainer from './activity/MessageContainer'
import moment from 'moment'
import MonthPicker from './common/MonthPicker'
import { icons } from './utils/_icons'
import { consts } from './utils/_consts'
import { translations } from './utils/_translations'
import SettingsWizard from './settings/settings_wizard/SettingsWizard'
import ViewEntity from './common/ViewEntity'
import _formatData, { _filterOverdue } from './dashboard/_utils'
import DashboardPanels from './dashboard/DashboardPanels'
import SidebarScaffold from './dashboard/SidebarScaffold'
import Overview from './dashboard/Overview'

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

export default class Dashboard extends Component {
    constructor (props) {
        super(props)
        this.getOption = this.getOption.bind(this)
        this.state = {
            dashboard_minimized: !!(localStorage.getItem('dashboard_minimized') && localStorage.getItem('dashboard_minimized') === 'true'),
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
            activeTab2: window.innerWidth <= 768 ? '' : '1',
            isMobile: window.innerWidth <= 768,
            view: {
                ignore: [],
                viewMode: false,
                viewedId: null,
                title: null
            },
            viewId: null
        }

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.settings = user_account[0].account.settings

        this.toggle = this.toggle.bind(this)
        this.toggleTab2 = this.toggleTab2.bind(this)
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
        this.handleScroll = this.handleScroll.bind(this)
    }

    componentDidMount () {
        window.addEventListener('scroll', this.handleScroll)

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

    handleScroll () {
        if (this.state.isMobile) {
            return
        }

        const offset = document.documentElement.scrollTop
        const offsetIndex = Math.floor((offset + 120) / 622)

        if (parseInt(this.state.activeTab2) !== offsetIndex.toString() && offsetIndex > 0 && offsetIndex < 8) {
            this.setState({ activeTab2: offsetIndex.toString() }, () => {
                // alert(selected_tab.toString())
            })
        }
    }

    componentWillMount () {
        window.addEventListener('resize', this.handleWindowSizeChange)
    }

    componentWillUnmount () {
        window.removeEventListener('resize', this.handleWindowSizeChange)
        window.removeEventListener('scroll', this.handleScroll)
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
                        // array = _formatData(myData, 1, start, end, 'amount', 'status', false)
                        break

                    case 'Overdue': {
                        const today = new Date()
                        const filterTasksByExpiration = this.state.tasks.filter((item) => {
                            return new Date(item.due_date) > today
                        })

                        array = _formatData(filterTasksByExpiration, 1, start, end, 'valued_at', 'status_id')
                    }

                        break
                }
                break

            case 'Invoices':
                switch (radioSelected) {
                    case 'Active':
                        array = _formatData(this.state.invoices, consts.invoice_status_draft, start, end, 'amount', 'status', false)
                        break

                    case 'Outstanding':
                        array = _formatData(this.state.invoices, consts.invoice_status_sent, start, end, 'amount', 'status', false)
                        break
                    case 'Paid':
                        array = _formatData(this.state.invoices, consts.invoice_status_paid, start, end, 'amount', 'status', false)
                        break

                    case 'Cancelled':
                        array = _formatData(this.state.invoices, consts.invoice_status_cancelled, start, end, 'amount', 'status', false)
                        break

                    case 'Overdue': {
                        const filterInvoicesByExpiration = _filterOverdue(this.state.invoices)

                        array = _formatData(filterInvoicesByExpiration, consts.invoice_status_sent, start, end, 'total', 'status_id')
                    }

                        break
                }
                break

            case 'Expenses':
                switch (radioSelected) {
                    case 'Logged':
                        array = _formatData(this.state.expenses, consts.expense_status_logged, start, end, 'amount', 'status', false)
                        break
                    case 'Pending':
                        array = _formatData(this.state.expenses, consts.expense_status_pending, start, end, 'amount', 'status', false)
                        break

                    case 'Invoiced':
                        array = _formatData(this.state.expenses, consts.expense_status_invoiced, start, end, 'amount', 'status', false)
                        break

                    case 'Paid':
                        array = _formatData(this.state.expenses, consts.expense_status_invoiced, start, end, 'amount', 'status', false)
                        break
                }

                break

            case 'Payments':
                switch (radioSelected) {
                    case 'Active':
                        array = _formatData(this.state.payments, consts.payment_status_pending, start, end, 'amount', 'status', false)
                        break
                    case 'Refunded':
                        array = _formatData(this.state.payments, consts.payment_status_refunded, start, end, 'amount', 'status', false)
                        break
                    case 'Completed':
                        array = _formatData(this.state.payments, consts.payment_status_completed, start, end, 'amount', 'status', false)
                        break
                }
                break

            case 'Quotes':
                switch (radioSelected) {
                    case 'Active':
                        array = _formatData(this.state.quotes, consts.quote_status_draft, start, end, 'amount', 'status', false)
                        break

                    case 'Approved':
                        array = _formatData(this.state.quotes, consts.quote_status_approved, start, end, 'amount', 'status', false)
                        break

                    case 'Unapproved':
                        array = _formatData(this.state.quotes, consts.quote_status_sent, start, end, 'amount', 'status', false)
                        break

                    case 'Overdue': {
                        const filterQuotesByExpiration = _filterOverdue(this.state.quotes)

                        array = _formatData(filterQuotesByExpiration, consts.quote_status_sent, start, end, 'total', 'status_id')
                    }

                        break
                }
                break

            case 'Credits':
                switch (radioSelected) {
                    case 'Active':
                        array = _formatData(this.state.credits, consts.credit_status_draft, start, end, 'amount', 'status', false)
                        break

                    case 'Completed':
                        array = _formatData(this.state.credits, consts.credit_status_applied, start, end, 'amount', 'status', false)
                        break

                    case 'Sent':
                        array = _formatData(this.state.credits, consts.credit_status_sent, start, end, 'amount', 'status', false)
                        break

                    case 'Overdue': {
                        const filterCreditsByExpiration = _filterOverdue(this.state.credits)

                        array = _formatData(filterCreditsByExpiration, consts.credit_status_sent, start, end, 'total', 'status_id')
                        // array = _formatData(this.state.credits, 2, start, end, 'amount', 'status', false)
                    }

                        break
                }
                break

            case 'Orders':
                switch (radioSelected) {
                    case 'Draft':
                        array = _formatData(this.state.orders, consts.order_status_draft, start, end, 'amount', 'status', false)
                        break

                    case 'Held':
                        array = _formatData(this.state.orders, consts.order_status_held, start, end, 'amount', 'status', false)
                        break

                    case 'Backordered':
                        array = _formatData(this.state.orders, consts.order_status_backorder, start, end, 'amount', 'status', false)
                        break

                    case 'Cancelled':
                        array = _formatData(this.state.orders, consts.order_status_cancelled, start, end, 'amount', 'status', false)
                        break

                    case 'Sent':
                        array = _formatData(this.state.orders, consts.order_status_sent, start, end, 'amount', 'status', false)
                        break

                    case 'Completed':
                        array = _formatData(this.state.orders, consts.order_status_complete, start, end, 'amount', 'status', false)
                        break

                    case 'Overdue': {
                        const filterOrdersByExpiration = _filterOverdue(this.state.orders)

                        array = _formatData(filterOrdersByExpiration, consts.order_status_draft, start, end, 'total', 'status_id')
                        // array = _formatData(this.state.orders, 3, start, end, 'amount', 'status', false)
                    }

                        break
                }
        }

        return array
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
                alert('here 1 ' + tab)
                this.setState({ activeTab2: tab }, () => {
                    if (this.state.activeTab !== '1') {
                        return
                    }

                    const index = parseInt(this.state.activeTab2)
                    const offset = document.documentElement.scrollTop
                    const offsetIndex = Math.floor((offset + 120) / 622)
                    const selected_tab = offsetIndex + 2

                    if (index !== offsetIndex) {
                        document.documentElement.scrollTop = (index * 622) + 1 // 1145
                    }
                })
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
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        const dashboardRightStyle = !this.state.isMobile ? {
            position: 'fixed',
            right: '0px',
            width: '540px',
            top: '44px'
        } : {}

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

        let leads = ''

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
                                            className={this.state.activeTab2 === '1' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggleTab2('1')
                                            }}>
                                            {translations.invoices}
                                        </NavLink>
                                    </NavItem>
                                    }

                                    {this.state.isMobile && this.state.isMobile && modules && modules.orders &&
                                    <NavItem>
                                        <NavLink
                                            className={this.state.activeTab2 === '2' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggleTab2('2')
                                            }}>
                                            {translations.orders}
                                        </NavLink>
                                    </NavItem>
                                    }

                                    {this.state.isMobile && modules && modules.payments &&
                                    <NavItem>
                                        <NavLink
                                            className={this.state.activeTab2 === '3' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggleTab2('3')
                                            }}>
                                            {translations.payments}
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

                                    {this.state.isMobile && modules && modules.credits &&
                                    <NavItem>
                                        <NavLink
                                            className={this.state.activeTab2 === '5' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggleTab2('5')
                                            }}>
                                            {translations.credits}
                                        </NavLink>
                                    </NavItem>
                                    }

                                    {this.state.isMobile && modules && modules.tasks &&
                                    <NavItem>
                                        <NavLink
                                            className={this.state.activeTab2 === '6' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggleTab2('6')
                                            }}>
                                            {translations.tasks}
                                        </NavLink>
                                    </NavItem>
                                    }

                                    {this.state.isMobile && modules && modules.expenses &&
                                    <NavItem>
                                        <NavLink
                                            className={this.state.activeTab2 === '7' ? 'active' : ''}
                                            onClick={() => {
                                                this.toggleTab2('7')
                                            }}>
                                            {translations.expenses}
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
                            <Overview sources={this.state.sources} leadsToday={this.state.leadsToday}
                                newDeals={this.state.newDeals} newCustomers={this.state.newCustomers}
                                totalBudget={this.state.totalBudget} onChartReady={this.onChartReady}
                                toggleModal={this.toggleModal}/>

                        </TabPane>

                        <TabPane tabId="2">
                            <MessageContainer/>
                        </TabPane>
                    </TabContent>
                </Col>

                <Col style={dashboardRightStyle} className={`dashboard-tabs-right ${dashboard_minimized ? 'd-none' : ''}`}
                    lg={5}>

                    <Card className="dashboard-border" style={{ maxHeight: '700px' }}>
                        <CardBody>
                            <SidebarScaffold customers={this.state.customers} viewId={this.state.viewId}
                                addUserToState={this.addUserToState}
                                toggleViewedEntity={this.toggleViewedEntity} isMobile={this.state.isMobile}
                                invoices={this.state.invoices}
                                orders={this.state.orders} tasks={this.state.tasks}
                                payments={this.state.payments}
                                expenses={this.state.expenses} quotes={this.state.quotes}
                                credits={this.state.credits} radioSelected={this.state.radioSelected}
                                activeTab2={this.state.activeTab2} toggleTab2={this.toggleTab2}/>
                        </CardBody>
                    </Card>
                </Col>
            </Row>

            <Row className={this.state.activeTab === '1' ? 'd-block z-index-high' : 'd-none'}>
                <Col sm={7}>
                    <DashboardPanels doExport={this.doExport} start_date={this.state.start_date}
                        end_date={this.state.end_date}
                        dashboard_filters={this.state.dashboard_filters} invoices={this.state.invoices}
                        orders={this.state.orders} tasks={this.state.tasks} payments={this.state.payments}
                        expenses={this.state.expenses} quotes={this.state.quotes}
                        credits={this.state.credits} radioSelected={this.state.radioSelected}/>
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
                <ModalBody className={theme}>
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
