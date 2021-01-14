import React, { Component } from 'react'
import { Button, Card, CardBody, CardTitle, Col, Form, Input, Row } from 'reactstrap'
import axios from 'axios'
import AddStory from './projects/edit/AddStory'
import EditProject from './projects/edit/EditProject'
import UserDropdown from './common/dropdowns/UserDropdown'
import CustomerDropdown from './common/dropdowns/CustomerDropdown'
import TaskStatusDropdown from './common/dropdowns/TaskStatusDropdown'
import TableSearch from './common/TableSearch'

export default class KanbanFilter extends Component {
    constructor (props) {
        super(props)
        this.state = {
            filters: {
                task_status: '',
                user_id: '',
                project_id: '',
                customer_id: '',
                task_type: ''
            },
            stories: []
        }
        this.handleChange = this.handleChange.bind(this)
        this.handleProjectChange = this.handleProjectChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.resetFilters = this.resetFilters.bind(this)
    }

    componentDidMount () {
        this.getStoryDetails()
    }

    getStoryDetails () {
        axios.get('/api/projects')
            .then((r) => {
                this.setState({
                    stories: r.data
                })
            })
            .catch((e) => {
                console.warn(e)
            })
    }

    handleProjectChange (event) {
        const projectId = event.target.value
        if (this.props.updateProjectId) {
            this.props.updateProjectId(projectId)
        }

        this.handleChange(event)
    }

    handleChange (event) {
        const column = event.target.id
        const value = event.target.value

        if (value === 'all') {
            const updatedRowState = this.state.filters.filter(filter => filter.column !== column)
            this.setState({ filters: updatedRowState }, function () {
                if (!this.props.handleFilters) {
                    this.handleSubmit()
                }
            })
            return true
        }

        this.setState(prevState => ({
            filters: {
                ...prevState.filters,
                [column]: value
            }
        }), function () {
            if (this.props.handleFilters) {
                this.props.handleFilters(this.state.filters)
            } else {
                this.handleSubmit()
            }
        })

        return true
    }

    resetFilters () {
        this.props.reset()
    }

    handleSubmit (event) {
        axios.post(`/api/tasks/filterTasks/${this.props.task_type}`,
            this.state.filters)
            .then((response) => {
                this.props.action(response.data)
            })
            .catch((error) => {
                alert(error)
            })
    }

    buildProjectOptions () {
        let storyTable = null
        if (this.state.stories && this.state.stories.length) {
            storyTable = this.state.stories.map((story, index) => {
                return (
                    <option key={story.id} value={story.id}>{story.title}</option>
                )
            })
        }

        return (
            <Input id="project_id" name="project_id" type="select" onChange={this.handleProjectChange}
                value={this.props.project_id}>
                <option>Choose Project</option>
                {storyTable}
            </Input>
        )
    }

    renderErrorFor () {

    }

    render () {
        const projectContent = this.props.task_type !== 2 && this.props.task_type !== 3 ? this.buildProjectOptions() : ''
        const addButton = this.props.task_type !== 2 && this.props.task_type !== 3
            ? <AddStory customers={this.props.customers} addProject={this.props.addProject}/>
            : ''
        const editButton = this.props.project_id
            ? <EditProject customers={this.props.customers} project_id={this.props.project_id}/> : ''

        return (
            <Card className="col-12 p-0">
                <CardBody>
                    <CardTitle>Filter</CardTitle>
                    <Form>
                        <Row form>
                            {editButton}

                            <Col md={3}>
                                {projectContent}
                            </Col>

                            <Col md={3}>
                                <UserDropdown
                                    user_id={this.state.filters.user_id}
                                    renderErrorFor={this.renderErrorFor}
                                    handleInputChanges={this.handleChange}
                                    users={this.props.users}
                                    name="user_id"
                                />
                            </Col>

                            <Col md={3}>
                                <CustomerDropdown
                                    customer={this.state.filters.customer_id}
                                    renderErrorFor={this.renderErrorFor}
                                    handleInputChanges={this.handleChange}
                                    customers={this.props.customers}
                                />
                            </Col>
                        </Row>

                        <Row form>
                            <Col md={3}>
                                <TableSearch onChange={this.handleChange}/>
                            </Col>
                            <Col md={3}>
                                <TaskStatusDropdown
                                    task_type={this.props.task_type}
                                    renderErrorFor={this.renderErrorFor}
                                    handleInputChanges={this.handleChange}
                                />
                            </Col>

                            <Col md={3}>
                                <Input
                                    type="select"
                                    id="task_type"
                                    name="task_type"
                                    onChange={this.handleChange}
                                >
                                    <option value="">Select Task Type</option>
                                    <option value="1">Task</option>
                                    <option value="2">Leads</option>
                                    <option value="3">Deals</option>
                                </Input>
                            </Col>

                            <Col md={3}>
                                <Button className="mr-2 ml-2" color="success">Submit</Button>
                                <Button onClick={this.resetFilters} color="primary">Reset</Button>
                            </Col>
                        </Row>

                    </Form>

                    {addButton}
                </CardBody>
            </Card>
        )
    }
}
