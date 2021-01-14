import React, { Component } from 'react'
import TaskTimeInputs from './TaskTimeInputs'
import moment from 'moment'
import TimerModel from '../../models/TimerModel'

export default class TaskTimeDesktop extends Component {
    constructor (props) {
        super(props)

        this.state = {
            timers: this.props.timers && this.props.timers.length ? this.props.timers : [{
                id: Date.now(),
                date: moment(new Date()).format('YYYY-MM-DD'),
                start_time: moment().format('HH:MM:ss'),
                end_date: moment(new Date()).format('YYYY-MM-DD'),
                end_time: moment().add('1', 'hour').format('HH:MM:ss'),
                duration: null
            }]
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

    handleChange (e) {
        const value = e.target.value

        if (!value || !value.length) {
            return true
        }

        const times = this.timerModel.addDuration(this.state.currentIndex, value)
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
