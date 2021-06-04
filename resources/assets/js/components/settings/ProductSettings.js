import React, { Component } from 'react'
import FormBuilder from './FormBuilder'
import { Card, CardBody } from 'reactstrap'
import axios from 'axios'
import { translations } from '../utils/_translations'
import SnackbarMessage from '../common/SnackbarMessage'
import AccountRepository from '../repositories/AccountRepository'
import CompanyModel from '../models/CompanyModel'
import EditScaffold from '../common/EditScaffold'

class ProductSettings extends Component {
    constructor (props) {
        super(props)

        this.state = {
            id: localStorage.getItem('account_id'),
            cached_settings: {},
            settings: {},
            success: false,
            error: false,
            changesMade: false,
            isSaving: false,
            loaded: false
        }

        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleChange = this.handleChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this.handleCheckboxChange = this.handleCheckboxChange.bind(this)
        this.getAccount = this.getAccount.bind(this)

        this.model = new CompanyModel({ id: this.state.id })
    }

    componentDidMount () {
        window.addEventListener('beforeunload', this.beforeunload.bind(this))
        this.getAccount()
    }

    componentWillUnmount () {
        window.removeEventListener('beforeunload', this.beforeunload.bind(this))
    }

    beforeunload (e) {
        if (this.state.changesMade) {
            if (!confirm(translations.changes_made_warning)) {
                e.preventDefault()
                return false
            }
        }
    }

    getAccount () {
        const accountRepository = new AccountRepository()
        accountRepository.getById(this.state.id).then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({
                loaded: true,
                settings: response.settings,
                cached_settings: response.settings
            }, () => {
                console.log(response)
            })
        })
    }

    handleChange (event) {
        this.setState({ [event.target.name]: event.target.value })
    }

    handleSettingsChange (event) {
        const name = event.target.name
        let value = event.target.type === 'checkbox' ? event.target.checked : event.target.value
        value = (value === 'true') ? true : ((value === 'false') ? false : (value))

        this.setState(prevState => ({
            changesMade: true,
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }

    handleSubmit (e) {
        this.setState({ isSaving: true })
        const formData = new FormData()
        formData.append('settings', JSON.stringify(this.state.settings))
        formData.append('_method', 'PUT')

        axios.post(`/api/accounts/${this.state.id}`, formData, {
            headers: {
                'content-type': 'multipart/form-data'
            }
        })
            .then((response) => {
                this.setState({
                    success: true,
                    cached_settings: this.state.settings,
                    changesMade: false,
                    isSaving: false
                }, () => this.model.updateSettings(this.state.settings))
            })
            .catch((error) => {
                console.error(error)
                this.setState({ error: true })
            })
    }

    getInventoryFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'should_update_inventory',
                    label: translations.should_update_inventory,
                    type: 'switch',
                    placeholder: translations.should_update_inventory,
                    value: settings.should_update_inventory,
                    help_text: translations.should_update_inventory_help_text
                },
                {
                    name: 'allow_backorders',
                    label: translations.allow_backorders,
                    type: 'switch',
                    placeholder: translations.allow_backorders,
                    value: settings.allow_backorders,
                    help_text: translations.allow_backorders_help_text
                },
                {
                    name: 'allow_partial_orders',
                    label: 'Allow Partial Orders',
                    type: 'switch',
                    placeholder: 'Allow Partial Orders',
                    value: settings.allow_partial_orders
                },
                {
                    name: 'inventory_enabled',
                    label: translations.inventory_enabled,
                    type: 'switch',
                    placeholder: translations.inventory_enabled,
                    help_text: translations.inventory_enabled_help_text,
                    value: settings.inventory_enabled
                }
                /* {
                    name: 'show_cost',
                    label: 'Show Cost',
                    type: 'switch',
                    placeholder: 'Show Cost',
                    value: settings.show_cost
                } */
            ]
        ]
    }

    getProductFields () {
        const settings = this.state.settings

        return [
            [
                {
                    name: 'should_update_products',
                    label: 'Update Products',
                    type: 'switch',
                    placeholder: 'Update Products',
                    value: settings.should_update_products
                },
                {
                    name: 'show_cost',
                    label: translations.show_cost,
                    help_text: translations.show_cost_help_text,
                    type: 'switch',
                    placeholder: 'Show Cost',
                    value: settings.show_cost
                },
                {
                    name: 'show_product_quantity',
                    label: translations.show_product_quantity,
                    help_text: translations.show_product_quantity_help_text,
                    type: 'switch',
                    placeholder: 'Show Product Quantity',
                    value: settings.show_product_quantity
                },
                {
                    name: 'fill_products',
                    label: translations.fill_products,
                    help_text: translations.fill_products_help_text,
                    type: 'switch',
                    placeholder: 'Fill Products',
                    value: settings.fill_products
                },
                {
                    name: 'convert_product_currency',
                    label: translations.convert_product_currency,
                    help_text: translations.convert_product_currency_help_text,
                    type: 'switch',
                    placeholder: 'Convert Products',
                    value: settings.convert_product_currency
                },
                {
                    name: 'has_minimum_quantity',
                    label: translations.has_minimum_quantity,
                    help_text: translations.has_minimum_quantity_help_text,
                    type: 'switch',
                    placeholder: 'Default Quantity',
                    value: settings.has_minimum_quantity
                },
                {
                    name: 'quantity_can_be_changed',
                    label: 'Quantity can be Changed',
                    type: 'switch',
                    placeholder: 'Quantity can be Changed',
                    value: settings.quantity_can_be_changed
                }
            ]
        ]
    }

    handleCheckboxChange (e) {
        const value = e.target.checked
        const name = e.target.name

        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }

    handleCancel () {
        this.setState({ settings: this.state.cached_settings, changesMade: false })
    }

    handleClose () {
        this.setState({ success: false, error: false })
    }

    render () {
        const tabs = {
            children: []
        }

        tabs.children[0] = <>
            <Card>
                <CardBody>
                    <FormBuilder
                        handleCheckboxChange={this.handleCheckboxChange}
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getProductFields()}
                    />
                </CardBody>
            </Card>

            <Card>
                <CardBody>
                    <FormBuilder
                        handleCheckboxChange={this.handleCheckboxChange}
                        handleChange={this.handleSettingsChange}
                        formFieldsRows={this.getInventoryFields()}
                    />
                </CardBody>
            </Card>
        </>

        return this.state.loaded === true ? (
            <React.Fragment>
                <SnackbarMessage open={this.state.success} onClose={this.handleClose.bind(this)} severity="success"
                    message={translations.settings_saved}/>

                <SnackbarMessage open={this.state.error} onClose={this.handleClose.bind(this)} severity="danger"
                    message={translations.settings_not_saved}/>

                <EditScaffold isLoading={!this.state.loaded} isSaving={this.state.isSaving}
                    isEditing={this.state.changesMade}
                    title={translations.product_settings}
                    cancelButtonDisabled={!this.state.changesMade}
                    handleCancel={this.handleCancel.bind(this)}
                    handleSubmit={this.handleSubmit.bind(this)}
                    tabs={tabs}/>

            </React.Fragment>
        ) : null
    }
}

export default ProductSettings
