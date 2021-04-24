import React, { Component } from 'react'
import { convertTimeToSeconds, formatSecondsToTime } from '../utils/_formatting'

export default class LiveText extends Component {
    constructor (props, context) {
        super(props, context)

        this.state = {
            duration: !this.props.duration && this.props.task_automation_enabled ? formatSecondsToTime(1) : this.props.duration,
            interval: null
        }

        this.interval = null
    }

    componentDidMount () {
        this.interval = setInterval(this.timer.bind(this), 1000)
    }

    componentWillUnmount () {
        clearInterval(this.interval)
    }

    timer () {
        const seconds = convertTimeToSeconds(this.state.duration) + 1

        this.setState({
            duration: formatSecondsToTime(seconds)
        })

        if (this.state.duration < 1) {
            clearInterval(this.interval)
        }
    }

    render () {
        return (
            <span>{this.state.duration}</span>
        )
    }
}
