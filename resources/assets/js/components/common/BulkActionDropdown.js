import React, { Component } from 'react'
import { ButtonDropdown, DropdownItem, DropdownMenu, DropdownToggle, UncontrolledTooltip } from 'reactstrap'
import { icons } from '../utils/_icons'
import { translations } from '../utils/_translations'

export default class BulkActionDropdown extends Component {
    constructor (props) {
        super(props)

        this.state = {
            dropdownButtonOpen: false
        }

        this.toggleDropdownButton = this.toggleDropdownButton.bind(this)
    }

    toggleDropdownButton (event) {
        this.setState({
            dropdownButtonOpen: !this.state.dropdownButtonOpen
        })
    }

    render () {
        return (
            <React.Fragment>
                <UncontrolledTooltip placement="top" target="bulkActionTooltip">
                    {translations.bulk_actions}
                </UncontrolledTooltip>

                <ButtonDropdown className="mr-2" isOpen={this.state.dropdownButtonOpen}
                    toggle={this.toggleDropdownButton}>
                    <DropdownToggle caret color="primary">
                        <i id="bulkActionTooltip" className={`fa ${icons.ellipsis}`} aria-hidden="true"
                            type="ellipsis"/> {translations.bulk_action}
                    </DropdownToggle>
                    <DropdownMenu className="bulk-options-menu">
                        {this.props.dropdownButtonActions.map(e => {
                            let column_name = e.replace(/_/g, ' ')
                            column_name = column_name.replace(
                                /\w\S*/g,
                                function (txt) {
                                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()
                                }
                            )
                            return <DropdownItem id={e} key={e}
                                onClick={this.props.saveBulk}>{column_name}</DropdownItem>
                        })}
                    </DropdownMenu>
                </ButtonDropdown>
            </React.Fragment>
        )
    }
}
