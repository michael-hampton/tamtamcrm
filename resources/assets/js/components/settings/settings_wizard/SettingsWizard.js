import React, { Component } from 'react'
import Step3 from './Step3'
import Step2 from './Step2'
import Step1 from './Step1'
import axios from 'axios'
import AccountRepository from '../../repositories/AccountRepository'
import { toast, ToastContainer } from 'react-toastify'
import { translations } from '../../utils/_translations'
import ConfirmPassword from '../../common/ConfirmPassword'
import UserModel from '../../models/UserModel'
import AccountModel from '../../models/AccountModel'
import { Button } from 'reactstrap'

export default class SettingsWizard extends Component {
    constructor (props) {
        super(props)
        this.state = {
            currentStep: 1,
            settings: {},
            success: false,
            error: false,
            domain: '',
            first_name: '',
            last_name: '',
            email: '',
            country_id: null,
            checking: true,
            domain_valid: false
        }

        this.handleChange = this.handleChange.bind(this)
        this.checkDomain = this.checkDomain.bind(this)
        this.handleSettingsChange = this.handleSettingsChange.bind(this)
        this.handleSubmit = this.handleSubmit.bind(this)
        this._prev = this._prev.bind(this)
        this._next = this._next.bind(this)
    }

    handleSettingsChange (event) {
        const name = event.target.name
        const value = event.target.value

        this.setState(prevState => ({
            settings: {
                ...prevState.settings,
                [name]: value
            }
        }))
    }

    checkDomain () {
        const accountRepository = new AccountRepository()

        this.setState({ checking: true })

        accountRepository.checkDomain(this.state.subdomain).then(response => {
            if (!response) {
                alert(a)
                this.setState({ checking: false, domain_valid: false }, () => {
                    toast.error(translations.updated_unsuccessfully.replace('{entity}', translations.plan), {
                        position: 'top-center',
                        autoClose: 5000,
                        hideProgressBar: false,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: true,
                        progress: undefined
                    })
                })
            }

            if (Object.keys(response).length && response.subdomain === this.state.subdomain) {
                this.setState({ checking: false, domain_valid: false }, () => {
                    toast.success(translations.updated_successfully.replace('{entity}', translations.plan), {
                        position: 'top-center',
                        autoClose: 5000,
                        hideProgressBar: false,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: true,
                        progress: undefined
                    })
                })
            } else {
                this.setState({ checking: false, domain_valid: true }, () => {
                    toast.success(translations.updated_successfully.replace('{entity}', translations.plan), {
                        position: 'top-center',
                        autoClose: 5000,
                        hideProgressBar: false,
                        closeOnClick: true,
                        pauseOnHover: true,
                        draggable: true,
                        progress: undefined
                    })
                })
            }
        })
    }

    handleChange (event) {
        const { name, value } = event.target
        this.setState({
            [name]: value
        })
    }

    handleSubmit (event) {
        const userModel = new UserModel()
        const accountModel = new AccountModel()

        const account_data = this.state
        account_data.settings = JSON.stringify(this.state.settings)

        try {
            userModel.save(this.state).then(() => {
                accountModel.save(account_data)
            })
        } catch (e) {
            alert('error')
        }

        alert('save')
        return false

        const formData = new FormData()
        formData.append('settings', JSON.stringify(this.state.settings))
        // formData.append('company_logo', this.state.company_logo)

        axios.post('/api/accounts', formData, {
            headers: {
                'content-type': 'multipart/form-data'
            }
        })
            .then((response) => {
                this.setState({ success: true })
            })
            .catch((error) => {
                console.error(error)
                this.setState({ error: true })
            })
    }

    _next () {
        let currentStep = this.state.currentStep
        currentStep = currentStep >= 2 ? 3 : currentStep + 1
        this.setState({
            currentStep: currentStep
        })
    }

    _prev () {
        let currentStep = this.state.currentStep
        currentStep = currentStep <= 1 ? 1 : currentStep - 1
        this.setState({
            currentStep: currentStep
        })
    }

    /*
    * the functions for our button
    */
    previousButton () {
        const currentStep = this.state.currentStep
        if (currentStep !== 1) {
            return (
                <button
                    className="btn btn-secondary"
                    type="button" onClick={this._prev}>
                    Previous
                </button>
            )
        }
        return null
    }

    nextButton () {
        const currentStep = this.state.currentStep

        if (currentStep < 3) {
            return (
                <button
                    className="btn btn-primary float-right"
                    type="button" onClick={this._next}>
                    Next
                </button>
            )
        }

        const show_password = false

        return show_password === true ? <ConfirmPassword callback={this.handleSubmit} button_color="btn-success"
            button_label={translations.save}/>
            : <Button color="success" onClick={this.handleSubmit}>{translations.save}</Button>
    }

    render () {
        return (
            <React.Fragment>
                <p>Step {this.state.currentStep} </p>

                <ToastContainer
                    position="top-center"
                    autoClose={5000}
                    hideProgressBar={false}
                    newestOnTop={false}
                    closeOnClick
                    rtl={false}
                    pauseOnFocusLoss
                    draggable
                    pauseOnHover
                />

                <div>
                    <Step1
                        checkDomain={this.checkDomain}
                        domain_valid={this.state.domain_valid}
                        checking={this.state.checking}
                        handleSettingsChange={this.handleSettingsChange}
                        settings={this.state.settings}
                        currentStep={this.state.currentStep}
                        handleChange={this.handleChange}
                        email={this.state.email}
                    />
                    <Step2
                        handleSettingsChange={this.handleSettingsChange}
                        settings={this.state.settings}
                        currentStep={this.state.currentStep}
                        handleChange={this.handleChange}
                        username={this.state.username}
                    />
                    <Step3
                        handleSettingsChange={this.handleSettingsChange}
                        settings={this.state.settings}
                        currentStep={this.state.currentStep}
                        handleChange={this.handleChange}
                        password={this.state.password}
                    />
                    {this.state.domain_valid === true && this.previousButton()}
                    {this.state.domain_valid === true && this.nextButton()}

                </div>
            </React.Fragment>
        )
    }
}
