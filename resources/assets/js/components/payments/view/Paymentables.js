import React from 'react'
import { ListGroup, Row } from 'reactstrap'
import { translations } from '../../utils/_translations'
import Paymentable from '../../common/entityContainers/Paymentable'

export default function Paymentables (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    return <React.Fragment>

        <Row>
            <ListGroup className="col-12 mt-2 mb-2">
                {!!props.paymentableInvoices && props.paymentableInvoices.map((line_item, index) => (
                    <Paymentable entity={translations.invoice} link={`/#/invoice?number=${line_item.number}`}
                        key={index} line_item={line_item}/>
                ))}
            </ListGroup>
        </Row>

        <Row className="mb-2">
            <ListGroup className="col-12 mt-2">
                {!!props.paymentableCredits && props.paymentableCredits.map((line_item, index) => (
                    <Paymentable entity={translations.credit} link={`/#/credits?number=${line_item.number}`}
                        key={index} line_item={line_item}/>
                ))}
            </ListGroup>
        </Row>
    </React.Fragment>
}
