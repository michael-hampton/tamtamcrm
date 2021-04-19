import axios from 'axios'
import React, { Component } from 'react'
import { Input, Button, FormGroup, Label } from 'reactstrap'
import { Draggable } from 'react-beautiful-dnd'
import { translations } from '../../utils/_translations'
import TaskModel from '../../models/TaskModel'
import DealModel from '../../models/DealModel'
import LeadModel from '../../models/LeadModel'
import TaskStatusModel from '../../models/TaskStatusModel'

export default class StatusItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            show_edit: false,
            name: props.item.name
        }

        this.saveStatus = this.saveStatus.bind(this)
    }

    saveStatus () {
        const model = new TaskStatusModel(this.props.item)
        model.update({
            name: this.state.name,
            task_type: this.props.type
        }).then(response => {
            if (!response) {
                this.setState({ errors: model.errors, message: model.error_message })
                return
            }

            this.setState({ show_edit: false })
        })
    }

    render () {
        const { item, index } = this.props
        return (
            <Draggable
                key={item.id}
                draggableId={item.id}
                index={index}
            >
                {(provided, snapshot) => {
                    return (
                        <div
                            ref={provided.innerRef}
                            {...provided.draggableProps}
                            {...provided.dragHandleProps}
                            style={{
                                userSelect: 'none',
                                width: '250px',
                                padding: 16,
                                margin: '0 0 8px 0',
                                minHeight: '50px',
                                backgroundColor: snapshot.isDragging
                                    ? '#263B4A'
                                    : this.props.item.color.length ? this.props.item.color : '#456C86',
                                color: 'white',
                                ...provided.draggableProps.style
                            }}
                        >
                            <div className="d-flex justify-content-between">
                                <a style={{ padding: '12px' }}
                                    onClick={(e) => {
                                        this.props.toggleViewedEntity(null, null, false, item)
                                    }}>{item.name}</a>

                                <a onClick={(e) => {
                                    this.setState({ show_edit: !this.state.show_edit })
                                }}>{this.state.show_edit === true ? translations.hide : translations.edit}</a>
                            </div>

                            <div className={this.state.show_edit === true ? 'd-block' : 'd-none'}>
                                <FormGroup className="mb-2">
                                    <Label>{translations.name}</Label>
                                    <Input type="text" name="name" value={this.state.name} onChange={(e) => {
                                        this.setState({ name: e.target.value })
                                    }}/>
                                </FormGroup>

                                <Button color="primary" onClick={this.saveStatus}>{translations.save}</Button>
                            </div>

                        </div>
                    )
                }}
            </Draggable>
        )
    }
}
