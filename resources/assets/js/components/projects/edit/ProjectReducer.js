import convertTaskToInvoiceItem from '../../tasks/edit/TaskReducer'

function _sortTaskByTime (task) {
    task.timers.sort(function (a, b) {
        // Turn your strings into dates, and then subtract them
        // to get a value that is either negative, positive, or zero.
        return new Date(b.date) - new Date(a.date)
    })

    return task.timers[0]
}

function _checkTimers (task) {
    const filtered = task.timers && task.timers.length ? task.timers.filter(timer => timer.end_date.toString().length) : []

    return filtered.length > 0
}

export default function convertProjectToInvoiceItem (project, line_item_only = false) {
    console.log('project tasks', project.tasks)

    const filtered_tasks = project.tasks && project.tasks.length ? project.tasks.filter(task => !task.deleted_at && !task.invoice_id && _checkTimers(task)) : []

    if (!filtered_tasks.length) {
        return []
    }

    const sorted_tasks = filtered_tasks.sort(function (a, b) {
        // Turn your strings into dates, and then subtract them
        // to get a value that is either negative, positive, or zero.
        return _sortTaskByTime(a) - _sortTaskByTime(b)
    })

    const line_items = []

    sorted_tasks.forEach(task => {
        const line_item = convertTaskToInvoiceItem(task, project, true)
        line_items.push(line_item)
    })

    const row = {}

    if (line_item_only) {
        return line_items
    }
    row.customer_id = project.customer_id
    row.line_items = line_items

    return row
}
