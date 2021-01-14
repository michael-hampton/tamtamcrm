import React, { Component } from 'react'
import { Progress } from 'reactstrap'

export default class AboutModal extends Component {
    constructor (props) {
        super(props)
        this.state = {
            percent: 0
        }

        this.toggle = this.toggle.bind(this)
    }

    toggle () {
        timerId = setInterval(function () {
            // increment progress bar
            percent += 5
            this.setState({ percent: percent })

            // complete
            if (percent >= 100) {
                clearInterval(timerId)
                this.setState({ percent: 0 })
            }
        }, 200)
    }

    render () {
        <Progress value={this.state.percent}/>
    }
}
