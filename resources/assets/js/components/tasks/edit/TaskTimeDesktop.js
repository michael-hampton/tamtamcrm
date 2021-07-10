import React, { Component } from 'react'
import TaskTimeInputs from './TaskTimeInputs'
import moment from 'moment'
import TimerModel from '../../models/TimerModel'

export default class TaskTimeDesktop extends Component {
    constructor (props) {
        super(props)

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        const settings = user_account[0].account.settings

        const timer = settings.task_automation_enabled === true ? [{
            id: Date.now(),
            date: moment(new Date()).format('YYYY-MM-DD'),
            end_date: '',
            start_time: '',
            end_time: '',
            // start_time: moment().format('HH:MM:ss'),
            // end_date: moment(new Date()).format('YYYY-MM-DD'),
            // end_time: moment().add('1', 'hour').format('HH:MM:ss'),
            duration: null
        }] : []

        this.state = {
            timers: this.props.timers && this.props.timers.length ? this.props.timers : timer
        }

        this.timerModel = new TimerModel()
        this.timerModel.time_log = this.state.timers

        this.handleChange = this.handleChange.bind(this)
        this.handleDateChange = this.handleDateChange.bind(this)
        this.handleTimeChange = this.handleTimeChange.bind(this)
        this.addTaskTime = this.addTaskTime.bind(this)
        this.handleDelete = this.handleDelete.bind(this)
    }

    handleTimeChange (e) {
        const times = this.timerModel.updateTaskTime(e.index, e.name, e.value)
        this.setState({ times: times }, () => {
            this.props.handleTaskTimeChange(times)
        })
        console.log('times', times)
        console.log('time', e.value)
    }

    handleDateChange (date, index) {
        const times = this.timerModel.updateTaskTime(index, 'date', moment(date).format('YYYY-MM-DD'), true)
        this.setState({ timers: times }, () => {
            this.props.handleTaskTimeChange(times)
        })
        console.log('times', times)
    }

    handleChange (e, text = false) {
        const value = e.target.value

        if (!value || !value.length) {
            return true
        }

        const times = this.timerModel.addDuration(this.state.currentIndex || 0, value)
        this.setState({ timers: times }, () => {
            this.props.handleTaskTimeChange(times)
        })
        console.log('times', times)
    }

    handleDelete (idx) {
        const times = this.timerModel.deleteTaskTime(idx)

        this.setState({
            timers: times
        }, () => {
            this.props.handleTaskTimeChange(times)
        })
    }

    addTaskTime () {
        const times = this.timerModel.addTaskTime()

        this.setState({ timers: times }, () => {
            this.props.handleTaskTimeChange(times)
        })
    }

    render () {
        const { timers } = this.state

        return (
            <form>
                <TaskTimeInputs
                    model={this.props.model}
                    handleDateChange={this.handleDateChange}
                    handleChange={this.handleChange} timers={timers}
                    handleTimeChange={this.handleTimeChange}
                    removeLine={this.handleDelete}
                    addLine={this.addTaskTime}/>

                <button style={{ borderRadius: '20px' }} className="btn btn-primary pull-right"
                    onClick={this.addTaskTime}>+
                </button>
            </form>
        )
    }
}
