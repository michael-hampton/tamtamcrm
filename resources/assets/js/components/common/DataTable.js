import React, { Component } from 'react'
import axios from 'axios'
import { Collapse, Progress, Spinner, Table, ListGroup } from 'reactstrap'
import PaginationBuilder from './PaginationBuilder'
import TableSort from './TableSort'
import ViewEntity from './ViewEntity'
import DisplayColumns from './DisplayColumns'
import { translations } from '../utils/_translations'
import CheckboxFilterBar from './CheckboxFilterBar'
import TableToolbar from './TableToolbar'
import CustomerModel from '../models/CustomerModel'

export default class DataTable extends Component {
    constructor (props) {
        super(props)
        this.state = {
            loadColumnList: true,
            bulk: [],
            showCheckboxes: false,
            display_list: localStorage.getItem('display_list') === 'true' || false,
            showColumns: false,
            showCheckboxFilter: false,
            allSelected: false,
            width: window.innerWidth,
            view: this.props.view,
            ignoredColumns: this.props.ignore || [],
            query: '',
            message: '',
            loading: false,
            entities: {
                current_page: 1,
                from: 1,
                last_page: 1,
                per_page: 5,
                to: 1,
                total: 1,
                data: []
            },
            first_page: 1,
            current_page: 1,
            sorted_column: this.props.defaultColumn ? this.props.defaultColumn : [],
            data: [],
            columns: [],
            offset: 4,
            order: 'asc',
            progress: 0
        }
        this.cancel = ''
        this.fetchEntities = this.fetchEntities.bind(this)
        this.setPage = this.setPage.bind(this)
        this.handleTableActions = this.handleTableActions.bind(this)
        this.toggleViewedEntity = this.toggleViewedEntity.bind(this)
        this.onChangeBulk = this.onChangeBulk.bind(this)
        this.saveBulk = this.saveBulk.bind(this)
        this.closeFilterBar = this.closeFilterBar.bind(this)
        this.checkAll = this.checkAll.bind(this)
        this.handleWindowSizeChange = this.handleWindowSizeChange.bind(this)
        this.updateIgnoredColumns = this.updateIgnoredColumns.bind(this)
        this.toggleProgress = this.toggleProgress.bind(this)
    }

    componentWillMount () {
        window.addEventListener('resize', this.handleWindowSizeChange)
        this.setPage()
    }

    componentWillUnmount () {
        window.removeEventListener('resize', this.handleWindowSizeChange)
    }

    handleWindowSizeChange () {
        this.setState({ width: window.innerWidth })
    }

    componentWillReceiveProps (nextProps, nextContext) {
        if (this.state.fetchUrl && this.state.fetchUrl !== nextProps.fetchUrl) {
            this.reset(nextProps.fetchUrl)
        }
    }

    updateIgnoredColumns (columns) {
        this.setState({ ignoredColumns: columns.concat('invitations', 'recurring', 'files', 'transactions', 'reviews', 'audits', 'paymentables', 'line_items', 'emails', 'timers', 'attributes', 'features') }, function () {
            console.log('ignored columns', this.state.ignoredColumns)
        })
    }

    toggleProgress () {
        let percent = 0
        const timerId = setInterval(() => {
            // increment progress bar
            percent += 5
            this.setState({ progress: percent })

            // complete
            if (percent >= 100) {
                clearInterval(timerId)
                this.setState({ progress: 0 })
            }
        }, 170)
    }

    saveBulk (e) {
        const action = e.target.id
        const self = this

        if (!this.state.bulk.length) {
            alert('You must select at least one item')
            return false
        }

        if (action === 'email') {
            let is_valid = true
            this.state.data.map((entity) => {
                const customer = this.props.customers.filter(customer => customer.id === parseInt(entity.customer_id))
                const customerModel = new CustomerModel(customer[0])
                const has_email = customerModel.hasEmailAddress()

                if (!has_email) {
                    is_valid = false
                }
            })

            if (!is_valid) {
                this.props.setError(translations.no_email)
                return false
            }
        }

        axios.post(this.props.bulk_save_url, { ids: this.state.bulk, action: action }).then(function (response) {
            let message = `${action} was completed successfully`

            if (action === 'email') {
                message = self.state.bulk.length === 1 ? translations.email_sent_successfully : translations.emails_sent_successfully
            }

            self.props.setSuccess(message)
        })
            .catch(function (error) {
                console.log('error', error)
                self.props.setError(`${action} could not complete.`)
            })
    }

    closeFilterBar () {
        this.setState({ showCheckboxes: false, showCheckboxFilter: false, allSelected: false, bulk: [] })
    }

