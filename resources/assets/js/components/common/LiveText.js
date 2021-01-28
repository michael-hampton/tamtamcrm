import React, { Component } from 'react'
import { convertTimeToSeconds, formatSecondsToTime } from '../utils/_formatting'

export default class LiveText extends Component {
    constructor (props, context) {
        super(props, context)

        this.state = {
            duration: 0
        }

        this.startTimer = this.startTimer.bind(this)
    }

    componentDidMount () {
        this.startTimer()
    }

    startTimer () {
        this.setState({
            duration: !this.props.duration && this.props.task_automation_enabled ? formatSecondsToTime(1) : this.props.duration
        })

        this.timer = setInterval(() => {
            const seconds = convertTimeToSeconds(this.state.duration) + 1
            this.setState({
                duration: formatSecondsToTime(seconds)
            })
        }, 1000)
    }

    render () {
        return (
            <span>{this.state.duration}</span>
        )
    }
}
