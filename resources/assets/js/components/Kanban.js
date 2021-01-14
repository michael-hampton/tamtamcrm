/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
import axios from 'axios'
import Story from './Story'
import KanbanFilter from './KanbanFilter'

class Kanban extends Component {
    constructor (props, context) {
        super(props, context)

        this.state = {
            open: false,
            show: true,
            tasks: [],
            users: [],
            customers: [],
            stories: [],
            err: '',
            err2: '',
            loading: true,
            loadingStory: true,
            hideNav: false,
            custom_fields: [],
            project_id: 0,
            task_type: this.getTaskType()

        }

        this.updateTasks = this.updateTasks.bind(this)
        this.getTaskType = this.getTaskType.bind(this)
        this.setProjectId = this.setProjectId.bind(this)
        this.addProject = this.addProject.bind(this)
        this.resetFilters = this.resetFilters.bind(this)
        this.getTaskUrl = this.getTaskUrl.bind(this)
        this.getUsers = this.getUsers.bind(this)
        this.getCustomers = this.getCustomers.bind(this)
        this.hideMenu = this.hideMenu.bind(this)
        this.resize = this.resize.bind(this)
        this.cachedTasks = []
    }

    componentDidMount () {
        this.getTasks()
        this.getUsers()
        this.getCustomers()
        this.resize()
        this.getCustomFields()

        window.addEventListener('resize', this.resize())
    }

    getTaskType () {
        switch (true) {
            case (window.location.href.indexOf('projects') > -1):
                return 1

            case (window.location.href.indexOf('leads') > -1):
                return 2

            case (window.location.href.indexOf('deals') > -1):
                return 3
        }

        if (window.location.href.indexOf('franky') > -1) {
            alert('your url contains the name franky')
        }
    }

    getCustomFields () {
        axios.get('api/accounts/fields/Task')
            .then((r) => {
                this.setState({
                    custom_fields: r.data.fields
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    err: e
                })
            })
    }

    setProjectId (project_id) {
        this.setState({ project_id: project_id })

        setTimeout(() => {
            this.getTasks()
        })
    }

    resize () {
        const currentHideNav = (window.innerWidth <= 760)

        if (currentHideNav !== this.state.hideNav) {
            this.setState({ hideNav: currentHideNav })
        }
    }

    getCustomers () {
        axios.get('/api/customers')
            .then((r) => {
                this.setState({
                    customers: r.data
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    err: e
                })
            })
    }

    getUsers () {
        axios.get('api/users')
            .then((r) => {
                this.setState({
                    users: r.data
                })
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    err: e
                })
            })
    }

    getTaskUrl () {
        switch (true) {
            case (typeof this.props.task_id !== 'undefined' && this.props.task_id !== null):
                return `/api/tasks/subtasks/${this.props.task_id}`

            case this.state.task_type === 2:
                return '/api/leads'

            case this.state.task_type === 3:
                return '/api/deals'

            default:
                return `/api/tasks/getTasksForProject/${this.state.project_id}`
        }
    }

    getTasks () {
        const url = this.getTaskUrl()

        axios.get(url)
            .then((r) => {
                this.setState({
                    tasks: r.data,
                    err: '',
                    loading: false
                })
                this.cachedTasks = r.data
            })
            .catch((e) => {
                this.setState({
                    loading: false,
                    err: e
                })
            })
    }

    updateTasks (tasks) {
        this.setState({
            tasks: tasks
        })
    }

    resetFilters () {
        this.setState({
            tasks: this.cachedTasks
        })
    }

    /**
     * Add new comment
     * @param {Object} comment
     */
    addProject (project) {
        this.setState({
            stories: [project, ...this.state.stories]
        })
    }

    hideMenu () {
        const body = document.body

        if (this.state.hideNav) {
            body.classList.remove('open')
        }

        // body.classList.add('open')
        // document.getElementsByClassName('navbar-toggler')[0].style.display = 'block'
    }

    render () {
        const divStyle = this.state.task_type === 2 || this.state.task_type === 3 ? {
            left: 0,
            width: '100%'
        } : {}

        this.hideMenu()

        return (
            <div className="kanban container">
                <div className="row m-0 m-md-2">
                    <KanbanFilter
                        customers={this.state.customers}
                        users={this.state.users}
                        reset={this.resetFilters}
                        action={this.updateTasks}
                        task_type={this.state.task_type}
                        updateProjectId={this.setProjectId}
                        addProject={this.addProject}
                        project_id={this.state.project_id}
                    />
                </div>

                <div className="row">

                    <div id="board" className="board">

                        <div style={divStyle}>
                            <aside>
                                <Story
                                    custom_fields={this.state.custom_fields}
                                    customers={this.state.customers}
                                    users={this.state.users}
                                    tasks={this.state.tasks}
                                    action={this.updateTasks}
                                    storyName={this.state.stories.filter(i => i.id === parseInt(this.state.project_id))}
                                    storyType={this.state.project_id}
                                    loading={this.state.loading}
                                    task_type={this.state.task_type}
                                    project_id={this.state.project_id}
                                />
                            </aside>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}

export default Kanban
