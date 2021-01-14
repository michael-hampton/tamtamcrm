/* eslint-disable no-unused-vars */
import React, { Component } from 'react'

export default class Tab extends Component {
    constructor (props) {
        super(props)
        this.onClick = this.onClick.bind(this)
    }

    onClick () {
        const { label, onClick } = this.props
        onClick(label)
    }

    render () {
        const { onClick, props: { activeTab, label } } = this
        let className = 'nav-item nav-link'
        if (activeTab === label) {
            className += ' active'
        }
        return (
            <a
                className={className}
                onClick={onClick}
            >
                {label}
            </a>
        )
    }
}
