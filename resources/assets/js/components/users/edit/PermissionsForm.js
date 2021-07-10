import React from 'react'
import { Card, CardBody, CardHeader, Col, CustomInput, Label, Row } from 'reactstrap'
import DepartmentDropdown from '../../common/dropdowns/DepartmentDropdown'
import RoleDropdown from '../../common/dropdowns/RoleDropdown'
import NestedCheckboxTree from './NestedCheckboxTree'
import { translations } from '../../utils/_translations'

export default class PermissionsForm extends React.Component {
    constructor (props) {
        super(props)

        this.state = {
            selectedAccounts: this.props.selectedAccounts,
            selectedRoles: this.props.selectedRoles
        }

        this.account_id = JSON.parse(localStorage.getItem('appState')).user.account_id

        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleCheck = this.handleCheck.bind(this)
    }

    handleCheck (e) {
        const account_id = parseInt(e.target.value)
        const checked = e.target.checked
        const name = e.target.name

        this.setState(prevState => ({
            selectedAccounts: {
                ...prevState.selectedAccounts,
                [name]: checked,
                account_id: account_id,
                permissions: ''
            }
        }), () => this.props.setAccounts(this.state.selectedAccounts))
    }

    hasErrorFor (field) {
        return !!this.props.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    render () {
        const account = this.props.accounts.filter(account => parseInt(account.id) === parseInt(this.account_id))
        const is_admin = this.state.selectedAccounts && this.state.selectedAccounts.is_admin === true
        const role = this.state.selectedRoles
        const itemList = {
            invoice: ['store', 'update', 'destroy', 'show'],
            credit: ['store', 'update', 'destroy', 'show'],
            order: ['store', 'update', 'destroy', 'show'],
            lead: ['store', 'update', 'destroy', 'show'],
            deal: ['store', 'update', 'destroy', 'show'],
            quote: ['store', 'update', 'destroy', 'show'],
            task: ['store', 'update', 'destroy', 'show'],
            project: ['store', 'update', 'destroy', 'show'],
            purchase_order: ['store', 'update', 'destroy', 'show'],
            company: ['store', 'update', 'destroy', 'show'],
            payment: ['store', 'update', 'destroy', 'show'],
            expense: ['store', 'update', 'destroy', 'show'],
            product: ['store', 'update', 'destroy', 'show'],
            customer: ['store', 'update', 'destroy', 'show']
        }

        const accountList = this.props.accounts.length && account ? (
            <React.Fragment key={account[0].id}>
                <span
                    className="list-group-item-dark list-group-item list-group-item-action flex-column align-items-start">
                    <div className="d-flex w-100 justify-content-between">
                        <h5 className="mb-1">
                            {translations.administrator}
                        </h5>
                        <CustomInput
                            checked={is_admin}
                            type="switch"
                            id="is_admin"
                            name="is_admin"
                            label=""
                            value={account && account.length ? account[0].id : false}
                            onChange={this.handleCheck}/>
                    </div>
                    <h6 id="passwordHelpBlock" className="form-text text-muted">
                        {translations.administrator_help}
                    </h6>
                </span>
            </React.Fragment>
        ) : null

        return (<Card>
            <CardHeader>Permissions</CardHeader>
            <CardBody>
                <Row form>

                    <Col md={6}>
                        <Label for="job_description">Department:</Label>
                        <DepartmentDropdown
                            departments={this.props.departments}
                            name="department"
                            errors={this.props.errors}
                            handleInputChanges={this.props.handleInput}
                        />
                    </Col>

                    <Col md={6}>
                        <RoleDropdown
                            name="role"
                            multiple={true}
                            errors={this.props.errors}
                            handleInputChanges={(e) => {
                                this.setState({ selectedRoles: Array.from(e.target.selectedOptions, (item) => item.value) })
                                this.props.handleMultiSelect(e)
                            }}
                            role={this.props.selectedRoles}
                        />
                    </Col>
                </Row>

                <Row form>
                    <Col>
                        {!!accountList && accountList}
                    </Col>
                </Row>

                <NestedCheckboxTree has_custom_permissions={this.props.has_custom_permissions}
                    setPermissions={(permissions, customize) => {
                        const user_permissions = {}
                        Object.keys(permissions).forEach((group) => {
                            Object.keys(permissions[group].children).forEach((key) => {
                                console.log('permissions', permissions[group].children[key])
                                user_permissions[permissions[group].children[key].value] = permissions[group].children[key].checked
                            })
                        })

                        this.props.setPermissions(user_permissions, customize)
                    }} list={itemList} selected_roles={role}/>
            </CardBody>
        </Card>

        )
    }
}
