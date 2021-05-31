import React, {Component} from 'react'
import {Card, CardBody} from 'reactstrap'
import {translations} from '../utils/_translations'
import Menu from '../settings/Menu'
import AppTabs from "./AppTabs";
import {AppBar, Box, Toolbar} from "@material-ui/core";
import SaveCancelButtons from "./SaveCancelButtons";
import Alert from '@material-ui/lab/Alert';

function TabPanel(props) {
    const {children, value, index, ...other} = props;
    return <div className={props.className} {...other}>{value === index && <Box>{children}</Box>}</div>;
}


export default class EditScaffold extends Component {
    constructor(props) {
        super(props)

        this.state = {
            width: window.innerWidth,
            container_width: 0,
            sidebar_open: true,
            window_width: window.innerWidth
        }

        this.handleWindowSizeChange = this.handleWindowSizeChange.bind(this)
    }

    componentDidMount() {
        setTimeout(() => {
            this.handleWindowSizeChange()
        }, 400);
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
        const sidebar_shown = document.getElementsByClassName('sidebar-show').length > 0
        const height = document.getElementsByClassName('MuiAppBar-root')[0].clientHeight

        this.setState({height: height, container_width: container_width, sidebar_open: sidebar_shown})
    }

    render() {
        let showUpgradeBanner = false;
        let isEnabled = (this.state.window_width <= 768 ||
            this.props.isEditing) &&
            (!this.props.isLoading && !this.props.isSaving);
        let isCancelEnabled = false;

        if (this.props.isAdvancedSettings && !['PROM', 'PROY'].includes(JSON.parse(localStorage.getItem('plan')).code)) {
            showUpgradeBanner = true;
            if (isEnabled) {
                isCancelEnabled = true;
                isEnabled = false;
            }
        }

        return <div className="mt-3 w-100" style={{maxWidth: '1275px'}}>
            <AppBar position="fixed" style={{
                zIndex: this.state.window_width <= 768 ? 9 : 9999,
                background: 'transparent',
                boxShadow: 'none',
                borderTop: '4px solid #000',
                top: '30px',
                width: this.state.container_width - 30,
                right: '16px'
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

                            <SaveCancelButtons handleCancel={this.props.handleCancel} isEnabled={isEnabled}
                                               isHeader={true} isCancelEnabled={isCancelEnabled}
                                               saveLabel={translations.save}
                                               isSaving={this.props.isSaving} onSavePressed={this.props.handleSubmit}
                                               onCancelPressed={this.props.handleCancel}/>
                        </div>

                        {!!this.props.tabs.tabs &&
                        <AppTabs fullWidth={this.state.window_width > 1024 && this.props.tabs.tabs.length <= 6} className="mt-3"
                                 tabs={this.props.tabs.tabs} handleChange={this.props.tabs.settings.toggle}
                                 value={this.props.tabs.settings.activeTab}
                                 children={this.props.tabs.children}/>}
                    </CardBody>
                </Card>
            </AppBar>

            <div style={{
                width: this.state.sidebar_open ? '100%' : this.state.container_width - 31,
                marginTop: this.state.height
            }}>
                {this.props.tabs.tabs && this.props.tabs.children.map((child, i) => (
                    <TabPanel className="mt-3" value={this.props.tabs.settings.activeTab} index={i}>
                        {showUpgradeBanner ?
                            <Alert severity="warning">{translations.upgrade_to_paid_plan}</Alert> : child}
                    </TabPanel>
                ))}

                {!this.props.tabs.tabs && this.props.tabs.children.map((child, i) => (
                    <div>{showUpgradeBanner ?
                        <Alert severity="warning">{translations.upgrade_to_paid_plan}</Alert> : child}</div>
                ))}
            </div>


        </div>
    }
}
