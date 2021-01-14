import React, { Component } from 'react'
import axios from 'axios'
import { CustomInput, Progress } from 'reactstrap'
import { toast, ToastContainer } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import './uploads.scss'
import { translations } from '../utils/_translations'
import { icons } from '../utils/_icons'

class FileUpload extends Component {
    constructor (props) {
        super(props)
        this.state = {
            selectedFile: [],
            loaded: 0,
            customer_can_view: false
        }

        this.allowed_extensions = [
            'png',
            'svg',
            'jpeg',
            'gif',
            'jpg',
            'bmp',
            'txt',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'pdf'
        ]
    }

    checkFileSize (event) {
        const files = event.target.files
        const size = 2000000
        const err = []
        for (var x = 0; x < files.length; x++) {
            if (files[x].size > size) {
                err[x] = files[x].type + 'is too large, please pick a smaller file\n'
            }
        }
        for (let z = 0; z < err.length; z++) {
            toast.error(err[z])
            event.target.value = null
        }
        return true
    }

    checkMimeType (event) {
        const files = event.target.files
        const err = []
        for (let x = 0; x < files.length; x++) {
            const extension = files[x].name.split('.').pop()

            if (!this.allowed_extensions.includes(extension)) {
                err[x] = files[x].type + ' is not a supported format\n'
            }
        }

        for (var z = 0; z < err.length; z++) {
            toast.error(err[z])
            event.target.value = null
        }
        return true
    }

    maxSelectFile (event) {
        const files = event.target.files
        if (files.length > 3) {
            const msg = 'Only 3 images can be uploaded at a time'
            event.target.value = null
            toast.warn(msg)
            return false
        }
        return true
    }

    onChangeHandler (event) {
        const files = event.target.files
        if (this.maxSelectFile(event) && this.checkMimeType(event) && this.checkFileSize(event)) {
            console.log('selected files', files)
            // if return true allow to setState
            this.setState({
                selectedFile: files,
                loaded: 0
            })
        }
    }

    handleInput (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({ [e.target.name]: value })
    }

    onClickHandler () {
        const data = new FormData()
        data.append('user_id', this.props.user_id)
        data.append('entity_id', this.props.entity.id)
        data.append('entity_type', this.props.entity_type)
        data.append('customer_can_view', this.state.customer_can_view)
        for (var x = 0; x < this.state.selectedFile.length; x++) {
            data.append('file[]', this.state.selectedFile[x])
        }
        axios.post('/api/uploads', data, {
            onUploadProgress: ProgressEvent => {
                this.setState({
                    loaded: (ProgressEvent.loaded / ProgressEvent.total * 100)
                })
            }
        })
            .then(response => { // then print response status
                if (response.data && response.data.length) {
                    response.data.map((file, index) => (
                        this.props.addFile(file)
                    ))
                }

                this.setState({ selectedFile: [] })
                toast.success('upload success')
            })
            .catch(err => { // then print response status
                this.setState({ selectedFile: [] })
                console.warn(err)
                toast.error('upload fail')
            })
    }

    render () {
        const file_list = []

        if (this.state.selectedFile.length) {
            Array.from(this.state.selectedFile).forEach(file => {
                file_list.push(
                    <div key={file.name} className="Row">
                        <span className="Filename">{file.name}</span>
                    </div>
                )
            })
        }

        return (
            <div className="container">
                <div className="row">
                    <div className="col-12">
                        <div className="form-group files">
                            <span className="btn btn-primary btn-file img-select-btn">
                                <span>{translations.browse}</span>
                                <input type="file" multiple name="img-file-input"
                                    onChange={this.onChangeHandler.bind(this)}/>
                            </span>
                        </div>

                        <a href="#"
                            className="mt-2 mb-2 list-group-item-dark list-group-item list-group-item-action flex-column align-items-start">
                            <div className="d-flex w-100 justify-content-between">
                                <h5 className="mb-1">
                                    <i style={{ fontSize: '24px', marginRight: '20px' }}
                                        className={`fa ${icons.customer}`}/>
                                    {translations.customer_can_view}
                                </h5>
                                <CustomInput
                                    checked={this.state.customer_can_view}
                                    type="switch"
                                    id="customer_can_view"
                                    name="customer_can_view"
                                    label=""
                                    onChange={this.handleInput.bind(this)}/>
                            </div>

                            <h6 id="passwordHelpBlock" className="form-text text-muted">
                                {translations.customer_can_view_help_text}
                            </h6>
                        </a>

                        <div className="form-group">
                            <ToastContainer/>
                            <Progress max="100" color="success"
                                value={this.state.loaded}>{Math.round(this.state.loaded, 2)}%</Progress>
                        </div>

                        {!!this.state.selectedFile.length &&
                        <button type="button" className="btn btn-success btn-block col-4 pull-right"
                            onClick={this.onClickHandler.bind(this)}>{translations.upload}
                        </button>
                        }

                        <div className="Files">
                            {file_list}
                        </div>

                        <hr className="mt-2 mb-5"/>
                    </div>
                </div>
            </div>
        )
    }
}

export default FileUpload
