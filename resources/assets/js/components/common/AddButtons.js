import React, { Component } from 'react'
import { Button, UncontrolledTooltip } from 'reactstrap'
import { icons } from '../utils/_icons'
import Fab from '@material-ui/core/Fab'
import Tooltip from '@material-ui/core/Tooltip'
import { translations } from '../utils/_translations'

export default class AddButtons extends Component {
    render () {
        return (
            <React.Fragment>
                <UncontrolledTooltip placement="right" target="addButtonTooltip">
                    {translations.add_item}
                </UncontrolledTooltip>

                <Button id="addButtonTooltip" style={{ position: 'absolute', top: '20px', right: '10px' }}
                    className="d-none d-md-inline-block" color="primary" onClick={this.props.toggle}>+</Button>
                {/* <Button id="addButtonTooltip" className="d-md-none float" color="primary" onClick={this.props.toggle}><i */}
                {/*    className={`fa ${icons.add} my-float`}/></Button> */}
                <Fab
                    className="d-md-none"
                    style={{ position: 'fixed', bottom: '40px', right: '40px', zIndex: 999 }}
                    size="large"
                    color="primary"
                    aria-label="add"
                    onClick={this.props.toggle}
                >
                    <Tooltip title={'Add new category'}>
                        <i className={`fa ${icons.add}`}/>
                    </Tooltip>
                </Fab>
            </React.Fragment>
        )
    }
}
