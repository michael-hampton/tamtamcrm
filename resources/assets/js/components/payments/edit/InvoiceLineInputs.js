import React from 'react'
import { Button, Col, FormGroup, Input, Label, Row } from 'reactstrap'
import InvoiceDropdown from '../../common/dropdowns/InvoiceDropdown'
import { translations } from '../../utils/_translations'

const InvoiceLineInputs = (props) => {
    return (
        props.lines.map((val, idx) => {
            return (
                <div key={idx}>
                    <Row form>
                        <Col md={6}>
                            <FormGroup>
                                <Label for="exampleEmail">{translations.invoice}</Label>
                                <InvoiceDropdown
                                    payment={props.payment}
                                    refund={props.refund}
                                    allowed_invoices={props.allowed_invoices}
                                    customer={props.payment.customer_id}
                                    invoices={props.invoices}
                                    data_id={idx}
                                    invoice_id={props.lines[idx].invoice_id}
                                    status={props.status}
                                    handleInputChanges={props.onChange}
                                    name="invoice_id"
                                    errors={props.errors}
                                />
                            </FormGroup>
                        </Col>
                        <Col md={6}>
                            <FormGroup>
                                <Label for="examplePassword">{translations.amount}</Label>
                                <Input type="text"
                                    data-invoice={props.invoices.length === 1 ? props.invoices[0].id : 'test'}
                                    data-id={idx}
                                    onChange={props.onChange}
                                    value={props.lines[idx].amount}
                                    autoFocus={(props.invoices && props.invoices.length === 1) || idx === 0}
                                    name="amount"
                                />
                            </FormGroup>
                        </Col>
                    </Row>

                    {idx !== 0 &&
                    <Button className="btn-sm" color="danger" onClick={() => props.removeLine(idx)}>
                        {translations.remove}
                    </Button>
                    }

                </div>
            )
        })
    )
}
export default InvoiceLineInputs
