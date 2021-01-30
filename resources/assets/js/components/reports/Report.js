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
            reports: [],
            currency_report: [],
            message: '',
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.expenses_imported_successfully,
            loading: false
        }

        this.getReport = this.getReport.bind(this)
        this.buildSelectList = this.buildSelectList.bind(this)
        this.handleInputChanges = this.handleInputChanges.bind(this)
    }

    componentDidMount () {
       this.getReport()
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

    render () {
        const {
            message,
            loading,
            show_success,
            error,
            error_message,
            success_message
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

                        {!!fileInfos.length &&
                        <div className="card mt-2">
                            <div
                                className="card-header">{translations[this.state.report_type]}>
                            </div>
                            <div className="card-body">
                                <div className="card-body">
                                <ImportPreview
                                    fieldsToExclude={['invitations', 'uniqueId', 'type']}
                                    dataItemManipulator={(field, value) => {
                                        return value
                                    }}
                                    disabledCheckboxes={[]}
                                    renderMasterCheckbox={false}
                                    rows={fileInfos}
                                    totalRows={fileInfos.length}
                                    currentPage={1}
                                    perPage={50}
                                    totalPages={1}
                                    loading={loading}
                                    noDataMessage={'No transactions found'}
                                    allowOrderingBy={['date', 'name', 'amount', 'id']}
                                    columnWidths={[]}
                                    disallowOrderingBy={['userInitiatedDate', 'uniqueId']}
                                    renderCheckboxes={false}
                                    buttons={[]}
                                    actions={[]}
                                    // changePage={this.changePage}
                                    // changeOrder={this.changeOrder}
                                    // changePerPage={this.changePerPage}
                                    // disallowOrderingBy={this.disallowOrderingBy}
                                    // footer={footer ? this.renderFooter : undefined}
                                    // {...props}
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
