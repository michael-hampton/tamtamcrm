import React from 'react'
import BulkActionDropdown from './BulkActionDropdown'
import { icons } from '../utils/_icons'
import { UncontrolledTooltip } from 'reactstrap'
import { translations } from '../utils/_translations'

export default function TableToolbar (props) {
    const text_color = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true')
        ? 'text-white' : 'text-dark'
    return (
        <React.Fragment>
            <UncontrolledTooltip placement="top" target="refresh">
                {translations.refresh}
            </UncontrolledTooltip>

            <UncontrolledTooltip placement="top" target="toggle-checkbox">
                {translations.toggle_checkbox}
            </UncontrolledTooltip>

            <UncontrolledTooltip placement="top" target="toggle-table">
                {translations.toggle_table}
            </UncontrolledTooltip>

            <UncontrolledTooltip placement="top" target="toggle-columns">
                {translations.toggle_columns}
            </UncontrolledTooltip>

            <UncontrolledTooltip placement="top" target="view-entity">
                {translations.preview}
            </UncontrolledTooltip>

            <div style={{ lineHeight: '32px' }} className="row justify-content-end">
                {props.dropdownButtonActions && <BulkActionDropdown
                    dropdownButtonActions={props.dropdownButtonActions}
                    saveBulk={props.saveBulk}/>}
                <i onClick={props.handleTableActions} id="refresh" className={`fa ${icons.refresh} ${text_color}`}
                    style={{ fontSize: '28px', cursor: 'pointer', marginRight: '6px' }}/>
                <i onClick={props.handleTableActions} id="toggle-checkbox" className={`fa ${icons.checkbox} mr-2`}
                    style={{ fontSize: '28px' }}/>
                <i onClick={props.handleTableActions} id="toggle-table" className={`fa ${icons.table} mr-2`}
                    style={{ fontSize: '28px' }}/>
                <i onClick={props.handleTableActions} id="toggle-columns" className={`fa ${icons.columns} mr-2`}
                    style={{ fontSize: '28px' }}/>
                <i onClick={props.handleTableActions} id="view-entity" className={`fa ${icons.view} mr-4`}
                    style={{ fontSize: '28px' }}/>
            </div>
        </React.Fragment>

    )
}
