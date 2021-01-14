import React from 'react'
import FormatDate from '../../common/FormatDate'
import { ListGroup, ListGroupItem, UncontrolledCollapse } from 'reactstrap'
import { translations } from '../../utils/_translations'
import ErrorLogModel from '../../models/ErrorLogModel'
import { icons } from '../../utils/_icons'
import ReactJson from 'react-json-view'

export default function ErrorLog (props) {
    const error_logs = props.error_logs.length ? props.error_logs.map((error_log, index) => {
        const errorLogModel = new ErrorLogModel(error_log)
        const icon = error_log.error_type === errorLogModel.EMAIL ? icons.email : icons.credit_card
        return (
            <ListGroupItem>
                <a id="toggler" className="btn collapsed w-100" data-toggle="collapse" href="#collapseExample1">
                    <div className="d-flex w-100 justify-content-between">
                        <h5 className="mb-1">{`${translations[errorLogModel.category]} › ${translations[errorLogModel.entity]}`}
                        </h5>
                        <span><i className={`fa ${icon}`}/></span>
                    </div>
                    <p className="d-flex w-100 justify-content-between mb-1">
                        <span>{translations[errorLogModel.event]}</span>
                        <span><FormatDate date={error_log.created_at} with_time={true}/></span>
                    </p>
                </a>
                <UncontrolledCollapse toggler="#toggler">
                    <ReactJson src={error_log.data} name="error"/>
                </UncontrolledCollapse>
            </ListGroupItem>
        )
    }) : null

    return <ListGroup>
        {error_logs}
    </ListGroup>
}
