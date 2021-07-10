import React, { Component } from 'react'
import { DropdownItem, DropdownMenu, DropdownToggle, Input, InputGroup, InputGroupButtonDropdown } from 'reactstrap'
import { translations } from '../utils/_translations'

export default class Duration extends Component {
    constructor (props) {
        super(props)

        this.state = {
            placeholder: '',
            dropdownOpen: false
        }
    }

    render () {
        const options = []
        const times = [15, 30, 45, 60, 75, 90, 105, 120]

        times.forEach(mins => {
            let h = Math.floor(mins / 60)
            let m = mins % 60
            h = h < 10 ? '0' + h : h
            m = m < 10 ? '0' + m : m
            const formatted_time = `${h}:${m}`

            options.push(
                <DropdownItem onClick={(event) => {
                    const e = {}
                    e.target = {
                        value: formatted_time
                    }
                    this.props.onChange(e)
                }}>{formatted_time}</DropdownItem>
            )
        })

        return <InputGroup className="btn-group">
            <Input style={{ width: '80%' }} type="text" onChange={(e) => {
                this.props.onChange(e)
                this.setState({ placeholder: e.target.value })
            }}/>
            <InputGroupButtonDropdown addonType="append" isOpen={this.state.dropdownOpen} toggle={(e) => {
                this.setState({ dropdownOpen: !this.state.dropdownOpen })
            }}>
                <DropdownToggle caret/>
                <DropdownMenu>
                    <DropdownItem>{translations.change_duration}</DropdownItem>
                    {options}
                </DropdownMenu>
            </InputGroupButtonDropdown>
        </InputGroup>
    }
}
