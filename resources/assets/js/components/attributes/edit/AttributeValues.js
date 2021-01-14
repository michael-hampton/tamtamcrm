import React, { Component } from 'react'
import { Button } from 'reactstrap'
import AttributeValueInputs from './AttributeValueInputs'

export default class AttributeValues extends Component {
    constructor (props) {
        super(props)

        this.state = {
            values: this.props.values && this.props.values.length ? this.props.values : [{
                value: ''
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

        const values = [...this.state.values]

        console.log('variations 2', values)

        values[idx][name] = value

        this.setState({ values }, () => {
            this.props.onChange(this.state.values)
        }
        )
    }

    addLine (e) {
        this.setState((prevState) => ({
            values: [...prevState.values, { value: '' }]
        }), () => this.props.onChange(this.state.values))
    }

    removeLine (idx) {
        this.setState({
            values: this.state.values.filter(function (value, sidx) {
                return sidx !== idx
            })
        }, () => this.props.onChange(this.state.values))
    }

    render () {
        const { values } = this.state
        return (
            <form>
                <AttributeValueInputs values={values} errors={this.props.errors}
                    onChange={this.handleChange}
                    removeLine={this.removeLine}/>
                <Button color="primary" onClick={this.addLine}>Add</Button>
            </form>
        )
    }
}
