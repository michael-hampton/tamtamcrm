import React, { Component } from 'react'
import axios from 'axios'
import { FormGroup, Input } from 'reactstrap'

export default class DesignDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            designs: []
        }

        this.getDesigns = this.getDesigns.bind(this)
        this.handleChange = this.handleChange.bind(this)
    }

    componentDidMount () {
        if (!this.props.designs || !this.props.designs.length) {
            this.getDesigns()
        } else {
            this.setState({ designs: this.props.designs })
        }
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback d-block'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    hasErrorFor (field) {
        return this.props.errors && !!this.props.errors[field]
    }

    handleChange (e) {
        if (this.props.handleChange) {
            this.props.handleChange(e)
            return
        }

        if (e.target.value === 'new') {
            this.props.resetCounters()
            return
        }

        const design_id = parseInt(e.target.value)
        const design = this.state.designs.filter((e) => e.id === design_id)
        this.props.handleInputChanges(design)
    }

    getDesigns () {
        axios.get('/api/designs')
            .then((r) => {
                this.setState({
                    designs: r.data
                })
            })
            .catch((e) => {
                console.error(e)
            })
    }

    render () {
        let designList = null
        if (!this.state.designs.length) {
            designList = <option value="">Loading...</option>
        } else {
            designList = this.state.designs.map((design, index) => (
                <option key={index} value={design.id}>{design.name}</option>
            ))
        }

        const name = this.props.name && this.props.name ? this.props.name : 'design'
        const emptyLabel = !this.props.handleChange ? <option value="new">New Design</option> : null

        return (
            <FormGroup className="mr-2">
                <Input value={this.props.design} onChange={this.handleChange} type="select"
                    name={name} id={name}>
                    {emptyLabel}
                    {designList}
                </Input>
            </FormGroup>
        )
    }
}
