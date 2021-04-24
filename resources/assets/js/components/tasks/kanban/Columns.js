import React, { Component } from 'react'
import Column from './Column'
import { Draggable } from 'react-beautiful-dnd'

export default class Columns extends Component {
    render () {
        console.log('tasks', this.props.column.items)
        return (
            <div className="col-sm-6 col-md-4 col-xl-3" key={this.props.column.id}>

                <div className="card bg-light">
                    <Column projects={this.props.projects} customers={this.props.customers} type={this.props.type}
                        toggleViewedEntity={this.props.toggleViewedEntity}
                        columnId={this.props.columnId} column={this.props.column}/>
                </div>

            </div>
        )
    }
}
