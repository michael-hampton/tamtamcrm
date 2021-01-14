import React, { Component } from 'react'
import Step3 from './Step3'
import Step2 from './Step2'
import Step1 from './Step1'
import axios from 'axios'

export default class SettingsWizard extends Component {
    constructor (props) {
        super(props)
        this.state = {
            currentStep: 1,
            settings: {},
            success: false,
            error: false
        }

        this.handleChange = this.handleChange.bind(this)
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

    handleChange (event) {
        const { name, value } = event.target
        this.setState({
            [name]: value
        })
    }

    handleSubmit (event) {
        event.preventDefault()

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
        return null
    }

    render () {
        return (
            <React.Fragment>
                <p>Step {this.state.currentStep} </p>

                <form onSubmit={this.handleSubmit}>
                    <Step1
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
                    {this.previousButton()}
                    {this.nextButton()}

                </form>
            </React.Fragment>
        )
    }
}
