import React from 'react'
import { Card, CardBody } from 'reactstrap'
import { translations } from '../utils/_translations'
import Menu from './Menu'

export default function Header (props) {
    return (
        <div className={`topbar ${props.className || ''}`}>
            <Card className="m-0">
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
                            <a className="pull-right btn btn-link p-0 pr-3" onClick={props.handleSubmit}>{translations.save}</a>
                            }

                            {!!props.handleCancel &&
                            <a className={`pull-right btn btn-link p-0 pr-3 mr-4 ${props.cancelButtonDisabled ? 'disabled' : ''}`} onClick={props.handleCancel}>{translations.cancel}</a>
                            }
                        </span>

                        {!!props.addButton &&
                        props.addButton
                        }
                    </div>

                    {!!props.tabs &&
                    props.tabs}
                </CardBody>
            </Card>
        </div>
    )
}
