import React, { Component } from 'react'
import { Button, FormGroup, Input, Label } from 'reactstrap'
import { Draggable } from 'react-beautiful-dnd'
import { translations } from '../../utils/_translations'
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
                        <React.Fragment>
                            <div className="p-2" style={{
                                userSelect: 'none',
                                backgroundColor: snapshot.isDragging
                                    ? '#263B4A'
                                    : '',
                                color: 'white',
                                ...provided.draggableProps.style
                            }} ref={provided.innerRef}
                            {...provided.draggableProps}
                            {...provided.dragHandleProps}>
                                <h6 onClick={(e) => {
                                    this.setState({ show_edit: !this.state.show_edit })
                                }} className="card-title text-uppercase text-truncate py-2">{item.name}</h6>

                                <div className={this.state.show_edit === true ? 'd-block card' : 'd-none card'}>
                                    <div className="card-body">
                                        <FormGroup className="mb-2">
                                            <Label>{translations.name}</Label>
                                            <Input type="text" name="name" value={this.state.name}
                                                onChange={(e) => {
                                                    this.setState({ name: e.target.value })
                                                }}/>
                                        </FormGroup>

                                        <Button color="link" onClick={(e) => {
                                            this.setState({ show_edit: false })
                                        }}>{translations.cancel}</Button>
                                        <Button color="primary" className="ml-2"
                                            onClick={this.saveStatus}>{translations.save}</Button>
                                    </div>
                                </div>
                            </div>
                        </React.Fragment>

                    )
                }}
            </Draggable>
        )
    }
}
