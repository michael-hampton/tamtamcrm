import React from 'react'
import { translations } from '../utils/_translations'
import Snackbar from '@material-ui/core/Snackbar'
import { Alert, Button, Card, CardBody, Collapse, FormGroup, Label, Modal, ModalBody, ModalFooter } from 'reactstrap'
import axios from 'axios'
import { icons } from '../utils/_icons'
import DynamicDataTable from './DynamicDataTable'
import { download, generateCsv } from './_utilities'
import Datepicker from '../common/Datepicker'

export default class Report extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            chart_type: '',
            group_by_frequency: '',
            width: window.innerWidth,
            manual_date_field: '',
            show: false,
            date_format: '',
            start_date: '',
            end_date: '',
            report_type: 'invoice',
            group_by: '',
            rows: [],
            filtered_value: {},
            cached_data: [],
            currency_report: [],
            message: '',
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.expenses_imported_successfully,
            loading: false,
            currentPage: 1,
            perPage: 25,
            totalPages: 1,
            totalRows: 0,
            orderByField: 'created_at',
            orderByDirection: 'desc',
            disallowOrderingBy: [],
            checkedItems: new Map(),
            groups: {
                income: [{ field: 'company_id', label: 'company' }, { field: 'customer_id', label: 'customer' }],
                customer: [{ field: 'currency_id', label: 'currency' }, { field: 'country_id', label: 'country' }],
                invoice: [{ field: 'customer_id', label: 'customer' }, {
                    field: 'date',
                    label: 'date'
                }, { field: 'due_date', label: 'due_date' }, { field: 'invoices.status_id', label: 'status' }],
                credit: [{ field: 'customer_id', label: 'customer' }, {
                    field: 'date',
                    label: 'date'
                }, { field: 'due_date', label: 'due_date' }, { field: 'credits.status_id', label: 'status' }],
                quote: [{ field: 'customer_id', label: 'customer' }, {
                    field: 'date',
                    label: 'date'
                }, { field: 'due_date', label: 'due_date' }, { field: 'quotes.status_id', label: 'status' }],
                purchase_order: [{ field: 'company_id', label: 'company' }, {
                    field: 'date',
                    label: 'date'
                }, { field: 'due_date', label: 'due_date' }, { field: 'purchase_orders.status_id', label: 'status' }],
                order: [{ field: 'customer_id', label: 'customer' }, {
                    field: 'date',
                    label: 'date'
                }, { field: 'due_date', label: 'due_date' }, { field: 'product_task.status_id', label: 'status' }],
                lead: [{ field: 'source_type', label: 'source_type' }, {
                    field: 'task_status_id',
                    label: 'status'
                }, { field: 'assigned_to', label: 'assigned_user' }],
                deal: [{ field: 'deals.customer_id', label: 'customer' }, {
                    field: 'source_type',
                    label: 'source_type'
                }, { field: 'task_status_id', label: 'status' }, {
                    field: 'assigned_to',
                    label: 'assigned_user'
                }, { field: 'project_id', label: 'project' }],
                task: [{ field: 'tasks.customer_id', label: 'customer' }, {
                    field: 'task_status_id',
                    label: 'status'
                }, { field: 'assigned_to', label: 'assigned_user' }, { field: 'project_id', label: 'project' }],
                expense: [{ field: 'customer_id', label: 'customer' }, { field: 'date', label: 'date' }, {
                    field: 'expenses.company_id',
                    label: 'company'
                }, { field: 'expense_category_id', label: 'category' }, {
                    field: 'expenses.status_id',
                    label: 'status'
                }],
                payment: [{ field: 'customer_id', label: 'customer' }, {
                    field: 'date',
                    label: 'date'
                }, { field: 'status_id', label: 'status' }],
                line_item: [{ field: 'product', label: 'product' }, { field: 'invoice', label: 'invoice' }],
                tax_rate: [{ field: 'number', label: 'number' }, { field: 'tax_name', label: 'name' }],
                document: [{ field: 'files.type', label: 'file_type' }, { field: 'files.fileable_type', label: 'record_type' }]
            },
            charts: {
                income: ['amount'],
                customer: ['balance', 'amount_paid'],
                invoice: ['total', 'balance'],
                credit: ['total', 'balance'],
                quote: ['total', 'balance'],
                purchase_order: ['total', 'balance'],
                order: ['total', 'balance'],
                lead: ['source_type'],
                deal: ['source_type'],
                task: ['duration'],
                expense: ['amount'],
                payment: ['amount'],
                line_item: [],
                tax_rate: [],
                document: []
            },
            ignored_columns: {
                income: ['address_1', 'address_2', 'shipping_address1', 'shipping_address2', 'town', 'city', 'company_country'],
                document: ['size', 'width', 'height']
            },
            all_columns: [],
            apiUrl: '/api/reports',
            date_container_open: false
        }

        this.buildSelectList = this.buildSelectList.bind(this)
        this.buildFrequencyList = this.buildFrequencyList.bind(this)
        this.handleInputChanges = this.handleInputChanges.bind(this)
        this.changePage = this.changePage.bind(this)
        this.changeOrder = this.changeOrder.bind(this)
        this.changePerPage = this.changePerPage.bind(this)
        this.handleColumnChange = this.handleColumnChange.bind(this)
        this.export = this.export.bind(this)
        this.toggle = this.toggle.bind(this)
        this.filterDates = this.filterDates.bind(this)
        this.handleWindowSizeChange = this.handleWindowSizeChange.bind(this)
    }

    componentWillMount () {
        window.addEventListener('resize', this.handleWindowSizeChange)
    }

    componentDidMount () {
        this.loadPage(1)
    }

    componentDidUpdate (prevProps) {
        if (JSON.stringify(prevProps.params) !== JSON.stringify(this.props.params)) {
            this.loadPage(1)
        }
    }

    componentWillUnmount () {
        window.removeEventListener('resize', this.handleWindowSizeChange)
    }

    handleWindowSizeChange () {
        this.setState({ width: window.innerWidth })
    }

    get loading () {
        const { loading: state } = this.state
        const { loading: prop } = this.props

        return state || prop
    }

    handleColumnChange (e) {
        const item = e.target.name
        const isChecked = e.target.checked
        this.setState(prevState => ({ checkedItems: prevState.checkedItems.set(item, isChecked) }), () => {
            console.log('checked items', this.state.checkedItems)
        })
    }

    get disallowOrderingBy () {
        const { disallowOrderingBy: state } = this.state
        const { disallowOrderingBy: prop } = this.props

        return [
            ...state,
            ...prop
        ]
    }

    renderFooter (args) {
        const { meta } = this.state
        const { footer } = this.props

        if (typeof footer === 'function') {
            return footer({ meta, ...args })
        }

        return footer
    }

    handleColumnFilter (value, column) {
        const filtered_value = this.state.filtered_value

        if (value.trim() === '') {
            filtered_value[column] = ''
            this.setState({ filtered_value: filtered_value, rows: this.state.cached_data, cached_data: [] })
            return true
        }

        const cached_data = !this.state.cached_data.length ? this.state.rows : this.state.cached_data
        const rows = cached_data.filter(row => row[column].toString().toLowerCase().trim().includes(value.toLowerCase().trim()))

        if (!rows.length) {
            alert('No search results')
            return false
        }

        filtered_value[column] = value

        this.setState({ filtered_value: filtered_value, rows: rows || [], cached_data: cached_data })
    }

    clearSearch (column) {
        const filtered_value = this.state.filtered_value
        filtered_value[column] = ''
        const rows = this.state.cached_data.length ? this.state.cached_data : this.state.rows
        this.setState({ filtered_value: filtered_value, cached_data: [], rows: rows })
    }

    setFilterOpen (isOpen) {
        this.setState({ isOpen: isOpen })
    }

    setError (message = null) {
        this.setState({ error: true, error_message: message === null ? translations.unexpected_error : message })
    }

    setSuccess (message = null) {
        this.setState({
            show_success: true,
            success_message: message === null ? translations.success_message : message
        })
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    changeImportType (e) {
        this.setState({
            [e.target.name]: e.target.value,
            group_by: '',
            date_format: '',
            group_by_frequency: ''
        }, () => {
            this.reload()
        })
    }

    buildFrequencyList () {
        return (
            <select className="form-control w-100" onChange={(e) => {
                this.setState({ group_by_frequency: e.target.value }, () => {
                    this.reload()
                })
            }}
            name="group_by_frequency" id="group_by_frequency">
                <option value="">{translations.select_option}</option>
                <option value="year">{translations.year}</option>
                <option value="month">{translations.month}</option>
                <option value="day">{translations.day}</option>
            </select>
        )
    }

    buildSelectList () {
        let columns = null
        if (!this.state.report_type.length) {
            columns = <option value="">Loading...</option>
        } else {
            columns = this.state.groups[this.state.report_type].map((column, index) => {
                const formatted_column = column.label.replace(/ /g, '_').toLowerCase()
                const value = translations[formatted_column] ? translations[formatted_column] : column.label
                return <option key={index} value={column.field}>{value}</option>
            })
        }

        return (
            <select className="form-control w-100" onChange={this.handleInputChanges}
                name="group_by" id="group_by" value={this.state.group_by}>
                <option value="">{translations.select_option}</option>
                {columns}
            </select>
        )
    }

    buildChartOptions () {
        let columns = null
        if (!this.state.report_type.length) {
            columns = <option value="">Loading...</option>
        } else {
            columns = this.state.charts[this.state.report_type].map((column, index) => {
                const formatted_column = column.label.replace(/ /g, '_').toLowerCase()
                const value = translations[formatted_column] ? translations[formatted_column] : column.label
                return <option key={index} value={column.field}>{value}</option>
            })
        }

        return (
            <select className="form-control w-100" onChange={(e) => {
                this.setState({chart_type: e.target.value }, () => {

                })
                }}
                name="chart_type" id="chart_type" value={this.state.chart_type}>
                <option value="">{translations.select_option}</option>
                {columns}
            </select>
        )
    }

    handleInputChanges (e) {
        this.setState({ group_by: e.target.value, group_by_frequency: '' }, () => {
            if ((this.state.group_by === 'date' || this.state.group_by === 'due_date') && !this.state.group_by_frequency.length) {
                return true
            }

            this.reload()
        })
    }

    reload (page = 1) {
        this.loadPage(page)
    }

    loadPage (page) {
        const { perPage, orderByField, orderByDirection, report_type, group_by, start_date, end_date, date_format, manual_date_field, group_by_frequency } = this.state

        this.setState(
            { loading: true },
            () => {
                axios.get(this.state.apiUrl, {

                    params: {
                        page,
                        perPage,
                        orderByField,
                        orderByDirection,
                        report_type,
                        group_by,
                        start_date,
                        end_date,
                        date_format,
                        manual_date_field,
                        group_by_frequency
                    }

                }).then(({ data: response }) => {
                    const { report, currency_report } = response
                    let disallow_ordering_by = []
                    let meta = {}

                    if (response.meta) {
                        ({ disallow_ordering_by, ...meta } = response.meta)
                    }

                    var map = new Map()

                    if (report.data.length) {
                        Object.keys(report.data[0]).filter((column) => {
                            return !this.state.ignored_columns[report_type] || !this.state.ignored_columns[report_type].includes(column)
                        }).map((column, index) => {
                            map.set(column, true)
                        })
                        console.log('new map', map)
                    }

                    const newState = {
                        checkedItems: map,
                        all_columns: report.data.length ? Object.keys(report.data[0]) : [],
                        rows: report.data,
                        currency_report: currency_report || [],
                        meta,
                        disallowOrderingBy: disallow_ordering_by,
                        totalRows: report.total,
                        currentPage: report.current_page,
                        totalPages: report.last_page,
                        loading: false
                    }

                    this.setState(newState)
                    // onLoad(newState)
                }).catch((e) => {
                    this.setState({ loading: false })
                    this.setError(e)
                })
            }
        )
    }

    changePage (page) {
        this.loadPage(page)
    }

    setDateFormat (date_format, column = null) {
        if (date_format === 'manual') {
            this.setState({ manual_date_field: column })
            this.toggleDateContainer()
            return false
        }

        this.setState({ date_format: date_format, manual_date_field: '' }, () => {
            this.toggleDateContainer()
            this.loadPage(1)
        })
    }

    changePerPage (limit) {
        this.setState(
            { perPage: limit },
            this.reload
        )
    }

    changeOrder (field, direction) {
        this.setState({ orderByField: field, orderByDirection: direction }, () => {
            this.loadPage(1)
        })
    }

    isLast () {
        // because for empty records page_number will still be 1
        if (this.state.totalPages === 0) {
            return true
        }

        if (this.state.currentPage === this.state.totalPages) {
            return true
        }

        return false
    }

    isFirst () {
        if (this.state.currentPage === 1) {
            return true
        }

        return false
    }

    firstPage (e) {
        e.preventDefault()
        if (this.isFirst()) return
        this.loadPage(1)
    }

    lastPage (e) {
        e.preventDefault()
        if (this.isLast()) return
        this.loadPage(e, this.state.totalPages)
    }

    previousPage (e) {
        e.preventDefault()
        if (this.isFirst()) return false
        this.loadPage(this.state.currentPage - 1)
    }

    nextPage (e) {
        e.preventDefault()
        if (this.isLast()) return
        this.loadPage(parseInt(this.state.currentPage) + 1)
    }

    export () {
        const content = generateCsv(this.state.rows)
        download({ content: content, name: this.state.report_type + '.csv', type: 'text//\\csv' })
        console.log('content', content)
    }

    toggle () {
        this.setState({
            show: !this.state.show
        })
    }

    toggleDateContainer () {
        this.setState({
            date_container_open: !this.state.date_container_open
        })
    }

    filterDates (e) {
        this.setState({ [e.target.name]: e.target.value }, () => {
            if (this.state.start_date.length && this.state.end_date.length) {
                this.loadPage(1)
            }
        })
    }

    isDateField (field) {
        return ['due_date', 'date'].includes(field)
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        const { rows, currency_report, message, success_message, error_message, error, show_success, totalRows, currentPage, totalPages, orderByField, orderByDirection, disallowOrderingBy, footer, perPage } = this.state

        const all_columns = this.state.all_columns.length ? this.state.all_columns.map((column, index) => {
            const formatted_column = column.replace(/ /g, '_').toLowerCase()
            const value = translations[formatted_column] ? translations[formatted_column] : column
            return <div key={`checkbox-container-${index}`} className="form-check">
                <input className="form-check-input" name={column} type="checkbox" onChange={this.handleColumnChange}
                    id={`label-${index}`} checked={this.state.checkedItems.get(column)}/>
                <label className="form-check-label" htmlFor={`label-${index}`}>
                    {value}
                </label>
            </div>
        }) : null

        const is_mobile = this.state.width <= 768

        const report_selector = <select name="report_type" id="report_type" className="form-control"
            value={this.state.report_type}
            onChange={this.changeImportType.bind(this)}>
            <option value="">{translations.select_option}</option>
            <option value="invoice">{translations.invoice}</option>
            <option value="customer">{translations.customer}</option>
            <option value="lead">{translations.lead}</option>
            <option value="deal">{translations.deal}</option>
            <option value="task">{translations.task}</option>
            <option value="expense">{translations.expense}</option>
            <option value="order">{translations.order}</option>
            <option value="credit">{translations.credit}</option>
            <option value="quote">{translations.quote}</option>
            <option value="purchase_order">{translations.purchase_order}</option>
            <option value="payment">{translations.payment}</option>
            <option value="line_item">{translations.line_items}</option>
            <option value="tax_rate">{translations.tax_rate}</option>
            <option value="income">{translations.income}</option>
            <option value="document">{translations.document}</option>
        </select>

        const filters = is_mobile ? <div className="row">
            <div className="col-12">
                <div className="card mt-2">
                    <div className="card-body">
                        <div>
                            <div className="row">
                                <div className="col-md-3 col-sm-12 mt-2">
                                    {report_selector}
                                </div>

                                <div className="col-md-3 col-sm-12 mt-2">
                                    {this.buildSelectList()}

                                    {this.state.group_by.length && 
                                    this.buildChartOptions()
                                    }
                                </div>

                                {!!this.isDateField(this.state.group_by) &&
                                    <div className="col-md-3 col-sm-12 mt-2">
                                        {this.buildFrequencyList()}
                                    </div>
                                }

                                <Collapse isOpen={this.state.date_container_open}>
                                    <FormGroup>
                                        <Label for="due_date">{translations.start_date}</Label>
                                        <Datepicker name="start_date" date={this.state.start_date}
                                            handleInput={this.filterDates}/>
                                    </FormGroup>

                                    <FormGroup>
                                        <Label for="due_date">{translations.due_date}(*):</Label>
                                        <Datepicker name="end_date" date={this.state.end_date}
                                            handleInput={this.filterDates}/>
                                    </FormGroup>
                                </Collapse>

                                <div className="d-flex align-items-center">
                                    <Button color="primary" className="mr-2"
                                        disabled={!this.state.report_type}
                                        onClick={this.export}
                                    >
                                        {translations.export}
                                    </Button>

                                    <Button color="primary" onClick={(e) => {
                                        this.setState({ show: !this.state.show })
                                    }}>Columns
                                    </Button>
                                </div>

                            </div>
                        </div>

                        {!!message &&
                            <div className="alert alert-danger mt-2" role="alert">
                                {message}
                            </div>
                        }
                    </div>
                </div>
            </div>
        </div>
            : <div className="row mt-5">
                <div className="col-12 p-0">
                    <div className="col-md-4">
                        <div className="card mt-2">
                            <div className="card-body">
                                <label>{translations.report_type}</label>
                                {report_selector}
                            </div>
                        </div>
                    </div>

                    <div className="col-md-4">
                        <div className="card mt-2">
                            <div className="card-body">
                                <FormGroup>
                                    <label>{translations.group}</label>
                                    {this.buildSelectList()}
                                </FormGroup>

                                {this.state.group_by.length && 
                                    <FormGroup>
                                    <label>{translations.chart}</label>
                                    {this.buildChartOptions()}
                                </FormGroup>
                                }

                                {!!this.isDateField(this.state.group_by) &&
                                <FormGroup>
                                    <Label>{translations.frequency}</Label>
                                    {this.buildFrequencyList()}
                                </FormGroup>

                                }
                            </div>
                        </div>
                    </div>

                    <div className="col-md-4">
                        <div className="card mt-2">
                            <div className="card-body">
                                {!this.state.date_container_open &&
                                <span>charts</span>
                                }

                                {!!this.state.date_container_open &&
                                <React.Fragment>
                                    <label className="d-flex items justify-content-between">
                                        {translations.filter_date}
                                        <a onClick={this.toggleDateContainer.bind(this)}>{translations.close}</a>
                                    </label>
                                    <Collapse isOpen={this.state.date_container_open}>
                                        <FormGroup>
                                            <Label for="due_date">{translations.start_date}</Label>
                                            <Datepicker name="start_date" date={this.state.start_date}
                                                handleInput={this.filterDates}/>
                                        </FormGroup>

                                        <FormGroup>
                                            <Label for="due_date">{translations.due_date}(*):</Label>
                                            <Datepicker name="end_date" date={this.state.end_date}
                                                handleInput={this.filterDates}/>
                                        </FormGroup>
                                    </Collapse>
                                </React.Fragment>

                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        return (
            <React.Fragment>

                {!is_mobile &&
                <div className="topbar">
                    <Card className="m-0">
                        <CardBody className="p-0">
                            <div className="d-flex justify-content-between align-items-center">
                                <div className="d-inline-flex">

                                    <h4 className="pl-3 pt-2">
                                        {translations.reports}
                                    </h4>
                                </div>

                                <span>
                                    <Button color="link" className="mr-2 pull-right"
                                        disabled={!this.state.report_type}
                                        onClick={this.export}
                                    >
                                        {translations.export}
                                    </Button>

                                    <Button color="link" className="pull-right" onClick={(e) => {
                                        this.setState({ show: !this.state.show })
                                    }}>Columns
                                    </Button>
                                </span>

                            </div>

                        </CardBody>
                    </Card>
                </div>

                }

                {filters}

                <div className="row">
                    <div className="col-12">

                        {!!currency_report.length &&
                        <div className="card mt-2">
                            <div
                                className="card-header">{translations.currencies}
                            </div>
                            <div className="card-body">
                                <div className="card-body">
                                    <DynamicDataTable
                                        rows={currency_report}
                                        totalRows={totalRows}
                                        currentPage={currentPage}
                                        perPage={this.state.perPage}
                                        totalPages={totalPages}
                                        orderByField={orderByField}
                                        orderByDirection={orderByDirection}
                                        loading={this.loading}
                                        fieldsToExclude={[]}
                                        // changePage={this.changePage}
                                        // changeOrder={this.changeOrder}
                                        // changePerPage={this.changePerPage}
                                        disallowOrderingBy={[]}
                                        footer={footer ? this.renderFooter : undefined}
                                        hoverable={true}
                                    />

                                </div>

                            </div>
                        </div>
                        }
                    </div>
                </div>

                <div className="row">
                    <div className="col-12">

                        <div className="card mt-2">
                            <div
                                className="card-header">{translations[this.state.report_type]}
                            </div>
                            <div className="card-body">
                                <div className="card-body">
                                    <DynamicDataTable
                                        perPageOptions={[10, this.state.perPage, 50]}
                                        fieldsToExclude={this.state.checkedItems}
                                        rows={rows}
                                        totalRows={totalRows}
                                        currentPage={currentPage}
                                        perPage={perPage}
                                        totalPages={totalPages}
                                        orderByField={orderByField}
                                        orderByDirection={orderByDirection}
                                        loading={this.loading}
                                        changePage={this.changePage}
                                        changeOrder={this.changeOrder}
                                        changePerPage={this.changePerPage}
                                        disallowOrderingBy={disallowOrderingBy}
                                        orderByAscIcon={<i className={`fa ${icons.down} mr-2`}/>}
                                        orderByDescIcon={<i className={`fa ${icons.up} mr-2`}/>}
                                        footer={footer ? this.renderFooter : undefined}
                                        prependOrderByIcon={true}
                                        hoverable={true}
                                        filterable={true}
                                        handleColumnFilter={this.handleColumnFilter.bind(this)}
                                        clearSearch={this.clearSearch.bind(this)}
                                        search_filters={this.state.filtered_value}
                                        setDateFormat={this.setDateFormat.bind(this)}
                                        date_format={this.state.manual_date_field.length ? 'manual' : this.state.date_format}
                                    />

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {error &&
                <Snackbar open={error} autoHideDuration={3000} onClose={this.handleClose.bind(this)}>
                    <Alert severity="danger">
                        {error_message}
                    </Alert>
                </Snackbar>
                }

                {show_success &&
                <Snackbar open={show_success} autoHideDuration={3000} onClose={this.handleClose.bind(this)}>
                    <Alert severity="success">
                        {success_message}
                    </Alert>
                </Snackbar>
                }

                <Modal isOpen={this.state.show} toggle={this.toggle} className={this.props.className}>
                    <ModalBody className={theme}>

                        {all_columns && all_columns}

                        <ModalFooter>
                            <a onClick={this.toggle} color="primary">{translations.done}</a>
                        </ModalFooter>
                    </ModalBody>
                </Modal>

            </React.Fragment>

        )
    }
}
