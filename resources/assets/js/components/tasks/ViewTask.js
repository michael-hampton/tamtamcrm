/* eslint-disable no-unused-vars */
import React from 'react'
import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap'
import TabContent from '../tabs/TabContent'
import CompleteTask from '../CompleteTask'
import axios from 'axios'

class ViewTask extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            errors: []
        }
        this.toggle = this.toggle.bind(this)
        this.convertLead = this.convertLead.bind(this)
    }

    toggle (e) {
        e.preventDefault()
        this.setState({
            modal: !this.state.modal
        }, () => {
            if (!this.state.modal) {
                localStorage.removeItem('orderForm')
            }
        })
    }

    convertLead () {
        axios.get(`/api/tasks/convertToDeal/${this.props.task.id}`)
            .then(function (response) {
                const arrTasks = [...this.props.allTasks]
                const index = arrTasks.findIndex(task => task.id === this.props.task.id)
                arrTasks.splice(index, 1)
                this.props.action(arrTasks)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <div>
                <a href='#' onClick={this.toggle}><h4 className="mb-1">{this.props.task.title}</h4></a>
                <Modal size="lg" isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <ModalHeader toggle={this.toggle}>
                        Task
                    </ModalHeader>

                    <ModalBody className={theme}>

                        <Button color="success" onClick={this.convertLead}>Convert to Deal</Button>

                        <TabContent
                            custom_fields={this.props.custom_fields}
                            project_id={this.props.project_id}
                            users={this.props.users}
                            customers={this.props.customers}
                            task_type={this.props.task_type}
                            allTasks={this.props.allTasks}
                            action={this.props.action}
                            task={this.props.task}
                        />
                    </ModalBody>

                    <ModalFooter>
                        <Button color="secondary" onClick={this.toggle}>Close</Button>
                        <CompleteTask
                            action={this.props.action}
                            tasks={this.props.allTasks}
                            taskId={this.props.task.id}
                        />
                    </ModalFooter>
                </Modal>
            </div>
        )
    }
}

export default ViewTask
