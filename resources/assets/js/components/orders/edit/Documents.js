import React from 'react'
import { Card, CardBody, CardHeader } from 'reactstrap'
import FileUploads from '../../documents/FileUploads'
import { translations } from '../../utils/_translations'

export default function Documents (props) {
    return (
        <Card>
            <CardHeader>{translations.documents}</CardHeader>
            <CardBody>
                <FileUploads entity_type="Order" entity={props.order}
                    user_id={props.order.user_id}/>
            </CardBody>
        </Card>

    )
}
