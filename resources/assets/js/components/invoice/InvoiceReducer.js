import { formatDate } from '../common/FormatDate'
import { consts } from '../utils/_consts'
import TaskModel from '../models/TaskModel'
import ExpenseModel from '../models/ExpenseModel'
import ProjectModel from '../models/ProjectModel'

export default class InvoiceReducer {
    constructor (entity_id, entity_type) {
        this.entity_id = entity_id
        this.entity_type = entity_type

        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.settings = user_account[0].account.settings
    }

    build (entity_type, data) {
        switch (entity_type) {
            case 'expense':
                return this.buildExpense(data)
            case 'task':
                return this.buildTask(data)
            case 'project':
                return this.buildProject(data)
        }
    }

    buildExpense (expense, line_item_only = false) {
        const expenseModel = new ExpenseModel(expense)

        const line_items = []
        const row = {}

        const line_item = {
            expense_id: parseInt(this.entity_id),
            unit_price: this.settings.inclusive_taxes === true ? expenseModel.convertedAmount : expenseModel.convertedNetAmount,
            quantity: this.settings.has_minimum_quantity === true ? 1 : null,
            type_id: consts.line_item_expense,
            notes: expense.category && Object.keys(expense.category).length ? expense.category.name : '',
            description: expense.category && Object.keys(expense.category).length ? expense.category.name : ''
        }

        if (line_item_only) {
            return line_item
        }

        line_items.push(line_item)

        row.customer_id = expense.customer_id
        row.line_items = line_items

        console.log('row', row)

        return row
    }

    buildProject (project, line_item_only = false) {
        const projectModel = new ProjectModel(project)

        const line_items = []
        const row = {}

        alert(this.entity_id)

        const line_item = {
            project_id: parseInt(this.entity_id),
            unit_price: project.task_rate,
            quantity: Math.round(project.budgeted_hours, 3),
            type_id: consts.line_item_project,
            notes: project.description || '',
            description: project.description || ''
        }

        if (line_item_only) {
            return line_item
        }

        line_items.push(line_item)

        row.customer_id = project.customer_id
        row.line_items = line_items

        console.log('row', row)

        return row
    }

    buildTask (task, line_item_only = false) {
        const task_rate = task.calculated_task_rate && task.calculated_task_rate > 0 ? task.calculated_task_rate : this.settings.task_rate
        let notes = task.description + '\n'

        if (this.settings.include_times_on_invoice === true) {
            if (task.timers) {
                task.timers.filter(time => {
                    return time.date.length && time.end_date.length
                }).map(time => {
                    const start = formatDate(`${time.date} ${time.start_time}`, true)
                    const end = formatDate(`${time.end_date} ${time.end_time}`, true)
                    notes += `\n ${start} - ${end}`
                })
            }
        }

        const taskModel = new TaskModel(task)
        const line_items = []
        const row = {}

        const line_item = {
            task_id: parseInt(this.entity_id),
            unit_price: taskModel.calculateAmount(task_rate),
            quantity: Math.round(task.duration, 3),
            type_id: consts.line_item_task,
            notes: notes
        }

        if (line_item_only) {
            return line_item
        }

        line_items.push(line_item)

        row.customer_id = task.customer_id
        row.line_items = line_items

        return row
    }
}
