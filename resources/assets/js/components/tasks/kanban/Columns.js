import React, { Component } from 'react'
import Column from './Column'
import { Draggable } from 'react-beautiful-dnd'
import {
    Button
} from 'reactstrap'
import AddDeal from '../../deals/edit/AddDeal'
import AddLead from '../../leads/edit/AddLeadForm'
import EditTaskDesktop from '../edit/EditTaskDesktop'

export default class Columns extends Component {
    render () {
        let addButton = null

        if (this.props.type === 'deal') {
            addButton = <AddDeal
                large_button={true}
                task_status={this.props.column.id}
                custom_fields={[]}
                modal={true}
                status={1}
                customers={this.props.customers}
                users={[]}
                action={this.props.updateTasks}
                deals={this.props.column.items}
            />
        } else if (this.props.type === 'lead') {
            addButton = <AddLead
                large_button={true}
                task_status={this.props.column.id}
                users={[]}
                leads={this.props.column.items}
                action={this.props.updateTasks}
                custom_fields={[]}
            />
        } else {
            addButton = <EditTaskDesktop
                large_button={true}
                task_status={this.props.column.id}
                modal={true}
                listView={true}
                custom_fields={[]}
                users={[]}
                task={{}}
                add={true}
                tasks={this.props.column.items}
                action={this.props.updateTasks}
            />
        }

        return (
            <div className="col-sm-6 col-md-4 col-xl-3" key={this.props.column.id}>

                <div className="card bg-light" style={{ minHeight: '400px' }}>
                    <Column updateTasks={this.updateTasks} projects={this.props.projects}
                        customers={this.props.customers} type={this.props.type}
                        toggleViewedEntity={this.props.toggleViewedEntity}
                        columnId={this.props.columnId} column={this.props.column}/>
                </div>

                {addButton}

            </div>
        )
    }
}
