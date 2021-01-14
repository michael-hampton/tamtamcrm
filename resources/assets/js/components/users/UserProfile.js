import * as React from 'react'
import { Button, Col, Container, Progress, Row } from 'reactstrap'
import axios from 'axios'
import { toast, ToastContainer } from 'react-toastify'
import 'react-toastify/dist/ReactToastify.css'
import EditUser from './edit/EditUser'
import { monthByNumber } from '../utils/helper'

class UserProfile extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            imagePreviewUrl: '',
            selectedFile: '',
            loaded: 0,
            user: {}
        }
        this.submit = this.submit.bind(this)
        this.fileChangedHandler = this.fileChangedHandler.bind(this)
    }

    componentDidMount () {
        axios.get(`/api/user/profile/${this.props.match.params.username}`)
            .then((r) => {
                this.setState({
                    user: r.data
                })
            })
            .catch((err) => {
                console.warn(err)
                toast.error('upload fail')
            })
    }

    /**
     * return date as format January 21, 1991
     * @param dob
     */
    formatDate (dob) {
        const date = new Date(dob)
        const startYear = date.getFullYear()
        const startMonth = date.getMonth()
        const startDay = date.getDate()

        return `${monthByNumber[startMonth]} ${startDay}, ${startYear}`
    }

    fileChangedHandler (e) {
        e.preventDefault()
        const reader = new FileReader()
        const file = e.target.files[0]
        reader.onloadend = () => {
            this.setState({
                selectedFile: file,
                imagePreviewUrl: reader.result
            })
        }
        reader.readAsDataURL(file)
    }

    submit () {
        const data = new FormData()
        data.append('file', this.state.selectedFile)
        axios.post('/api/user/upload', data, {
            onUploadProgress: ProgressEvent => {
                this.setState({
                    loaded: (ProgressEvent.loaded / ProgressEvent.total * 100)
                })
            }
        })
            .then(response => { // then print response status
                toast.success('upload success')
            })
            .catch(err => { // then print response status
                console.warn(err)
                toast.error('upload fail')
            })
    }

    render () {
        const imgUrl = this.state.user.profile_photo ? `/storage/${this.state.user.profile_photo}` : 'https://cdn.bootstrapsnippet.net/assets/image/dummy-avatar.jpg'
        const gender = this.state.user.gender ? this.state.user.gender[0].toUpperCase() + this.state.user.gender.slice(1) : ''

        let $imagePreview = (<img className="w-100 rounded border" src={imgUrl}/>)
        let userData = ''
        let button = ''
        let uploadButton = ''

        if (this.state.imagePreviewUrl) {
            $imagePreview = (<img className="w-100 rounded border" src={this.state.imagePreviewUrl} alt="icon"/>)
        }

        if (this.state.user && this.state.user.id) {
            button = parseInt(JSON.parse(localStorage.getItem('appState')).user.id) === this.state.user.id
                ? <EditUser user={this.state.user} user_id={this.state.user.id}/>
                : ''

            uploadButton = parseInt(JSON.parse(localStorage.getItem('appState')).user.id) === this.state.user.id
                ? <label className="btn btn-default btn-file">
                    Browse <input onChange={this.fileChangedHandler.bind(this)} type="file"
                        style={{ display: 'none' }}/>
                </label>
                : ''

            userData = (
                <React.Fragment>
                    <div className="d-flex align-items-center">
                        <h2 className="font-weight-bold m-0">
                            {`${this.state.user.first_name} ${this.state.user.last_name}`}
                        </h2>

                    </div>

                    <p className="h5 text-primary mt-2 d-block font-weight-light">
                        {this.state.user.job_description}
                    </p>

                    <section className="d-flex mt-5">
                        {button}
                    </section>

                    <h6 className="text-uppercase font-weight-light text-secondary">
                        Contact Information
                    </h6>
                    <dl className="row mt-4 mb-4 pb-3">
                        <dt className="col-sm-3">Phone</dt>
                        <dd className="col-sm-9">{this.state.user.phone_number}</dd>

                        <dt className="col-sm-3">Email address</dt>
                        <dd className="col-sm-9">
                            <a href={this.state.user.email}>{this.state.user.email}</a>
                        </dd>
                    </dl>

                    <h6 className="text-uppercase font-weight-light text-secondary">
                        Basic Information
                    </h6>
                    <dl className="row mt-4 mb-4 pb-3">
                        <dt className="col-sm-3">Birthday</dt>
                        <dd className="col-sm-9">{this.formatDate(this.state.user.dob)}</dd>

                        <dt className="col-sm-3">Gender</dt>
                        <dd className="col-sm-9">{gender}</dd>
                    </dl>
                </React.Fragment>
            )
        }
        return (
            <Container className="py-4 my-2">
                <Row>
                    <Col className="pr-md-5" md={4}>
                        {$imagePreview}
                        <div className="pt-4 mt-2">
                            <section className="mb-4 pb-1">
                                <h3 className="h6 font-weight-light text-secondary text-uppercase"> Change Profile
                                    Picture</h3>
                                <div className="pt-2">
                                    <ToastContainer/>
                                    <Progress max="100" color="success"
                                        value={this.state.loaded}>{Math.round(this.state.loaded, 2)}%</Progress>

                                    {uploadButton}
                                    <Button color="success" onClick={this.submit}>Save changes</Button>
                                </div>
                            </section>
                        </div>
                    </Col>

                    <Col md={8}>
                        {userData}
                    </Col>
                </Row>
            </Container>
        )
    }
}

export default UserProfile
