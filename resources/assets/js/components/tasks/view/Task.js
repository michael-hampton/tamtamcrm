import React, { Component } from 'react'
import { Alert, Card, CardBody, CardHeader, Col, Nav, NavItem, NavLink, Row, TabContent, TabPane } from 'reactstrap'
import { translations } from '../../utils/_translations'
import FileUploads from '../../documents/FileUploads'
import TaskModel from '../../models/TaskModel'
import axios from 'axios'
import BottomNavigationButtons from '../../common/BottomNavigationButtons'
import Overview from './Overview'
import formatDuration from '../../utils/_formatting'

export default class Task extends Component {
    constructor (props) {
        super(props)
        this.state = {
            entity: this.props.entity,
            activeTab: '1',
            obj_url: null,
            show_success: false,
            totalOn: false,
            totalStart: 0,
            totalTime: 0,
            lastOn: false,
            lastStart: 0,
            lastTime: 0
        }

        this.taskModel = new TaskModel(this.state.entity)
        this.toggleTab = this.toggleTab.bind(this)
        this.triggerAction = this.triggerAction.bind(this)
        this.loadPdf = this.loadPdf.bind(this)
        this.refresh = this.refresh.bind(this)
        this.startTimer = this.startTimer.bind(this)
        this.stopTimer = this.stopTimer.bind(this)

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.settings = user_account[0].account.settings
    }

    componentDidMount () {
        if (this.taskModel.isRunning && this.state.entity.timers && this.state.entity.timers.length) {
            this.startTimer()
        }
    }

    startTimer () {
        const last_timer = this.state.entity.timers[this.state.entity.timers.length - 1]
        const first_timer = this.state.entity.timers[0]
        const start_date = new Date(first_timer.date + ' ' + first_timer.start_time)
        const start_date_last = new Date(last_timer.date + ' ' + last_timer.start_time)

        let diff = 0

        if (this.state.entity.timers && this.state.entity.timers.length) {
            this.state.entity.timers.map((timer, index) => {
                var timeStart = new Date(timer.date + ' ' + timer.start_time).getTime()
                var timeEnd = timer.end_time && timer.end_time.length ? new Date(timer.end_date + ' ' + timer.end_time).getTime() : new Date().getTime()
                diff += timeEnd - timeStart
            })
        }

        this.setState({
            totalOn: true,
            totalTime: diff / 1000,
            totalStart: (Date.now() / 1000) - (start_date.getTime() / 1000),
            lastOn: true,
            lastTime: (Date.now() / 1000) - (start_date_last.getTime() / 1000)
        })

        this.timer = setInterval(() => {
            this.setState({
                totalTime: this.state.totalTime + 1,
                lastTime: this.state.lastTime + 1
            }, () => {
                console.log('total time', this.state.totalTime)
            })
        }, 1000)
    }

    stopTimer () {
        this.setState({ totalOn: false })
        clearInterval(this.timer)
    }

    refresh (entity) {
        this.taskModel = new TaskModel(entity)
        this.setState({ entity: entity })
    }

    triggerAction (action) {
        this.taskModel.completeAction(this.state.entity, action).then(response => {
            this.setState({ show_success: true, entity: response }, () => {
                this.props.updateState(response, this.refresh)

                if (action === 'stop_timer') {
                    this.stopTimer()
                }

                if (action === 'start_timer' || action === 'resume_timer') {
                    this.startTimer()
                }
            })

            setTimeout(
                function () {
                    this.setState({ show_success: false })
                }
                    .bind(this),
                2000
            )
        })
    }

    loadPdf () {
        axios.post('/api/preview', {
            entity: 'Task',
            entity_id: this.state.entity.id
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
                var blob = new Blob([view], { type: 'application/pdf' })
                var url = URL.createObjectURL(blob)

                /* const file = new Blob (
                 [ response.data.data ],
                 { type: 'application/pdf' } ) */
                // const fileURL = URL.createObjectURL ( file )

                this.setState({ obj_url: url }, () => URL.revokeObjectURL(url))
            })
            .catch((error) => {
                alert(error)
            })
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab }, () => {
                if (this.state.activeTab === '3') {
                    this.loadPdf()
                }
            })
        }
    }

    render () {
        const task_rate = this.state.entity.calculated_task_rate && this.state.entity.calculated_task_rate > 0 ? this.state.entity.calculated_task_rate : this.settings.task_rate
        const button1_action = !this.state.entity.invoice_id ? (e) => location.href = '/#/invoice?entity_type=task&entity_id=' + this.state.entity.id : (e) => this.toggleTab('6')
        const button1_label = !this.state.entity.invoice_id ? translations.new_invoice : translations.view_pdf

        return (
            <React.Fragment>
                <Nav tabs className="nav-justified disable-scrollbars">
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '1' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('1')
                            }}
                        >
                            {translations.details}
                        </NavLink>
                    </NavItem>
                    <NavItem>
                        <NavLink
                            className={this.state.activeTab === '2' ? 'active' : ''}
                            onClick={() => {
                                this.toggleTab('2')
                            }}
                        >
                            {translations.documents} ({this.taskModel.fileCount})
                        </NavLink>
                    </NavItem>
                </Nav>
                <TabContent activeTab={this.state.activeTab}>
                    <TabPane tabId="1">
                        <Overview customers={this.props.customers} model={this.taskModel}
                            lastTime={this.state.lastTime} totalTime={this.state.totalTime}
                            totalDuration={formatDuration(this.taskModel.duration)}
                            calculatedAmount={this.taskModel.calculateAmount(task_rate)}
                            entity={this.state.entity}/>
                    </TabPane>

                    <TabPane tabId="2">
                        <Row>
                            <Col>
                                <Card>
                                    <CardHeader>{translations.documents}</CardHeader>
                                    <CardBody>
                                        <FileUploads entity_type="Task" entity={this.state.entity}
                                            user_id={this.state.entity.user_id}/>
                                    </CardBody>
                                </Card>
                            </Col>
                        </Row>
                    </TabPane>

                    <TabPane tabId="3">
                        <Row>
                            <Col>
                                <Card>
                                    <CardHeader> {translations.pdf} </CardHeader>
                                    <CardBody>
                                        <iframe style={{ width: '400px', height: '400px' }}
                                            className="embed-responsive-item" id="viewer"
                                            src={this.state.obj_url}/>
                                    </CardBody>
                                </Card>
                            </Col>
                        </Row>
                    </TabPane>
                </TabContent>

                {this.state.show_success &&
                <Alert color="primary">
                    {translations.action_completed}
                </Alert>
                }

                <BottomNavigationButtons button1_click={button1_action}
                    button1={{ label: button1_label }}
                    button2_click={(e) => this.triggerAction((this.taskModel.isRunning) ? ('stop_timer') : ((!this.state.entity.timers || !this.state.entity.timers.length) ? ('start_timer') : ('resume_timer')))}
                    button2={{ label: (this.taskModel.isRunning) ? (translations.stop) : ((!this.state.entity.timers || !this.state.entity.timers.length) ? (translations.start) : (translations.resume)) }}/>
            </React.Fragment>
        )
    }
}
