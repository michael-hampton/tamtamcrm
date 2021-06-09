import moment from 'moment'

export default function _formatData (myData, status, startDate, endDate, column, compare_column, doGrouping = true, dir = 'asc') {
    if (!myData.length) {
        return null
    }

    // sort by date
    myData = orderByDate(myData, dir)

    // get data for specified date range
    let filteredData = filterByDate(startDate, endDate, myData)

    if (!filteredData.length) {
        return null
    }

    // Calculate the sums and group data (while tracking count)
    if (doGrouping) {
        filteredData = groupDataByDate(filteredData, column, status, compare_column)
    } else {
        filteredData = groupByStatus(myData, status, compare_column)
    }

    const avgs = Object.keys(filteredData).length ? _getAverages(Object.values(filteredData)) : {
        avg: 0,
        value: 0,
        pct: 0
    }

    return { ...{ data: filteredData }, ...avgs }
}

export function _removeNullValues (array, column) {
    return array.filter(e => e[column] !== null && e[column] !== '')
}

export function orderByDate (array, dir) {
    return array.sort(function (a, b) {
        const dateA = new Date(a.created_at)
        const dateB = new Date(b.created_at)

        return dir === 'asc' ? dateA - dateB : dateB - dateA // sort by
        // date
        // ascending
    })
}

export function groupByStatus (array, status, compare_column) {
    return array.filter(e => e[compare_column] === status)
}

export function groupDataByDate (array, column, status, compare_column) {
    return array.reduce(function (m, d) {
        if (status !== null && d[compare_column] !== status) {
            return m
        }

        const date = moment(d.created_at).format('DD')

        if (!m[date]) {
            m[date] = parseFloat(d[column])
            return m
        }
        m[date] += parseFloat(d[column])
        return m
    }, {})
}

export function filterByDate (startDate, endDate, array) {
    startDate = new Date(startDate)
    endDate = new Date(endDate)

    // return matches for date range
    return array.filter(function (a) {
        const date = new Date(a.created_at)
        return date >= startDate && date <= endDate
    })
}

export function _filterOverdue (array) {
    const today = new Date()
    return array.filter((item) => {
        return new Date(item.due_date) < today
    })
}

export function _makeLabels (currentMoment, endMoment) {
    const dates = []
    while (currentMoment.isBefore(endMoment, 'day')) {
        currentMoment.add(1, 'days')
        dates.push(currentMoment.format('DD'))
    }

    return dates
}

export function _groupByStatus (array, status, compare_column) {
    return array.filter(e => e[compare_column] === status)
}

export function _getLast30Days (array) {
    const last_date = new Date()
    last_date.setDate(last_date.getDate() - 30)

    return array.filter((item) => {
        return new Date(item.created_at) > last_date && !item.deleted_at
    })
}

function _calculateAverage (array) {
    if (!array.length) {
        return 0
    }

    return Math.round(array.reduce((a, b) => (a + b)) / array.length * 100 + Number.EPSILON) / 100
}

export function _getAverages (array) {
    const avg = _calculateAverage(array)

    console.log('array', array)

    const totals = _calculateTotals(array)
    const pct = _calculatePercentage(avg, totals)

    return {
        avg: Math.round(avg),
        value: totals,
        pct: Math.round(pct)
    }
}

function _calculateTotals (array) {
    return array.reduce((a, b) => a + b, 0)
}

function _calculatePercentage (number1, number2) {
    if (number1 <= 0 || number2 <= 0) {
        return 0
    }

    return Math.floor((number1 / number2) * 100)
}
