/* eslint-disable no-unused-vars */
import React, { Component } from 'react'
import axios from 'axios'
import { Input } from 'reactstrap'

export default class Directory extends Component {
    constructor (props) {
        super(props)
        this.state = {
            query: '',
            message: '',
            loading: false,
            entities: {
                current_page: 1,
                from: 1,
                last_page: 1,
                per_page: 5,
                to: 1,
                total: 1,
                data: []
            },
            first_page: 1,
            current_page: 1,
            sorted_column: [],
            data: [],
            columns: [],
            offset: 4,
            order: 'asc'
        }
        this.cancel = ''
        this.handleSearchChange = this.handleSearchChange.bind(this)
        this.setPage = this.setPage.bind(this)
    }

    componentDidMount () {
        this.setPage()
    }

    pagesNumbers () {
        if (!this.state.entities.to) {
            return []
        }
        let from = this.state.entities.current_page - this.state.offset
        if (from < 1) {
            from = 1
        }
        let to = from + (this.state.offset * 2)
        if (to >= this.state.entities.last_page) {
            to = this.state.entities.last_page
        }
        const pagesArray = []
        for (let page = from; page <= to; page++) {
            pagesArray.push(page)
        }
        return pagesArray
    }

    setPage () {
        this.setState({ current_page: this.state.entities.current_page }, () => {
            this.fetchEntities()
        })
    }

    changePage (pageNumber) {
        this.setState({ current_page: pageNumber }, () => {
            this.fetchEntities()
        })
    }

    handleSearchChange (event) {
        const query = event.target.value
        if (query.length < 3 && query.length > 0) {
            this.setState({
                query,
                loading: false,
                message: ''
            })
            return false
        }
        this.setState({
            query,
            loading: true,
            message: ''
        }, () => {
            this.fetchEntities()
        })
    }

    fetchEntities () {
        if (this.cancel) {
            this.cancel.cancel()
        }

        this.cancel = axios.CancelToken.source()
        const fetchUrl = `${this.props.fetchUrl}?page=${this.state.current_page}&search_term=${this.state.query}&column=${this.state.sorted_column}&order=${this.state.order}&per_page=${this.state.entities.per_page}`

        axios.get(fetchUrl, {
            cancelToken: this.cancel.token
        })
            .then(response => {
                this.setState({
                    entities: response.data,
                    loading: false
                })
                const columns = this.props.columns && this.props.columns.length ? this.props.columns : Object.keys(response.data.data[0])
                this.setState({ columns: columns })
                this.props.updateState(response.data.data)
            })
            .catch(error => {
                if (axios.isCancel(error) || error) {
                    this.setState({
                        loading: false,
                        message: 'Failed to fetch the data. Please check network'
                    })
                }
            })
    }

    pageList () {
        return this.pagesNumbers().map(page => {
            return <li className={page === this.state.entities.current_page ? 'page-item active' : 'page-item'}
                key={page}>
                <button className="page-link" onClick={() => this.changePage(page)}>{page}</button>
            </li>
        })
    }

    render () {
        const { query, loading, message } = this.state
        const loader = loading ? <h2>Loading...</h2> : ''

        return (
            <React.Fragment>
                <div className="col-8 col-lg-6">
                    <Input
                        type="text"
                        name="query"
                        value={query}
                        id="search-input"
                        placeholder="Search..."
                        onChange={this.handleSearchChange}
                    />

                    {message && <p className="message">{message}</p>}

                    {loader}
                </div>

                {this.props.userList()}

                {(this.state.entities.data && this.state.entities.data.length > 0) &&
                <nav className="pull-left">
                    <ul className="pagination">
                        <li className="page-item">
                            <button className="page-link"
                                disabled={this.state.entities.current_page === 1}
                                onClick={() => this.changePage(this.state.entities.current_page - 1)}>
                                Previous
                            </button>
                        </li>
                        {this.pageList()}
                        <li className="page-item">
                            <button className="page-link"
                                disabled={this.state.entities.last_page === this.state.entities.current_page}
                                onClick={() => this.changePage(this.state.entities.current_page + 1)}>
                                Next
                            </button>
                        </li>
                        <span> &nbsp;
                            <i>Displaying {this.state.entities.data.length} of {this.state.entities.total} entries.</i></span>
                    </ul>
                </nav>
                }
            </React.Fragment>
        )
    }
}
