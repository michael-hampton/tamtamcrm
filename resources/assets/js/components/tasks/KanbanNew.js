import React, { Component } from 'react'
import { DragDropContext, Draggable, Droppable } from 'react-beautiful-dnd'
import DealModel from '../models/DealModel'
import LeadModel from '../models/LeadModel'
import TaskModel from '../models/TaskModel'
import queryString from 'query-string'
import { taskTypes } from '../utils/_consts'
import { Col, Row } from 'reactstrap'
import ViewEntity from '../common/ViewEntity'
import EditTask from './edit/EditTask'
import TaskRepository from '../repositories/TaskRepository'
import LeadRepository from '../repositories/LeadRepository'
import DealRepository from '../repositories/DealRepository'
import CustomerRepository from '../repositories/CustomerRepository'
import ProjectDropdown from '../common/dropdowns/ProjectDropdown'
import AddTaskStatus from '../taskStatus/edit/AddTaskStatus'
import TaskStatusRepository from '../repositories/TaskStatusRepository'
import Columns from './kanban/Columns'
import Header from './kanban/Header'
import { toast, ToastContainer } from 'react-toastify'
import { translations } from '../utils/_translations'

export default class KanbanNew extends Component {
    constructor (props) {
        super(props)

        this.state = {
            type: queryString.parse(this.props.location.search).type || 'task',
            project_id: queryString.parse(this.props.location.search).project_id || '',
            columns: {},
            entities: {},
            statuses: {},
            customers: {},
            view: {
                viewMode: false,
                edit: false,
                viewedId: false
            }
        }

        this.colorArray = ['#FF6633', '#32CD32', '#DC143C', '#FF1493', '#20B2AA',
            '#9400D3', '#DA70D6', '#999966', '#FF7F50', '#B34D4D',
            '#80B300', '#809900', '#E6B3B3', '#6680B3', '#66991A',
            '#FF99E6', '#CCFF1A', '#FF1A66', '#E6331A', '#33FFCC',
            '#66994D', '#B366CC', '#4D8000', '#B33300', '#CC80CC',
            '#66664D', '#991AFF', '#E666FF', '#4DB3FF', '#1AB399',
            '#E666B3', '#33991A', '#CC9999', '#B3B31A', '#00E680',
            '#4D8066', '#809980', '#E6FF80', '#1AFF33', '#999933',
            '#FF3380', '#CCCC00', '#66E64D', '#4D80CC', '#9900B3',
            '#E64D66', '#4DB380', '#FF4D4D', '#99E6E6', '#6666FF']

        this.formatColumns = this.formatColumns.bind(this)
        this.save = this.save.bind(this)
        this.load = this.load.bind(this)
        this.getCustomers = this.getCustomers.bind(this)
        this.toggleViewedEntity = this.toggleViewedEntity.bind(this)
        this.addUserToState = this.addUserToState.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.updateStatuses = this.updateStatuses.bind(this)
    }

    componentDidMount () {
        this.load()
        this.getCustomers()
    }

    handleInput (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value

        this.setState({
            [e.target.name]: value
        }, () => {
            this.load()
        })
    }

    addUserToState (statuses) {
        const cachedData = !this.state.cachedData.length ? statuses : this.state.cachedData
        this.setState({
            statuses: statuses,
            cachedData: cachedData
        })
    }

