import React from 'react'

export default function MetaItem (props) {
    return <span><img style={{ height: '20px' }}
        src={`/img/payment_methods/${props.meta.brand}.png`}/> {`****${props.meta.last4} ${props.meta.exp_month}/${props.meta.exp_year}`}</span>
}
