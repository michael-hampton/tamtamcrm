import React from 'react'
import {
    Card,
    CardBody,
    CardHeader,
    DropdownItem,
    Modal,
    ModalBody,
    Nav,
    NavItem,
    NavLink,
    TabContent,
    TabPane
} from 'reactstrap'
import axios from 'axios'
import PropTypes from 'prop-types'
import SuccessMessage from '../../common/SucessMessage'
import ErrorMessage from '../../common/ErrorMessage'
import DetailsForm from './DetailsForm'
import ImageForm from './ImageForm'
import ProductListDropdown from '../ProductListDropdown'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import Variations from './Variations'
import Features from './Features'
import ProductAttribute from './ProductAttribute'
import FileUploads from '../../documents/FileUploads'
import ProductModel from '../../models/ProductModel'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'

class EditProduct extends React.Component {
    constructor (props) {
        super(props)

        this.productModel = new ProductModel(this.props.product)
        this.initialState = this.productModel.fields
        this.state = this.initialState
        this.state = { ...this.state, ...this.productAttributes }

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleMultiSelect = this.handleMultiSelect.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.handleCheck = this.handleCheck.bind(this)
        this.deleteImage = this.deleteImage.bind(this)
        this.handleFileChange = this.handleFileChange.bind(this)
        this.onChangeHandler = this.onChangeHandler.bind(this)
        this.handleVariations = this.handleVariations.bind(this)
        this.handleFeatures = this.handleFeatures.bind(this)
    }

    getFormData () {
        const formData = new FormData()
        formData.append('cover', this.state.cover)

        if (this.state.image && this.state.image.length) {
            for (let x = 0; x < this.state.image.length; x++) {
                formData.append('image[]', this.state.image[x])
            }
        }

        formData.append('name', this.state.name)
        formData.append('assigned_to', this.state.assigned_to)
        formData.append('variations', JSON.stringify(this.state.variations))
        formData.append('features', JSON.stringify(this.state.features))
        formData.append('notes', this.state.notes)
        formData.append('is_featured', this.state.is_featured)
        formData.append('description', this.state.description)
        formData.append('quantity', this.state.quantity)
        formData.append('price', this.state.price)
        formData.append('cost', this.state.cost)
        formData.append('sku', this.state.sku)
        formData.append('length', this.state.length)
        formData.append('width', this.state.width)
        formData.append('height', this.state.height)
        formData.append('weight', this.state.weight)
        formData.append('mass_unit', this.state.mass_unit)
        formData.append('distance_unit', this.state.distance_unit)
        formData.append('company_id', this.state.company_id)
        formData.append('brand_id', this.state.brand_id)
        formData.append('category', this.state.selectedCategories)
        formData.append('range_from', this.state.range_from)
        formData.append('range_to', this.state.range_to)
        formData.append('payable_months', this.state.payable_months)
        formData.append('number_of_years', this.state.number_of_years)
        formData.append('minimum_downpayment', this.state.minimum_downpayment)
        formData.append('interest_rate', this.state.interest_rate)
        formData.append('custom_value1', this.state.custom_value1)
        formData.append('custom_value2', this.state.custom_value2)
        formData.append('custom_value3', this.state.custom_value3)
        formData.append('custom_value4', this.state.custom_value4)
        formData.append('_method', 'PUT')

        return formData
    }

    toggleTab (tab) {
        if (this.state.activeTab !== tab) {
            this.setState({ activeTab: tab })
        }
    }

    handleVariations (variations) {
        this.setState({ variations: variations }, () => console.log('variations', this.state.variations))
    }

    handleFeatures (features) {
        this.setState({ features: features }, () => console.log('features', this.state.features))
    }

    handleClick () {
        const formData = this.getFormData()

        this.productModel.save(formData).then(response => {
            if (!response) {
                this.setState({ errors: this.productModel.errors, message: this.productModel.error_message })
                return
            }

            const index = this.props.products.findIndex(product => product.id === this.props.product.id)
            this.props.products[index] = response
            this.props.action(this.props.products)
            this.setState({
                editMode: false,
                changesMade: false
            })
            this.toggle()
        })
    }

