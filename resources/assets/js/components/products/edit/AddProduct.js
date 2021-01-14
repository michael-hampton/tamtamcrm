import React from 'react'
import { Card, CardBody, CardHeader, Modal, ModalBody, Nav, NavItem, NavLink, TabContent, TabPane } from 'reactstrap'
import AddButtons from '../../common/AddButtons'
import ImageForm from './ImageForm'
import DetailsForm from './DetailsForm'
import CustomFieldsForm from '../../common/CustomFieldsForm'
import { translations } from '../../utils/_translations'
import Variations from './Variations'
import Features from './Features'
import ProductAttribute from './ProductAttribute'
import FileUploads from '../../documents/FileUploads'
import ProductModel from '../../models/ProductModel'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'

class AddProduct extends React.Component {
    constructor (props) {
        super(props)

        this.productModel = new ProductModel(null)
        this.initialState = this.productModel.fields
        this.state = this.initialState

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleInput = this.handleInput.bind(this)
        this.handleMultiSelect = this.handleMultiSelect.bind(this)
        this.handleFileChange = this.handleFileChange.bind(this)
        this.handleCheck = this.handleCheck.bind(this)
        this.onChangeHandler = this.onChangeHandler.bind(this)
        this.handleVariations = this.handleVariations.bind(this)
        this.handleFeatures = this.handleFeatures.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'productForm')) {
            const storedValues = JSON.parse(localStorage.getItem('productForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
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
        const formData = new FormData()
        formData.append('cover', this.state.cover)

        if (this.state.image && this.state.image.length) {
            for (let x = 0; x < this.state.image.length; x++) {
                formData.append('image[]', this.state.image[x])
            }
        }

        formData.append('name', this.state.name)
        formData.append('description', this.state.description)
        formData.append('variations', JSON.stringify(this.state.variations))
        formData.append('features', JSON.stringify(this.state.features))
        formData.append('price', this.state.price)
        formData.append('is_featured', this.state.is_featured)
        formData.append('cost', this.state.cost)
        formData.append('quantity', this.state.quantity)
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
        formData.append('notes', this.state.notes)
        formData.append('assigned_to', this.state.assigned_to)
        formData.append('custom_value1', this.state.custom_value1)
        formData.append('custom_value2', this.state.custom_value2)
        formData.append('custom_value3', this.state.custom_value3)
        formData.append('custom_value4', this.state.custom_value4)

        this.productModel.save(formData).then(response => {
            if (!response) {
                this.setState({ errors: this.productModel.errors, message: this.productModel.error_message })
                return
            }
            this.props.products.push(response)
            this.props.action(this.props.products)
            this.setState(this.initialState)
            localStorage.removeItem('productForm')
        })
    }

    handleCheck () {
        this.setState({ is_featured: !this.state.is_featured }, () => localStorage.setItem('productForm', JSON.stringify(this.state)))
    }

    handleFileChange (e) {
        this.setState({
            [e.target.name]: e.target.files[0]
        })
    }

    onChangeHandler (e) {
        // if return true allow to setState
        this.setState({
            [e.target.name]: e.target.files
        }, () => localStorage.setItem('productForm', JSON.stringify(this.state)))
    }

    handleMultiSelect (e) {
        this.setState({ selectedCategories: Array.from(e.target.selectedOptions, (item) => item.value) }, () => localStorage.setItem('productForm', JSON.stringify(this.state)))
    }

    handleInput (e) {
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value
        this.setState({
            [e.target.name]: value
        }, () => localStorage.setItem('productForm', JSON.stringify(this.state)))
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState(this.initialState, () => localStorage.removeItem('productForm'))
            }
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <AddButtons toggle={this.toggle}/>
                <Modal size="lg" isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_product}/>

                    <ModalBody className={theme}>
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
                            </TabPane>

                            <TabPane tabId="2">
                                <ImageForm errors={this.state.errors} images={this.state.images}
                                    deleteImage={null} handleFileChange={this.handleFileChange}
                                    onChangeHandler={this.onChangeHandler}/>
                            </TabPane>

                            <TabPane tabId="3">
                                <Variations variations={this.state.variations} onChange={this.handleVariations}/>
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
                                <Features features={this.state.features} onChange={this.handleFeatures}/>
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

export default AddProduct
