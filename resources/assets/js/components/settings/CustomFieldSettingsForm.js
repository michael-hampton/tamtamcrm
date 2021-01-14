import React from 'react'
import { Col, Form, FormGroup, Input, Label, Row } from 'reactstrap'
import DynamicOptionList from './DynamicOptionList'
import { consts } from '../utils/_consts'

export default class CustomFieldSettingsForm extends React.Component {
    render () {
        return (
            <React.Fragment>
                <Form className="clearfix" key={this.props.idx}>
                    <Row form>
                        <Col md={6}>
                            <FormGroup>
                                <Label htmlFor={this.props.ageId}>Label</Label>
                                <Input
                                    type="text"
                                    name={this.props.ageId}
                                    data-id={this.props.idx}
                                    data-entity={this.props.entity}
                                    id={this.props.ageId}
                                    data-field="label"
                                    onChange={this.props.handleChange}
                                    value={this.props.label}
                                />
                            </FormGroup>
                        </Col>
                        <Col md={6}>
                            <FormGroup className="mb-4" key={this.props.idx}>
                                <Label htmlFor={this.props.catId}>{`Custom Field #${this.props.idx + 1}`}</Label>
                                <Input
                                    type="select"
                                    name={this.props.catId}
                                    data-id={this.props.idx}
                                    data-entity={this.props.entity}
                                    id={this.props.catId}
                                    data-field="type"
                                    onChange={this.props.handleChange}
                                    value={this.props.type}
                                >
                                    <option value={consts.text}>Text</option>
                                    <option value={consts.textarea}>Textarea</option>
                                    <option value={consts.select}>Select List</option>
                                    <option value={consts.switch}>Switch</option>
                                    <option value={consts.date}>Date</option>
                                </Input>
                            </FormGroup>
                        </Col>
                    </Row>

                    {this.props.type === 'select' &&
                    <div className="row col-12">
                        <DynamicOptionList showCorrectColumn={false}
                            data-entity={this.props.entity}
                            data-id={this.props.idx}
                            canHaveOptionCorrect={false}
                            canHaveOptionValue={true}
                            // data={this.props.preview.state.data}
                            updateElement={this.props.handleOptionChange}
                            // preview={this.props.preview}
                            element={Object.assign(this.props.obj, {
                                data_id: this.props.idx,
                                data_entity: this.props.entity
                            })}
                            key={this.props.obj.options.length}/>
                    </div>
                    }
                </Form>
            </React.Fragment>
        )
    }
}
