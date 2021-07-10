export default function filterSearchResults (search_term, data, customers = [], companies = []) {
    const value = typeof search_term === 'string' ? search_term.toLowerCase() : search_term
    const found = []

    let myArrayFiltered = []

    if (customers && customers.length) {
        myArrayFiltered = data.filter((el) => {
            return customers.some((f) => {
                if (f.id === el.customer_id && f.name.toLowerCase().includes(value)) {
                    found.push(el.id)
                    return true
                }

                return false
            })
        }) || []
    }

    if (companies && companies.length) {
        const filteredCompanies = data.filter((el) => {
            return companies.some((f) => {
                if (f.id === el.customer_id && f.name.toLowerCase().includes(value)) {
                    found.push(el.id)
                    return true
                }

                return false
            })
        }) || []

        if (filteredCompanies.length) {
            myArrayFiltered.push(...filteredCompanies)
        }
    }

    const search_results = data.filter(obj => Object.keys(obj).some(key => obj[key] && obj[key].length && !found.includes(obj.id) ? obj[key].toString().toLowerCase().includes(value) : false)) || []
    myArrayFiltered.push(...search_results)

    return myArrayFiltered
}

export function filterStatuses (data, value, filters = null) {
    if (filters && filters.customer_id && filters.customer_id.toString().length) {
        data = data.filter(obj => parseInt(obj.customer_id) === parseInt(filters.customer_id)) || []
    }

    if (filters && filters.company_id && filters.company_id.toString().length) {
        data = data.filter(obj => parseInt(obj.company_id) === parseInt(filters.company_id)) || []
    }

    if (filters && filters.user_id && filters.user_id.toString().length) {
        data = data.filter(obj => parseInt(obj.assigned_to) === parseInt(filters.user_id)) || []
    }

    if (filters && filters.project_id && filters.project_id.toString().length) {
        data = data.filter(obj => parseInt(obj.project_id) === parseInt(filters.project_id)) || []
    }

    if (filters && filters.department_id && filters.department_id.toString().length) {
        data = data.filter(obj => parseInt(obj.department_id) === parseInt(filters.department_id)) || []
    }

    if (filters && filters.role_id && filters.role_id.toString().length) {
        data = data.filter(obj => parseInt(obj.role_id) === parseInt(filters.role_id)) || []
    }

    if (filters && filters.category_id && filters.category_id.toString().length) {
        data = data.filter(obj => parseInt(obj.category_id) === parseInt(filters.category_id)) || []
    }

    if (filters && filters.expense_category_id && filters.expense_category_id.toString().length) {
        data = data.filter(obj => parseInt(obj.expense_category_id) === parseInt(filters.expense_category_id)) || []
    }

    if (filters && filters.task_status_id && filters.task_status_id.toString().length) {
        data = data.filter(obj => parseInt(obj.task_status_id) === parseInt(filters.task_status_id)) || []
    }

    let status = 'active'

    if (filters.status_id && filters.status_id.toString().length) {
        status = filters.status_id
    } else if (filters.status && filters.status.toString().length) {
        status = filters.status
    }

    status = status.split(',')

    return data.filter(obj => {
        return status.some((status_id) => {
            switch (status_id) {
                case 'unapplied':
                    return obj.applied === 0
                case 'partially_applied':
                    return obj.applied > 0 && obj.applied < obj.amount
                case 'active':
                    return !obj.deleted_at
                case 'deleted':
                    return obj.hide === 1 || obj.hide === true
                case 'archived':
                    return obj.deleted_at && obj.deleted_at.toString().length && obj.hide === false
                default:
                    return parseInt(obj.status_id) === parseInt(status_id)
            }
        })
    })
}
