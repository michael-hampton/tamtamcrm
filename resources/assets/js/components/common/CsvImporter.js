import React, { Component } from 'react'
import axios from 'axios'
import { UncontrolledTooltip } from 'reactstrap'
import { translations } from '../utils/_translations'
import { icons } from '../utils/_icons'
import { formatDate } from './FormatDate'
import { formatMoney } from './FormatMoney'
import { frequencyOptions } from '../utils/_consts'

export default class CsvImporter extends Component {
    constructor (props) {
        super(props)

        this.export = this.export.bind(this)
    }

    objectToCSVRow (dataObject, headers, isHeader = false) {
        const dataArray = []
        for (const o in dataObject) {
            if (!isHeader && !headers.includes(o)) {
                continue
            }

            if (typeof dataObject[o] === 'object') {
                dataObject[o] = ''
            }

            if (typeof dataObject[o] === 'boolean') {
                dataObject[o] = dataObject[o] === true ? 'Yes' : 'No'
            }

            let innerValue = dataObject[o] === null ? '' : dataObject[o].toString()
            console.log('value 3', innerValue)

            if (!isHeader) {
                innerValue = this.convertField(o, dataObject[o], dataObject)
            }

            try {
                innerValue = typeof dataObject[o] === 'string' ? innerValue.replace(/"/g, '""') : innerValue
            } catch (e) {
                console.log('error', o)
                console.log('error', innerValue)
                console.log(dataObject)
            }

            innerValue = '"' + innerValue + '"'
            dataArray.push(innerValue)
        }
        return dataArray.join(',') + '\r\n'
    }

    convertField (field, value, data) {
        switch (field) {
            case 'assigned_to': {
                const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(value))
                return assigned_user && assigned_user.length ? `${assigned_user[0].first_name} ${assigned_user[0].last_name}` : ''
            }
            case 'user_id': {
                const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(value))
                return `${user[0].first_name} ${user[0].last_name}`
            }
            case 'exchange_rate':
            case 'balance':
            case 'total':
            case 'discount_total':
            case 'tax_total':
            case 'sub_total':
            case 'paid_to_date':
            case 'payment_date':
            case 'amount':
                return formatMoney(value, data.customer_id || null, this.props.customers || [])
            case 'status_id':
                return !data.deleted_at && this.props.statuses
                    ? this.props.statuses[value]
                    : translations.archived
            case 'priority_id':
                return this.props.priorities
                    ? this.props.priorities[value]
                    : ''
            case 'frequency':
                return translations[frequencyOptions[value]]
            case 'date':
            case 'due_date':
            case 'date_to_send':
            case 'created_at':
                return formatDate(value)

            case 'customer_id': {
                const index = this.props.customers && data.customer_id ? this.props.customers.findIndex(customer => customer.id === data.customer_id) : null
                const customer = index !== null ? this.props.customers[index] : null
                return customer !== null ? customer.name : ''
            }

            case 'currency_id': {
                const currency = JSON.parse(localStorage.getItem('currencies')).filter(currency => currency.id === parseInt(value))
                return currency.length ? currency[0].iso_code : ''
            }

            case 'company_id': {
                const companyIndex = this.props.companies && data.company_id ? this.props.companies.findIndex(company => company.id === parseInt(value)) : null
                const company = companyIndex !== null ? this.props.companies[companyIndex] : null
                return company !== null ? company.name : ''
            }
            default:
                return value
        }
    }

    export () {
        axios.get(this.props.url)
            .then(response => {
                if (response.data.data && Object.keys(response.data.data).length) {
                    const colNames = this.props.columns && this.props.columns.length ? this.props.columns : Object.keys(response.data.data[0])

                    let csvContent = 'data:text/csv;charset=utf-8,'
                    csvContent += this.objectToCSVRow(colNames, colNames, true)

                    response.data.data.forEach((item, index) => {
                        csvContent += this.objectToCSVRow(item, colNames)
                    })

                    const encodedUri = encodeURI(csvContent)
                    const link = document.createElement('a')
                    link.setAttribute('href', encodedUri)
                    link.setAttribute('download', this.props.filename)
                    document.body.appendChild(link)
                    link.click()
                    document.body.removeChild(link)
                }
            })
    }

    render () {
        return <React.Fragment>
            <UncontrolledTooltip placement="right"
                target="exportTooltip">
                {translations.export}
            </UncontrolledTooltip>
            <i style={{ fontSize: '24px', lineHeight: '32px' }} id="exportTooltip" onClick={this.export}
                className={`fa ${icons.cloud_download}`}/>
        </React.Fragment>
    }
}
