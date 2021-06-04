import React, { Component } from 'react'
import PropTypes from 'prop-types'

import { AppSidebarToggler } from '@coreui/react'
import SupportModal from '../../common/SupportModal'
import AboutModal from '../../common/AboutModal'
import { Button } from 'reactstrap'

const propTypes = {
    children: PropTypes.node
}

const defaultProps = {}

class DefaultHeader extends Component {
    render () {
        // eslint-disable-next-line
        const { children, ...attributes } = this.props

        return (
            <React.Fragment>
                <AppSidebarToggler className="d-lg-none" display="md" mobile/>
                <AppSidebarToggler className="d-md-down-none" display="lg"/>
                <SupportModal/>
                <AboutModal/>
                <Button color="link" onClick={e => this.props.onLogout(e)}><i className="fa fa-lock" /> Logout</Button>

                {/* <AppNavbarBrand */}
                {/*    full={{ */}
                {/*        src: logo, */}
                {/*        width: 89, */}
                {/*        height: 25, */}
                {/*        alt: 'CoreUI Logo' */}
                {/*    }} */}
                {/*    minimized={{ */}
                {/*        src: sygnet, */}
                {/*        width: 30, */}
                {/*        height: 30, */}
                {/*        alt: 'CoreUI Logo' */}
                {/*    }} */}
                {/* /> */}

                {/* <Nav className="d-md-down-none" navbar> */}
                {/*  <NavItem className="px-3"> */}
                {/*    <NavLink to="/dashboard" className="nav-link" >Dashboard</NavLink> */}
                {/*  </NavItem> */}
                {/*  <NavItem className="px-3"> */}
                {/*    <Link to="/users" className="nav-link">Users</Link> */}
                {/*  </NavItem> */}
                {/*  <NavItem className="px-3"> */}
                {/*    <NavLink to="#" className="nav-link">Settings</NavLink> */}
                {/*  </NavItem> */}
                {/* </Nav> */}
                {/* <Nav className="ml-auto" navbar> */}
                {/*    <NavItem className="d-md-down-none"> */}
                {/*        <NavLink to="#" className="nav-link"><i className="icon-bell" /><Badge pill */}
                {/*            color="danger">5</Badge></NavLink> */}
                {/*    </NavItem> */}
                {/*    <NavItem className="d-md-down-none"> */}
                {/*        <NavLink to="#" className="nav-link"><i className="icon-list" /></NavLink> */}
                {/*    </NavItem> */}
                {/*    <NavItem className="d-md-down-none"> */}
                {/*        <NavLink to="#" className="nav-link"><i className="icon-location-pin" /></NavLink> */}
                {/*    </NavItem> */}
                {/*    <UncontrolledDropdown nav direction="down"> */}
                {/*        <DropdownToggle nav> */}
                {/*            <img src={'../../assets/img/avatars/6.jpg'} className="img-avatar" */}
                {/*                alt="admin@bootstrapmaster.com"/> */}
                {/*        </DropdownToggle> */}
                {/*        <DropdownMenu right> */}
                {/*            <DropdownItem header tag="div" */}
                {/*                className="text-center"><strong>Account</strong></DropdownItem> */}
                {/*            <DropdownItem><i className="fa fa-bell-o" /> Updates<Badge */}
                {/*                color="info">42</Badge></DropdownItem> */}
                {/*            <DropdownItem><i className="fa fa-envelope-o" /> Messages<Badge color="success">42</Badge></DropdownItem> */}
                {/*            <DropdownItem><i className="fa fa-tasks" /> Tasks<Badge */}
                {/*                color="danger">42</Badge></DropdownItem> */}
                {/*            <DropdownItem><i className="fa fa-comments" /> Comments<Badge */}
                {/*                color="warning">42</Badge></DropdownItem> */}
                {/*            <DropdownItem header tag="div" */}
                {/*                className="text-center"><strong>Settings</strong></DropdownItem> */}
                {/*            <DropdownItem><i className="fa fa-user" /> Profile</DropdownItem> */}
                {/*            <DropdownItem><i className="fa fa-wrench" /> Settings</DropdownItem> */}
                {/*            <DropdownItem><i className="fa fa-usd" /> Payments<Badge */}
                {/*                color="secondary">42</Badge></DropdownItem> */}
                {/*            <DropdownItem><i className="fa fa-file" /> Projects<Badge */}
                {/*                color="primary">42</Badge></DropdownItem> */}
                {/*            <DropdownItem divider/> */}
                {/*            <DropdownItem><i className="fa fa-shield" /> Lock Account</DropdownItem> */}
                {/*            <DropdownItem onClick={e => this.props.onLogout(e)}><i */}
                {/*                className="fa fa-lock" /> Logout</DropdownItem> */}
                {/*        </DropdownMenu> */}
                {/*    </UncontrolledDropdown> */}
                {/* </Nav> */}
                {/* <AppAsideToggler className="d-md-down-none"/> */}
                {/* <AppAsideToggler className="d-lg-none" mobile /> */}
            </React.Fragment>
        )
    }
}

DefaultHeader.propTypes = propTypes
DefaultHeader.defaultProps = defaultProps

export default DefaultHeader
