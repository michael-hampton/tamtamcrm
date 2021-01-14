import React, { Component } from 'react'
import { Col, FormGroup, Input, Label, Row } from 'reactstrap'
import ProductAttributeDropdown from './dropdowns/ProductAttributeDropdown'
import ProductDropdown from './dropdowns/ProductDropdown'
import TaskDropdown from './dropdowns/TaskDropdown'
import ExpenseDropdown from './dropdowns/ExpenseDropdown'
import FormatMoney from './FormatMoney'
import { translations } from '../utils/_translations'
import { consts } from '../utils/_consts'
import ProjectDropdown from './dropdowns/ProjectDropdown'
import DeleteModal from './DeleteModal'

class LineItem extends Component {
    constructor (props) {
        super(props)
        // this.state = Object.assign({}, props.lineItemData)
        this.handleDeleteClick = this.handleDeleteClick.bind(this)
        const account_id = JSON.parse(localStorage.getItem('appState')).user.account_id
        const user_account = JSON.parse(localStorage.getItem('appState')).accounts.filter(account => account.account_id === parseInt(account_id))
        this.settings = user_account[0].account.settings
    }

    handleDeleteClick () {
        this.props.onDelete(this.props.id)
    }

    renderErrorFor () {

    }

    render () {
        const uses_inclusive_taxes = this.settings.inclusive_taxes
        const color = localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true' ? 'text-secondary' : 'text-dark'

        return this.props.rows.map((lineItem, index) => {
            let total = 0

            if (lineItem.unit_price > 0 && lineItem.quantity > 0) {
                total = lineItem.unit_price * lineItem.quantity

                if (lineItem.unit_discount > 0) {
                    if (this.props.invoice.is_amount_discount === true) {
                        total -= lineItem.unit_discount
                    } else {
                        const percentage = total * lineItem.unit_discount / 100
                        total -= percentage
                    }
                }

                if (lineItem.unit_tax > 0) {
                    const tax_percentage = total * lineItem.unit_tax / 100

                    if (uses_inclusive_taxes === false) {
                        total += tax_percentage
                    }
                }
            }

            return lineItem.type_id === this.props.line_type ? <React.Fragment key={index}>
                <Row className="border-bottom border-primary my-3" form>
                    {lineItem.type_id === 1 &&
                    <Col md={3} data-id={index}>
                        <FormGroup>
                            <Label>{translations.product}</Label>
                            <ProductDropdown
                                dataId={index}
                                renderErrorFor={this.renderErrorFor}
                                name="product_id"
                                handleInputChanges={this.props.onChange}
                                product={lineItem.product_id}
                                products={this.props.products}
                            />
                        </FormGroup>
                    </Col>
                    }

                    {lineItem.type_id === consts.line_item_task && this.props.tasks.length &&
                    <Col md={3} data-id={index}>
                        <FormGroup>
                            <Label>{translations.task}</Label>
                            <TaskDropdown
                                tasks={this.props.tasks}
                                dataId={index}
                                single_only={true}
                                renderErrorFor={this.renderErrorFor}
                                name="task_id"
                                handleInputChanges={this.props.onChange}
                                task={lineItem.task_id}

                            />
                        </FormGroup>
                    </Col>
                    }

                    {lineItem.type_id === consts.line_item_expense && this.props.expenses.length &&
                    <Col md={3} data-id={index}>
                        <FormGroup>
                            <Label>{translations.expense}</Label>
                            <ExpenseDropdown
                                dataId={index}
                                expenses={this.props.expenses}
                                renderErrorFor={this.renderErrorFor}
                                name="expense_id"
                                handleInputChanges={this.props.onChange}
                                expense={lineItem.expense_id}

                            />
                        </FormGroup>
                    </Col>
                    }

                    {lineItem.type_id === consts.line_item_project && this.props.projects.length &&
                    <Col md={3} data-id={index}>
                        <FormGroup>
                            <Label>{translations.project}</Label>
                            <ProjectDropdown
                                dataId={index}
                                projects={this.props.projects}
                                renderErrorFor={this.renderErrorFor}
                                name="project_id"
                                handleInputChanges={this.props.onChange}
                                project={lineItem.project_id}

                            />
                        </FormGroup>
                    </Col>
                    }

                    <Col md={2} data-id={index}>
                        <FormGroup>
                            <Label>{lineItem.type_id === consts.line_item_task ? translations.rate : translations.price}</Label>
                            <Input key={`a-${index}`} name="unit_price" data-line={index} type='text' data-column="5"
                                value={lineItem.unit_price} onChange={this.props.onChange}
                                className='pa2 mr2 f6 form-control'/>
                        </FormGroup>
                    </Col>

                    <Col md={1} data-id={index}>
                        <FormGroup>
                            <Label>{lineItem.type_id === consts.line_item_task ? translations.hours : translations.quantity}</Label>
                            <Input key={`b-${index}`} name="quantity" data-line={index} type='text'
                                value={lineItem.quantity}
                                onChange={this.props.onChange} className='pa2 mr2 f6 form-control'/>
                        </FormGroup>
                    </Col>

                    <Col md={2} data-id={index}>
                        <FormGroup>
                            <Label>{translations.discount}</Label>
                            <Input key={`c-${index}`} name="unit_discount" data-line={index} type='text'
                                value={lineItem.unit_discount}
                                onChange={this.props.onChange} className='pa2 mr2 f6 form-control'/>
                        </FormGroup>
                    </Col>

                    <Col md={2} data-id={index}>
                        <FormGroup>
                            <Label>{translations.tax}</Label>
                            <Input key={`d_${index}`} name="unit_tax" data-line={index} type='select'
                                value={lineItem.tax_rate_id}
                                onChange={this.props.onChange} className='pa2 mr2 f6 form-control'>
                                <option value="0">No Tax</option>
                                {this.props.tax_rates.map(tax_rate =>
                                    <option key={tax_rate.id} data-rate={tax_rate.rate}
                                        value={tax_rate.id}>{`${tax_rate.name} (${tax_rate.rate})`}</option>
                                )}
                            </Input>
                        </FormGroup>
                    </Col>
                    <FormGroup className="mr-4">
                        <Label>{translations.tax_total}</Label>
                        <p className='pa2 mr2 f6'>{<FormatMoney
                            amount={lineItem.tax_total}/>}</p>
                    </FormGroup>

                    <FormGroup>
                        <Label>{translations.subtotal}</Label>
                        <p className='pa2 mr2 f6'>{<FormatMoney
                            amount={total}/>}</p>
                    </FormGroup>

                    <Col md={2} data-id={index}>
                        <FormGroup>
                            <Label>{translations.description}</Label>
                            <Input key={`e-${index}`} name="description" data-line={index} type='text'
                                value={lineItem.description}
                                onChange={this.props.onChange} className='pa2 mr2 f6 form-control'/>
                        </FormGroup>
                    </Col>

                    {lineItem.type_id === consts.line_item_product &&
                    <Col md={3} data-id={index}>
                        <FormGroup>
                            <Label>{translations.variation}</Label>
                            <ProductAttributeDropdown
                                dataId={index}
                                renderErrorFor={this.renderErrorFor}
                                name="attribute_id"
                                handleInputChanges={this.props.onChange}
                                attribute_value_id={lineItem.attribute_id}
                                product_id={lineItem.product_id}
                            />
                        </FormGroup>
                    </Col>
                    }

                    <Col className="pt-4" md={2} data-id={index}>
                        <DeleteModal is_button={true} deleteFunction={this.props.onDelete} archive={false}
                            id={index}/>
                    </Col>
                </Row>
            </React.Fragment> : null
        })
    }
}

export default LineItem
