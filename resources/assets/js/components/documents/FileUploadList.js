/* eslint-disable no-unused-vars */
import React from 'react'
import FileUpload from './FileUpload'
import axios from "axios";

export default class FileUploadList extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            files: this.props.files ? this.props.files : [],
            errors: []
        }

        this.deleteFile = this.deleteFile.bind(this)
    }

    deleteFile (id, password) {
        const data = {
            password: password
        }

        axios.delete(`/api/uploads/${id}`, { data: data })
            .then((r) => {
                const arrFiles = [...this.state.files]
                const index = arrFiles.findIndex(file => file.id === id)
                arrFiles.splice(index, 1)
                this.setState({ files: arrFiles })
            })
            .catch((error) => {
                if (error.response.data.errors) {
                    this.setState({
                        errors: error.response.data.errors
                    })
                } else {
                    this.setState({ message: error.response.data })
                }
            })
    }

    render () {
        return (
            <div className="row text-center text-lg-left">

                {this.state.files.length === 0 && !this.props.loading ? (
                    <div className="alert text-center alert-info">
                        Upload a file
                    </div>
                ) : null}

                {
                    this.state.files.map((file, index) => (
                        <FileUpload key={index} delete={this.deleteFile} file={file}/>
                    ))
                }
            </div>
        )
    }
}
