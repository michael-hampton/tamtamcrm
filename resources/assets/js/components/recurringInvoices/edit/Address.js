/* eslint-disable no-unused-vars */
import React, { Component } from 'react'

export default class extends Component {
    render () {
        const address = this.props.address
        return (
            <div className='pv5 f6'>
                <p className='mv0'>{address.line1}</p>
                <p className='mv0'>{address.town}</p>
                <p className='mv0'>{address.county}</p>
                <p className='mv0'>{address.country}</p>
            </div>
        )
    }
}
