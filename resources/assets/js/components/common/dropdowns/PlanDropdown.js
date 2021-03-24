import React, { Component } from 'react'
import { FormGroup, Input } from 'reactstrap'
import { translations } from '../../utils/_translations'
import TaskStatusRepository from '../../repositories/TaskStatusRepository'
import PlanRepository from '../../repositories/PlanRepository'

export default class PlanDropdown extends Component {
    constructor (props) {
        super(props)
        this.state = {
            plans: []
        }

        this.getPlans = this.getPlans.bind(this)
    }

    componentDidMount () {
        if (!this.props.plans || !this.props.plans.length) {
            this.getPlans()
        } else {
            this.setState({ plans: this.props.plans })
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

    getPlans () {
        const planRepository = new PlanRepository()
        planRepository.plans().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ plans: response }, () => {
                console.log('plans', this.state.plans)
            })
        })
    }

    render () {
        let planList = null
        if (!this.state.plans.length) {
            planList = <option value="">Loading...</option>
        } else {
            planList = this.state.plans.map((status, index) => (
                <option key={index} value={status.id}>{status.name}</option>
            ))
        }

        return (
            <FormGroup className="ml-2">
                <Input value={this.props.plan} onChange={this.props.handleInputChanges} type="select"
                    name="plan_id" id="plan_id">
                    <option value="">{translations.select_option}</option>
                    {planList}
                    {/* <option value="archived">Archived</option> */}
                    {/* <option value="deleted">Deleted</option> */}
                </Input>
                {this.renderErrorFor('plan_id')}
            </FormGroup>
        )
    }
}
