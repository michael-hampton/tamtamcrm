import React from 'react'
import { Card, CardBody, CardHeader } from 'reactstrap'
import FileUploads from '../../documents/FileUploads'
import { translations } from '../../utils/_translations'

export default function Documents (props) {
    return (
        <Card>
            <CardHeader>{translations.documents}</CardHeader>
            <CardBody>
                <FileUploads entity_type="Payment" entity={props.payment}
                    user_id={props.payment.user_id}/>
            </CardBody>
        </Card>

    )
}
