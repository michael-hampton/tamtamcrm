/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
import FileUploadForm from './FileUploadForm'
import FileUploadList from './FileUploadList'
import axios from 'axios'

export default class FileUploads extends Component {
    constructor (props) {
        super(props)
        this.state = {
            files: [],
            loading: false
        }
        this.addFile = this.addFile.bind(this)

        if (this.props.entity.id) {
            this.getFiles = this.getFiles.bind(this)
        }
    }

    componentDidMount () {
        this.getFiles()
    }

    getFiles () {
        // loading
        this.setState({ loading: true })

        // get all the comments
        axios.get(`/api/uploads/${this.props.entity_type}/${this.props.entity.id}`)
            .then((r) => {
                this.setState({
                    files: r.data,
                    loading: false
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false
                })
            })
    }

    /**
     * Add new file
     * @param {Object} file
     */
    addFile (file) {
        this.setState({
            files: [file, ...this.state.files]
        })
    }

    render () {
        const list = this.state.files && this.state.files.length
            ? <FileUploadList
                loading={this.state.loading}
                files={this.state.files}
            />
            : ''

        return (
            <div className="col-12">
                {<FileUploadForm
                    // entity={this.props.entity}
                    addFile={this.addFile}
                    hide_checkbox={this.props.hide_checkbox}
                    user_id={this.props.user_id}
                    entity={this.props.entity}
                    entity_type={this.props.entity_type}
                />}

                {list}
            </div>
        )
    }
}
