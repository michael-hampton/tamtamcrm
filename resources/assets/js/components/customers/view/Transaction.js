import React from 'react'
import FormatMoney from '../../common/FormatMoney'
import FormatDate from '../../common/FormatDate'
import { Badge, ListGroup, ListGroupItem } from 'reactstrap'
import { getEntityIcon } from '../../utils/_icons'
import { translations } from '../../utils/_translations'

export default function Transaction (props) {
    const transactions = props.transactions.length ? props.transactions.map((transaction, index) => {
        const amount = transaction.amount
        const text_color = amount.toString().includes('-') ? 'danger' : 'success'

        return (<ListGroupItem key={index}
            className="list-group-item-dark list-group-item-action flex-column align-items-start">
            <div className="d-flex w-100 justify-content-between">
                <h5 className="mb-1">
                    <i className={`fa ${getEntityIcon(transaction.entity_name)} mr-4`}/>
                    {translations[transaction.entity_name.toLowerCase()]} > {transaction.entity_number} </h5>
                <FormatMoney className="lead mb-1" amount={transaction.updated_balance}/>
            </div>

            <div className="d-flex w-100 justify-content-between">
                <span style={{ fontSize: '16px' }} className="text-muted"><FormatDate date={transaction.created_at}
                    with_time={true}/></span>
                <Badge color={text_color}><FormatMoney amount={amount}/></Badge>
            </div>
        </ListGroupItem>)
    }) : null

    return <ListGroup>
        {transactions}
    </ListGroup>
}
