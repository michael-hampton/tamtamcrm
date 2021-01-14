/* eslint-disable no-unused-vars */
import React from 'react'

class ElapsedTime extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            secondsElapsed: '',
            currentStartTime: this.props.currentStartTime,
            date: this.props.date,
            errors: [],
            subtasks: [],
            visible: 'collapse'
        }

        this.tick = this.tick.bind(this)
    }

    componentDidMount () {
        this.tick()
        this.interval = setInterval(this.tick, 1000)
    }

    tick () {
        const formattedDate = new Date(this.state.currentStartTime).getTime() / 1000
        const now = new Date().getTime() / 1000
        const elapsedSeconds = (Date.now() / 1000) - formattedDate
        this.setState({ secondsElapsed: elapsedSeconds })
    }

    render () {
        return this.props.model.formatTime(this.state.secondsElapsed)
    }
}

export default ElapsedTime