    getCustomers () {
        const customerRepository = new CustomerRepository()
        customerRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ customers: response }, () => {
                console.log('customers', this.state.customers)
            })
        })
    }

    async load () {
        const task_type = taskTypes[this.state.type]
        const taskStatusRepository = new TaskStatusRepository()
        taskStatusRepository.get(task_type).then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ statuses: response }, () => {
                if (this.state.type === 'task') {
                    this.getTasks()
                }

                if (this.state.type === 'lead') {
                    this.getLeads()
                }

                if (this.state.type === 'deal') {
                    this.getDeals()
                }

                console.log('statuses', this.state.statuses)
            })
        })
    }

    getTasks () {
        const taskRepository = new TaskRepository()
        taskRepository.get(null, null, this.state.project_id.length ? this.state.project_id : null).then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ entities: response }, () => {
                console.log('entities', this.state.entities)
                this.formatColumns()
            })
        })
    }

    getLeads () {
        const leadRepository = new LeadRepository()
        leadRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ entities: response }, () => {
                console.log('entities', this.state.entities)
                this.formatColumns()
            })
        })
    }

    getDeals () {
        const dealRepository = new DealRepository()
        dealRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ entities: response }, () => {
                console.log('entities', this.state.entities)
                this.formatColumns()
            })
        })
    }

    save (element, status) {
        console.log('element', element)

        element.task_status_id = parseInt(status)
        element.id = parseInt(element.id)

        let model

        switch (this.state.type) {
            case 'task':
                model = new TaskModel(element)
                break

            case 'deal':
                model = new DealModel(element)
                break

            case 'lead':
                model = new LeadModel(element)
                break
        }

        model.update(element).then(response => {
            if (!response) {
                this.setState({
                    showErrorMessage: true,
                    loading: false,
                    errors: this.model.errors,
                    message: this.model.error_message
                })

                toast.error(translations.updated_unsuccessfully.replace('{entity}', translations.task), {
                    position: 'top-center',
                    autoClose: 5000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                })

                return
            }

            toast.success(translations.updated_successfully.replace('{entity}', translations.task), {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })
        })
    }

    updateSortOrder (tasks) {
        console.log('tasks', tasks)

        let repo

        switch (this.state.type) {
            case 'task':

                repo = new TaskRepository()
                break

            case 'deal':
                repo = new DealRepository()
                break

            case 'lead':
                repo = new LeadRepository()
                break
        }

        repo.updateSortOrder(tasks).then(response => {
            if (!response) {
                this.setState({
                    showErrorMessage: true,
                    loading: false,
                    errors: repo.errors,
                    message: repo.error_message
                })

                toast.error(translations.updated_unsuccessfully.replace('{entity}', translations.task), {
                    position: 'top-center',
                    autoClose: 5000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                })

                return
            }

            toast.success(translations.updated_successfully.replace('{entity}', translations.task), {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })
        })
    }

    toggleViewedEntity (id, title, edit, entity) {
        if (!entity) {
            this.setState({
                view: {
                    ...this.state.view,
                    viewMode: false
                }
            }, () => {
                this.setState({
                    view: {
                        ...this.state.view,
                        edit: edit
                    }
                })
            })

            return
        }

        this.setState({
            view: {
                ...this.state.view,
                viewedId: entity,
                viewMode: !this.state.view.viewMode,
                title: entity.name
            }
        }, () => {
            this.setState({ edit: edit })
        })
    }

    formatColumns () {
        const columns = []
        const statuses = this.state.statuses
        statuses.sort((a, b) => (a.order_id - b.order_id))

        statuses.map((entity, index) => {
            columns.push({
                name: entity.name,
                id: entity.id.toString(),
                items: []
            })
        })

        this.state.entities.map((entity, index) => {
            entity.id = entity.id.toString()

            const statusIndex = columns.findIndex(column => parseInt(column.id) === parseInt(entity.task_status_id))
            columns[statusIndex].items.push(entity)
        })

        this.setState({ columns: columns })
    }

    updateStatuses (result) {
        const items = [...this.state.columns]
        const [reorderedItem] = items.splice(result.source.index, 1)
        items.splice(result.destination.index, 0, reorderedItem)

        const statusIds = []

        items.map((entity, index) => {
            items[index].order_id = (index + 1)
            statusIds.push(entity.id)
        })

        const repo = new TaskStatusRepository()
        repo.updateSortOrder(items).then(response => {
            if (!response) {
                this.setState({
                    showErrorMessage: true,
                    loading: false,
                    errors: repo.errors,
                    message: repo.error_message
                })

                toast.error(translations.updated_unsuccessfully.replace('{entity}', translations.task_status), {
                    position: 'top-center',
                    autoClose: 5000,
                    hideProgressBar: false,
                    closeOnClick: true,
                    pauseOnHover: true,
                    draggable: true,
                    progress: undefined
                })
                return
            }

            toast.success(translations.updated_successfully.replace('{entity}', translations.task_status), {
                position: 'top-center',
                autoClose: 5000,
                hideProgressBar: false,
                closeOnClick: true,
                pauseOnHover: true,
                draggable: true,
                progress: undefined
            })

            this.setState({ columns: items })
        })
    }

    onDragEnd (result, columns, setColumns) {
        if (!result.destination) return
        const { source, destination, type } = result

        if (source.droppableId !== destination.droppableId) {
            const sourceIndex = columns.findIndex(column => parseInt(column.id) === parseInt(source.droppableId))
            const destIndex = columns.findIndex(column => parseInt(column.id) === parseInt(destination.droppableId))

            const sourceColumn = columns[sourceIndex]
            const destColumn = columns[destIndex]
            const sourceItems = [...sourceColumn.items]
            const destItems = [...destColumn.items]

            const entity = sourceItems[source.index]

            const [removed] = sourceItems.splice(source.index, 1)
            destItems.splice(destination.index, 0, removed)

            columns[sourceIndex] = {
                ...sourceColumn,
                items: sourceItems
            }

            columns[destIndex] = {
                ...destColumn,
                items: destItems
            }

            this.setState({ columns: columns }, () => {
                this.save(entity, destination.droppableId)
            })
        } else {
            const sourceIndex = columns.findIndex(column => parseInt(column.id) === parseInt(source.droppableId))
            const column = columns[sourceIndex]
            const copiedItems = [...column.items]
            const [removed] = copiedItems.splice(source.index, 1)
            copiedItems.splice(destination.index, 0, removed)

            columns[sourceIndex] = {
                ...column,
                items: copiedItems
            }

            const taskIds = []

            this.setState({ columns: columns }, () => {
                const columns = this.state.columns
                const column = columns[sourceIndex]
                column.items.map((entity, index) => {
                    column.items[index].task_sort_order = (index + 1)
                    taskIds.push(entity.id)
                })

                columns[sourceIndex] = column

                this.setState({ columns: columns }, () => {
                    console.log('sort', this.state.columns)
                    this.updateSortOrder(column.items)
                })
            })
        }
    }

    // Normally you would want to split things out into separate components.
    // But in this example everything is just done in one place for simplicity
    render () {
        const { project_id, type, statuses, columns, customers, entities } = this.state
        const edit = this.state.type === 'task' && this.state.view.viewedId
            ? <EditTask listView={true} modal={true} show={this.state.view.edit} tasks={this.props.entities}
                task={this.state.view.viewedId}/> : null

        return customers.length && columns.length && entities.length ? (
            <React.Fragment>
                <ToastContainer
                    position="top-center"
                    autoClose={5000}
                    hideProgressBar={false}
                    newestOnTop={false}
                    closeOnClick
                    rtl={false}
                    pauseOnFocusLoss
                    draggable
                    pauseOnHover
                />

                <Row>
                    <Col sm={12} className="mt-2 mb-3">
                        <div className="d-flex justify-content-between align-items-center">
                            {type === 'task' &&
                            <ProjectDropdown handleInputChanges={this.handleInput}
                                project={project_id} name="project_id"
                            />
                            }

                            <AddTaskStatus
                                customers={customers}
                                statuses={statuses}
                                action={this.addUserToState}
                            />
                        </div>
                    </Col>
                </Row>

                <Row>
                    <Col className="w-100 overflow-auto pr-2" sm={12}>

                        <Header type={this.state.type} updateStatuses={this.updateStatuses} statuses={columns}/>

                        <div style={{ display: 'flex', height: '100%' }}>
                            <DragDropContext
                                onDragEnd={result => this.onDragEnd(result, columns)}
                            >
                                <Droppable
                                    droppableId="all-columns"
                                    direction="horizontal"
                                    type="column"
                                >
                                    {provided => {
                                        return <div {...provided.droppableProps}
                                            ref={provided.innerRef} className="d-flex">
                                            {columns.map((column, index) => {
                                                return <Columns columnId={column.id} column={column} index={index}
                                                    colorArray={this.colorArray} type={this.state.type}
                                                    toggleViewedEntity={this.toggleViewedEntity}/>
                                            })}
                                            {provided.placeholder}
                                        </div>
                                    }}
                                </Droppable>
                            </DragDropContext>
                        </div>
                    </Col>
                </Row>

                <ViewEntity
                    edit={edit}
                    toggle={this.toggleViewedEntity}
                    title={this.state.view.title}
                    viewed={this.state.view.viewMode}
                    customers={this.state.customers}
                    entity={this.state.view.viewedId}
                    entity_type={this.state.type.charAt(0).toUpperCase() + this.state.type.slice(1)}
                />

            </React.Fragment>

        ) : <div>Loading</div>
    }
}
