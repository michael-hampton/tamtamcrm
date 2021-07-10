import ExpenseModel from '../../models/ExpenseModel'
import { consts } from '../../utils/_consts'

export default function convertExpenseToInvoiceItem (expense, line_item_only = false) {
    const expenseModel = new ExpenseModel(expense)
    const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
    const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
    const settings = user_account[0].account.settings

    const line_items = []
    const row = {}

    const line_item = {
        expense_id: parseInt(this.entity_id),
        unit_price: settings.inclusive_taxes === true ? expenseModel.convertedAmount : expenseModel.convertedNetAmount,
        quantity: settings.has_minimum_quantity === true ? 1 : null,
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
