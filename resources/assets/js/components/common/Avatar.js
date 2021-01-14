/* eslint-disable no-unused-vars */
import React, { Component } from 'react'

class Avatar extends Component {
    render () {
        const large = this.props.lg && this.props.lg === true ? 'avatar-circle-md ml-3' : 'avatar-circle-sm'
        const className = this.props.className ? this.props.className : ''
        const initials = this.props.name.split(' ').map(x => x.charAt(0)).join('').substr(0, 2).toUpperCase()
        const classNames = this.props.inline && this.props.inline === true ? 'avatar-circle avatar-circle-xs d-inline-block m-2' : 'avatar-circle ' + large + ' ' + className

        return (
            <React.Fragment>
                <div title={this.props.name} className={classNames}>
                    <span className='initials'>{initials}</span>
                </div>
            </React.Fragment>
        )
    }
}

export default Avatar
