import React, { Component } from 'react'
import { Pagination, PaginationItem, PaginationLink } from 'reactstrap'

export default class PaginationBuilder extends Component {
    constructor (props) {
        super(props)
        this.state = {
            roles: [],
            modal: false
        }
    }

    pagesNumbers () {
        if (!this.props.to) {
            return []
        }
        let from = this.props.current_page - this.props.offset
        if (from < 1) {
            from = 1
        }
        let to = from + (this.props.offset * 2)
        if (to >= this.props.last_page) {
            to = this.props.last_page
        }
        const pagesArray = []
        for (let page = from; page <= to; page++) {
            pagesArray.push(page)
        }
        return pagesArray
    }

    changePage (pageNumber) {
        this.props.fetchEntities(pageNumber)
    }

    pageList () {
        return this.pagesNumbers().map(page => {
            return <PaginationItem className="d-none d-md-block" active={page === this.props.current_page}
                key={page}>
                <PaginationLink onClick={() => this.changePage(page)}>{page}</PaginationLink>
            </PaginationItem>
        })
    }

    render () {
        const { from, to, recordCount, current_page, page, last_page } = this.props
        const counter = from > 0 && to > 0
            ? <div className="pull-left col-6">
                Showing {from} to {to} of {recordCount} entries
            </div> : null

        return (<div className="row">
            {counter}

            <Pagination className="pull-right" listClassName="pull-right">
                {current_page > 1
                    ? <PaginationItem><PaginationLink
                        onClick={() => this.changePage(1)}>
                        First
                    </PaginationLink></PaginationItem> : null}
                {current_page > 1
                    ? <PaginationItem><PaginationLink
                        onClick={() => this.changePage(page - 1)}>
                        Previous
                    </PaginationLink></PaginationItem> : null}
                {this.pageList()}
                {page < last_page
                    ? <PaginationItem><PaginationLink
                        onClick={() => this.changePage(page + 1)}>
                        Next
                    </PaginationLink></PaginationItem> : null}
                {page < last_page
                    ? <PaginationItem><PaginationLink
                        onClick={() => this.changePage(last_page)}>
                        Last
                    </PaginationLink></PaginationItem> : null}
            </Pagination>
        </div>)
    }
}
