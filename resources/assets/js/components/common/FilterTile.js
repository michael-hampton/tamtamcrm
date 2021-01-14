import React, { Component } from 'react'
import { Collapse, Form } from 'reactstrap'
import { icons } from '../utils/_icons'

export default class FilterTile extends Component {
    constructor (props) {
        super(props)

        this.state = {
            isOpen: window.innerWidth > 670
        }

        this.toggleFilters = this.toggleFilters.bind(this)
    }

    toggleFilters () {
        this.setState({ isOpen: !this.state.isOpen }, () => {
            localStorage.setItem('datatable_collapsed', !this.state.isOpen)
            this.props.setFilterOpen(this.state.isOpen)
        })
    }

    render () {
        return (
            <Form>
                <span onClick={this.toggleFilters}
                    style={{ marginBottom: '1rem', fontSize: '18px' }}>
                    <i style={{ display: (this.state.isOpen ? 'none' : 'block'), marginTop: '6px' }}
                        className={`fa fa-fw ${icons.right} pull-left`}/>
                    <i style={{ display: (!this.state.isOpen ? 'none' : 'block'), marginTop: '6px' }}
                        className={`fa fa-fw ${icons.down} pull-left`}/>
                </span>

                <Collapse
                    isOpen={this.state.isOpen}
                >
                    {this.props.filters}
                </Collapse>
            </Form>
        )
    }
}
