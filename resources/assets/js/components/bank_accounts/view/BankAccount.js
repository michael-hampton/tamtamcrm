import React, { Component } from 'react'
import { Row } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import FieldGrid from '../../common/entityContainers/FieldGrid'
import PaymentRepository from '../../repositories/PaymentRepository'
import PlainEntityHeader from '../../common/entityContainers/PlanEntityHeader'
import EntityListTile from '../../common/entityContainers/EntityListTile'
import InfoMessage from '../../common/entityContainers/InfoMessage'

export default class BankAccount extends Component {
    constructor (props) {
        super(props)

        this.state = {
            payments: []
        }

        this.getPayments = this.getPayments.bind(this)
    }

    componentDidMount () {
        this.getPayments()
    }

    getPayments () {
        const paymentRepository = new PaymentRepository()
        paymentRepository.get().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ payments: response }, () => {
                console.log('payements', this.state.payments)
            })
        })
    }

    render () {
        const fields = []

        if (this.props.entity.name.length) {
            fields.name = this.props.entity.name
        }

        if (this.props.entity.username.length) {
            fields.username = this.props.entity.username
        }

        let user

        if (this.props.entity.assigned_to) {
            const assigned_user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === parseInt(this.props.entity.assigned_to))
            user = <EntityListTile entity={translations.user}
                title={`${assigned_user[0].first_name} ${assigned_user[0].last_name}`}
                icon={icons.user}/>
        }

        return (
            <React.Fragment>
                <PlainEntityHeader heading_1={translations.name} value_1={this.props.entity.name}/>

                {!!this.props.entity.private_notes.length &&
                <Row>
                    <InfoMessage message={this.props.entity.private_notes}/>
                </Row>
                }

                {!!user &&
                user
                }

                <Row>
                    <EntityListTile entity={translations.bank} title={this.props.entity.bank.name}
                        icon={icons.bank}/>
                </Row>

                <FieldGrid fields={fields}/>
            </React.Fragment>

        )
    }
}
