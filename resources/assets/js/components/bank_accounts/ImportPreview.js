import React, { Component } from 'react'
import classNames from 'classnames'
import DataRow from './DataRow'

export default class ImportPreview extends Component {
    constructor (props) {
        super(props)

        this.state = {
            checkedRows: []
        }
    }

    static noop () {
        return null
    }

    static rowRenderer ({ row, onClick, buttons, fields, onMouseUp, onMouseDown, renderCheckboxes, disableCheckbox, checkboxIsChecked, onCheckboxChange, dataItemManipulator, dangerouslyRenderFields, actions, editableColumns, index }) {
        return (
            <DataRow
                key={row.id}
                row={row}
                onClick={onClick}
                onMouseUp={onMouseUp}
                onMouseDown={onMouseDown}
                buttons={buttons}
                fields={fields}
                actions={actions}
                renderCheckboxes={renderCheckboxes}
                disableCheckbox={disableCheckbox}
                editableColumns={editableColumns}
                checkboxIsChecked={checkboxIsChecked}
                checkboxChange={onCheckboxChange}
                dataItemManipulator={(field, value, row) => dataItemManipulator(field, value, row)}
                dangerouslyRenderFields={dangerouslyRenderFields}
                index={index}
            />
        )
    }

    renderLoadingTable () {
        const { loadingIndicator, loadingMessage, loadingComponent, className } = this.props

        if (loadingComponent) {
            return loadingComponent
        }

        return (
            <div className="table-responsive">
                <table className={className}>
                    <tbody>
                        <tr>
                            <td className="text-center">
                                {!!loadingIndicator && (
                                    <div>
                                        {loadingIndicator}
                                    </div>
                                )}
                                {!!loadingMessage && (
                                    <div>
                                        {loadingMessage}
                                    </div>
                                )}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        )
    }

    renderErrorTable () {
        const { className, errorMessage } = this.props
        return (
            <div className="table-responsive">
                <table className={className}>
                    <tbody>
                        <tr>
                            <td className="text-center">
                                {errorMessage}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        )
    }

    renderEmptyTable () {
        const { noDataMessage, noDataComponent, className } = this.props

        if (React.isValidElement(noDataComponent)) {
            return noDataComponent
        }

        return (
            <div className="table-responsive">
                <table className={className}>
                    <tbody>
                        <tr>
                            <td className="text-center">
                                {noDataMessage}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        )
    }

    className () {
        const { onClick, onMouseUp, onMouseDown, hoverable, className } = this.props

        return classNames([
            className,
            {
                'table table-hover table-bordered table-striped table-dark':
                    onClick !== ImportPreview.noop ||
                    onMouseUp !== ImportPreview.noop ||
                    onMouseDown !== ImportPreview.noop ||
                    hoverable
            }
        ])
    }

    checkboxChange (event, row) {
        const { rows, onMasterCheckboxChange, onCheckboxChange, isCheckboxChecked, disabledCheckboxes } = this.props
        const { target } = event

        if (row === 'all') {
            if (onMasterCheckboxChange !== null) {
                onMasterCheckboxChange(event, rows)
            }
        } else if (onCheckboxChange !== null) {
            onCheckboxChange(event, row)
        }

        if (isCheckboxChecked !== null) {
            return
        }

        if (row === 'all') {
            if (target.checked) {
                const checkedRows = []

                rows.filter(({ id }) => !disabledCheckboxes.includes(id))
                    .forEach(row => checkedRows.push(row))

                this.setState({ checkedRows })
            } else {
                this.setState({ checkedRows: [] })
            }

            return
        }

        let index = -1
        const { checkedRows } = this.state
        const selected = JSON.stringify(row)

        for (let i = 0; i < checkedRows.length; i++) {
            const current = JSON.stringify(checkedRows[i])

            if (current === selected) {
                index = i

                break
            }
        }

        if (target.checked) {
            if (index === -1) {
                checkedRows.push(row)
            }
        } else {
            if (index !== -1) {
                checkedRows.splice(index, 1)
            }
        }

        this.setState({ checkedRows })
    }

