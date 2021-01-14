import React from 'react'
import { Card, CardBody, CardHeader } from 'reactstrap'
import LineItemEditor from '../../common/LineItemEditor'

export default function Items (props) {
    return (
        <Card>
            <CardHeader>Items</CardHeader>
            <CardBody>
                <LineItemEditor
                    model={props.model}
                    line_type={props.line_type}
                    customers={props.customers}
                    invoice={props.invoice}
                    delete={props.handleDelete}
                    update={props.handleFieldChange}
                    onAddFiled={props.handleAddFiled}
                    setTotal={props.setTotal}
                />
                <br/>
                <br/>
            </CardBody>
        </Card>

    )
}
