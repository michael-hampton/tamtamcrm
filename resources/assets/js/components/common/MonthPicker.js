import React, { Component } from 'react'
import Picker from 'react-month-picker'
import MonthBox from './MonthBox'

export default class MonthPicker extends Component {
    constructor (props, context) {
        super(props, context)

        this.state = {
            mvalue: { year: 2014, month: 11 },
            mvalue2: { year: 2016, month: 7 },
            mrange: {
                from: { year: this.props.start_year, month: this.props.start_month },
                to: { year: this.props.end_year, month: this.props.end_month }
            },
            mrange2: { from: { year: 2013, month: 11 }, to: { year: 2016, month: 3 } }
        }

        this.handleClickMonthBox = this.handleClickMonthBox.bind(this)
        this.handleAMonthChange = this.handleAMonthChange.bind(this)
        this.handleAMonthDissmis = this.handleAMonthDissmis.bind(this)

        this.handleClickMonthBox2 = this.handleClickMonthBox2.bind(this)
        this.handleAMonthChange2 = this.handleAMonthChange2.bind(this)
        this.handleAMonthDissmis2 = this.handleAMonthDissmis2.bind(this)

        this._handleClickRangeBox = this._handleClickRangeBox.bind(this)
        this.handleRangeChange = this.handleRangeChange.bind(this)
        this.handleRangeDissmis = this.handleRangeDissmis.bind(this)

        this._handleClickRangeBox2 = this._handleClickRangeBox2.bind(this)
        this.handleRangeChange2 = this.handleRangeChange2.bind(this)
        this.handleRangeDissmis2 = this.handleRangeDissmis2.bind(this)
    }

    componentWillReceiveProps (nextProps) {
        this.setState({
            value: nextProps.value || 'N/A'
        })
    }

    render () {
        const previousYear = new Date()
        previousYear.setFullYear(previousYear.getFullYear() - 1)

        const pickerLang = {
            months: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            from: 'From',
            to: 'To'
        }
        const mvalue = this.state.mvalue
        const mrange = this.state.mrange

        const makeText = m => {
            if (m && m.year && m.month) return (pickerLang.months[m.month - 1] + '. ' + m.year)
            return '?'
        }

        return (
            <ul className="mt-2 mb-0">
                <li>
                    {/* <label><b>Pick A Span of Months</b></label> */}
                    <div className="edit">
                        <Picker
                            ref="pickRange"
                            years={{
                                min: {
                                    year: previousYear.getFullYear(),
                                    month: 1
                                },
                                max: {
                                    year: new Date().getFullYear(),
                                    month: 12
                                }
                            }}
                            range={mrange}
                            lang={pickerLang}
                            theme="dark"
                            onChange={this.handleRangeChange}
                            onDismiss={this.handleRangeDissmis}
                        >
                            <MonthBox
                                value={makeText(mrange.from) + ' ~ ' + makeText(mrange.to)}
                                onClick={this._handleClickRangeBox}/>
                        </Picker>
                    </div>
                </li>
            </ul>
        )
    }

    handleClickMonthBox (e) {
        this.refs.pickAMonth.show()
    }

    handleAMonthChange (year, month) {
        /* var firstDay = new Date(year, (month - 1), 1)
        var lastDay = new Date(year, month, 0)
        const obj = {year: year, month}

        this.setState({mvalue: {year: year, month: month}} () => {
            this.props.onChange(firstDay, lastDay)
        }) */
    }

    handleAMonthDissmis (value) {
        this.setState({ mvalue: value })
    }

    handleClickMonthBox2 (e) {
        this.refs.pickAMonth2.show()
    }

    handleAMonthChange2 (value, text) {
        //
    }

    handleAMonthDissmis2 (value) {
        this.setState({ mvalue2: value })
    }

    _handleClickRangeBox (e) {
        this.refs.pickRange.show()
    }

    handleRangeChange (value, text, listIndex) {
        if (listIndex === 0) {
            const firstDay = new Date(value, (text - 1), 1)
            const dateObj = { start_date: firstDay }
            const newState = {
                ...this.state.mrange,
                from: { year: value, month: text }
            }

            this.setState({ mrange: newState }, () => {
                this.props.onChange(dateObj)
            })
            return
        }

        const lastDay = new Date(value, text, 0)
        const dateObj = { end_date: lastDay }

        const newState = {
            ...this.state.mrange,
            to: { year: value, month: text }
        }
        this.setState({ mrange: newState }, () => {
            this.props.onChange(dateObj)
        })
    }

    handleRangeDissmis (value) {
        this.setState({ mrange: value })
    }

    _handleClickRangeBox2 (e) {
        this.refs.pickRange2.show()
    }

    handleRangeChange2 (value, text, listIndex) {
        //
    }

    handleRangeDissmis2 (value) {
        this.setState({ mrange2: value })
    }
}
