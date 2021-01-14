import React from 'react'
import { Modal, ModalBody, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import AddButtons from '../../common/AddButtons'
import { translations } from '../../utils/_translations'
import Details from './Details'
import GatewayModel from '../../models/GatewayModel'
import Settings from './Settings'
import FeesAndLimits from './FeesAndLimits'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'

class AddGateway extends React.Component {
    constructor (props) {
        super(props)

        this.gatewayModel = new GatewayModel(null)
        this.initialState = this.gatewayModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.handleConfig = this.handleConfig.bind(this)
        this.updateCards = this.updateCards.bind(this)
        this.updateFields = this.updateFields.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.updateFeesAndLimits = this.updateFeesAndLimits.bind(this)
    }

    handleConfig (e) {
        const name = e.target.name
        const value = e.target.value
        this.setState({
            config: {
                ...this.state.config,
                [name]: value
            }
        })
    }

    updateCards (e) {
        const item = e.target.name
        const isChecked = e.target.checked
        this.setState(prevState => ({ accepted_cards: prevState.accepted_cards.set(item, isChecked) }))
    }

    updateFields (e) {
        const item = e.target.name
        const isChecked = e.target.checked
        this.setState(prevState => ({ required_fields: prevState.required_fields.set(item, isChecked) }), () => {
            console.log('cards', this.state.required_fields)
        })
    }

    updateFeesAndLimits (e) {
        const name = e.target.name
        let value = e.target.value

        const fees_and_limits = [...this.state.fees_and_limits]
        const item = { ...fees_and_limits[0] }

        if (e.target.name === 'tax' || e.target.name === 'tax_2' || e.target.name === 'tax_3') {
            const tax_name = e.target.options[e.target.selectedIndex].getAttribute('data-name')
            value = e.target.options[e.target.selectedIndex].getAttribute('data-rate')
            const tax_rate_name = e.target.name === 'tax' ? 'tax_rate_name' : `tax_rate_name_${e.target.name.split('_')[1]}`

            item[tax_rate_name] = tax_name
        }

        item[name] = value
        fees_and_limits[0] = item
        this.setState({ fees_and_limits }, () => {
            console.log('fees', this.state.fees_and_limits)
        })
    }

    handleInput (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            [e.target.name]: value
        })
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    hasErrorFor (field) {
        return !!this.state.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.state.errors[field][0]}</strong>
                </span>
            )
        }
    }

    handleClick () {
        const required_fields = {}

        if (this.state.required_fields.size) {
            const test = Array.from(this.state.required_fields.keys())

            test.map((item) => {
                required_fields[item] = true
            })
        }

        const formData = new FormData()
        formData.append('accepted_credit_cards', Array.from(this.state.accepted_cards.keys()).join(','))
        formData.append('fees_and_limits', JSON.stringify(this.state.fees_and_limits))
        formData.append('config', JSON.stringify(this.state.config))
        formData.append('update_details', this.state.update_details === true ? 1 : 0)
        formData.append('gateway_key', this.state.gateway_key)
        formData.append('customer_id', this.props.customer_id)
        formData.append('name', this.state.name)
        formData.append('description', this.state.description)
        formData.append('group_id', this.props.group_id)
        formData.append('fields', JSON.stringify(required_fields))
        formData.append('require_cvv', this.state.require_cvv === true ? 1 : 0)

        this.gatewayModel.save(formData).then(response => {
            if (!response) {
                this.setState({ errors: this.gatewayModel.errors, message: this.gatewayModel.error_message })
                return
            }
            this.props.gateways.push(response)
            this.props.action(this.props.gateways)
            this.setState(this.initialState)
            localStorage.removeItem('gatewayForm')
        })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <AddButtons toggle={this.toggle}/>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_gateway}/>

                    <ModalBody className={theme}>
                        <Nav tabs className="pl-3">
                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '1' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('1')
                                    }}>
                                    {translations.credentials}
                                </NavLink>
                            </NavItem>
                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '2' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('2')
                                    }}>
                                    {translations.settings}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '3' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('3')
                                    }}>
                                    {translations.limits_and_fees}
                                </NavLink>
                            </NavItem>
                        </Nav>

                        <TabContent activeTab={this.state.activeTab}>
                            <TabPane tabId="1">
                                <Details is_edit={false} renderErrorFor={this.renderErrorFor}
                                    errors={this.state.errors}
                                    handleInput={this.handleInput}
                                    gateway={this.state}
                                    handleConfig={this.handleConfig}/>
                            </TabPane>

                            <TabPane tabId="2">
                                <Settings renderErrorFor={this.renderErrorFor} errors={this.state.errors}
                                    handleInput={this.handleInput}
                                    gateway={this.state}
                                    updateFields={this.updateFields}
                                    updateCards={this.updateCards}/>
                            </TabPane>

                            <TabPane tabId="3">
                                <FeesAndLimits renderErrorFor={this.renderErrorFor} errors={this.state.errors}
                                    handleInput={this.handleInput}
                                    gateway={this.state}
                                    updateFeesAndLimits={this.updateFeesAndLimits}/>
                            </TabPane>
                        </TabContent>
                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>
                </Modal>
            </React.Fragment>
        )
    }
}

export default AddGateway
