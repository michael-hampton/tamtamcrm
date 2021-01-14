import React, { Component } from 'react'
import { FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'
import Datepicker from '../../common/Datepicker'
import CaseCategoryDropdown from '../../common/dropdowns/CaseCategoryDropdown'
import CasePriorityDropdown from '../../common/dropdowns/CasePriorityDropdown'
import UserDropdown from '../../common/dropdowns/UserDropdown'
import CaseDropdown from '../../common/dropdowns/CaseDropdown'

export default class Details extends Component {
    render () {
        return (
            <React.Fragment>
                <FormGroup>
                    <Label for="subject">{translations.subject} <span className="text-danger">*</span></Label>
                    <Input className={this.props.hasErrorFor('subject') ? 'is-invalid' : ''} type="text"
                        name="subject"
                        id="subject" value={this.props.case.subject} placeholder={translations.subject}
                        onChange={this.props.handleInput}/>
                    {this.props.renderErrorFor('subject')}
                </FormGroup>

                <FormGroup>
                    <Label for="message">{translations.message}<span className="text-danger">*</span></Label>
                    <Input className={this.props.hasErrorFor('message') ? 'is-invalid textarea-lg' : 'textarea-lg'}
                        type="textarea" name="message"
                        id="message" value={this.props.case.message} placeholder={translations.message}
                        onChange={this.props.handleInput}/>
                    {this.props.renderErrorFor('message')}
                </FormGroup>

                {/* <FormGroup> */}
                {/*    <Label for="description">{translations.customer}(*):</Label> */}
                {/*    <CustomerDropdown */}
                {/*        customer={this.props.case.customer_id} */}
                {/*        errors={this.props.errors} */}
                {/*        renderErrorFor={this.props.renderErrorFor} */}
                {/*        handleInputChanges={this.props.handleInput} */}
                {/*        customers={this.props.customers} */}
                {/*    /> */}
                {/* </FormGroup> */}

                <FormGroup>
                    <Label for="examplePassword">{translations.due_date}</Label>
                    <Datepicker className="form-control" name="due_date" date={this.props.case.due_date}
                        handleInput={this.props.handleInput}/>
                </FormGroup>

                <FormGroup>
                    <Label for="examplePassword">{translations.private_notes}</Label>
                    <Input value={this.props.case.private_notes} type="textarea"
                        name="private_notes"
                        onChange={this.props.handleInput} id="private_notes"
                    />
                </FormGroup>

                <FormGroup>
                    <Label for="examplePassword">{translations.priority}</Label>
                    <CasePriorityDropdown
                        name="priority_id"
                        priority={this.props.case.priority_id}
                        renderErrorFor={this.renderErrorFor}
                        handleInputChanges={this.props.handleInput}
                    />
                </FormGroup>

                <FormGroup>
                    <Label>{translations.category}</Label>
                    <CaseCategoryDropdown
                        name="category_id"
                        category={this.props.case.category_id}
                        errors={this.props.errors}
                        renderErrorFor={this.props.renderErrorFor}
                        handleInputChanges={this.props.handleInput}
                    />
                </FormGroup>

                <FormGroup>
                    <Label for="postcode">{translations.assigned_user}:</Label>
                    <UserDropdown
                        user_id={this.props.case.assigned_to}
                        name="assigned_to"
                        errors={this.props.errors}
                        handleInputChanges={this.props.handleInput}
                    />
                </FormGroup>

                <FormGroup>
                    <Label for="postcode">{translations.parent}:</Label>
                    <CaseDropdown
                        cases={this.props.cases}
                        case_id={this.props.case.parent_id}
                        name="parent_id"
                        errors={this.props.errors}
                        handleInputChanges={this.props.handleInput}
                    />
                </FormGroup>
            </React.Fragment>
        )
    }
}