    handleTableActions (event) {
        if (event.target.id === 'toggle-checkbox') {
            this.setState({
                showCheckboxes: !this.state.showCheckboxes,
                showCheckboxFilter: !this.state.showCheckboxes
            })
        }

        if (event.target.id === 'toggle-table') {
            this.setState({ display_list: !this.state.display_list }, () => {
                localStorage.setItem('display_list', this.state.display_list)
            })
        }

        if (event.target.id === 'toggle-columns') {
            this.setState({ showColumns: !this.state.showColumns })
        }

        if (event.target.id === 'refresh') {
            this.toggleProgress()
            this.fetchEntities()
        }

        if (event.target.id === 'view-entity') {
            const viewId = !this.state.view.viewedId ? this.state.data[0] : this.state.view.viewedId
            let title = ''

            if (!this.state.view.title) {
                title = !this.state.data[0].number ? this.state.data[0].name : this.state.data[0].number
            }

            this.toggleViewedEntity(viewId, title)
        }
    }

    toggleViewedEntity (id, title = null, edit = null) {
        if (this.state.view.viewMode === true) {
            this.setState({
                view: {
                    ...this.state.view,
                    viewMode: false,
                    viewedId: null
                }
            }, () => console.log('view', this.state.view))

            return
        }

        this.setState({
            view: {
                ...this.state.view,
                viewMode: !this.state.view.viewMode,
                viewedId: id,
                edit: edit,
                title: title
            }
        }, () => console.log('view', this.state.view))
    }

    reset (fetchUrl = null) {
        this.setState({
            query: '',
            current_page: 1,
            loading: true,
            fetchUrl: fetchUrl !== null ? fetchUrl : this.state.fetchUrl
        }, () => {
            this.fetchEntities()
        })
    }

    setPage () {
        this.setState({
            current_page: this.state.entities.current_page,
            loading: true,
            fetchUrl: this.props.fetchUrl
        }, () => {
            this.fetchEntities()
        })
    }

    preferredOrder (arrObjects, order) {
        const newObject = []

        arrObjects.forEach((obj) => {
            const test = {}
            for (let i = 0; i < order.length; i++) {
                // eslint-disable-next-line no-prototype-builtins
                if (obj.hasOwnProperty(order[i])) {
                    test[order[i]] = obj[order[i]]
                }
            }

            newObject.push(test)
        })

        return newObject
    }

    groupBy (data, key) {
        return data.reduce(function (acc, item) {
            (acc[item[key]] = acc[item[key]] || []).push(item)
            return acc
        }, {})
    }

    fetchEntities (pageNumber = false, order = false, sorted_column = false) {
        if (this.cancel) {
            this.cancel.cancel()
        }

        pageNumber = !pageNumber || typeof pageNumber === 'object' ? this.state.current_page : pageNumber
        order = !order ? this.state.order : order
        sorted_column = !sorted_column ? this.state.sorted_column : sorted_column
        const noPerPage = !localStorage.getItem('number_of_rows') ? Math.ceil(window.innerHeight / 90) : localStorage.getItem('number_of_rows')
        this.cancel = axios.CancelToken.source()
        const fetchUrl = `${this.props.fetchUrl}${this.props.fetchUrl.includes('?') ? '&' : '?'}page=${pageNumber}&column=${sorted_column}&order=${order}&per_page=${noPerPage}`

        axios.get(fetchUrl, {})
            .then(response => {
                let data = response.data.data && Object.keys(response.data.data).length ? response.data.data : []
                const columns = (this.props.columns && this.props.columns.length) ? (this.props.columns) : ((Object.keys(data).length) ? (Object.keys(data[0])) : null)

                if (this.props.order) {
                    data = this.preferredOrder(data, this.props.order)
                }

                this.setState({
                    order: order,
                    current_page: pageNumber,
                    sorted_column: sorted_column,
                    entities: response.data,
                    perPage: noPerPage,
                    loading: false,
                    data: data,
                    columns: columns
                    // progress: 0
                }, () => {
                    this.props.updateState(data)
                    this.buildColumnList()
                })
            })
            .catch(error => {
                this.setState(({ progress: 0 }))
                this.props.setError('Failed to fetch the data. Please check network')
            })
    }

    buildColumnList () {
        if (this.state.data && this.state.data.length && this.props.default_columns && this.state.loadColumnList) {
            const allFields = []

            Object.keys(this.state.data[0]).map((field) => {
                if (!this.props.default_columns.includes(field)) {
                    allFields.push(field)
                }
            })

            this.setState({ ignoredColumns: allFields, loadColumnList: false })
        }
    }

