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
