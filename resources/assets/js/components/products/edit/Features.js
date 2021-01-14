import React, { Component } from 'react'
import { Button } from 'reactstrap'
import FeatureInputs from './FeatureInputs'
import { translations } from '../../utils/_translations'

export default class Features extends Component {
    constructor (props) {
        super(props)

        this.state = {
            features: this.props.features && this.props.features.length ? this.props.features : [{
                description: ''
            }]
        }

        this.handleChange = this.handleChange.bind(this)
        this.addLine = this.addLine.bind(this)
        this.removeLine = this.removeLine.bind(this)
    }

    handleChange (e) {
        const name = e.target.name
        const idx = e.target.dataset.id
        const value = e.target.type === 'checkbox' ? e.target.checked : e.target.value

        const features = [...this.state.features]

        console.log('variations 2', features)

        features[idx][name] = value

        this.setState({ features }, () => {
            this.props.onChange(this.state.features)
        }
        )
    }

    addLine (e) {
        if (this.state.features.length >= 5) {
            alert(translations.maximum_5_features)
            return false
        }

        this.setState((prevState) => ({
            features: [...prevState.features, {
                description: ''
            }]
        }), () => this.props.onChange(this.state.features))
    }

    removeLine (idx) {
        this.setState({
            features: this.state.features.filter(function (feature, sidx) {
                return sidx !== idx
            })
        }, () => this.props.onChange(this.state.features))
    }

    render () {
        const { features } = this.state
        return (
            <form>
                <FeatureInputs features={features} errors={this.props.errors}
                    onChange={this.handleChange}
                    removeLine={this.removeLine}/>
                <Button color="primary" onClick={this.addLine}>Add</Button>
            </form>
        )
    }
}
