import React from 'react'
import { Card, CardBody, CardHeader, Col, FormGroup, Input, Label, Row } from 'reactstrap'
import DepartmentDropdown from '../../common/dropdowns/DepartmentDropdown'
import RoleDropdown from '../../common/dropdowns/RoleDropdown'

export default class PermissionsForm extends React.Component {
    constructor (props) {
        super(props)

        this.state = {
            selectedAccounts: this.props.selectedAccounts
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

        const accountList = this.props.accounts.length && account ? (
            <React.Fragment key={account[0].id}>
                <div>
                    <FormGroup check inline>
                        <Label check>
                            <Input name="is_admin" checked={is_admin}
                                value={account && account.length ? account[0].id : false}
                                onChange={this.handleCheck}
                                type="checkbox"/>
                            Administrator
                        </Label>
                    </FormGroup>
                </div>
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
                            handleInputChanges={this.props.handleMultiSelect}
                            role={this.props.selectedRoles}
                        />
                    </Col>
                </Row>

                <Row form>
                    <h4>Accounts</h4>
                    <Col md={6}>
                        {!!accountList && accountList}
                    </Col>
                </Row>
            </CardBody>
        </Card>

        )
    }
}
