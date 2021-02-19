import { formatDate } from '../common/FormatDate'
import { consts } from '../utils/_consts'
import TaskModel from '../models/TaskModel'
import ExpenseModel from '../models/ExpenseModel'
import ProjectModel from '../models/ProjectModel'
import convertProjectToInvoiceItem from '../projects/edit/ProjectReducer'
import convertTaskToInvoiceItem from '../tasks/edit/TaskReducer'
import convertExpenseToInvoiceItem from '../expenses/edit/ExpenseReducer'

export default class InvoiceReducer {
    constructor (entity_id, entity_type) {
        this.entity_id = entity_id
        this.entity_type = entity_type
    }

    build (entity_type, data) {
        switch (entity_type) {
            case 'expense':
                return convertExpenseToInvoiceItem(data)
            case 'task':
                return convertTaskToInvoiceItem(data)
            case 'project':
                return convertProjectToInvoiceItem(data)
        }
    }
}
