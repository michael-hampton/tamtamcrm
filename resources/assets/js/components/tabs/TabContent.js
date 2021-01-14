import React, { Component } from 'react'
import TabList from './TabList'
import Comments from '../comments/Comments'
import FileUploads from '../documents/FileUploads'
import EditTask from '../tasks/edit/EditTask'
import EditCustomer from '../customers/edit/EditCustomer'
import EventTab from './EventTab'
import TaskTab from './TaskTab'
import EditInvoice from '../invoice/edit/EditInvoice'
import EditQuote from '../quotes/edit/EditQuote'
import EditOrder from '../orders/edit/EditOrder'

export default class TabContent extends Component {
    render () {
        const userId = JSON.parse(localStorage.getItem('appState')).user.id

        return (
            <div className="container mt-5">
                <TabList>
                    <div label="Task" className="tab-content">
                        <EditTask
                            custom_fields={this.props.custom_fields}
                            users={this.props.users}
                            task_type={this.props.task_type}
                            allTasks={this.props.allTasks}
                            action={this.props.action}
                            task={this.props.task}
                            user_id={userId}
                        />
                    </div>

                    <div label="Customer" className="tab-content">
                        <EditCustomer
                            customer={this.props.task.customer}
                            modal={false}
                            task={this.props.task}
                        />
                    </div>

                    <div label="Product" className="tab-content">
                        <EditOrder customers={this.props.customers} task_id={this.props.task.id}/>
                    </div>

                    <div label="Event" className="tab-content">
                        <EventTab
                            customer_id={this.props.task.customer_id}
                            task_id={this.props.task.id}
                        />
                    </div>

                    <div label="Tasks" className="tab-content">
                        <TaskTab

                            project_id={this.props.project_id}
                            customers={this.props.customers}
                            users={this.props.users}
                            task_id={this.props.task.id}
                            task_type={this.props.task_type}
                        />
                    </div>

                    <div label="Invoice" className="tab-content">
                        <EditInvoice
                            customers={this.props.customers}
                            customer_id={this.props.task.customer_id}
                            finance_type={1}
                            task_id={this.props.task.id}/>
                    </div>

                    <div label="Quote" className="tab-content">
                        <EditQuote
                            customers={this.props.customers}
                            customer_id={this.props.task.customer_id}
                            finance_type={2}
                            task_id={this.props.task.id}/>
                    </div>

                    <div label="Attachment" className="tab-content">
                        <FileUploads entity_type="App\Models\Task" entity={this.props.task} user_id={userId}/>
                    </div>
                    <div label="Comment" className="tab-content">
                        <Comments entity_type="Task" entity={this.props.task} user_id={userId}/>
                    </div>
                </TabList>
            </div>
        )
    }
}
