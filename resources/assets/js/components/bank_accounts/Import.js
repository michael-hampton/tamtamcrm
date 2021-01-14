import React from 'react'
import UploadService from './UploadService'
import ImportPreview from './ImportPreview'
import { translations } from '../utils/_translations'
import Snackbar from '@material-ui/core/Snackbar'
import { Alert } from 'reactstrap'
import queryString from 'query-string'
import FormatMoney from '../common/FormatMoney'

export default class Import extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            selectedFiles: null,
            currentFile: null,
            progress: 0,
            message: '',
            error: '',
            show_success: false,
            error_message: translations.unexpected_error,
            success_message: translations.expenses_imported_successfully,
            fileInfos: [],
            loading: false,
            checked: new Set(),
            bank_id: queryString.parse(this.props.location.search).bank_id || 0
        }

        this.selectFile = this.selectFile.bind(this)
        this.upload = this.upload.bind(this)
    }

    componentDidMount () {
        UploadService.getFiles().then((response) => {
            this.setState({
                fileInfos: response.data
            })
        })
    }

    selectFile (event) {
        this.setState({
            selectedFiles: event.target.files
        })
    }

    upload () {
        const currentFile = this.state.selectedFiles[0]

        this.setState({
            progress: 0,
            currentFile: currentFile
        })

        UploadService.upload(currentFile, 'api/bank_accounts/ofx/preview', { import_type: 'bank_account' }, (event) => {
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

    render () {
        const {
            checked,
            selectedFiles,
            currentFile,
            progress,
            message,
            fileInfos,
            loading,
            show_success,
            error,
            error_message,
            success_message
        } = this.state

        const total = checked.size > 0 && fileInfos.length ? fileInfos.filter(row => checked.has(row.uniqueId)).reduce((result, { amount }) => result += amount, 0) : 0

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

                                <label className="btn btn-default">
                                    <input type="file" onChange={this.selectFile}/>
                                </label>

                                <button className="btn btn-success"
                                    disabled={!selectedFiles}
                                    onClick={this.upload}
                                >
                                    {translations.upload}
                                </button>

                                {!!message &&
                                <div className="alert alert-danger" role="alert">
                                    {message}
                                </div>
                                }
                            </div>
                        </div>

                        {fileInfos.length &&
                        <div className="card mt-2">
                            <div
                                className="card-header">{translations.expenses} {this.state.checked.size > 0 ? ` - ${this.state.checked.size} selected ` : ''}
                                {!!total > 0 &&
                                <FormatMoney amount={total}/>
                                }
                            </div>
                            <div className="card-body">
                                <ImportPreview
                                    isCheckboxChecked={({ id }) => this.state.checked.has(id)}
                                    onMasterCheckboxChange={(_, rows) => {
                                        let all = true
                                        const checked = this.state.checked

                                        rows.forEach(({ id }) => {
                                            if (!checked.has(id)) {
                                                all = false
                                            }
                                        })

                                        rows.forEach(({ id }) => {
                                            if (all) {
                                                checked.delete(id)
                                            } else if (!checked.has(id)) {
                                                checked.add(id)
                                            }
                                        })

                                        this.setState({ checked: checked }, () => {
                                            console.log('checked', this.state.checked)
                                        })
                                    }}
                                    onCheckboxChange={(_, { id }) => {
                                        const checked = this.state.checked

                                        if (checked.has(id)) {
                                            checked.delete(id)
                                        } else {
                                            checked.add(id)
                                        }

                                        this.setState({ checked: checked }, () => {
                                            console.log('checked', checked)
                                        })
                                    }}
                                    dataItemManipulator={(field, value) => {
                                        return value
                                    }}
                                    editableColumns={[
                                        {
                                            name: 'name',
                                            controlled: true,
                                            type: 'text',
                                            onChange: this.handleChange.bind(this)
                                        },
                                        {
                                            name: 'memo',
                                            controlled: true,
                                            type: 'text',
                                            onChange: this.handleChange.bind(this)
                                        }]}
                                    disabledCheckboxes={[]}
                                    renderMasterCheckbox={true}
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
                                    renderCheckboxes={true}
                                    buttons={[]}
                                    actions={[]}
                                    fieldsToExclude={['userInitiatedDate', 'uniqueId', 'type']}
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
