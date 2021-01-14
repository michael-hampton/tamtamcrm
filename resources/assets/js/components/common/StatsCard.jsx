import React, { Component } from 'react'
import { Card, CardHeader, CardText } from 'reactstrap'

export class StatsCard extends Component {
    render () {
        return (
            <Card style={{ height: '479px' }}>
                <CardHeader style={{ backgroundColor: '#FFF' }} className='no-border'>
                    Activity Timeline
                </CardHeader>
                <div className="mt-1">
                    <CardText>
                        <section className="cd-horizontal-timeline m-0 loaded">
                            <div className="timeline">
                                <div className="events-wrapper">
                                    <div className="events" style={{ width: '1140px' }}>
                                        <ol className="list-unstyled">
                                            <li><a href="#0" data-date="16/01/2015" className="selected p-0"
                                                style={{ left: '120px' }}>16 Jan</a></li>
                                            <li><a href="#0" data-date="28/02/2015" className="p-0"
                                                style={{ left: '300px' }}>28 Feb</a></li>
                                            <li><a href="#0" data-date="20/04/2015" className="p-0"
                                                style={{ left: '480px' }}>20 Mar</a></li>
                                            <li><a href="#0" data-date="20/05/2015" className="p-0"
                                                style={{ left: '600px' }}>20 May</a></li>
                                            <li><a href="#0" data-date="09/07/2015" className="p-0"
                                                style={{ left: '780px' }}>09 Jul</a></li>
                                            <li><a href="#0" data-date="30/08/2015" className="p-0"
                                                style={{ left: '960px' }}>30 Aug</a></li>
                                            <li><a href="#0" data-date="15/09/2015" className="p-0"
                                                style={{ left: '1020px' }}>15 Sep</a></li>
                                        </ol>
                                        <span className="filling-line" aria-hidden="true"
                                            style={{ transform: 'scaleX(0.12794)' }}/>
                                    </div>
                                </div>
                                <ul className="cd-timeline-navigation list-unstyled">
                                    <li><a href="#0" className="prev inactive">Prev</a></li>
                                    <li><a href="#0" className="next">Next</a></li>
                                </ul>
                            </div>
                            <div className="events-content">
                                <ol className="list-unstyled">
                                    <li className="selected" data-date="16/01/2015">
                                        <div className="media">
                                            <div className="media-left mr-1">
                                                <img className="media-object avatar avatar-md rounded-circle"
                                                    src="/files/avatar-s-4.png" alt="Generic placeholder image"/>
                                            </div>
                                            <div className="media-body">
                                                <p className="text-bold-600 m-0">Philip Garrett</p>
                                                <p className="text-muted m-0">Marketing Manager</p>
                                            </div>
                                            <div className="media-body text-right">
                                                <h4 className="m-0">Horizontal Timeline</h4>
                                                <p className="text-muted mb-1"><em className="font-medium-2">January
                                                    16th, 2015</em></p>
                                            </div>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur
                                            aspernatur at, eaque hic repellendus sit dicta consequatur quae, ut harum
                                            ipsam molestias maxime non nisi reiciendis eligendi! </p>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa</p>
                                        <button type="button" className="btn btn-outline-primary float-right mt-1"><i
                                            className="ft-calendar"/> Add to Calender
                                        </button>
                                    </li>

                                    <li data-date="28/02/2015">
                                        <div className="media">
                                            <div className="media-left mr-1">
                                                <img className="media-object avatar avatar-md rounded-circle"
                                                    src="/files/avatar-s-4.png" alt="Generic placeholder image"/>
                                            </div>
                                            <div className="media-body">
                                                <p className="text-bold-600 m-0">Philip Garrett</p>
                                                <p className="text-muted m-0">Marketing Manager</p>
                                            </div>
                                            <div className="media-body text-right">
                                                <h4 className="m-0">Horizontal Timeline</h4>
                                                <p className="text-muted mb-1"><em className="font-medium-2">January
                                                    16th, 2015</em></p>
                                            </div>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur
                                            aspernatur at, eaque hic repellendus sit dicta consequatur quae, ut harum
                                            ipsam molestias maxime non nisi reiciendis eligendi! </p>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa</p>
                                        <button type="button" className="btn btn-outline-primary float-right mt-1"><i
                                            className="ft-calendar"/> Add to Calender
                                        </button>
                                    </li>

                                    <li data-date="20/04/2015">
                                        <div className="media">
                                            <div className="media-left mr-1">
                                                <img className="media-object avatar avatar-md rounded-circle"
                                                    src="/files/avatar-s-4.png" alt="Generic placeholder image"/>
                                            </div>
                                            <div className="media-body">
                                                <p className="text-bold-600 m-0">Philip Garrett</p>
                                                <p className="text-muted m-0">Marketing Manager</p>
                                            </div>
                                            <div className="media-body text-right">
                                                <h4 className="m-0">Horizontal Timeline</h4>
                                                <p className="text-muted mb-1"><em className="font-medium-2">January
                                                    16th, 2015</em></p>
                                            </div>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur
                                            aspernatur at, eaque hic repellendus sit dicta consequatur quae, ut harum
                                            ipsam molestias maxime non nisi reiciendis eligendi! </p>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa</p>
                                        <button type="button" className="btn btn-outline-primary float-right mt-1"><i
                                            className="ft-calendar"/> Add to Calender
                                        </button>
                                    </li>

                                    <li data-date="20/05/2015">
                                        <div className="media">
                                            <div className="media-left mr-1">
                                                <img className="media-object avatar avatar-md rounded-circle"
                                                    src="/files/avatar-s-4.png" alt="Generic placeholder image"/>
                                            </div>
                                            <div className="media-body">
                                                <p className="text-bold-600 m-0">Philip Garrett</p>
                                                <p className="text-muted m-0">Marketing Manager</p>
                                            </div>
                                            <div className="media-body text-right">
                                                <h4 className="m-0">Horizontal Timeline</h4>
                                                <p className="text-muted mb-1"><em className="font-medium-2">January
                                                    16th, 2015</em></p>
                                            </div>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur
                                            aspernatur at, eaque hic repellendus sit dicta consequatur quae, ut harum
                                            ipsam molestias maxime non nisi reiciendis eligendi! </p>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa</p>
                                        <button type="button" className="btn btn-outline-primary float-right mt-1"><i
                                            className="ft-calendar"/> Add to Calender
                                        </button>
                                    </li>

                                    <li data-date="09/07/2015">
                                        <div className="media">
                                            <div className="media-left mr-1">
                                                <img className="media-object avatar avatar-md rounded-circle"
                                                    src="/files/avatar-s-4.png" alt="Generic placeholder image"/>
                                            </div>
                                            <div className="media-body">
                                                <p className="text-bold-600 m-0">Philip Garrett</p>
                                                <p className="text-muted m-0">Marketing Manager</p>
                                            </div>
                                            <div className="media-body text-right">
                                                <h4 className="m-0">Horizontal Timeline</h4>
                                                <p className="text-muted mb-1"><em className="font-medium-2">January
                                                    16th, 2015</em></p>
                                            </div>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur
                                            aspernatur at, eaque hic repellendus sit dicta consequatur quae, ut harum
                                            ipsam molestias maxime non nisi reiciendis eligendi! </p>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa</p>
                                        <button type="button" className="btn btn-outline-primary float-right mt-1"><i
                                            className="ft-calendar"/> Add to Calender
                                        </button>

                                    </li>

                                    <li data-date="30/08/2015">
                                        <div className="media">
                                            <div className="media-left mr-1">
                                                <img className="media-object avatar avatar-md rounded-circle"
                                                    src="/files/avatar-s-4.png" alt="Generic placeholder image"/>
                                            </div>
                                            <div className="media-body">
                                                <p className="text-bold-600 m-0">Philip Garrett</p>
                                                <p className="text-muted m-0">Marketing Manager</p>
                                            </div>
                                            <div className="media-body text-right">
                                                <h4 className="m-0">Horizontal Timeline</h4>
                                                <p className="text-muted mb-1"><em className="font-medium-2">January
                                                    16th, 2015</em></p>
                                            </div>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur
                                            aspernatur at, eaque hic repellendus sit dicta consequatur quae, ut harum
                                            ipsam molestias maxime non nisi reiciendis eligendi! </p>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa</p>
                                        <button type="button" className="btn btn-outline-primary float-right mt-1"><i
                                            className="ft-calendar"/> Add to Calender
                                        </button>
                                    </li>

                                    <li data-date="15/09/2015">
                                        <div className="media">
                                            <div className="media-left mr-1">
                                                <img className="media-object avatar avatar-md rounded-circle"
                                                    src="/files/avatar-s-4.png" alt="Generic placeholder image"/>
                                            </div>
                                            <div className="media-body">
                                                <p className="text-bold-600 m-0">Philip Garrett</p>
                                                <p className="text-muted m-0">Marketing Manager</p>
                                            </div>
                                            <div className="media-body text-right">
                                                <h4 className="m-0">Horizontal Timeline</h4>
                                                <p className="text-muted mb-1"><em className="font-medium-2">January
                                                    16th, 2015</em></p>
                                            </div>
                                        </div>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa, quia velit nulla adipisci? Consequuntur
                                            aspernatur at, eaque hic repellendus sit dicta consequatur quae, ut harum
                                            ipsam molestias maxime non nisi reiciendis eligendi! </p>
                                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Illum praesentium
                                            officia, fugit recusandae ipsa</p>
                                        <button type="button" className="btn btn-outline-primary float-right mt-1"><i
                                            className="ft-calendar"/> Add to Calender
                                        </button>
                                    </li>
                                </ol>
                            </div>
                        </section>
                    </CardText>
                </div>

            </Card>
        )
    }
}

export default StatsCard