    checkAll (e) {
        const checked = e.target.checked
        // current array of options
        const options = this.state.bulk
        let index

        this.state.data.forEach((element) => {
            // check if the check box is checked or unchecked
            if (checked) {
                // add the numerical value of the checkbox to options array
                options.push(+element.id)
            } else {
                // or remove the value from the unchecked checkbox from the array
                index = options.indexOf(element.id)
                options.splice(index, 1)
            }
        })

        // update the state with the new array of options
        this.setState({ bulk: options, allSelected: checked }, () => console.log('bulk', this.state.bulk))
    }

    onChangeBulk (e) {
        // current array of options
        const options = this.state.bulk
        let index

        // check if the check box is checked or unchecked
        if (e.target.checked) {
            // add the numerical value of the checkbox to options array
            options.push(+e.target.value)
        } else {
            // or remove the value from the unchecked checkbox from the array
            index = options.indexOf(e.target.value)
            options.splice(index, 1)
        }

        // update the state with the new array of options
        this.setState({ bulk: options }, () => console.log('bulk', this.state.bulk))
    }

    render () {
        const { loading, message, width, progress } = this.state
        const isMobile = width <= 500
        const loader = loading ? <Spinner style={{
            width: '3rem',
            height: '3rem'
        }}/> : null

        const columnFilter = this.state.entities.data && this.state.entities.data.length
            ? <DisplayColumns onChange2={this.updateIgnoredColumns}
                columns={Object.keys(this.state.entities.data[0]).concat(this.state.ignoredColumns)}
                ignored_columns={this.state.ignoredColumns}
                default_columns={this.props.default_columns}/> : null

        const table_class = 'mt-2 data-table'
        const tableSort = !isMobile ? <TableSort fetchEntities={this.fetchEntities}
            columnMapping={this.props.columnMapping}
            columns={this.props.order ? this.props.order : this.state.columns}
            default_columns={this.props.default_columns}
            ignore={this.state.ignoredColumns}
            disableSorting={this.props.disableSorting}
            sorted_column={this.state.sorted_column}
            order={this.state.order}/> : null

        const table_dark = localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true' || false

        const list = this.props.userList({
            show_list: this.state.display_list,
            bulk: this.state.bulk,
            ignoredColumns: this.state.ignoredColumns,
            toggleViewedEntity: this.toggleViewedEntity,
            viewId: this.state.view.viewedId ? this.state.view.viewedId.id : null,
            showCheckboxes: this.state.showCheckboxes,
            onChangeBulk: this.onChangeBulk
        })

        const table = !this.props.hide_table && !this.state.display_list
            ? <Table className={table_class} responsive striped bordered hover dark={table_dark}>
                {tableSort}
                <tbody>
                    {list}
                </tbody>
            </Table> : <ListGroup className="mt-3 mb-3">{list}</ListGroup>

        return (
            <React.Fragment>

                {message && <p className="message">{message}</p>}

                {progress > 0 &&
                <Progress value={progress}/>
                }

                {loader}

                <Collapse className="pull-left col-12 col-md-8" isOpen={this.state.showColumns}>
                    {columnFilter}
                </Collapse>

                <Collapse className="pull-left col-12 col-md-8" isOpen={this.state.showCheckboxFilter}>
                    <CheckboxFilterBar count={this.state.bulk.length} isChecked={this.state.allSelected}
                        checkAll={this.checkAll}
                        cancel={this.closeFilterBar}/>
                </Collapse>

                <TableToolbar dropdownButtonActions={this.props.dropdownButtonActions}
                    saveBulk={this.saveBulk}
                    handleTableActions={this.handleTableActions}/>

                {table}

                {this.props.view && <ViewEntity
                    updateState={this.props.updateState}
                    toggle={this.toggleViewedEntity}
                    title={this.state.view.title}
                    viewed={this.state.view.viewMode}
                    edit={this.state.view.edit}
                    companies={this.props.companies}
                    customers={this.props.customers && this.props.customers.length ? this.props.customers : []}
                    entities={this.state.data}
                    entity={this.state.view.viewedId}
                    entity_type={this.props.entity_type}
                />}

                <PaginationBuilder last_page={this.state.entities.last_page} page={this.state.entities.page}
                    current_page={this.state.entities.current_page}
                    from={this.state.entities.from}
                    offset={this.state.offset}
                    to={this.state.entities.to} fetchEntities={this.fetchEntities}
                    recordCount={this.state.entities.total} perPage={this.state.perPage}/>
            </React.Fragment>
        )
    }
}
