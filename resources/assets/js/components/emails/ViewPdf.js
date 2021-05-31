import React, {Component} from 'react'
import parse from 'html-react-parser'
import CaseModel from "../models/CaseModel";
import AccountRepository from "../repositories/AccountRepository";
import HtmlViewer from "./HtmlViewer";
import PdfViewer from "./PdfViewer";
import {translations} from "../utils/_translations";
import AppSwitch from "../common/AppSwitch";

export default class ViewPdf extends Component {
    constructor(props) {
        super(props)
        this.state = {
            obj_url: null,
            show_html: false
        }

        this.loadPdf = this.loadPdf.bind(this)
    }

    componentDidMount() {
        this.isComponentMounted = true
        this.loadPdf()
    }

    static getDerivedStateFromProps(props, state) {
        if (props.show_html && props.show_html !== state.show_html) {
            return {show_html: props.show_html}
        }

        return null
    }

    componentDidUpdate(prevProps, prevState) {
        if (this.props.show_html && this.props.show_html !== prevProps.show_html) {
            this.loadPdf()
        }
    }

    loadPdf() {
        this.setState({obj_url: ''})
        const accountRepository = new AccountRepository()

        accountRepository.previewPdf(this.state.show_html, this.props.model.entity, this.props.model.id).then(response => {
            const data = this.state.show_html ? response.data : response
            this.setState({obj_url: data}, () => {
                if (!this.props.show_html) {
                    URL.revokeObjectURL(response)
                }
            })
        })
    }

    render() {
        const width = this.props.width ? this.props.width + 'px' : '100%'

        let content = null

        if (this.state.obj_url && this.state.obj_url.length) {
           content = this.state.show_html ? <HtmlViewer width={width} html={this.state.obj_url}/> :
                <PdfViewer width={width} pdf={this.state.obj_url}/>
        }

        return <div>
            <AppSwitch label={translations.html_mode} name="show_html" isOn={this.state.show_html} handleToggle={(e) => {
                this.setState({show_html: !this.state.show_html}, () => {
                    this.loadPdf()
                })
            }} />

            {content}
        </div>
    }
}
