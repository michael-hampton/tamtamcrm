import React, { Component } from 'react'
import { Droppable } from 'react-beautiful-dnd'
import DraggableTaskItem from './DraggableTaskItem'

export default class Column extends Component {
    render () {
        return <Droppable droppableId={this.props.column.id} key={this.props.column.id} type="task">
            {(provided, snapshot) => {
                return (
                    <div className="card-body"
                        {...provided.droppableProps}
                        ref={provided.innerRef}
                        style={{
                            background: snapshot.isDraggingOver
                                ? 'lightblue'
                                : ''
                        }}
                    >
                        <div className="items">

                            {this.props.column.items.map((item, index) => {
                                return <DraggableTaskItem projects={this.props.projects} customers={this.props.customers}
                                    provided={provided}
                                    snapshot={snapshot}
                                    item={item} items={this.props.column.items} index={index}
                                    toggleViewedEntity={this.props.toggleViewedEntity}
                                    type={this.props.type}
                                />
                            })}
                            {provided.placeholder}
                        </div>
                    </div>
                )
            }}
        </Droppable>
    }
}
