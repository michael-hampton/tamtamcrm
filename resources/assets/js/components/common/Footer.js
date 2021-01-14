import React, { Component } from 'react'

export default class Footer extends Component {
    render () {
        return (
            <nav className="navbar fixed-bottom navbar-expand-sm navbar-dark bg-dark">
                <a className="navbar-brand" href="#">Bottom navbar</a>
                <button className="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <span className="navbar-toggler-icon"/>
                </button>
                <div className="collapse navbar-collapse" id="navbarCollapse">
                    <ul className="navbar-nav mr-auto">
                        <li className="nav-item active">
                            <a className="nav-link" href="#">Home <span className="sr-only">(current)</span></a>
                        </li>
                        <li className="nav-item">
                            <a className="nav-link" href="#">Link</a>
                        </li>
                        <li className="nav-item">
                            <a className="nav-link disabled" href="#">Disabled</a>
                        </li>
                        <li className="nav-item dropup">
                            <a className="nav-link dropdown-toggle" href="https://getbootstrap.com/" id="dropdown10"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropup</a>
                            <div className="dropdown-menu" aria-labelledby="dropdown10">
                                <a className="dropdown-item" href="#">Action</a>
                                <a className="dropdown-item" href="#">Another action</a>
                                <a className="dropdown-item" href="#">Something else here</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        )
    }
}
