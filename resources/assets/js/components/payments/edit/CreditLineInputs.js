import React from 'react'
import { Button, Col, FormGroup, Input, Label, Row } from 'reactstrap'
import CreditDropdown from '../../common/dropdowns/CreditDropdown'
import { translations } from '../../utils/_translations'

const CreditLineInputs = (props) => {
    return (
        props.lines.map((val, idx) => {
            return (
                <div key={idx}>
                    <Row form>
                        <Col md={6}>
                            <FormGroup>
                                <Label for="exampleEmail">{translations.credit}</Label>
                                <CreditDropdown
                                    refund={props.refund}
                                    payment={props.payment}
                                    allowed_credits={props.allowed_credits}
                                    customer={props.payment.customer_id}
                                    credits={props.credits}
                                    data_id={idx}
                                    credit_id={props.lines[idx].credit_id}
                                    status={props.status}
                                    handleInputChanges={props.onChange}
                                    name="credit_id"
                                    errors={props.errors}
                                />
                            </FormGroup>
                        </Col>
                        <Col md={6}>
                            <FormGroup>
                                <Label for="examplePassword">{translations.amount}</Label>
                                <Input type="text"
                                    data-credit={props.credits.length === 1 ? props.credits[0].id : 'test'}
                                    data-id={idx}
                                    onChange={props.onChange}
                                    value={props.lines[idx].amount}
                                    autoFocus={(props.credits && props.credits.length === 1) || idx === 0}
                                    name="amount"
                                />
                            </FormGroup>
                        </Col>
                    </Row>

                    <Button className="btn-sm" color="danger" onClick={() => props.removeLine(idx)}>
                        {translations.remove}
                    </Button>
                </div>
            )
        })
    )
}
export default CreditLineInputs
