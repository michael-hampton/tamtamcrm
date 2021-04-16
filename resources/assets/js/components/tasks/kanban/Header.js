import React, { Component } from 'react'
import Column from './Column'
import { Draggable, DragDropContext, Droppable } from 'react-beautiful-dnd'
import StatusItem from './StatusItem'

export default class Header extends Component {
    render () {
        return <DragDropContext onDragEnd={this.props.updateStatuses}>
            <Droppable droppableId="characters" direction="horizontal">
                {(provided) => (
                    <ul className="w-100 d-flex justify-content-between" {...provided.droppableProps}
                        ref={provided.innerRef}>
                        {this.props.statuses.map((item, index) => {
                            console.log('item', item)
                            return <StatusItem type={this.props.type} item={item} index={index}/>
                        })}
                        {provided.placeholder}
                    </ul>
                )}
            </Droppable>
        </DragDropContext>
    }
}
