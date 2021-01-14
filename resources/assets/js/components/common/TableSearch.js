import React, { Component } from 'react'
import { Button, Input, InputGroup, UncontrolledTooltip } from 'reactstrap'
import { icons } from '../utils/_icons'
import { translations } from '../utils/_translations'

export default class TableSearch extends Component {
    constructor (props) {
        super(props)
        this.state = {
            query: ''
        }

        this.handleSearchChange = this.handleSearchChange.bind(this)
        this.reset = this.reset.bind(this)
    }

    handleSearchChange (event) {
        const query = event.target.value
        if (query.length === 0 || query.length > 3) {
            this.props.onChange(event)
        }

        this.setState({ query: query })
    }

    reset () {
        const e = {
            target: {
                name: 'searchText',
                id: 'searchText',
                value: ''
            }
        }

        this.setState({ query: '' }, () => this.props.onChange(e))
    }

    render () {
        const { query } = this.state

        return (
            <React.Fragment>
                <UncontrolledTooltip placement="right" target="clearSearch">
                    {translations.clear_search}
                </UncontrolledTooltip>

                <UncontrolledTooltip placement="top" target="searchText">
                    {translations.search}
                </UncontrolledTooltip>

                <InputGroup className="mb-3">
                    <Input id="searchText" name="searchText" type="text" placeholder="Search..." value={query}
                        onChange={this.handleSearchChange}/>
                    <Button color="link" className="bg-transparent"
                        style={{ marginLeft: '-40px', zIndex: 100, color: '#e4e7ea' }}
                        onClick={() => this.reset()}>
                        <i id="clearSearch" className={`fa ${icons.clear}`}/>
                    </Button>
                </InputGroup>
            </React.Fragment>
        )
    }
}
