import React, { Component } from 'react'
import moment from 'moment'
import axios from 'axios'
import $ from 'jquery'
import 'jquery-ui-dist/jquery-ui'
import './dragdrop'
import Loader from './Loader'
import ViewTask from './tasks/ViewTask'
import Subtasks from './tasks/Subtasks'
import Avatar from './common/Avatar'
import RestoreModal from './common/RestoreModal'
import EditLeadForm from './leads/edit/EditLeadForm'

class Task extends Component {
    componentWillReceiveProps () {
        setTimeout(function () {
            $('.mcell-task').draggable({
                appendTo: 'body',
                cursor: 'move',
                helper: 'clone',
                revert: 'invalid'
            })

            $('.mcell').droppable({
                tolerance: 'intersect',
                accept: '.mcell-task',
                activeClass: 'ui-state-default',
                hoverClass: 'ui-state-hover',
                drop: function (event, ui) {
                    event.preventDefault()

                    $(this).append($(ui.draggable))
                    const id = $(ui.draggable).attr('id')
                    const status = $(this).data('status')

                    axios.put(`/api/tasks/status/${id}`, {
                        task_status: status
                    })
                        .then((response) => {
                        })
                        .catch((error) => {
                            alert(error)
                        })
                }
            })
        }, 3000)
    }

    api (id) {
        const self = this

        axios.delete('/api/tasks/' + id)
            .then(function (response) {
                const filteredArray = self.props.tasks.filter(item => item.id !== id)
                self.props.action(filteredArray)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    deleteLead (id) {
        const self = this

        axios.delete('/api/leads/' + id)
            .then(function (response) {
                const filteredArray = self.props.tasks.filter(item => item.id !== id)
                self.props.action(filteredArray)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const { tasks, loading, column } = this.props
        const filter = column.id

        let content
        if (loading) {
            content = <div className="loader">
                <Loader/>
            </div>
        } else if (tasks && tasks.length) {
            content =
                tasks.filter(i => i.task_status === Number(filter))
                    .map((i, index) => {
                        let contributors = ''

                        const deleteButton = !i.deleted_at
                            ? <i id="delete" className="fa fa-times" onClick={() => this.api(i.id)}/>
                            : <RestoreModal id={i.id} entities={tasks} updateState={this.props.action}
                                url={`/api/tasks/restore/${i.id}`}/>

                        const deleteLeadButton = !i.deleted_at
                            ? <i id="delete" className="fa fa-times" onClick={() => this.deleteLead(i.id)}/>
                            : <RestoreModal id={i.id} entities={tasks} updateState={this.props.action}
                                url={`/api/leads/restore/${i.id}`}/>

                        if (i.users && i.users.length) {
                            contributors = i.users.map((user, index) => {
                                return (
                                    <Avatar key={index} inline={true} name={user.first_name + ' ' + user.last_name}/>
                                )
                            })
                        }

                        const description = this.props.task_type === 2 ? i.description : i.content

                        const divStyle = {
                            borderLeft: `2px solid ${this.props.column.column_color}`
                        }

                        const edit = this.props.task_type === 2 ? <EditLeadForm users={this.props.users} lead={i}/>
                            : <ViewTask
                                custom_fields={this.props.custom_fields}
                                project_id={this.props.project_id}
                                customers={this.props.customers}
                                users={this.props.users}
                                task_type={this.props.task_type}
                                allTasks={this.props.tasks}
                                action={this.props.action}
                                task={i}
                            />

                        const deleteButtonDisplay = this.props.task_type === 2 ? deleteLeadButton : deleteButton

                        return (
                            <div style={divStyle} data-task={i.id} id={i.id}
                                className="col-12 col-md-12 mcell-task card" key={index}>

                                <span className="task-name">
                                    {edit}
                                    {deleteButtonDisplay}
                                </span>

                                <h3>{i.name}</h3>
                                <h5 className="m-3">{i.valued_at}</h5>
                                <p className="mb-1">{description}</p>

                                <div>
                                    <span
                                        className="task-due">Start: {moment(i.startDate).format('DD.MM.YYYY')}</span>
                                    <span
                                        className="task-due">Due: {moment(i.dueDate).format('DD.MM.YYYY')}</span>
                                    <span className="task-contributors">
                                        {contributors}
                                    </span>
                                </div>
                                <div className={i.color}/>

                                <Subtasks task_id={i.id}
                                    customers={this.props.customers}
                                    users={this.props.users}
                                    task_type={this.props.task_type}
                                    allTasks={this.props.tasks}
                                    action={this.props.action}
                                />
                            </div>
                        )
                    })
        }
        return (
            <div className="process">{content}</div>
        )
    }
}

export default Task
