import axios from 'axios'
import moment from 'moment'
import BaseModel from './BaseModel'

const TaskTimeItem = {
    id: Date.now(),
    date: moment(new Date()).format('YYYY-MM-DD'),
    start_time: '',
    end_time: '',
    //start_time: moment().format('HH:MM:ss'),
    end_date: moment(new Date()).format('YYYY-MM-DD'),
    //end_time: moment().add('1', 'hour').format('HH:MM:ss')
}

export default class TimerModel extends BaseModel {
    constructor (data = null, customers) {
        super()

        this._url = '/api/timer'
        this._time_log = []

        this._fields = {}

        if (data !== null) {
            this._fields = { ...this.fields, ...data }
        }
    }

    get url () {
        return this._url
    }

    get time_log () {
        return this._time_log
    }

    set time_log (time_log) {
        this._time_log = time_log
    }

    addTaskTime () {
        const newArray = this.time_log.slice()
        newArray.push(TaskTimeItem)
        this.time_log = newArray
        return newArray
    }

    addDuration (index, value) {
        const data = [...this.time_log]

        if (!data[index]) {
            data[index] = {}
        }

        const [hh = '0', mm = '0', ss = '0'] = (value || '0:0:0').split(':')
        const hour = parseInt(hh, 10) || 0
        const minute = parseInt(mm, 10) || 0
        const second = parseInt(ss, 10) || 0
        const seconds = (hour * 3600) + (minute * 60) + (second)

        const time = data[index].start_time && data[index].start_time.toString().length ? moment(data[index].start_time, 'HH:mm:ss') : moment(new Date()).subtract(seconds, 'seconds')
        time.add(seconds, 'seconds')
        data[index].end_time = time.format('HH:mm:ss')

        if (!data[index].start_time || !data[index].start_time.toString().length) {
            data[index].start_time = moment(new Date()).subtract(seconds, 'seconds').format('HH:mm:ss')
            data[index].date = moment(new Date()).format('YYYY-MM-DD')
            data[index].end_date = moment(new Date()).format('YYYY-MM-DD')
        }

        this.time_log = data

        console.log('time log', this.time_log)

        return data
    }

    updateTaskTime (index, field, value, update_end_time = false) {
        const data = [...this.time_log]
        data[index][field] = value

        if (update_end_time) {
            data[index].end_date = value
        }

        this.time_log = data
        return data
    }

    deleteTaskTime (index) {
        const array = [...this.time_log] // make a separate copy of the array
        array.splice(index, 1)
        this.time_log = array
        return array
    }

    calculateAmount (taskRate) {
        const total_duration = this.duration

        if (!total_duration) {
            return 0
        }

        const duration = taskRate * total_duration
        return Math.round(duration, 3)
    }

    getTotalDuration () {
        let seconds = 0
        this.fields.timers.map(timer => {
            seconds += this.calculateDuration(timer.start_time, timer.end_time, true)
        })

        return seconds
    }

    calculateDuration (currentStartTime, currentEndTime, returnAsSeconds = false) {
        const startTime = moment(currentStartTime, 'hh:mm:ss a')
        let endTime = ''

        if (currentEndTime.length) {
            endTime = moment(currentEndTime, 'hh:mm:ss a')
            const hours = (endTime.diff(startTime, 'hours'))
            const totalHours = ('0' + hours).slice(-2)
            const totalMinutes = endTime.diff(startTime, 'minutes')
            const minutes = totalMinutes % 60
            const clearMinutes = ('0' + minutes).slice(-2)

            if (returnAsSeconds === true) {
                const duration = parseFloat(hours + '.' + minutes)
                return duration * 3600
            }

            return `${totalHours}:${clearMinutes}`
        }

        return ''
    }

    async timerAction (data) {
        try {
            this.errors = []
            this.error_message = ''
            const res = await axios.post(`${this._url}/${this.fields.id}`, data)

            if (res.status === 200) {
                // test for status you want, etc
                console.log(res.status)
            }
            // Don't forget to return something
            return res.data
        } catch (e) {
            this.handleError(e)
            return false
        }
    }
}
