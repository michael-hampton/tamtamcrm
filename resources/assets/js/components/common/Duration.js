import React, { Component } from 'react'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../utils/_translations'
import moment from 'moment'

export default class Duration extends Component {
    constructor (props) {
        super(props)

        this.state = {
            placeholder: ''
        }
    }

    render () {
        let hours, minutes
        const options = []

        for (var i = 0; i <= 120; i += 15) {
            if (i === 0) {
                options.push(<option value="">{translations.change_duration}</option>)
                continue
            }

            hours = Math.floor(i / 60)
            minutes = i % 60
            let formatted_minutes = minutes

            if (minutes < 10) {
                formatted_minutes = '0' + minutes // adding leading zero
            }

            if (hours > 0) {
                minutes += 60 * hours
                console.log('minutes', minutes)
            }

            options.push(<option value={minutes}>{hours + ':' + formatted_minutes}</option>)
        }

        return <FormGroup>
            <input onClick={(e) => {
                const input = document.getElementById('duration')
                input.value = ''
                input.click()
            }} placeholder={this.state.placeholder} id="duration" className="form-control custom-select custom-select-sm" type="text" autoComplete="off" list="durations" onChange={(e) => {
                this.props.onChange(e)
                this.setState({ placeholder: e.target.value })
            }}/>
            <datalist onClick={(e) => {
            }} onChange={(e) => {
                this.props.onChange(e)
                this.setState({ placeholder: e.target.value })
            }} id="durations">
                {options}
            </datalist>
            {/* <Input value={this.props.value} onChange={this.props.onChange} type="select"> */}
            {/*    {options} */}
            {/* </Input> */}
        </FormGroup>
    }
}