    handleCheck () {
        this.setState({ is_featured: !this.state.is_featured })
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

    hasErrorFor (field) {
        return !!this.state.errors[field]
    }

    handleInput (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            [e.target.name]: value,
            changesMade: true
        })
    }

    handleMultiSelect (e) {
        this.setState({ selectedCategories: Array.from(e.target.selectedOptions, (item) => item.value) })
    }

    handleFileChange (e) {
        this.setState({
            [e.target.name]: e.target.files[0]
        })
    }

    onChangeHandler (e) {
        const files = e.target.files

        console.log('files', files)

        // if return true allow to setState
        this.setState({
            [e.target.name]: e.target.files
        })
    }

    toggle () {
        if (this.state.modal && this.state.changesMade) {
            if (window.confirm('Your changes have not been saved?')) {
                this.setState({ ...this.initialState })
            }

            return
        }

        this.setState({
            modal: !this.state.modal,
            errors: []
        })
    }

    deleteImage (e) {
        const src = e.target.getAttribute('data-src')

        axios.post('/api/products/removeImages', {
            product: this.state.id,
            image: src
        })
            .then(function (response) {
                // const arrProducts = [...self.state.products]
                // const index = arrProducts.findIndex(product => product.id === id)
                // arrProducts.splice(index, 1)
                // self.addProductToState(arrProducts)
            })
            .catch(function (error) {
                console.log(error)
            })
    }

    render () {
        const successMessage = this.state.showSuccessMessage === true
            ? <SuccessMessage message="Invoice was updated successfully"/> : null
        const errorMessage = this.state.showErrorMessage === true
            ? <ErrorMessage message="Something went wrong"/> : null
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <DropdownItem onClick={this.toggle}><i className={`fa ${icons.edit}`}/>{translations.edit_product}
                </DropdownItem>
                <Modal size="lg" isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.edit_product}/>

                    <ModalBody className={theme}>

                        <ProductListDropdown id={this.state.id} formData={this.getFormData()}/>
                        {successMessage}
                        {errorMessage}

                        <Nav tabs>
                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '1' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('1')
                                    }}>
                                    {translations.details}
                                </NavLink>
                            </NavItem>
                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '2' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('2')
                                    }}>
                                    {translations.images}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '3' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('3')
                                    }}>
                                    {translations.variations}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '4' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('4')
                                    }}>
                                    {translations.attributes}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '5' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('5')
                                    }}>
                                    {translations.features}
                                </NavLink>
                            </NavItem>

                            <NavItem>
                                <NavLink
                                    className={this.state.activeTab === '6' ? 'active' : ''}
                                    onClick={() => {
                                        this.toggleTab('6')
                                    }}>
                                    {translations.documents}
                                </NavLink>
                            </NavItem>
                        </Nav>

                        <TabContent activeTab={this.state.activeTab}>
                            <TabPane tabId="1">
                                <DetailsForm errors={this.state.errors} handleInput={this.handleInput}
                                    product={this.state}
                                    handleMultiSelect={this.handleMultiSelect}
                                    categories={this.props.categories}
                                    selectedCategories={this.state.selectedCategories}
                                    companies={this.state.companies}
                                    handleCheck={this.handleCheck}/>

                                <CustomFieldsForm handleInput={this.handleInput}
                                    custom_value1={this.state.custom_value1}
                                    custom_value2={this.state.custom_value2}
                                    custom_value3={this.state.custom_value3}
                                    custom_value4={this.state.custom_value4}
                                    custom_fields={this.props.custom_fields}/>
                                />
                            </TabPane>

                            <TabPane tabId="2">
                                <ImageForm errors={this.state.errors} product={this.props.product}
                                    images={this.state.images}
                                    deleteImage={this.deleteImage} handleFileChange={this.handleFileChange}
                                    onChangeHandler={this.onChangeHandler}/>
                            </TabPane>

                            <TabPane tabId="3">
                                <Card>
                                    <CardHeader>{translations.attributes}</CardHeader>
                                    <CardBody>

                                        <Variations variations={this.state.variations}
                                            onChange={this.handleVariations}/>
                                    </CardBody>
                                </Card>
                            </TabPane>

                            <TabPane tabId="4">
                                <Card>
                                    <CardHeader>{translations.attributes}</CardHeader>
                                    <CardBody>
                                        <ProductAttribute errors={this.state.errors} handleInput={this.handleInput}
                                            product={this.state}/>
                                    </CardBody>
                                </Card>
                            </TabPane>

                            <TabPane tabId="5">
                                <Card>
                                    <CardHeader>{translations.features}</CardHeader>
                                    <CardBody>

                                        <Features features={this.state.features} onChange={this.handleFeatures}/>
                                    </CardBody>
                                </Card>
                            </TabPane>

                            <TabPane tabId="6">
                                <Card>
                                    <CardHeader>{translations.documents}</CardHeader>
                                    <CardBody>
                                        <FileUploads entity_type="Product" entity={this.state}
                                            user_id={this.state.user_id}/>
                                    </CardBody>
                                </Card>
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

export default EditProduct

EditProduct.propTypes = {
    product: PropTypes.object,
    products: PropTypes.array,
    action: PropTypes.func
}
