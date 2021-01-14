import React from 'react'
import { Card, CardBody, CardHeader, FormGroup, Input, Label } from 'reactstrap'
import UserDropdown from '../../common/dropdowns/UserDropdown'
import { translations } from '../../utils/_translations'
import CustomerDropdown from '../../common/dropdowns/CustomerDropdown'
import Datepicker from '../../common/Datepicker'
import TaskStatusDropdown from '../../common/dropdowns/TaskStatusDropdown'
import DesignDropdown from '../../common/dropdowns/DesignDropdown'
import ProjectDropdown from '../../common/dropdowns/ProjectDropdown'
import ColorPickerNew from '../../common/ColorPickerNew'

export default class Details extends React.Component {
    constructor (props) {
        super(props)

        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.buildSourceTypeOptions = this.buildSourceTypeOptions.bind(this)
    }

    hasErrorFor (field) {
        return !!this.props.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    buildSourceTypeOptions () {
        let sourceTypeContent
        if (!this.props.sourceTypes.length) {
            sourceTypeContent = <option value="">Loading...</option>
        } else {
            sourceTypeContent = this.props.sourceTypes.map((user, index) => (
                <option key={index} value={user.id}>{user.name}</option>
            ))
        }

        return (
            <FormGroup>
                <Label for="source_type">Source Type:</Label>
                <Input value={this.props.deal.source_type}
                    className={this.hasErrorFor('source_type') ? 'is-invalid' : ''} type="select"
                    name="source_type" id="source_type" onChange={this.props.handleInput}>
                    <option value="">Choose:</option>
                    {sourceTypeContent}
                </Input>
                {this.renderErrorFor('source_type')}
            </FormGroup>
        )
    }

    render () {
        const sourceTypeOptions = this.buildSourceTypeOptions()

        return (
            <React.Fragment>
                <Card>
                    <CardHeader>{translations.details}</CardHeader>
                    <CardBody>
                        <FormGroup>
                            <Label for="name"> {translations.name} </Label>
                            <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''} type="text"
                                id="name" onChange={this.props.handleInput}
                                name="name"
                                value={this.props.deal.name}
                                placeholder={translations.name}/>
                            {this.renderErrorFor('name')}
                        </FormGroup>

                        <FormGroup>
                            <Label for="description"> {translations.description} </Label>
                            <Input className={this.hasErrorFor('description') ? 'is-invalid' : ''} type="textarea"
                                id="first_name" onChange={this.props.handleInput} name="description"
                                value={this.props.deal.description}
                                placeholder={translations.description}/>
                            {this.renderErrorFor('description')}
                        </FormGroup>

                        <FormGroup>
                            <Label>{translations.due_date}</Label>
                            <Datepicker name="due_date" date={this.props.deal.due_date}
                                handleInput={this.props.handleInput}
                                className={this.hasErrorFor('due_date') ? 'form-control is-invalid' : 'form-control'}/>
                        </FormGroup>

                        <FormGroup className="mb-3">
                            <Label>{translations.customer}</Label>
                            <CustomerDropdown
                                customer={this.props.deal.customer_id}
                                renderErrorFor={this.renderErrorFor}
                                handleInputChanges={this.props.handleInput}
                                customers={this.props.customers}
                            />
                            {this.renderErrorFor('customer_id')}
                        </FormGroup>

                        <FormGroup>
                            <Label for="valued_at"> {translations.amount} </Label>
                            <Input className={this.hasErrorFor('valued_at') ? 'is-invalid' : ''} type="text"
                                id="valued_at"
                                value={this.props.deal.valued_at}
                                onChange={this.props.handleInput.bind(this)} name="valued_at"
                                placeholder={translations.amount}/>
                            {this.renderErrorFor('valued_at')}
                        </FormGroup>

                        <FormGroup>
                            <Label>{translations.assigned_user}</Label>
                            <UserDropdown handleInputChanges={this.props.handleInput}
                                user_id={this.props.deal.assigned_to} name="assigned_to"
                                users={this.props.users}/>
                        </FormGroup>

                        <FormGroup>
                            <Label>{translations.status}</Label>
                            <TaskStatusDropdown
                                task_type={2}
                                status={this.props.deal.task_status_id}
                                handleInputChanges={this.props.handleInput}
                            />
                        </FormGroup>

                        <FormGroup>
                            <Label>{translations.design}</Label>
                            <DesignDropdown name="design_id" design={this.props.deal.design_id}
                                handleChange={this.props.handleInput}/>
                        </FormGroup>

                        <FormGroup>
                            <Label>{translations.project}</Label>
                            <ProjectDropdown handleInputChanges={this.props.handleInput}
                                customer-id={this.props.deal.customer_id}
                                project={this.props.deal.project_id} name="project_id"
                            />
                        </FormGroup>

                        <ColorPickerNew color={this.props.deal.column_color} onChange={(color) => {
                            const e = {}
                            e.target = {
                                name: 'column_color',
                                value: color
                            }

                            this.props.handleInput(e)
                        }}/>

                        {sourceTypeOptions}
                    </CardBody>
                </Card>

            </React.Fragment>
        )
    }
}
