import React, { Component } from 'react'
import moment from 'moment'
import axios from 'axios'
import { Button, Card, CardBody, CardHeader, FormGroup, Label } from 'reactstrap'
import SuccessMessage from '../../common/SucessMessage'
import ErrorMessage from '../../common/ErrorMessage'
import { translations } from '../../utils/_translations'
import TimePickerInput from '../../common/TimePickerInput'
import Duration from '../../common/Duration'
import { KeyboardDatePicker, MuiPickersUtilsProvider } from '@material-ui/pickers'
import MomentUtils from '@date-io/moment'
import TimerModel from '../../models/TimerModel'
import TaskTimeItem from '../../common/entityContainers/TaskTimeItem'
import TaskModel from '../../models/TaskModel'

class EditTaskTimes extends Component {
    constructor (props, context) {
        super(props, context)
        this.state = {
            entity: this.props.entity,
            currentIndex: null,
            showSuccess: false,
            showError: false,
            times: this.props.timers,
            visible: 'collapse',
            dropdownOpen: false,
            dropdown2Open: true,
            totalOn: false,
            totalStart: 0,
            totalTime: 0,
            lastOn: false,
            lastStart: 0,
            lastTime: 0
        }

        this.timerModel = new TimerModel()
        this.timerModel.time_log = this.state.times
        this.handleSlideClick = this.handleSlideClick.bind(this)
        this.closeForm = this.closeForm.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleDateChange = this.handleDateChange.bind(this)
        this.handleDelete = this.handleDelete.bind(this)
        this.handleSave = this.handleSave.bind(this)
        this.handleTimeChange = this.handleTimeChange.bind(this)
        this.addTaskTime = this.addTaskTime.bind(this)
        this.startTimer = this.startTimer.bind(this)
        this.stopTimer = this.stopTimer.bind(this)
        this.triggerAction = this.triggerAction.bind(this)

        this.model = this.props.model
    }

    componentDidMount () {
        if (this.model.isRunning && this.state.entity.timers && this.state.entity.timers.length) {
            this.startTimer()
        }
    }

    handleSlideClick (index) {
        this.setState({
            currentIndex: index || this.state.currentIndex,
            dropdownOpen: !this.state.dropdownOpen,
            dropdown2Open: !this.state.dropdown2Open
        })
    }

