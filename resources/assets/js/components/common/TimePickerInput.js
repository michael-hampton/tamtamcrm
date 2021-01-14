import React, { Component } from 'react'
import { Button, Dialog, DialogActions, IconButton, Input, InputAdornment } from '@material-ui/core'
import { TimePicker } from 'material-ui-time-picker'
import moment from 'moment'

export default class TimePickerInput extends Component {
    constructor (props) {
        super(props)
        this.state = {
            is_open: false
        }

        this.openDialog = this.openDialog.bind(this)
        this.closeDialog = this.closeDialog.bind(this)
        this.handleDialogTimeChange = this.handleDialogTimeChange.bind(this)
        this.handleKeyboardTimeChange = this.handleKeyboardTimeChange.bind(this)
    }

    openDialog () {
        this.setState({ is_open: true })
    }

    closeDialog () {
        this.setState({ is_open: false })
    }

    handleDialogTimeChange (newValue) {
        const hours = newValue
            .getHours()
            .toString()
            .padStart(2, '0')
        const minutes = newValue
            .getMinutes()
            .toString()
            .padStart(2, '0')
        const textValue = hours + ':' + minutes

        const e = {
            name: this.props.name,
            value: textValue,
            index: this.props.index
        }

        this.props.setValue(e)
    }

    handleKeyboardTimeChange (event) {
        const e = {
            name: this.props.name,
            value: event.target.value,
            index: this.props.index
        }

        this.props.setValue(e)
    }

    createDateFromTextValue (value) {
        const splitParts = value.split(':')
        return new Date(1970, 1, 1, splitParts[0], splitParts[1])
    }

    convert12HourFormat (timeString) {
        var hourEnd = timeString.indexOf(':')
        var H = +timeString.substr(0, hourEnd)
        var h = H % 12 || 12
        var ampm = H < 12 ? 'AM' : 'PM'
        timeString = h + timeString.substr(hourEnd, 3) + ' ' + ampm

        return timeString
    }

    render () {
        const value = (!this.props.value || !this.props.value.length) && this.props.name === 'end_time' ? moment().format('hh:mm') : this.props.value
        // value = this.convert12HourFormat(value)

        return (
            <div>
                <Input
                    value={value}
                    onChange={this.handleKeyboardTimeChange}
                    endAdornment={
                        <InputAdornment position="end">
                            <IconButton onClick={this.openDialog}>
                                <i className="fa fa-clock-o"/>
                            </IconButton>
                        </InputAdornment>
                    }
                />
                <Dialog maxWidth="xs" open={this.state.is_open}>
                    <TimePicker
                        ampm
                        value={this.createDateFromTextValue(value)}
                        onChange={this.handleDialogTimeChange}
                    />
                    <DialogActions>
                        <Button onClick={this.closeDialog} color="primary">
                            Cancel
                        </Button>
                        <Button onClick={this.closeDialog} color="primary">
                            Ok
                        </Button>
                    </DialogActions>
                </Dialog>
            </div>
        )
    }
}
