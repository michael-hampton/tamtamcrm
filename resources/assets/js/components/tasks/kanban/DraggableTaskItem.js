import axios from 'axios'
import React, { Component } from 'react'
import { Input, Button, FormGroup, Label } from 'reactstrap'
import { Draggable } from 'react-beautiful-dnd'
import { translations } from '../../utils/_translations'
import TaskModel from '../../models/TaskModel'
import DealModel from '../../models/DealModel'
import LeadModel from '../../models/LeadModel'

export default class DraggableTaskItem extends Component {
    constructor (props) {
        super(props)

        this.state = {
            show_edit: false,
            name: props.item.name,
            description: props.item.description
        }

        this.saveTask = this.saveTask.bind(this)
    }

    saveTask () {
        let model

        switch (this.props.type) {
            case 'task':
                model = new TaskModel(this.props.item)
                break

            case 'deal':
                model = new DealModel(this.props.item)
                break

            case 'lead':
                model = new LeadModel(this.props.item)
                break
        }

        model.update({
            name: this.state.name,
            description: this.state.description,
            due_date: this.props.item.due_date
        }).then(response => {
            if (!response) {
                this.setState({ errors: model.errors, message: model.error_message })
                return
            }

            this.setState({ show_edit: false })
        })
    }

    render () {
        console.log('task', this.props.item)
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
                                padding: 16,
                                margin: '0 0 8px 0',
                                minHeight: '50px',
                                backgroundColor: snapshot.isDragging
                                    ? '#263B4A'
                                    : '#456C86',
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

                                <FormGroup>
                                    <Label>{translations.description}</Label>
                                    <Input type="textarea" name="description" value={this.state.description}
                                        onChange={(e) => {
                                            this.setState({ description: e.target.value })
                                        }}/>
                                </FormGroup>

                                <Button color="primary" onClick={this.saveTask}>{translations.save}</Button>
                            </div>

                        </div>
                    )
                }}
            </Draggable>
        )
    }
}
