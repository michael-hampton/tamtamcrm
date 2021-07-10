import { formatDate } from '../../common/FormatDate'
import TaskModel from '../../models/TaskModel'
import { consts } from '../../utils/_consts'

export default function convertTaskToInvoiceItem (task, project = null, line_item_only = false) {
    const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
    const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
    const settings = user_account[0].account.settings

    const task_rate = task.calculated_task_rate && task.calculated_task_rate > 0 ? task.calculated_task_rate : settings.task_rate
    let notes = task.description + '\n'
    const dates = []

    if (settings.include_times_on_invoice === true || settings.include_dates_on_invoice === true) {
        if (task.timers) {
            task.timers.filter(time => {
                return time.date.length && time.end_date.length
            }).map(time => {
                if (settings.include_times_on_invoice === true && settings.include_dates_on_invoice === true) {
                    const start = formatDate(`${time.date} ${time.start_time}`, true)
                    const end = formatDate(`${time.end_date} ${time.end_time}`, true)
                    notes += `\n ${start} - ${end}`
                } else if (settings.include_dates_on_invoice === true) {
                    const date = formatDate(time.date, false)
                    dates.push(date)
                } else {
                    const start = formatDate(`${time.date} ${time.start_time}`, true, false)
                    const end = formatDate(`${time.end_date} ${time.end_time}`, true, false)
                    notes += `\n ${start} - ${end}`
                }
            })
        }
    }

    if (settings.include_dates_on_invoice === true && settings.include_times_on_invoice === false) {
        notes += '\n' + dates.join('\n')
    }

    const taskModel = new TaskModel(task)
    const line_items = []
    const row = {}

    const line_item = {
        task_id: parseInt(task.id),
        unit_price: taskModel.calculateAmount(task_rate),
        quantity: Math.round(task.duration, 3),
        type_id: consts.line_item_task,
        notes: notes,
        description: notes
    }

    if (line_item_only) {
        return line_item
    }

    line_items.push(line_item)

    row.customer_id = task.customer_id
    row.project = project
    row.line_items = line_items

    return row
}
