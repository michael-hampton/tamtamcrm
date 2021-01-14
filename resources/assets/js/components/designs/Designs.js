import React from 'react'
import {
    Card,
    CardBody,
    CardHeader,
    Col,
    FormGroup,
    Input,
    Label,
    Nav,
    NavItem,
    NavLink,
    Progress,
    Row,
    TabContent,
    TabPane
} from 'reactstrap'
import axios from 'axios'
import DesignDropdown from '../common/dropdowns/DesignDropdown'
import { translations } from '../utils/_translations'
import Variables from '../settings/Variables'
import SnackbarMessage from '../common/SnackbarMessage'
import Header from '../settings/Header'

class Designs extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
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
            activeTab: '1',
            loading: false,
            errors: []
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

    toggleTabs (tab, e) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab }, () => {
                if (this.state.activeTab === '2' && this.state.is_mobile) {
                    alert('yes')
                    this.getPreview()
                }
            })
        }

        const parent = e.currentTarget.parentNode
        const rect = parent.getBoundingClientRect()
        const rect2 = parent.nextSibling.getBoundingClientRect()
        const rect3 = parent.previousSibling.getBoundingClientRect()
        const winWidth = window.innerWidth || document.documentElement.clientWidth
        const widthScroll = winWidth * 33 / 100

        if (rect.left <= 10 || rect3.left <= 10) {
            const container = document.getElementsByClassName('setting-tabs')[0]
            container.scrollLeft -= widthScroll
        }

        if (rect.right >= winWidth - 10 || rect2.right >= winWidth - 10) {
            const container = document.getElementsByClassName('setting-tabs')[0]
            container.scrollLeft += widthScroll
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
        axios.post('/api/preview', {
            design: design
        }, {
            onUploadProgress: ProgressEvent => {
                this.setState({
                    loaded: (ProgressEvent.loaded / ProgressEvent.total * 100)
                })
            }
        })
            .then((response) => {
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
                const blob = new Blob([view], { type: 'application/pdf' })
                const url = URL.createObjectURL(blob)

                console.log('url', url)

                /* const file = new Blob (
                 [ response.data.data ],
                 { type: 'application/pdf' } ) */
                // const fileURL = URL.createObjectURL ( file )

                this.setState({ loaded: 0, obj_url: url, is_loading: false }, () => URL.revokeObjectURL(url))
            })
            .catch((error) => {
                this.setState({
                    errors: error.response.data.errors
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

        const tabs = <Nav tabs className="nav-justified setting-tabs disable-scrollbars">
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '1' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggleTabs('1', e)
                    }}>
                    {translations.settings}
                </NavLink>
            </NavItem>

            {!!this.state.is_mobile &&
            <NavItem>
                <NavLink
                    className={this.state.activeTab === '2' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggleTabs('2', e)
                    }}>
                    {translations.preview}
                </NavLink>
            </NavItem>
            }

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '3' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggleTabs('3', e)
                    }}>
                    {translations.header}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '4' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggleTabs('4', e)
                    }}>
                    {translations.body}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '5' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggleTabs('5', e)
                    }}>
                    {translations.total}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '6' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggleTabs('6', e)
                    }}>
                    {translations.footer}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '7' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggleTabs('7', e)
                    }}>
                    {translations.product}
                </NavLink>
            </NavItem>

            <NavItem>
                <NavLink
                    className={this.state.activeTab === '8' ? 'active' : ''}
                    onClick={(e) => {
                        this.toggleTabs('8', e)
                    }}>
                    {translations.task}
                </NavLink>
            </NavItem>
        </Nav>

        return (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={translations.settings_not_saved}/>

                <Row>
                    <Col sm={7}>

                        <Header title={translations.designs} className="header-md"
                            tabs={tabs}/>

                        <TabContent className="fixed-margin-mobile bg-transparent" activeTab={this.state.activeTab}>
                            <TabPane className="px-0" tabId="1">
                                <Card>
                                    <CardBody>
                                        {title}

                                        <FormGroup>
                                            <Label for="name">{translations.design} <span
                                                className="text-danger">*</span></Label>
                                            <DesignDropdown resetCounters={this.resetCounters}
                                                handleInputChanges={this.switchDesign}/>
                                        </FormGroup>
                                    </CardBody>
                                </Card>

                                <Card className="border-0">
                                    <CardBody>
                                        <Row>
                                            <Col sm={12}>
                                                <Variables class="fixed-margin-mobile"/>
                                            </Col>
                                        </Row>
                                    </CardBody>
                                </Card>
                            </TabPane>

                            {!!this.state.is_mobile &&
                            <TabPane tabId="2" className="px-0">
                                <Card>
                                    <CardHeader>{translations.preview}</CardHeader>
                                    <CardBody>
                                        <div className="embed-responsive embed-responsive-21by9">
                                            <iframe className="embed-responsive-item" id="viewer"
                                                src={this.state.obj_url}/>
                                        </div>
                                    </CardBody>
                                </Card>
                            </TabPane>
                            }

                            <TabPane tabId="3" className="px-0">
                                <Card>
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
                            </TabPane>

                            <TabPane tabId="4" className="px-0">
                                <Card>
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
                            </TabPane>

                            <TabPane tabId="5" className="px-0">
                                <Card>
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
                            </TabPane>

                            <TabPane tabId="6" className="px-0">
                                <Card>
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
                            </TabPane>

                            <TabPane tabId="7" className="px-0">
                                <Card>
                                    <CardHeader>{translations.product}</CardHeader>
                                    <CardBody/>
                                </Card>
                            </TabPane>

                            <TabPane tabId="8" className="px-0">
                                <Card>
                                    <CardHeader>{translations.task}</CardHeader>
                                    <CardBody/>
                                </Card>
                            </TabPane>
                        </TabContent>
                    </Col>

                    {!this.state.is_mobile &&
                    <Col md={5} className="mt-2 pl-0">
                        {this.state.loaded > 0 &&
                        <Progress max="100" color="success"
                            value={this.state.loaded}>{Math.round(this.state.loaded, 2)}%</Progress>
                        }

                        <div style={{ minHeight: '600px' }} className="embed-responsive embed-responsive-21by9">
                            <iframe className="embed-responsive-item" id="viewer" src={this.state.obj_url}/>
                        </div>
                    </Col>
                    }
                </Row>

            </React.Fragment>
        )
    }
}

export default Designs
