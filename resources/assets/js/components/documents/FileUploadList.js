/* eslint-disable no-unused-vars */
import React from 'react'
import FileUpload from './FileUpload'

export default class FileUploadList extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            files: this.props.files ? this.props.files : [],
            errors: []
        }

        this.deleteFile = this.deleteFile.bind(this)
    }

    deleteFile (id) {
        const arrFiles = [...this.state.files]
        const index = arrFiles.findIndex(file => file.id === id)
        arrFiles.splice(index, 1)
        this.setState({ files: arrFiles })
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
