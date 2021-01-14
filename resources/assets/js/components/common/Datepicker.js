import React, { Component } from 'react'
import moment from 'moment'
import 'react-datepicker/dist/react-datepicker.css'
import MomentUtils from '@date-io/moment'
import { KeyboardDatePicker, MuiPickersUtilsProvider } from '@material-ui/pickers'

export default class Datepicker extends Component {
    constructor (props) {
        super(props)
        this.state = {
            roles: [],
            modal: false
        }

        this.handleDateChange = this.handleDateChange.bind(this)
    }

    handleDateChange (date) {
        const formatted_date = !date || date === 'undefined' || date === '' ? '' : moment(date).format('YYYY-MM-DD')
        const e = {}
        e.target = {
            name: this.props.name,
            value: formatted_date
        }

        this.props.handleInput(e)
    }

    render () {
        const date = !this.props.date || this.props.date === 'undefined' || this.props.date === '' ? moment(new Date()).add(1, 'days').format('YYYY-MM-DD') : this.props.date
        const class_name = this.props.className === '' ? 'form-control' : this.props.className
        return (
            <MuiPickersUtilsProvider libInstance={moment} utils={MomentUtils}>
                <KeyboardDatePicker
                    margin="normal"
                    id={this.props.name}
                    format="MMMM DD, YYYY"
                    value={moment(date).format('YYYY-MM-DD')}
                    onChange={this.handleDateChange}
                    KeyboardButtonProps={{
                        'aria-label': 'change date'
                    }}
                />
            </MuiPickersUtilsProvider>
        )
    }
}
