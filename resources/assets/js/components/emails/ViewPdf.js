import React, { Component } from 'react'

export default class ViewPdf extends Component {
    constructor (props) {
        super(props)
        this.state = {
            obj_url: null
        }

        this.loadPdf = this.loadPdf.bind(this)
    }

    componentDidMount () {
        this.isComponentMounted = true
        this.loadPdf()
    }

    loadPdf () {
        this.props.model.loadPdf().then(url => {
            console.log('url', url)
            this.setState({ obj_url: url }, () => URL.revokeObjectURL(url))
        })
    }

    render () {
        const width = this.props.width ? this.props.width + 'px' : '100%'
        return (
            <iframe style={{ width: `${width}`, height: '400px' }}
                className="embed-responsive-item" id="viewer"
                src={this.state.obj_url}/>
        )
    }
}
