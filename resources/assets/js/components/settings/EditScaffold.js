import React, {Component} from 'react'
import {Card, CardBody} from 'reactstrap'
import {translations} from '../utils/_translations'
import Menu from './Menu'
import AppTabs from "../common/AppTabs";
import {AppBar, Box, Toolbar} from "@material-ui/core";

function TabPanel(props) {
    const {children, value, index, ...other} = props;
    return <div className={props.className} {...other}>{value === index && <Box>{children}</Box>}</div>;
}


export default class EditScaffold extends Component {
    constructor(props) {
        super(props)

        this.state = {
            width: window.innerWidth,
            header_width: 0,
            body_margin: 0,
            card_width: 0,
            tab_width: 0,
            zIndex: 0,
            window_width: window.innerWidth
        }

        this.handleWindowSizeChange = this.handleWindowSizeChange.bind(this)
    }

    componentDidMount() {
        this.handleWindowSizeChange()
    }

    componentWillMount() {
        window.addEventListener('resize', () => {
            setTimeout(() => {
                this.handleWindowSizeChange()
            }, 600);
        })

        const buttonEls = document.getElementsByClassName('navbar-toggler')

        buttonEls.forEach((element) => {
            // Add event listener
            element.addEventListener('click', () => {
                setTimeout(() => {
                    this.handleWindowSizeChange()
                }, 600);
            });
        })

    }

    componentWillUnmount() {
        window.removeEventListener('resize', this.handleWindowSizeChange)
    }

    handleWindowSizeChange() {
        const container_width = document.getElementsByClassName('container')[0].clientWidth

        let header_width = container_width

        let card_width = container_width - 32
        let tab_width = container_width
        let zIndex = 9999;

        const sidebar_shown = document.getElementsByClassName('sidebar-show').length

        if (sidebar_shown) {
            const sidebar_width = document.getElementsByClassName('sidebar')[0].clientWidth

            //header_width -= sidebar_width
            tab_width -= sidebar_width

            card_width = '100%'

            if(this.state.window_width <= 768) {
                zIndex = 9
            }
        }

        header_width -= 28

        const height = document.getElementsByClassName('MuiAppBar-root')[0].clientHeight

        this.setState({header_width: header_width, body_margin: height, card_width: card_width, tab_width:tab_width, zIndex: zIndex})
    }

    render() {
        return <div className="mt-3 w-100" style={{maxWidth: '1275px'}}>
            <AppBar position="fixed" style={{
                zIndex: this.state.zIndex,
                background: 'transparent',
                boxShadow: 'none',
                borderTop: '4px solid #000',
                top: '30px',
                width: this.state.header_width,
                right: '15px'
            }}>
                <Card>
                    <CardBody className="p-0">
                        <div className="d-flex justify-content-between align-items-center">
                            <div className="d-inline-flex">
                                <Menu/>

                                <h4 className="pl-3 pt-2">
                                    {this.props.title}
                                </h4>
                            </div>

                            <span>
                            {!!this.props.handleSubmit &&
                            <a className="pull-right btn btn-link p-0 pr-3"
                               onClick={this.props.handleSubmit}>{translations.save}</a>
                            }

                                {!!this.props.handleCancel &&
                                <a className={`pull-right btn btn-link p-0 pr-3 mr-4 ${this.props.cancelButtonDisabled ? 'disabled' : ''}`}
                                   onClick={this.props.handleCancel}>{translations.cancel}</a>
                                }
                        </span>

                            {!!this.props.addButton &&
                            this.props.addButton
                            }
                        </div>

                        {!!this.props.tabs.tabs &&
                        <AppTabs width={this.state.tab_width} fullWidth={this.props.fullWidth} className="mt-3"
                                 tabs={this.props.tabs.tabs} handleChange={this.props.tabs.settings.toggle}
                                 value={this.props.tabs.settings.activeTab}
                                 children={this.props.tabs.children}/>}
                    </CardBody>
                </Card>
            </AppBar>

            <div style={{width: this.state.card_width, marginTop: this.state.body_margin}}>
                {this.props.tabs.tabs && this.props.tabs.children.map((child, i) => (
                    <TabPanel className="mt-3" value={this.props.tabs.settings.activeTab} index={i}>
                        {child}
                    </TabPanel>
                ))}

                {!this.props.tabs.tabs && this.props.tabs.children.map((child, i) => (
                    <div>{child}</div>
                ))}
            </div>


        </div>
    }
}
