import React, { Component } from 'react'
import Column from './Column'
import { Draggable } from 'react-beautiful-dnd'

export default class Columns extends Component {
    render () {
        console.log('tasks', this.props.column.items)
        return (
            <div
                style={{
                    display: 'flex',
                    flexDirection: 'column',
                    alignItems: 'center'
                }}
                key={this.props.column.id}
            >

                <div style={{
                    margin: 8,
                    borderLeft: '4px solid ' + this.props.colorArray[this.props.index]
                }}>
                    <Column type={this.props.type}
                        toggleViewedEntity={this.props.toggleViewedEntity}
                        columnId={this.props.columnId} column={this.props.column}/>
                </div>

            </div>
        )
    }
}
