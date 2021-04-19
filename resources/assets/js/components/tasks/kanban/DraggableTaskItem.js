import axios from 'axios'
import React, { Component } from 'react'
import { Input, Button, FormGroup, Label, UncontrolledTooltip } from 'reactstrap'
import { Draggable } from 'react-beautiful-dnd'
import { translations } from '../../utils/_translations'
import TaskModel from '../../models/TaskModel'
import DealModel from '../../models/DealModel'
import LeadModel from '../../models/LeadModel'
import LiveText from '../../common/LiveText'
import formatDuration, { formatSecondsToTime } from '../../utils/_formatting'
import { icons } from '../../utils/_icons'

export default class DraggableTaskItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            entity: props.item,
            show_edit: false,
            name: props.item.name,
            description: props.item.description
        }

        console.log('task', props.item)

        this.taskModel = new TaskModel(props.item)

        this.saveTask = this.saveTask.bind(this)
        this.startTimer = this.startTimer.bind(this)
        this.stopTimer = this.stopTimer.bind(this)
        this.triggerAction = this.triggerAction.bind(this)
        this.refresh = this.refresh.bind(this)
    }

    componentDidMount () {
        if (this.taskModel.isRunning && this.state.entity.timers && this.state.entity.timers.length) {
            // this.startTimer()
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
                this.refresh(response)
                // this.props.updateState(response, this.refresh)

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

    saveTask () {
        let model

        switch (this.props.type) {
            case 'task':
                model = new TaskModel(this.state.entity)
                break

            case 'deal':
                model = new DealModel(this.state.entity)
                break

            case 'lead':
                model = new LeadModel(this.state.entity)
                break
        }

        model.update({
            name: this.state.name,
            description: this.state.description,
            due_date: this.state.entity.due_date
        }).then(response => {
            if (!response) {
                this.setState({ errors: model.errors, message: model.error_message })
                return
            }

            this.setState({ show_edit: false })
        })
    }

    render () {
        const customer = this.props.customers.filter(customer => customer.id === this.state.entity.customer_id)
        const project = this.state.entity.project_id && this.state.entity.project_id.toString().length ? this.state.entity.project : null
        const timer_display = this.state.entity.timers.length && this.taskModel.isRunning
            ? <span>
                {translations.duration + ' '}
                <LiveText
                    duration={formatSecondsToTime(this.taskModel.calculateDurationFromDatabase(true))}
                    task_automation_enabled={this.taskModel.autoStartTask}/>
            </span> : <span>{formatDuration(this.taskModel.duration)}</span>

        const { item, index } = this.props

        return (
            <Draggable
                key={item.id}
                draggableId={item.id}
                index={index}
            >
                {(provided, snapshot) => {
                    return (
                        <div
                            ref={provided.innerRef}
                            {...provided.draggableProps}
                            {...provided.dragHandleProps}
                            style={{
                                userSelect: 'none',
                                padding: 16,
                                margin: '0 0 8px 0',
                                minHeight: '50px',
                                backgroundColor: snapshot.isDragging
                                    ? '#263B4A'
                                    : '#456C86',
                                color: 'white',
                                ...provided.draggableProps.style
                            }}
                        >
                            <div className="d-flex justify-content-between">
                                <a style={{ padding: '12px' }}
                                    onClick={(e) => {
                                        this.props.toggleViewedEntity(null, null, false, item)
                                    }}>
                                    {project !== null &&
                                        <React.Fragment>
                                            <UncontrolledTooltip placement="right" target="projectTooltip">
                                                {project.name}
                                            </UncontrolledTooltip>
                                            <i id="projectTooltip" style={{ fontSize: '18px' }} className={`fa ${icons.project} mr-3`}/>
                                        </React.Fragment>

                                    }
                                    {item.name} {customer[0].name}</a>

                                <a onClick={(e) => {
                                    this.setState({ show_edit: !this.state.show_edit })
                                }}>{this.state.show_edit === true ? translations.hide : translations.edit}</a>
                            </div>

                            <div className={this.state.show_edit === true ? 'd-block' : 'd-none'}>
                                <FormGroup className="mb-2">
                                    <Label>{translations.name}</Label>
                                    <Input type="text" name="name" value={this.state.name} onChange={(e) => {
                                        this.setState({ name: e.target.value })
                                    }}/>
                                </FormGroup>

                                <FormGroup>
                                    <Label>{translations.description}</Label>
                                    <Input type="textarea" name="description" value={this.state.description}
                                        onChange={(e) => {
                                            this.setState({ description: e.target.value })
                                        }}/>
                                </FormGroup>

                                <Button color="primary" onClick={this.saveTask}>{translations.save}</Button>
                            </div>

                            <div className="d-flex justify-content-between">
                                {timer_display}

                                <Button onClick={(e) => this.triggerAction((this.taskModel.isRunning) ? ('stop_timer') : ((!this.state.entity || !this.state.entity.length) ? ('start_timer') : ('resume_timer')))}>
                                    {(this.taskModel.isRunning) ? (translations.stop) : ((!this.state.entity.timers || !this.state.entity.timers.length) ? (translations.start) : (translations.resume)) }
                                </Button>
                            </div>
                        </div>
                    )
                }}
            </Draggable>
        )
    }
}
