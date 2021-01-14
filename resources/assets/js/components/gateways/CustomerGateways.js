import React from 'react'
import { Button, Card, CardBody } from 'reactstrap'
import { translations } from '../utils/_translations'
import GatewayModel from '../models/GatewayModel'
import { icons } from '../utils/_icons'
import { toast, ToastContainer } from 'react-toastify'

export default class CustomerGateways extends React.Component {
    constructor (props) {
        super(props)

        this.state = {
            company_gateways: [],
            gateways: []
        }

        this.getCompanyGateways = this.getCompanyGateways.bind(this)
        this.addGateway = this.addGateway.bind(this)
        this.removeGateway = this.removeGateway.bind(this)
        this.onSubmit = this.onSubmit.bind(this)
    }

    componentDidMount () {
        this.getCompanyGateways()
    }

    addGateway (e) {
        const gateways = this.props.model.addGateway(e.target.dataset.gateway)
        this.setState({ gateways: gateways }, () => {
        })
    }

    removeGateway (e) {
        const gateways = this.props.model.removeGateway(e.target.dataset.gateway)
        this.setState({ gateways: gateways })
    }

    onSubmit () {
        this.props.model.saveSettings().then(response => {
            if (!response) {
                toast.error('There was an issue updating the settings')
                return
            }

            toast.success('Settings updated successfully')
        })
    }

    getCompanyGateways () {
        const gatewayModel = new GatewayModel()
        gatewayModel.getGateways().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ company_gateways: response })
        })
    }

    render () {
        const gateways = this.state.company_gateways.length ? this.state.company_gateways.map((gateway, index) => {
            console.log('mike', this.props.model.gateways)
            const icon = this.props.model.gateways.length && this.props.model.gateways.includes(gateway.id)
                ? <i data-gateway={gateway.id} onClick={this.removeGateway} className={`fa ${icons.delete}`}/>
                : <i data-gateway={gateway.id} onClick={this.addGateway} className={`fa ${icons.tick}`}/>

            return <li key={index}
                className="list-group-item-dark list-group-item d-flex justify-content-between align-items-center">
                {gateway.gateway.name}
                {icon}
            </li>
        }) : null

        return <React.Fragment>
            <ToastContainer/>

            <Card>
                <CardBody>
                    {gateways}
                    <Button color="success" onClick={this.onSubmit}>{translations.save}</Button>
                </CardBody>
            </Card>
        </React.Fragment>
    }
}
