import React, { Component } from 'react'
import { Button } from 'reactstrap'

export default class BlockButton extends Component {
    render () {
        return (
            <Button className={this.props.className || ''} style={{ marginBottom: '30px' }} onClick={(e) => {
                if (this.props.onClick) {
                    this.props.onClick()
                } else {
                    e.preventDefault()
                    window.location.href = this.props.button_link
                }
            }} color="primary" size="lg" block><i
                    style={{ transform: 'rotate(20deg)', marginRight: '14px', fontSize: '24px' }}
                    className={`fa ${this.props.icon}`}/>{this.props.button_text}</Button>
        )
    }
}
