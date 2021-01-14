import React from 'react'
import { Button, DropdownItem, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import axios from 'axios'
import DesignDropdown from '../common/dropdowns/DesignDropdown'
import CKEditor from '@ckeditor/ckeditor5-react'
import ClassicEditor from '@ckeditor/ckeditor5-build-classic'

class EditDesign extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            name: '',
            design: {
                header: '',
                body: '',
                footer: ''
            },
            obj_url: null,
            loading: false,
            errors: []
        }

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.getPreview = this.getPreview.bind(this)
        this.switchDesign = this.switchDesign.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'designForm')) {
            const storedValues = JSON.parse(localStorage.getItem('designForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
    }

    handleChange (el) {
        const inputName = el.target.name
        const inputValue = el.target.value

        const statusCopy = Object.assign({}, this.state)
        statusCopy.design[inputName].value = inputValue

        this.setState(statusCopy)
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value
        }, () => localStorage.setItem('designForm', JSON.stringify(this.state)))
    }

    hasErrorFor (field) {
        return !!this.state.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.state.errors[field][0]}</strong>
                </span>
            )
        }
    }

    handleClick () {
        axios.post('/api/designs', {
            name: this.state.name,
            design: this.state.design
        })
            .then((response) => {
                const newUser = response.data
                this.props.designs.push(newUser)
                this.props.action(this.props.designs)
                localStorage.removeItem('designForm')
                this.setState({
                    name: null
                })
                this.toggle()
            })
            .catch((error) => {
                this.setState({
                    errors: error.response.data.errors
                })
            })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState({
                    name: null,
                    icon: null
                }, () => localStorage.removeItem('designForm'))
            }
        })
    }

    getPreview () {
        axios.post('/api/preview', {
            body: this.state.design.body,
            header: this.state.design.header,
            footer: this.state.design.footer
            // entity_id: 1371,
            // entity: 'invoice'
        })
            .then((response) => {
                console.log('respons', response.data.data)
                var base64str = response.data.data

                // decode base64 string, remove space for IE compatibility
                var binary = atob(base64str.replace(/\s/g, ''))
                var len = binary.length
                var buffer = new ArrayBuffer(len)
                var view = new Uint8Array(buffer)
                for (var i = 0; i < len; i++) {
                    view[i] = binary.charCodeAt(i)
                }

                // create the blob object with content-type "application/pdf"
                var blob = new Blob([view], { type: 'application/pdf' })
                var url = URL.createObjectURL(blob)

                /* const file = new Blob (
                 [ response.data.data ],
                 { type: 'application/pdf' } ) */
                // const fileURL = URL.createObjectURL ( file )

                this.setState({ obj_url: url }, () => URL.revokeObjectURL(url))
            })
            .catch((error) => {
                this.setState({
                    errors: error.response.data.errors
                })
            })
    }

    switchDesign (design) {
        this.setState({
            design: design.design,
            name: design.name
        })
    }

    render () {
        const editors = this.state.modal === true ? <React.Fragment> <FormGroup>
            <Label for="name">Header <span className="text-danger">*</span></Label>
            <CKEditor
                data={this.state.design.header}
                editor={ClassicEditor}
                config={{
                    toolbar: ['heading', '|', 'bold', 'italic', 'blockQuote', 'link', 'numberedList', 'bulletedList', 'imageUpload', 'insertTable',
                        'tableColumn', 'tableRow', 'mergeTableCells', 'mediaEmbed', '|', 'undo', 'redo']
                }}
                onInit={editor => {
                    // You can store the "editor" and use when it is needed.
                    console.log('Editor is ready to use!', editor)
                }}
                onChange={(event, editor) => {
                    const data = editor.getData()
                    this.setState(prevState => ({
                        design: { // object that we want to update
                            ...prevState.design, // keep all other key-value pairs
                            header: data // update the value of specific key
                        }
                    }), () => console.log('design', this.state.design))
                }}
            />
        </FormGroup>

        <FormGroup>
            <Label for="name">Body <span className="text-danger">*</span></Label>
            <CKEditor
                data={this.state.design.body}
                editor={ClassicEditor}
                config={{
                    toolbar: ['heading', '|', 'bold', 'italic', 'blockQuote', 'link', 'numberedList', 'bulletedList', 'imageUpload', 'insertTable',
                        'tableColumn', 'tableRow', 'mergeTableCells', 'mediaEmbed', '|', 'undo', 'redo']
                }}
                onInit={editor => {
                    // You can store the "editor" and use when it is needed.
                    console.log('Editor is ready to use!', editor)
                }}
                onChange={(event, editor) => {
                    alert('mike')
                    const data = editor.getData()
                    this.setState(prevState => ({
                        design: { // object that we want to update
                            ...prevState.design, // keep all other key-value pairs
                            body: data // update the value of specific key
                        }
                    }), () => console.log('design', this.state.design))
                }}
            />
        </FormGroup>

        <FormGroup>
            <Label for="name">Footer <span className="text-danger">*</span></Label>
            <CKEditor
                data={this.state.design.footer}
                editor={ClassicEditor}
                config={{
                    toolbar: ['heading', '|', 'bold', 'italic', 'blockQuote', 'link', 'numberedList', 'bulletedList', 'imageUpload', 'insertTable',
                        'tableColumn', 'tableRow', 'mergeTableCells', 'mediaEmbed', '|', 'undo', 'redo']
                }}
                onInit={editor => {
                    // You can store the "editor" and use when it is needed.
                    console.log('Editor is ready to use!', editor)
                }}
                onChange={(event, editor) => {
                    const data = editor.getData()
                    this.setState(prevState => ({
                        design: { // object that we want to update
                            ...prevState.design, // keep all other key-value pairs
                            footer: data // update the value of specific key
                        }
                    }), () => console.log('design', this.state.design))
                }}
            />
        </FormGroup> </React.Fragment> : null

        return (
            <React.Fragment>
                <DropdownItem onClick={this.toggle}><i className="fa fa-edit"/>Edit</DropdownItem>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>
                        Add Design
                    </ModalHeader>
                    <ModalBody>
                        <FormGroup>
                            <Label for="name">Name <span className="text-danger">*</span></Label>
                            <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="name"
                                id="name" value={this.state.name} placeholder="Name"
                                onChange={this.handleInput.bind(this)}/>
                            {this.renderErrorFor('name')}
                        </FormGroup>

                        <FormGroup>
                            <Label for="name">Design <span className="text-danger">*</span></Label>
                            <DesignDropdown handleInputChanges={this.switchDesign}/>
                        </FormGroup>

                        {editors}

                        <div className="embed-responsive embed-responsive-21by9">
                            <iframe className="embed-responsive-item" id="viewer" src={this.state.obj_url}/>
                        </div>

                        <Button onClick={this.getPreview} color="primary">Preview</Button>
                    </ModalBody>

                    <ModalFooter>
                        <Button color="primary" onClick={this.handleClick.bind(this)}>Add</Button>
                        <Button color="secondary" onClick={this.toggle}>Close</Button>
                    </ModalFooter>
                </Modal>
            </React.Fragment>
        )
    }
}

export default EditDesign
