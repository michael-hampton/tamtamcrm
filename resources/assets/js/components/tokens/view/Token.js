import React, { Component } from 'react'
import { Alert, ListGroup, Row } from 'reactstrap'
import { icons } from '../../utils/_icons'
import { translations } from '../../utils/_translations'
import InfoItem from '../../common/entityContainers/InfoItem'
import UserModel from '../../models/UserModel'
import PlainEntityHeader from '../../common/entityContainers/PlanEntityHeader'
import FormatDate from '../../common/FormatDate'
import TokenModel from '../../models/TokenModel'

export default class Token extends Component {
    constructor (props) {
        super(props)

        this.state = {
            users: [],
            show_success: false,
            success_message: ''
        }

        this.tokenModel = new TokenModel(this.props.entity)
        this.getUsers = this.getUsers.bind(this)
        this.copyToken = this.copyToken.bind(this)
    }

    componentDidMount () {
        this.getUsers()
    }

    getUsers () {
        const userModel = new UserModel()
        userModel.getUsers().then(response => {
            if (!response) {
                alert('error')
            }

            this.setState({ users: response }, () => {
                console.log('users', this.state.users)
            })
        })
    }

    copyToken () {
        new TokenModel(this.props.entity).copyToken()
        this.setState({ success_message: translations.token_copied, show_success: true })
    }

    render () {
        const user = this.state.users.length ? this.state.users.filter(user => user.id === parseInt(this.props.entity.user_id)) : []

        return (
            <React.Fragment>
                {user.length &&
                <PlainEntityHeader heading_1={translations.user}
                    value_1={`${user[0].first_name} ${user[0].last_name}`}
                    heading_2={translations.created_on}
                    value_2={<FormatDate date={this.props.entity.created_at}/>}/>
                }

                <Row>
                    <ListGroup className="col-12">
                        <InfoItem icon={icons.building} value={this.props.entity.name}
                            title={translations.name}/>

                        <InfoItem icon={icons.link} value={`${this.props.entity.token.substring(0, 10)}xxxxxxxxxx`}
                            title={translations.token}/>
                    </ListGroup>

                    <a className="ml-4 mt-4" onClick={this.copyToken}><i style={{ fontSize: '24px' }}
                        className={`fa ${icons.clone}`}/> </a>
                </Row>

                {this.state.show_success &&
                <Alert color="primary">
                    {this.state.success_message}
                </Alert>
                }
            </React.Fragment>

        )
    }
}
