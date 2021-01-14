import React, { Component, Suspense } from 'react'
import * as router from 'react-router-dom'
import { Redirect, Switch } from 'react-router-dom'
import { Container } from 'reactstrap'
import PrivateRoute from '../../PrivateRoute'
import moment from 'moment'

import {
    AppAside,
    AppHeader,
    AppSidebar,
    AppSidebarFooter,
    AppSidebarForm,
    AppSidebarHeader,
    AppSidebarMinimizer,
    AppSidebarNav2 as AppSidebarNav
} from '@coreui/react'
// sidebar nav config
import navigation from '../../_nav'
// routes config
import routes from '../../routes'
import DefaultAside from './DefaultAside'
import DefaultHeader from './DefaultHeader'
import AccountList from '../../common/AccountList'
import { toast, ToastContainer } from 'react-toastify'
import axios from 'axios'
import { formatDate } from '../../common/FormatDate'

class DefaultLayout extends Component {
    constructor (props) {
        super(props)
        this.loading = <div className="animated fadeIn pt-1 text-center">Loading...</div>

        this.shown_notifications = []
    }

    signOut (e) {
        e.preventDefault()
        this.props.history.push('/login')
    }

    componentDidMount () {
        this.getNotifications()

        setInterval(() => {
            this.getNotifications()
        }, 60000)
    }

    getNotifications () {
        axios.get('/api/activity?read_only=1')
            .then((r) => {
                if (r.data.notifications && r.data.notifications.length) {
                    const yesterday = moment().subtract(2, 'hours')

                    const notifications = r.data.notifications.filter(notification => moment(notification.created_at) >= yesterday)

                    if (notifications.length) {
                        let message = ''

                        notifications.map((notification, index) => {
                            if (!this.shown_notifications.includes(notification.id)) {
                                const user = JSON.parse(localStorage.getItem('users')).filter(user => user.id === notification.user_id)
                                const username = user && user.length ? `${user[0].first_name} ${user[0].last_name}` : ''
                                message += `${username} - ${notification.data.message}`
                                message += ` - ${formatDate(notification.created_at, true)} . `
                            }

                            this.shown_notifications.push(notification.id)
                        })

                        if (message.length) {
                            toast.info(message, {
                                position: 'top-right',
                                autoClose: 5000,
                                hideProgressBar: false,
                                closeOnClick: true,
                                pauseOnHover: true,
                                draggable: true,
                                progress: undefined
                            })
                        }
                    }
                }
            })
            .catch((e) => {
                alert(e)
            })
    }

    render () {
        const theme = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'dark-theme' : 'light-theme'
        return (
            <div className={`app ${theme}`}>
                <ToastContainer
                    limit={1}
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

                <AppHeader fixed>
                    <Suspense fallback={this.loading}>
                        <DefaultHeader onLogout={e => this.signOut(e)}/>
                    </Suspense>
                </AppHeader>
                <div className="app-body">

                    <AppSidebar fixed display="lg">
                        <AppSidebarHeader/>
                        <AppSidebarForm/>

                        <AccountList/>

                        <Suspense>
                            <AppSidebarNav navConfig={navigation} {...this.props} router={router}/>
                        </Suspense>
                        <AppSidebarFooter/>
                        <AppSidebarMinimizer/>

                    </AppSidebar>
                    <main className="main">
                        {/* <AppBreadcrumb appRoutes={routes} router={router}/> */}
                        <Container>
                            <Suspense fallback={this.loading}>
                                <Switch>
                                    {routes.map((route, idx) => {
                                        return route.component ? (
                                            <PrivateRoute
                                                key={idx}
                                                path={route.path}
                                                exact={route.exact}
                                                name={route.name}
                                                component={route.component}
                                                render={props => (
                                                    <route.component {...props} />
                                                )}/>
                                        ) : (null)
                                    })}
                                    <Redirect from="/" to="/dashboard"/>
                                </Switch>
                            </Suspense>
                        </Container>

                    </main>
                    <AppAside fixed>
                        <Suspense fallback={this.loading}>
                            <DefaultAside/>
                        </Suspense>
                    </AppAside>
                </div>

                {/* <AppFooter> */}
                {/*    <Suspense> */}
                {/*        <DefaultFooter/> */}
                {/*    </Suspense> */}
                {/* </AppFooter> */}
            </div>
        )
    }
}

export default DefaultLayout
