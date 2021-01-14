import React, { Component } from 'react'
import { ButtonDropdown, DropdownMenu, DropdownToggle, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../utils/_translations'

export default class StatusDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            allowed_statuses: [],
            dropdownOpen: false,
            statuses: this.props.statuses && this.props.statuses.length ? this.props.statuses : []
        }

        this.filterStatuses = this.filterStatuses.bind(this)
        this.toggle = this.toggle.bind(this)
    }

    componentDidMount () {
        this.state.statuses.push({ value: 'active', label: translations.active })
        this.state.statuses.push({ value: 'archived', label: translations.archived })
        this.state.statuses.push({ value: 'deleted', label: translations.deleted })
    }

    filterStatuses (event) {
        const allowed_statuses = this.state.allowed_statuses
        const check = event.target.checked
        const selected_status = !isNaN(event.target.value) ? parseInt(event.target.value) : event.target.value
        const fieldToUpdate = this.props.name && this.props.name.length ? this.props.name : 'status_id'

        const e = {}

        if (check) {
            this.setState({
                allowed_statuses: [...this.state.allowed_statuses, selected_status]
            }, () => {
                console.log('status', this.state.allowed_statuses.join(','))
                e.target = {
                    id: fieldToUpdate,
                    value: this.state.allowed_statuses.join(',')
                }
                this.props.filterStatus(e)
            })

            return
        }

        const index = allowed_statuses.indexOf(selected_status)
        if (index > -1) {
            allowed_statuses.splice(index, 1)
            this.setState({
                allowed_statuses: allowed_statuses
            }, () => {
                console.log('status', this.state.allowed_statuses.join(','))
                e.target = {
                    id: fieldToUpdate,
                    value: this.state.allowed_statuses.join(',')
                }

                this.props.filterStatus(e)
            })
        }
    }

    toggle () {
        this.setState({
            dropdownOpen: !this.state.dropdownOpen
        })
    }

    buildDropdownMenu (list) {
        return (
            <ButtonDropdown isOpen={this.state.dropdownOpen} toggle={this.toggle}>
                <DropdownToggle caret className="status-dropdown">
                    {translations.select_status}
                </DropdownToggle>
                <DropdownMenu style={{ width: '100%' }}>
                    {list}
                </DropdownMenu>
            </ButtonDropdown>
        )
    }

    render () {
        const list = this.state.statuses.map((status, index) => {
            const currentValue = !isNaN(status.value) ? parseInt(status.value) : status.value
            const isChecked = this.state.allowed_statuses.includes(currentValue)
            return (
                <li className="p-1" style={{ lineHeight: '32px' }} key={index}>
                    <FormGroup check>
                        <Label check>
                            <Input className="mt-2" checked={isChecked} onClick={this.filterStatuses} type="checkbox"
                                value={status.value}/>
                            {status.label}
                        </Label>
                    </FormGroup>
                </li>
            )
        })

        return this.buildDropdownMenu(list)
    }
}
