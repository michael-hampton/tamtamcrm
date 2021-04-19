import React, { Component } from 'react'
import { Droppable } from 'react-beautiful-dnd'
import DraggableTaskItem from './DraggableTaskItem'

export default class Column extends Component {
    render () {
        return <Droppable droppableId={this.props.column.id} key={this.props.column.id} type="task">
            {(provided, snapshot) => {
                return (
                    <div
                        {...provided.droppableProps}
                        ref={provided.innerRef}
                        style={{
                            background: snapshot.isDraggingOver
                                ? 'lightblue'
                                : 'lightgrey',
                            padding: 4,
                            width: 250,
                            minHeight: 500
                        }}
                    >
                        {this.props.column.items.map((item, index) => {
                            return <DraggableTaskItem projects={this.props.projects} customers={this.props.customers}
                                provided={provided}
                                snapshot={snapshot}
                                item={item} index={index}
                                toggleViewedEntity={this.props.toggleViewedEntity}
                                type={this.props.type}
                            />
                        })}
                        {provided.placeholder}
                    </div>
                )
            }}
        </Droppable>
    }
}