    checkboxIsChecked (row) {
        const { checkedRows } = this.state
        const { rows, isCheckboxChecked, disabledCheckboxes } = this.props

        if (isCheckboxChecked !== null) {
            return isCheckboxChecked(row, rows)
        }

        if (row === 'all') {
            return (
                checkedRows.length === rows.filter(({ id }) => (
                    !disabledCheckboxes.includes(id)
                )).length
            )
        }

        let index = -1
        const selected = JSON.stringify(row)

        for (let i = 0; i < checkedRows.length; i++) {
            const current = JSON.stringify(checkedRows[i])

            if (current === selected) {
                index = i

                break
            }
        }

        return index !== -1
    }

    getFields () {
        const { rows } = this.props
        let { fieldsToExclude, fieldMap, fieldOrder } = this.props

        const fields = []

        if (!fieldsToExclude) {
            fieldsToExclude = []
        }

        if (!fieldMap) {
            fieldMap = []
        }

        if (!rows || !rows.length) {
            return []
        }

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i]

            const rowFields = Object.keys(row)

            for (let j = 0; j < rowFields.length; j++) {
                const rowFieldName = rowFields[j]
                let exists = false

                for (let k = 0; k < fields.length; k++) {
                    const field = fields[k]

                    if (field.name === rowFieldName) {
                        exists = true
                        break
                    }
                }

                if (!exists) {
                    const label = rowFieldName
                        .replace(new RegExp('_', 'g'), ' ')
                        .replace(/^(.)|\s+(.)/g, function ($1) {
                            return $1.toUpperCase()
                        })
                        .trim()

                    fields.push({
                        name: rowFieldName,
                        label
                    })
                }
            }
        }

        const regExpsToExclude = fieldsToExclude.filter(field => field.constructor && field.constructor === RegExp)

        for (let i = 0; i < fields.length; i++) {
            const field = fields[i]
            let shouldExclude = false

            // Field exclusion
            if (fieldsToExclude.indexOf(field.name) !== -1) {
                shouldExclude = true
            } else {
                for (let j = 0; j < regExpsToExclude.length; j++) {
                    if (regExpsToExclude[j].test(field.name)) {
                        shouldExclude = true

                        break
                    }
                }
            }

            if (shouldExclude) {
                fields.splice(i, 1)
                i--

                continue
            }

            // Field mapping
            if (fieldMap.hasOwnProperty(field.name)) {
                fields[i].label = fieldMap[field.name]
            }
        }

        return fields
    }

    renderCheckboxCell (value) {
        if (!this.props.renderCheckboxes) {
            return
        }

        const checkbox = (
            <div className="form-check">
                <input
                    type="checkbox"
                    value={value}
                    checked={this.checkboxIsChecked(value)}
                    onChange={event => this.checkboxChange(event, value)}
                />
            </div>
        )

        if (value === 'all') {
            if (!this.props.renderMasterCheckbox) {
                return <th/>
            }

            return (
                <th>{checkbox}</th>
            )
        }

        return (
            <td>{checkbox}</td>
        )
    }

    renderHeader (field) {
        const { orderByField, orderByDirection, orderByAscIcon, orderByDescIcon, prependOrderByIcon = false, allowOrderingBy, disallowOrderingBy, changeOrder, columnWidths } = this.props
        let { orderByIcon = '' } = this.props

        if (orderByField === field.name) {
            if (orderByDirection === 'asc') {
                orderByIcon = orderByAscIcon
            } else {
                orderByIcon = orderByDescIcon
            }
        }

        const canOrderBy = (
            (allowOrderingBy.length === 0 || allowOrderingBy.includes(field.name)) &&
            !disallowOrderingBy.includes(field.name)
        )

        const onClickHandler = (
            canOrderBy
                ? () => this.changeOrder(field)
                : () => {
                }
        )

        const cursor = (
            changeOrder && canOrderBy
                ? 'pointer'
                : 'default'
        )

        let width = columnWidths[field.name]

        if (typeof width === 'number') {
            width = `${width}%`
        }

        return (
            <th
                key={field.name}
                width={width}
                onClick={onClickHandler}
                style={{ cursor }}
            >
                {canOrderBy && prependOrderByIcon ? orderByIcon : ''}
                {field.label}
                &nbsp;
                {canOrderBy && !prependOrderByIcon ? orderByIcon : ''}
            </th>
        )
    }

    renderActionsCell () {
        const { actions, buttons } = this.props
        const state = this.state

        if (!buttons.length && !actions.length) {
            return null
        } else if (!actions.length) {
            return <th/>
        }

        return (
            <th className="rddt-action-cell">
                <div className="dropdown">
                    <button
                        className="btn btn-secondary dropdown-toggle"
                        type="button"
                        id="dropdownMenuButton"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                        disabled={!state.checkedRows.length}
                    >
                        Actions
                    </button>
                    <div className="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        {this.props.actions.map(action => this.renderActionButton(action))}
                    </div>
                </div>
            </th>
        )
    }

    renderActionsCell () {
        const { actions, buttons } = this.props
        const state = this.state

        if (!buttons.length && !actions.length) {
            return null
        } else if (!actions.length) {
            return <th/>
        }

        return (
            <th className="rddt-action-cell">
                <div className="dropdown">
                    <button
                        className="btn btn-secondary dropdown-toggle"
                        type="button"
                        id="dropdownMenuButton"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                        disabled={!state.checkedRows.length}
                    >
                        Actions
                    </button>
                    <div className="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        {this.props.actions.map(action => this.renderActionButton(action))}
                    </div>
                </div>
            </th>
        )
    }

    renderRow (row, index) {
        const {
            onClick, onMouseUp, onMouseDown, buttons, renderCheckboxes, disabledCheckboxes, dataItemManipulator, rowRenderer, dangerouslyRenderFields, actions, editableColumns
        } = this.props

        return ImportPreview.rowRenderer({
            row,
            onClick,
            onMouseUp,
            onMouseDown,
            buttons,
            renderCheckboxes,
            disableCheckbox: disabledCheckboxes.includes(row.id),
            key: row.id,
            fields: this.getFields(),
            dataItemManipulator: (field, value, row) => dataItemManipulator(field, value, row),
            checkboxIsChecked: (value) => this.checkboxIsChecked(value),
            onCheckboxChange: (e) => this.checkboxChange(e, row),
            dangerouslyRenderFields,
            actions,
            editableColumns,
            index
        })
    }

    render () {
        const { errorMessage, loading, rows, footer } = this.props
        const fields = this.getFields()

        if (errorMessage) {
            return this.renderErrorTable()
        }

        if (loading) {
            return this.renderLoadingTable()
        }

        if (!rows || !rows.length) {
            return this.renderEmptyTable()
        }

        return (
            <div>
                <div className="table-responsive">
                    <table className={this.className()}>
                        <thead>
                            <tr>
                                {this.renderCheckboxCell('all')}
                                {fields.map(field => this.renderHeader(field))}
                                {this.renderActionsCell()}
                            </tr>
                        </thead>
                        <tbody>
                            {rows.map((row, index) => this.renderRow(row, index))}
                        </tbody>
                        {!!footer && (
                            <tfoot>
                                {this.renderFooter()}
                            </tfoot>
                        )}
                    </table>
                </div>
            </div>
        )

        // return <table>
        //     <thead>
        //         <tr>
        //             <th>Company</th>
        //             <th>Notes</th>
        //             <th>Date</th>
        //             <th>Amount</th>
        //             <th>Action</th>
        //         </tr>
        //     </thead>
        //     <tbody>
        //         {
        //             rows.map((item) => (
        //                 <tr key={item.uniqueId}>
        //                     <td>{item.name}</td>
        //                     <td>{item.memo}</td>
        //                     <td>{item.date}</td>
        //                     <td>{item.amount}</td>
        //                     <td/>
        //                 </tr>
        //             ))
        //         }
        //     </tbody>
        // </table>
    }
}
