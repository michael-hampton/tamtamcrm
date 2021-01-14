import React, { Component } from 'react'
import 'react-dates/initialize'
import 'react-dates/lib/css/_datepicker.css'
import { DateRangePicker } from 'react-dates'

export default class DateFilter extends Component {
    constructor (props) {
        super(props)

        this.state = {
            startDate: null,
            endDate: null,
            focusedInput: null
        }

        this.onDatesChange = this.onDatesChange.bind(this)
    }

    onDatesChange ({ startDate, endDate }) {
        this.setState({
            startDate: startDate,
            endDate: endDate
        }, () => {
            if (this.state.startDate && this.state.endDate) {
                const startDate = this.state.startDate.format('YYYY-MM-DD')
                const endDate = this.state.endDate.format('YYYY-MM-DD')
                this.props.onChange({ start_date: startDate, end_date: endDate })

                /* const data = this.props.data.filter(item => {
                    item.created_at = moment(item.created_at).format('YYYY-MM-DD')
                    return item.created_at >= startDate && item.created_at <= endDate
                })

                this.setState({ data: data })
                this.props.update(data) */
            }
        })
    }

    render () {
        return (
            <DateRangePicker
                isOutsideRange={() => false}
                startDate={this.state.startDate} // momentPropTypes.momentObj or null,
                startDateId="your_unique_start_date_id" // PropTypes.string.isRequired,
                endDate={this.state.endDate} // momentPropTypes.momentObj or null,
                endDateId="your_unique_end_date_id" // PropTypes.string.isRequired,
                onDatesChange={this.onDatesChange}
                focusedInput={this.state.focusedInput}
                onFocusChange={(focusedInput) => {
                    this.setState({ focusedInput })
                }}
            />
        )
    }
}