    closeForm () {
        this.setState({
            currentIndex: null,
            dropdownOpen: !this.state.dropdownOpen,
            dropdown2Open: !this.state.dropdown2Open
        })
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
            })
        }, 1000)
    }

    stopTimer () {
        this.setState({ totalOn: false })
        clearInterval(this.timer)
    }

    triggerAction (action) {
        this.model.completeAction(this.state.entity, action).then(response => {
            this.model = new TaskModel(response)

            this.setState({ show_success: true, entity: response }, () => {
                if (action === 'stop_timer') {
                    this.stopTimer()
                }

                if (action === 'start_timer' || action === 'resume_timer') {
                    this.startTimer()
                }
            })
        })
    }

    handleTimeChange (e) {
        const times = this.timerModel.updateTaskTime(e.index, e.name, e.value)
        this.setState({ times: times })
        console.log('times', times)
        console.log('time', e.value)
    }

    handleDateChange (date, index) {
        const times = this.timerModel.updateTaskTime(index, 'date', moment(date).format('YYYY-MM-DD'))
        this.setState({ times: times })
        console.log('times', times)
    }

    handleChange (e) {
        const value = e.target.value

        if (!value || !value.length) {
            return true
        }

        const times = this.timerModel.addDuration(this.state.currentIndex, value)
        this.setState({ times: times })
        console.log('times', times)
    }

    handleDelete (idx) {
        const times = this.timerModel.deleteTaskTime(idx)

        this.setState({
            times: times
        }, () => {
            this.closeForm()
            this.handleSave(true)
        })
    }

    addTaskTime () {
        const times = this.timerModel.addTaskTime()

        console.log('times', times)

        const entity = { ...this.state.entity }
        entity.timers = times

        console.log('entity', entity)

        this.setState({ entity: entity }, () => {
            this.model = new TaskModel(entity)
        })
    }

    handleSave (isDelete = false) {
        axios.post('/api/timer', {
            time_log: this.state.times,
            task_id: this.props.task_id
        })
            .then((response) => {
                this.setState({ showSuccess: true, showError: false })
                this.closeForm()
            })
            .catch((error) => {
                this.setState({
                    showSuccess: false,
                    showError: true,
                    err: error.response.data.errors
                })
            })
    }

    render () {
        const { model } = this.props
        const time_display = model.formatTime(this.state.lastTime)
        const last_timer = this.state.entity.timers[this.state.entity.timers.length - 1]

        const { currentIndex, times, showSuccess, showError } = this.state
        const timeList = this.state.entity.timers && this.state.entity.timers.length ? this.state.entity.timers.map((timer, index) => {
            const lastTime = timer.id === last_timer.id ? time_display : null
            return <TaskTimeItem show_edit={true} edit={() => this.handleSlideClick(index)} lastTime={lastTime}
                key={index} taskTime={timer}/>
        }) : <h2>{translations.no_timers}</h2>

        const showSuccessMessage = showSuccess === true ? <SuccessMessage message="Times updated successfully"/> : null
        const showErrorMessage = showError === true
            ? <ErrorMessage message="Times could not be updated successfully"/> : null

        const currentData = currentIndex !== null ? times[currentIndex] : null
        const end = currentData !== null ? currentData.end_date + ' ' + currentData.end_time : moment().format('YYYY-MM-DD HH:mm:ss')
        const start = currentData !== null ? currentData.date + ' ' + currentData.start_time : 0

        const form = currentData && Object.keys(currentData).length ? <React.Fragment>
            <FormGroup>
                <Label>{translations.date}</Label>

                <MuiPickersUtilsProvider libInstance={moment} utils={MomentUtils}>
                    <KeyboardDatePicker
                        margin="normal"
                        id="date-picker-dialog"
                        format="MMMM DD, YYYY"
                        value={moment(currentData.date).format('YYYY-MM-DD')}
                        onChange={(e) => {
                            this.handleDateChange(e, currentIndex)
                        }}
                        KeyboardButtonProps={{
                            'aria-label': 'change date'
                        }}
                    />
                </MuiPickersUtilsProvider>
            </FormGroup>

            <FormGroup>
                <Label>{translations.start_time}</Label>
                <TimePickerInput name="start_time" index={currentIndex} value={currentData.start_time}
                    setValue={this.handleTimeChange}/>
            </FormGroup>

            <FormGroup>
                <Label>{translations.end_time}</Label>
                <TimePickerInput name="end_time" index={currentIndex} value={currentData.end_time}
                    setValue={this.handleTimeChange}/>
            </FormGroup>

            <FormGroup>
                <Label>{translations.duration} {model.calculateDuration(start, end)}</Label>
                <Duration onChange={this.handleChange}/>
            </FormGroup>

            <Button className="mr-2" color="primary" onClick={this.handleSave}>{translations.done}</Button>
            <Button color="danger" onClick={() => this.handleDelete(currentIndex)}>{translations.remove}</Button>
        </React.Fragment> : null

        return (
            <React.Fragment>
                {showSuccessMessage}
                {showErrorMessage}
                <div className={`list-group ${this.state.dropdown2Open ? 'collapse show' : 'collapse'}`}>
                    {timeList}
                </div>

                <div className={this.state.dropdownOpen ? 'collapse show' : 'collapse'}>
                    <Card>
                        <CardHeader><i onClick={() => this.handleSlideClick(null)}
                            className="fa fa-chevron-left mr-2"/> Update</CardHeader>
                        <CardBody>
                            {form}
                        </CardBody>
                    </Card>
                </div>

                <button style={{ borderRadius: '20px' }} className="btn btn-primary pull-right"
                    onClick={(e) => this.triggerAction((this.model.isRunning) ? ('stop_timer') : ((!this.state.entity.timers || !this.state.entity.timers.length) ? ('start_timer') : ('resume_timer')))}>{(this.model.isRunning) ? (
                        <i className="fa fa-stop"/>) : ((!this.state.entity.timers || !this.state.entity.timers.length) ? (
                        <i className="fa fa-play"/>) : (<i className="fa fa-play"/>))}
                </button>

            </React.Fragment>
        )
    }
}

export default EditTaskTimes
