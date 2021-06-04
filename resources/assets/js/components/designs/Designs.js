import React from 'react'
import {
    Card,
    CardBody,
    CardHeader,
    Col,
    FormGroup,
    Input,
    Label,
    Progress,
    Row
} from 'reactstrap'
import axios from 'axios'
import DesignDropdown from '../common/dropdowns/DesignDropdown'
import { translations } from '../utils/_translations'
import Variables from '../settings/Variables'
import SnackbarMessage from '../common/SnackbarMessage'
import EditScaffold from '../common/EditScaffold'
import TextareaAutosize from '@material-ui/core/TextareaAutosize'
import AppSwitch from '../common/AppSwitch'
import AccountRepository from '../repositories/AccountRepository'
import HtmlViewer from '../emails/HtmlViewer'
import PdfViewer from '../emails/PdfViewer'

class Designs extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            show_html: false,
            success: false,
            error: false,
            loaded: 0,
            is_loading: false,
            is_mobile: window.innerWidth <= 768,
            modal: false,
            name: 'custom',
            id: null,
            is_custom: true,
            design: {
                header: '',
                body: '',
                footer: '',
                // includes: '',
                product: '',
                task: ''
            },
            obj_url: null,
            activeTab: 0,
            loading: false,
            errors: [],
            cached_settings: {},
            settings: {},
            company_logo: null,
            changesMade: false,
            isSaving: false
        }

        this.toggleTabs = this.toggleTabs.bind(this)
        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.getPreview = this.getPreview.bind(this)
        this.switchDesign = this.switchDesign.bind(this)
        this.resetCounters = this.resetCounters.bind(this)
        this.update = this.update.bind(this)
        this.save = this.save.bind(this)
        this.handleWindowSizeChange = this.handleWindowSizeChange.bind(this)
    }

    componentWillMount () {
        window.addEventListener('resize', this.handleWindowSizeChange)
    }

    // make sure to remove the listener
    // when the component is not mounted anymore
    componentWillUnmount () {
        window.removeEventListener('resize', this.handleWindowSizeChange)
    }

    handleWindowSizeChange () {
        this.setState({ is_mobile: window.innerWidth <= 768 })
    }

    componentDidMount () {
        if (localStorage.hasOwnProperty('designForm')) {
            const storedValues = JSON.parse(localStorage.getItem('designForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
    }

    toggleTabs (event, tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab }, () => {
                if (this.state.activeTab === 2 && this.state.is_mobile) {
                    this.getPreview()
                }
            })
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

    getFormData () {
        return {
            name: this.state.name,
            design: this.state.design
        }
    }

    save () {
        axios.post('/api/designs', this.getFormData())
            .then((response) => {
                const newUser = response.data
                this.props.designs.push(newUser)
                this.props.action(this.props.designs)
                localStorage.removeItem('designForm')
                this.setState({
                    name: null
                })
                // this.toggle ()
            })
            .catch((error) => {
                this.setState({
                    errors: error.response.data.errors
                })
            })
    }

    update () {
        axios.put(`/api/designs/${this.state.id}`, this.getFormData())
            .then((response) => {
                const index = this.props.designs.findIndex(design => design.id === parseInt(this.state.id))
                this.props.designs[index] = response.data
                this.props.action(this.props.designs)
            })
            .catch((error) => {
                this.setState({
                    errors: error.response.data.errors
                })
            })
    }

    handleClick () {
        if (this.state.id !== null) {
            this.update()
            return
        }

        this.save()
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
        const design = {
            name: this.state.name,
            is_custom: this.state.is_custom,
            design: {
                body: this.state.design.body,
                header: this.state.design.header,
                footer: this.state.design.footer,
                // includes: this.state.design.includes,
                table: this.state.design.table,
                totals: this.state.design.totals,
                product: '',
                task: ''
            }
        }

        this.setState({ obj_url: '' })
        const accountRepository = new AccountRepository()

        accountRepository.previewPdf(this.state.show_html, null, null, design, this.state.id).then(response => {
            console.log('url', response)
            const data = this.state.show_html ? response.data : response
            this.setState({ obj_url: data }, () => {
                if (!this.props.show_html) {
                    URL.revokeObjectURL(response)
                }
            })
        })
    }

    resetCounters () {
        this.setState({
            name: '',
            id: null,
            design: { header: '', body: '', footer: '' },
            obj_url: null,
            is_custom: true
        })
    }

    handleCancel () {
        this.setState({ settings: this.state.cached_settings, changesMade: false })
    }

    switchDesign (design) {
        this.setState({
            design: design[0].design,
            name: design[0].name,
            id: design[0].id,
            is_custom: false
        }, () => {
            if (!this.state.is_mobile) {
                this.getPreview()
            }
        })
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        const tabs = {
            settings: {
                activeTab: this.state.activeTab,
                toggle: this.toggleTabs
            },
            tabs: [
                {
                    label: translations.settings
                },
                {
                    label: this.state.is_mobile ? translations.preview : translations.header
                },
                {
                    label: translations.body
                },
                {
                    label: translations.total
                },
                {
                    label: translations.footer
                },
                {
                    label: translations.product
                },
                {
                    label: translations.task
                }
            ],
            children: []
        }

        const title = this.state.is_custom === true ? <FormGroup>
            <Label for="name">Name <span className="text-danger">*</span></Label>
            <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="name"
                id="name" value={this.state.name} placeholder="Name"
                onChange={this.handleInput.bind(this)}/>
            {this.renderErrorFor('name')}
        </FormGroup> : <FormGroup>
            <Label for="name">Name <span className="text-danger">*</span></Label>
            <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="name"
                id="name" disabled="disabled" value={this.state.name} placeholder="Name"
                onChange={this.handleInput.bind(this)}/>
            {this.renderErrorFor('name')}
        </FormGroup>

        let content = null

        if (this.state.obj_url && this.state.obj_url.length) {
            content = this.state.show_html ? <HtmlViewer height="600px" html={this.state.obj_url}/>
                : <PdfViewer height="600px" pdf={this.state.obj_url}/>
        }

        tabs.children[0] =
            <>
                <Card>
                    <CardBody>
                        {title}

                        <FormGroup>
                            <Label for="name">{translations.design} <span
                                className="text-danger">*</span></Label>
                            <DesignDropdown resetCounters={this.resetCounters}
                                handleInputChanges={this.switchDesign}/>
                        </FormGroup>

                        <FormGroup>
                            <AppSwitch label={translations.html_mode} name="show_html" isOn={this.state.show_html} handleToggle={(e) => {
                                this.setState({ show_html: !this.state.show_html }, () => {
                                    this.getPreview()
                                })
                            }} />
                        </FormGroup>
                    </CardBody>
                </Card>

                <Card className="border-0">
                    <CardBody>
                        <Row>
                            <Col sm={12}>
                                {!this.state.show_html && <Variables class="fixed-margin-mobile"/>}
                                {!!this.state.show_html && <TextareaAutosize value={this.state.obj_url} style={{ width: '100%' }} rowsMin={16} rowsMax={null} />}
                            </Col>
                        </Row>
                    </CardBody>
                </Card>
            </>

        tabs.children[1] = this.state.is_mobile
            ? <Card>
                <CardHeader>{translations.preview}</CardHeader>
                <CardBody>
                    <div className="embed-responsive embed-responsive-21by9">
                        <iframe className="embed-responsive-item" id="viewer"
                            src={this.state.obj_url}/>
                    </div>
                </CardBody>
            </Card> : <Card>
                <CardHeader>{translations.header}</CardHeader>
                <CardBody>

                    <FormGroup>
                        <Label for="name">{translations.header} <span
                            className="text-danger">*</span></Label>
                        <Input type="textarea" style={{ height: '400px' }} size="lg"
                            value={this.state.design.header}
                            onChange={(e) => {
                                const value = e.target.value
                                this.setState(prevState => ({
                                    design: { // object that we want to update
                                        ...prevState.design, // keep all other key-value pairs
                                        header: value // update the value of specific key
                                    }
                                }), () => {
                                    if (!this.state.is_loading && !this.state.is_mobile) {
                                        this.setState({ is_loading: true })
                                        setTimeout(() => {
                                            this.getPreview()
                                        }, 1000)
                                    }
                                })
                            }}
                        />
                    </FormGroup>
                </CardBody>
            </Card>

        tabs.children[2] = <Card>
            <CardHeader>{translations.body}</CardHeader>
            <CardBody>
                <FormGroup>
                    <Label for="name">{translations.body} <span className="text-danger">*</span></Label>
                    <Input type="textarea" style={{ height: '400px' }} size="lg"
                        value={this.state.design.body}
                        onChange={(e) => {
                            const value = e.target.value
                            this.setState(prevState => ({
                                design: { // object that we want to update
                                    ...prevState.design, // keep all other key-value pairs
                                    body: value // update the value of specific key
                                }
                            }), () => {
                                if (!this.state.is_loading && !this.state.is_mobile) {
                                    this.setState({ is_loading: true, obj_url: '' })
                                    setTimeout(() => {
                                        this.getPreview()
                                    }, 2000)
                                }
                            })
                        }}
                    />
                </FormGroup>
            </CardBody>
        </Card>

        tabs.children[3] = <Card>
            <CardHeader>{translations.total}</CardHeader>
            <CardBody>
                <FormGroup>
                    <Label for="name">{translations.total} <span
                        className="text-danger">*</span></Label>
                    <Input type="textarea" style={{ height: '400px' }} size="lg"
                        value={this.state.design.totals}
                        onChange={(e) => {
                            const value = e.target.value
                            this.setState(prevState => ({
                                design: { // object that we want to update
                                    ...prevState.design, // keep all other key-value pairs
                                    totals: value // update the value of specific key
                                }
                            }), () => {
                                if (!this.state.is_loading && !this.state.is_mobile) {
                                    this.setState({ is_loading: true, obj_url: '' })
                                    setTimeout(() => {
                                        this.getPreview()
                                    }, 2000)
                                }
                            })
                        }}
                    />
                </FormGroup>
            </CardBody>
        </Card>

        tabs.children[4] = <Card>
            <CardHeader>{translations.footer}</CardHeader>
            <CardBody>
                <FormGroup>
                    <Label for="name">{translations.footer} <span
                        className="text-danger">*</span></Label>
                    <Input type="textarea" style={{ height: '400px' }} size="lg"
                        value={this.state.design.footer}
                        onChange={(e) => {
                            const value = e.target.value
                            this.setState(prevState => ({
                                design: { // object that we want to update
                                    ...prevState.design, // keep all other key-value pairs
                                    footer: value // update the value of specific key
                                }
                            }), () => {
                                if (!this.state.is_loading && !this.state.is_mobile) {
                                    this.setState({ is_loading: true, obj_url: '' })
                                    setTimeout(() => {
                                        this.getPreview()
                                    }, 2000)
                                }
                            })
                        }}
                    />
                </FormGroup>
            </CardBody>
        </Card>

        tabs.children[5] = <Card>
            <CardHeader>{translations.product}</CardHeader>
            <CardBody/>
        </Card>

        tabs.children[6] = <Card>
            <CardHeader>{translations.task}</CardHeader>
            <CardBody/>
        </Card>

        return (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={translations.settings_not_saved}/>

                <Row>
                    <Col sm={6}>
                        <EditScaffold overide_width={true} isLoading={!this.state.loaded} isSaving={this.state.isSaving}
                            title={translations.designs}
                            isEditing={this.state.changesMade}
                            cancelButtonDisabled={!this.state.changesMade}
                            handleCancel={this.handleCancel.bind(this)}
                            handleSubmit={this.handleSubmit}
                            tabs={tabs}/>

                    </Col>

                    {!this.state.is_mobile &&
                    <Col md={6} className="mt-2 pl-0">
                        {this.state.loaded > 0 &&
                        <Progress max="100" color="success"
                            value={this.state.loaded}>{Math.round(this.state.loaded, 2)}%</Progress>
                        }

                        {content}
                    </Col>
                    }
                </Row>

            </React.Fragment>
        )
    }
}

export default Designs
