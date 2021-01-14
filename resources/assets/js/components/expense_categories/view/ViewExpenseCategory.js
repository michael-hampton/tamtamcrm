import React from 'react'
import { ListGroup, Row } from 'reactstrap'
import { translations } from '../../utils/_translations'
import SectionItem from '../../common/entityContainers/SectionItem'
import { getEntityIcon } from '../../utils/_icons'

export default function Audit (props) {
    const count = props.entity.expense_count > 0 ? props.entity.expense_count : translations.none
    return (
        <Row>
            <ListGroup className="col-12">
                <SectionItem link={`/#/expenses?customer_id=${props.entity.id}`}
                    icon={getEntityIcon('Expense')} title={translations.expenses + ' ' + count}/>

            </ListGroup>
        </Row>

    )
}
