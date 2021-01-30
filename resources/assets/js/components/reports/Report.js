import React from 'react'
import { translations } from '../utils/_translations'
import Snackbar from '@material-ui/core/Snackbar'
import { Alert, CustomInput, Form, FormGroup, Label } from 'reactstrap'
import queryString from 'query-string'
import FormatMoney from '../common/FormatMoney'
import moment from 'moment'
import axios from 'axios'
import { icons } from '../utils/_icons'

export default class Report extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
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
            perPage,
            totalPages: 1,
            totalRows: 0,
            orderByField: defaultOrderByField,
            orderByDirection: defaultOrderByDirection,
            disallowOrderingBy: [],
        }

        this.getReport = this.getReport.bind(this)
        this.buildSelectList = this.buildSelectList.bind(this)
        this.handleInputChanges = this.handleInputChanges.bind(this)
        this.changePage = this.changePage.bind(this);
        this.changeOrder = this.changeOrder.bind(this);
        this.changePerPage = this.changePerPage.bind(this);
    }

    componentDidMount() {
        this.loadPage(1);
    }

    componentDidUpdate(prevProps) {
        if (JSON.stringify(prevProps.params) !== JSON.stringify(this.props.params)) {
            this.loadPage(1);
        }
    }

    get loading() {
        const { loading: state } = this.state;
        const { loading: prop } = this.props;

        return state || prop;
    }

    get disallowOrderingBy() {
        const { disallowOrderingBy: state } = this.state;
        const { disallowOrderingBy: prop } = this.props;

        return [
            ...state,
            ...prop
        ];
    }

    renderFooter(args) {
        const { meta } = this.state;
        const { footer } = this.props;

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

        axios.post('/api/reports', {report_type: this.state.report_type, group_by: this.state.group_by})
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
            this.getReport()
        })
    }

    buildSelectList (header) {
        let columns = null
        if (!this.state.report_type.length) {
            columns = <option value="">Loading...</option>
        } else {
            columns = this.state.columns[this.state.report_type].map((column, index) => {
                const formatted_column = column.replace(/ /g, '_').toLowerCase()
                const value = translations[formatted_column] ? translations[formatted_column] : column
                return <option key={index} value={column}>{value}</option>
            })
        }

        return (
            <select className="form-control form-control-inline" onChange={this.handleInputChanges}
                name={header} id={header}>
                <option value="">{translations.select_option}</option>
                {columns}
            </select>
        )
    }

    handleInputChanges (e) {
        this.setState({ group_by: e.target.value }, () => {
           this.getReport()
        })
    }

    reload(page = 1) {
        this.loadPage(page);
    }

    loadPage(page) {
        const {perPage, orderByField, orderByDirection, report_type, group_by} = this.state;
        const {onLoad, onError, params, axios} = this.props;

        this.setState(
            { loading: true },
            () => {
                axios.get(this.props.apiUrl, {

                    params: { ...params, page, perPage, orderByField, orderByDirection, report_type, group_by }

                }).then(({ data: response }) => {

                    const { data: rows, total, current_page, last_page } = response.data;
                    let disallow_ordering_by = [];
                    let meta = {}

                    if (response.meta) {
                        ({ disallow_ordering_by, ...meta } = response.meta);
                    }

                    const newState = {
                        rows,
                        meta,
                        disallowOrderingBy: disallow_ordering_by,
                        totalRows: total,
                        currentPage: current_page,
                        totalPages:last_page,
                        loading: false
                    };

                    this.setState(newState);
                    onLoad(newState);

                }).catch((e) => {

                    this.setState({ loading: false });
                    onError(e);

                });
            }
        );
    }

    changePage(page) {
        this.loadPage(page)
    }

    changePerPage(limit) {
        this.setState(
            { perPage: limit },
            this.reload
        )
    }

    changeOrder(field, direction) {
        this.setState({ orderByField: field, orderByDirection: direction }, () => {

            this.loadPage(1);

        });
    }

    render () {
        const {
            message,
            loading,
            show_success,
            error,
            error_message,
            success_message,
            rows,
            currency_report
        } = this.state

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

                                            <button className="btn btn-success ml-2"
                                                disabled={!this.state.report_type}
                                                onClick={this.export}
                                            >
                                                {translations.export}
                                            </button>

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

                        {!!preview &&
                        preview
                        }

                        {!!errors.length &&
                        errors
                        }

                        {!!reports.length &&
                        <div className="card mt-2">
                            <div
                                className="card-header">{translations[this.state.report_type]}>
                            </div>
                            <div className="card-body">
                                <div className="card-body">
                                    <DynamicDataTable
                                        rows={rows}
                                        totalRows={totalRows}
                                        currentPage={currentPage}
                                        perPage={this.state.perPage}
                                        totalPages={totalPages}
                                        orderByField={orderByField}
                                        orderByDirection={orderByDirection}
                                        loading={this.loading}
                                        changePage={this.changePage}
                                        changeOrder={this.changeOrder}
                                        changePerPage={this.changePerPage}
                                        disallowOrderingBy={this.disallowOrderingBy}
                                        footer={footer ? this.renderFooter : undefined}
                                        {...props}
                                    />

                              
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
            </React.Fragment>

        )
    }
}
