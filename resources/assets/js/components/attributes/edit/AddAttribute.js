import React from 'react'
import { FormGroup, Input, Label, Modal, ModalBody } from 'reactstrap'
import axios from 'axios'
import AddButtons from '../../common/AddButtons'
import { translations } from '../../utils/_translations'
import AttributeValues from './AttributeValues'
import DefaultModalHeader from '../../common/ModalHeader'
import DefaultModalFooter from '../../common/ModalFooter'

export default class AddAttribute extends React.Component {
    constructor (props) {
        super(props)
        this.state = {
            modal: false,
            name: '',
            target_url: '',
            loading: false,
            errors: [],
            values: []
        }

        this.toggle = this.toggle.bind(this)
        this.hasErrorFor = this.hasErrorFor.bind(this)
        this.renderErrorFor = this.renderErrorFor.bind(this)
        this.handleVariations = this.handleVariations.bind(this)
    }

    componentDidMount () {
        if (Object.prototype.hasOwnProperty.call(localStorage, 'attributeForm')) {
            const storedValues = JSON.parse(localStorage.getItem('attributeForm'))
            this.setState({ ...storedValues }, () => console.log('new state', this.state))
        }
    }

    handleInput (e) {
        this.setState({
            [e.target.name]: e.target.value
        }, () => localStorage.setItem('attributeForm', JSON.stringify(this.state)))
    }

    handleVariations (values) {
        this.setState({ values: values }, () => console.log('values', this.state.values))
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
        axios.post('/api/attributes', {
            name: this.state.name,
            values: this.state.values
        })
            .then((response) => {
                const newUser = response.data
                this.props.attributes.push(newUser)
                this.props.action(this.props.attributes)
                localStorage.removeItem('attributeForm')
                this.setState({
                    name: '',
                    target_url: ''
                })
                this.toggle()
            })
            .catch((error) => {
                this.setState({
                    errors: error.response.data.errors
                })
            })
    }

    toggle () {
        this.setState({
            modal: !this.state.modal,
            errors: []
        }, () => {
            if (!this.state.modal) {
                this.setState({
                    name: '',
                    target_url: ''
                }, () => localStorage.removeItem('attributeForm'))
            }
        })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'

        return (
            <React.Fragment>
                <AddButtons toggle={this.toggle}/>
                <Modal isOpen={this.state.modal} toggle={this.toggle} className={this.props.className}>
                    <DefaultModalHeader toggle={this.toggle} title={translations.add_attribute}/>

                    <ModalBody className={theme}>
                        <FormGroup>
                            <Label for="name">{translations.name} <span className="text-danger">*</span></Label>
                            <Input className={this.hasErrorFor('name') ? 'is-invalid' : ''} type="text" name="name"
                                id="name" value={this.state.name} placeholder={translations.name}
                                onChange={this.handleInput.bind(this)}/>
                            {this.renderErrorFor('name')}
                        </FormGroup>

                        <AttributeValues values={this.state.values} onChange={this.handleVariations}/>

                    </ModalBody>

                    <DefaultModalFooter show_success={true} toggle={this.toggle}
                        saveData={this.handleClick.bind(this)}
                        loading={false}/>

                </Modal>
            </React.Fragment>
        )
    }
}
