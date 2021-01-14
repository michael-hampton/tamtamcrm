import React from 'react'
import { Button, Col, FormGroup, Label, Row } from 'reactstrap'
import { translations } from '../../utils/_translations'
import { KeyboardDatePicker, MuiPickersUtilsProvider } from '@material-ui/pickers'
import moment from 'moment'
import MomentUtils from '@date-io/moment'
import TimePickerInput from '../../common/TimePickerInput'
import Duration from '../../common/Duration'

const TaskTimeInputs = (props) => {
    return (
        props.timers.map((val, idx) => {
            const end = props.timers[idx].end_date !== null ? moment(props.timers[idx].end_date + ' ' + props.timers[idx].end_time).format('YYYY-MM-DD hh:mm:ss a') : moment().format('YYYY-MM-DD HH:mm:ss a')
            const start = props.timers[idx].start_time !== null ? moment(props.timers[idx].date + ' ' + props.timers[idx].start_time).format('YYYY-MM-DD hh:mm:ss a') : 0

            return (
                <div key={idx}>
                    <Row form>
                        <Col md={3}>
                            <FormGroup className="desktop">
                                <Label>{translations.date}</Label>
                                <MuiPickersUtilsProvider libInstance={moment} utils={MomentUtils}>
                                    <KeyboardDatePicker
                                        margin="normal"
                                        id="date-picker-dialog"
                                        format="MMMM DD, YYYY"
                                        value={moment(props.timers[idx].date).format('YYYY-MM-DD')}
                                        onChange={(e) => {
                                            props.handleDateChange(e, idx)
                                        }}
                                        KeyboardButtonProps={{
                                            'aria-label': 'change date'
                                        }}
                                    />
                                </MuiPickersUtilsProvider>
                            </FormGroup>
                        </Col>
                        <Col md={3}>
                            <FormGroup>
                                <Label>{translations.start_time}</Label>
                                <TimePickerInput name="start_time" index={idx} value={props.timers[idx].start_time}
                                    setValue={props.handleTimeChange}/>
                            </FormGroup>
                        </Col>

                        <Col md={3}>
                            <FormGroup>
                                <Label>{translations.end_time}</Label>
                                <TimePickerInput name="end_time" index={idx} value={props.timers[idx].end_time}
                                    setValue={props.handleTimeChange}/>
                            </FormGroup>
                        </Col>

                        <Col md={3}>
                            <FormGroup>
                                <Label>{translations.duration} {props.model.calculateDuration(start, end)}</Label>
                                <Duration onChange={props.handleChange}/>
                            </FormGroup>
                        </Col>
                    </Row>

                    {idx !== 0 &&
                    <Button color="danger" onClick={() => props.removeLine(idx)}>{translations.remove}</Button>
                    }

                </div>
            )
        })
    )
}
export default TaskTimeInputs
