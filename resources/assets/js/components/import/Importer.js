import React from 'react'
import UploadService from '../bank_accounts/UploadService'
import ImportPreview from '../bank_accounts/ImportPreview'
import { translations } from '../utils/_translations'
import Snackbar from '@material-ui/core/Snackbar'
import { Alert, CustomInput, Form, FormGroup, Label } from 'reactstrap'
import queryString from 'query-string'
import FormatMoney from '../common/FormatMoney'
import moment from 'moment'
import { icons } from '../utils/_icons'

export default class Importer extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            export_data: '',
            import_type: '',
            file_type: '',
            selectedFile: null,
            currentFile: null,
            progress: 0,
            message: '',
            error: '',
            import_errors: {},
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.expenses_imported_successfully,
            fileInfos: [],
            loading: false,
            checked: new Set(),
            bank_id: queryString.parse(this.props.location.search).bank_id || 0,
            headers: [],
            columns: [],
            hash: '',
            mappings: {},
            template: ''
        }

        this.selectFile = this.selectFile.bind(this)
        this.upload = this.upload.bind(this)
        this.export = this.export.bind(this)
        this.preview = this.preview.bind(this)
        this.buildSelectList = this.buildSelectList.bind(this)
        this.handleInputChanges = this.handleInputChanges.bind(this)
    }

    componentDidMount () {
        UploadService.getFiles().then((response) => {
            this.setState({
                fileInfos: response.data
            })
        })
    }

    selectFile (e) {
        console.log('file', e.target.files)
        this.setState({
            selectedFile: e.target.files[0]
        })
    }

    export () {
        if (!this.state.import_type.length) {
            alert('Please select an import type')
            return false
        }

        UploadService.export(this.state.import_type, {}, (event) => {
            this.setState({
                progress: Math.round((100 * event.loaded) / event.total)
            })
        })
            .then((response) => {
                var downloadLink = document.createElement('a')
                var blob = new Blob(['\ufeff', response.data])
                var url = URL.createObjectURL(blob)
                downloadLink.href = url
                downloadLink.download = `${moment().format('Y-M-d')}-${this.state.import_type}.csv`

                document.body.appendChild(downloadLink)
                downloadLink.click()
                document.body.removeChild(downloadLink)
            })
            .catch((e) => {
                alert(e)
                this.setState({
                    progress: 0,
                    message: 'Could not upload the file!',
                    currentFile: undefined
                })
            })
    }

    preview () {
        if (!this.state.import_type.length) {
            alert('Please select an import type')
            return false
        }

        const currentFile = this.state.selectedFile

        this.setState({
            progress: 0,
            currentFile: currentFile
        })

        UploadService.preview(currentFile, `/api/import/preview?file_type=${this.state.file_type}`, this.state.import_type, (event) => {
            this.setState({
                progress: Math.round((100 * event.loaded) / event.total)
            })
        })
            .then((response) => {
                this.setState({
                    error: response.data.length === 0,
                    error_message: response.data.length === 0 ? translations.no_expenses_found : translations.unexpected_error,
                    headers: response.data.headers,
                    columns: response.data.columns,
                    hash: response.data.filename,
                    template: response.data.template,
                    progress: 0,
                    currentFile: undefined
                })
                // return UploadService.getFiles()
            })
            .catch((e) => {
                alert(e)
                this.setState({
                    progress: 0,
                    message: 'Could not upload the file!',
                    currentFile: undefined
                })
            })
    }

    upload () {
        if (!this.state.import_type.length) {
            alert('Please select an import type')
            return false
        }

        let isEmpty = false

        Object.values(this.state.headers).forEach(key => {
            if (!this.state.mappings[key] || !this.state.mappings[key].length) {
                isEmpty = true
            }
        })

        if (isEmpty || !Object.keys(this.state.mappings).length) {
            this.setState({ message: 'All mappings must have a value' })
            return false
        }

        const currentFile = this.state.selectedFile

        this.setState({
            template: '',
            hash: '',
            headers: [],
            columns: [],
            progress: 0,
            currentFile: currentFile
        })

        console.log('mapoings', this.state.mappings)

        UploadService.upload(currentFile, `/api/import?file_type=${this.state.file_type || 'csv'}`, {
            import_type: this.state.import_type,
            mappings: JSON.stringify(this.state.mappings),
            hash: this.state.hash
        }, (event) => {
            this.setState({
                progress: Math.round((100 * event.loaded) / event.total)
            })
        })
            .then((response) => {
                this.setState({
                    error: response.data.length === 0,
                    error_message: response.data.length === 0 ? translations.no_expenses_found : translations.unexpected_error,
                    fileInfos: response.data,
                    progress: 0,
                    currentFile: undefined
                })

                if (response.data.errors && Object.keys(response.data.errors).length) {
                    this.setState({ import_errors: response.data.errors })
                }

                // return UploadService.getFiles()
            })
            .catch((e) => {
                alert(e)
                this.setState({
                    progress: 0,
                    message: 'Could not upload the file!',
                    currentFile: undefined
                })
            })

        this.setState({
            selectedFiles: undefined
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

    save () {
        const data = {
            bank_id: this.state.bank_id,
            checked: Array.from(this.state.checked),
            data: this.state.fileInfos
        }

        UploadService.save(data).then(response => {
            if (!response) {
                this.setState({ error: true, error_message: translations.expense_import_failed })
                return
            }

            this.setState({ show_success: true })
        })
    }

    handleChange (event, column, row, index) {
        const data = this.state.fileInfos
        data[index][column] = event.target.value

        this.setState({ fileInfos: data })
        console.log('data', data)
    }

    handleClose () {
        this.setState({ error: '', show_success: false })
    }

    changeImportType (e) {
        this.setState({ [e.target.name]: e.target.value })
    }

    handleInputChanges (e) {
        const mappings = this.state.mappings

        mappings[e.target.name] = e.target.value

        this.setState({ mappings: mappings }, () => {
            console.log('mappings', this.state.mappings)
        })
    }

    buildSelectList (header) {
        let columns = null
        if (!this.state.columns.length) {
            columns = <option value="">Loading...</option>
        } else {
            columns = this.state.columns.map((column, index) => {
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

    render () {
        const {
            checked,
            selectedFile,
            currentFile,
            progress,
            message,
            fileInfos,
            headers,
            columns,
            loading,
            show_success,
            error,
            error_message,
            success_message,
            import_errors
        } = this.state

        const total = checked.size > 0 && fileInfos.length ? fileInfos.filter(row => checked.has(row.uniqueId)).reduce((result, { amount }) => result += amount, 0) : 0

        const preview = headers.length && columns.length
            ? headers.map((header, index) => {
                const select_list = this.buildSelectList(header)
                return <Form className="p-2" key={index} inline>
                    <FormGroup className="mb-2 mr-sm-2 mb-sm-0">
                        <Label for="exampleEmail" className="mr-sm-2">{header}</Label>
                        {select_list}
                    </FormGroup>
                </Form>
            })
            : null

        const errors = []

        console.log('errors', import_errors)
        console.log('errors', import_errors.required_headers)

        if (Object.keys(import_errors).length) {
            if (import_errors.data && import_errors.data.length) {
                import_errors.data.forEach((message) => {
                    errors.push(<div className="alert alert-danger">{message}</div>)
                })
            }

            if (import_errors.required_headers && import_errors.required_headers.length) {
                import_errors.required_headers.forEach((message) => {
                    errors.push(<div className="alert alert-danger">{message}</div>)
                })
            }

            if (import_errors.duplicates && import_errors.duplicates.length) {
                import_errors.duplicates.forEach((message) => {
                    errors.push(<div className="alert alert-danger">{message}</div>)
                })
            }

            if (import_errors.headers && import_errors.headers.length) {
                import_errors.headers.forEach((message) => {
                    errors.push(<div className="alert alert-danger">{message}</div>)
                })
            }
        }

        return (
            <React.Fragment>
                <div className="row">
                    <div className="col-12">

                        <div className="card mt-2">
                            <div className="card-body">
                                {currentFile && (
                                    <div className="progress">
                                        <div
                                            className="progress-bar progress-bar-info progress-bar-striped"
                                            role="progressbar"
                                            aria-valuenow={progress}
                                            aria-valuemin="0"
                                            aria-valuemax="100"
                                            style={{ width: progress + '%' }}
                                        >
                                            {progress}%
                                        </div>
                                    </div>
                                )}

                                <div>
                                    <div className="row">
                                        <div className="col">
                                            <CustomInput
                                                onChange={this.selectFile} type="file"
                                                id="file" name="file"
                                                label="File"/>
                                        </div>
                                        <div className="col">
                                            <select name="import_type" id="import_type" className="form-control"
                                                value={this.state.import_type}
                                                onChange={this.changeImportType.bind(this)}>
                                                <option value="">{translations.select_option}</option>
                                                <option value="invoice">{translations.invoice}</option>
                                                <option value="customer">{translations.customer}</option>
                                                <option value="lead">{translations.lead}</option>
                                                <option value="deal">{translations.deal}</option>
                                                <option value="product">{translations.product}</option>
                                                <option value="expense">{translations.expense}</option>
                                                <option value="company">{translations.company}</option>
                                                <option value="payment">{translations.payment}</option>
                                            </select>
                                        </div>

                                        <div className="col">
                                            <select name="file_type" id="file_type" className="form-control"
                                                value={this.state.file_type}
                                                onChange={this.changeImportType.bind(this)}>
                                                <option value="csv">CSV</option>
                                                <option value="json">JSON</option>
                                            </select>
                                        </div>

                                        <div className="col">
                                            {!!this.state.hash.length &&
                                            <button className="btn btn-success"
                                                disabled={!selectedFile}
                                                onClick={this.upload}
                                            >
                                                {translations.upload}
                                            </button>
                                            }

                                            <button className="btn btn-success ml-2"
                                                disabled={!this.state.import_type}
                                                onClick={this.export}
                                            >
                                                {translations.export}
                                            </button>

                                            {!this.state.hash.length &&
                                            <button className="btn btn-success ml-2"
                                                disabled={!this.state.import_type}
                                                onClick={this.preview}
                                            >
                                                {translations.preview}
                                            </button>
                                            }

                                            {!!this.state.template &&
                                            <a download={true} href={this.state.template}><i
                                                style={{ fontSize: '26px' }}
                                                className={`ml-2 fa ${icons.cloud_download}`}/></a>
                                            }
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
                                className="card-header">{translations.expenses} {this.state.checked.size > 0 ? ` - ${this.state.checked.size} selected ` : ''}
                                {!!total > 0 &&
                                <FormatMoney amount={total}/>
                                }
                            </div>
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

                                <button className="btn btn-primary"
                                    onClick={this.save.bind(this)}>{translations.save}</button>
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
