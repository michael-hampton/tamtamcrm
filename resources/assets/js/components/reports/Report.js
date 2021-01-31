import React from 'react'
import { translations } from '../utils/_translations'
import Snackbar from '@material-ui/core/Snackbar'
import { Alert, Modal, ModalBody, ModalFooter } from 'reactstrap'
import axios from 'axios'
import { icons } from '../utils/_icons'
import DynamicDataTable from './DynamicDataTable'
import { download, generateCsv } from './_utilities'

export default class Report extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            show: false,
            date_format: '',
            report_type: 'invoice',
            group_by: '',
            rows: [],
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
            groups: { invoice: ['customer_id'], credit: ['customer_id'], quote: ['customer_id'], purchase_order: ['company_id'], order: ['customer_id'], lead: ['source_type', 'task_status', 'assigned_to'], deal: ['customer_id' 'source_type', 'task_status', 'assigned_to'], task: ['customer_id', 'task_status', 'project_id', 'assigned_to'], expense: ['customer_id', 'company_id'], payment: ['customer_id']},
            date_fields: { invoice: ['date', 'due_date'], credit: ['date', 'due_date'], quote: ['date', 'due_date'], purchase_order: ['date', 'due_date'], order: ['date', 'due_date'], lead: [], deal: ['due_date'], task: ['due_date'], expense: ['date'], payment: ['date']}
            all_columns: [],
            apiUrl: '/api/reports'
        }

        this.getReport = this.getReport.bind(this)
        this.buildSelectList = this.buildSelectList.bind(this)
        this.handleInputChanges = this.handleInputChanges.bind(this)
        this.changePage = this.changePage.bind(this)
        this.changeOrder = this.changeOrder.bind(this)
        this.changePerPage = this.changePerPage.bind(this)
        this.handleColumnChange = this.handleColumnChange.bind(this)
        this.export = this.export.bind(this)
        this.toggle = this.toggle.bind(this)
    }

    componentDidMount () {
        this.loadPage(1)
    }

    componentDidUpdate (prevProps) {
        if (JSON.stringify(prevProps.params) !== JSON.stringify(this.props.params)) {
            this.loadPage(1)
        }
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

    getReport () {
        if (!this.state.report_type.length) {
            alert('Please select a report type')
            return false
        }

        axios.post('/api/reports', { report_type: this.state.report_type, group_by: this.state.group_by })
            .then((response) => {
                this.setState({
                    reports: response.data.reports,
                    currency_report: response.data.currency_report
                })
            })
            .catch((error) => {
                alert(error)
                this.setState({
                    errors: error.response.data.errors
                })
            })
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
        this.setState({ [e.target.name]: e.target.value }, () => {
            this.reload()
        })
    }

    buildSelectList () {
        let columns = null
        if (!this.state.report_type.length) {
            columns = <option value="">Loading...</option>
        } else {
            columns = this.state.groups[this.state.report_type].map((column, index) => {
                const formatted_column = column.replace(/ /g, '_').toLowerCase()
                const value = translations[formatted_column] ? translations[formatted_column] : column
                return <option key={index} value={column}>{value}</option>
            })
        }

        return (
            <select className="form-control form-control-inline" onChange={this.handleInputChanges}
                name="report_type" id="report_type">
                <option value="">{translations.select_option}</option>
                {columns}
            </select>
        )
    }

    buildDateOptions (header) {
        let columns = null
        if (!this.state.date_format.length || !this.state.date_fields[this.state.report_type].length) {
            columns = <option value="">Loading...</option>
        } else {
            columns = this.state.date_fields[this.state.report_type].map((column, index) => {
                //const formatted_column = column.replace(/ /g, '_').toLowerCase()
                //const value = translations[formatted_column] ? translations[formatted_column] : column
                return <option key={index} value={column}>{value}</option>
                    <option key={index} value={column+'|7'}>{column} 7 days</option>
                    <option key={index} value={column+'|30'}>{column} 30 days</option>
                    <option key={index} value={column+'|last_month'}>{column} Last Month</option>
                    <option key={index} value={column+'|last_year'}>{column} Last Year</option>
            })
        }

        return (
            <select className="form-control form-control-inline" onChange={(e) => {
                this.setState({date_format: e.target.value})
                })}
                name={header} id={header}>
                <option value="">{translations.select_option}</option>
                {columns}
            </select>
        )
    }

    handleInputChanges (e) {
        this.setState({ group_by: e.target.value }, () => {
            this.reload()
        })
    }

    reload (page = 1) {
        this.loadPage(page)
    }

    loadPage (page) {
        const { perPage, orderByField, orderByDirection, report_type, group_by } = this.state

        this.setState(
            { loading: true },
            () => {
                axios.get(this.state.apiUrl, {

                    params: { page, perPage, orderByField, orderByDirection, report_type, group_by }

                }).then(({ data: response }) => {
                    const { report, currency_report } = response
                    let disallow_ordering_by = []
                    let meta = {}

                    if (response.meta) {
                        ({ disallow_ordering_by, ...meta } = response.meta)
                    }

                    var map = new Map()

                    if (report.data.length) {
                        Object.keys(report.data[0]).map((column, index) => {
                            map.set(column, true)
                        })
                        console.log('new map', map)
                    }

                    console.log('currency_report', currency_report)

                    const newState = {
                        checkedItems: map,
                        all_columns: report.data.length ? Object.keys(report.data[0]) : [],
                        rows: report.data,
                        currency_report,
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

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        const { rows, currency_report, message, success_message, error_message, error, show_success, totalRows, currentPage, totalPages, orderByField, orderByDirection, disallowOrderingBy, footer, perPage } = this.state

        const all_columns = this.state.all_columns.length ? this.state.all_columns.map((column, index) => {
            const formatted_column = column.replace(/ /g, '_').toLowerCase()
            const value = translations[formatted_column] ? translations[formatted_column] : column
            return <div key={`checkbox-container-${index}`} className="form-check">
                <input className="form-check-input" name={column} type="checkbox" onChange={this.handleColumnChange} id={`label-${index}`} checked={this.state.checkedItems.get(column)} />
                <label className="form-check-label" htmlFor={`label-${index}`}>
                    {value}
                </label>
            </div>
        }) : null

        return (
            <React.Fragment>
                <div className="row">
                    <div className="col-12">
                        <div className="card mt-2">
                            <div className="card-body">
                                <div>
                                    <div className="row">
                                        <div className="col">
                                            <select name="report_type" id="report_type" className="form-control"
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
                                            </select>
                                        </div>

                                        <div className="col">
                                            {this.buildSelectList()}
                                        </div>

                                        <div className="col">
                                            {this.buildDateOptions()}
                                        </div>

                                        <button className="btn btn-success ml-2"
                                            disabled={!this.state.report_type}
                                            onClick={this.export}
                                        >
                                            {translations.export}
                                        </button>

                                        <a onClick={(e) => {
                                            this.setState({ show: !this.state.show })
                                        }}><i className={`fa ${icons.columns}`} style={{ fontSize: '20px' }}/> </a>
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
                                    />

                                </div>

                            </div>
                        </div>
                        }
                    </div>
                </div>

                <div className="row">
                    <div className="col-12">

                        {!!rows.length &&
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
                                        orderByAscIcon={<i className={`fa ${icons.down}`}/>}
                                        orderByDescIcon={<i className={`fa ${icons.up}`}/>}
                                        footer={footer ? this.renderFooter : undefined}
                                    />

                                </div>

                            </div>
                        </div>
                        }
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
