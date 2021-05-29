import React from 'react'
import {Card, CardBody} from 'reactstrap'
import {translations} from '../utils/_translations'
import Menu from './Menu'
import AppTabs from "../common/AppTabs";
import {Box} from "@material-ui/core";

function TabPanel(props) {
    const {children, value, index, ...other} = props;
    return <div className={props.className} {...other}>{value === index && <Box>{children}</Box>}</div>;
}

export default function EditScaffold(props) {
    return (
        <div className="mt-3 w-100" style={{maxWidth: '1275px'}}>
            <Card>
                <CardBody className="p-0">
                    <div className="d-flex justify-content-between align-items-center">
                        <div className="d-inline-flex">
                            <Menu/>

                            <h4 className="pl-3 pt-2">
                                {props.title}
                            </h4>
                        </div>

                        <span>
                            {!!props.handleSubmit &&
                            <a className="pull-right btn btn-link p-0 pr-3"
                               onClick={props.handleSubmit}>{translations.save}</a>
                            }

                            {!!props.handleCancel &&
                            <a className={`pull-right btn btn-link p-0 pr-3 mr-4 ${props.cancelButtonDisabled ? 'disabled' : ''}`}
                               onClick={props.handleCancel}>{translations.cancel}</a>
                            }
                        </span>

                        {!!props.addButton &&
                        props.addButton
                        }
                    </div>
                </CardBody>

                {!!props.tabs.tabs &&
                <AppTabs fullWidth={props.fullWidth} className="mt-3" tabs={props.tabs.tabs} handleChange={props.tabs.settings.toggle}
                         value={props.tabs.settings.activeTab}
                         children={props.tabs.children}/>}
            </Card>

            {props.tabs.tabs && props.tabs.children.map((child, i) => (
                <TabPanel className="mt-3" value={props.tabs.settings.activeTab} index={i}>
                    {child}
                </TabPanel>
            ))}

            {!props.tabs.tabs && props.tabs.children.map((child, i) => (
                <div>{child}</div>
            ))}
        </div>
    )
}
