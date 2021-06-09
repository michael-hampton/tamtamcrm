import { Button, Col, Row, TabPane } from 'reactstrap'
import { CardModule } from '../common/Card'
import React from 'react'
import ReactEcharts from 'echarts-for-react'

export default function Overview (props) {
    const pie_options = {
        tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b} : {c} ({d}%)'
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: ['Website', 'Personal Contact', 'Email', 'Other', 'Call']
        },
        series: [
            {
                name: 'Sources',
                type: 'pie',
                radius: '55%',
                center: ['50%', '60%'],
                data: props.sources,
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }
        ]
    }

    const onEvents = {
        // click: this.onChartClick,
        // legendselectchanged: this.onChartLegendselectchanged
    }

    return <>
        <Row>
            <Col className="pl-0" md={6}>
                <CardModule
                    body={true}
                    content={
                        <div>
                            <div className="media">
                                <div className="media-body text-left">
                                    <h3 className="success">{props.leadsToday}</h3>
                                    <span>Today's Leads</span>
                                </div>
                                <div className="media-right media-middle">
                                    <i className="ft-award success font-large-2 float-right"/>
                                </div>
                            </div>

                            <div className="progress mt-1 mb-0" style={{ height: '7px' }}>
                                <div className="progress-bar bg-success" role="progressbar"
                                    style={{ width: '80%' }} aria-valuenow="80"
                                    aria-valuemin="0"
                                    aria-valuemax="100"/>
                            </div>
                        </div>
                    }
                />

                <CardModule
                    body={true}
                    content={
                        <div>
                            <div className="media">
                                <div className="media-body text-left">
                                    <h3 className="deep-orange">{props.newDeals}</h3>
                                    <span>New Deal</span>
                                </div>
                                <div className="media-right media-middle">
                                    <i className="ft-package deep-orange font-large-2 float-right"/>
                                </div>
                            </div>

                            <div className="progress mt-1 mb-0" style={{ height: '7px' }}>
                                <div className="progress-bar bg-deep-orange" role="progressbar"
                                    style={{ width: '35%' }} aria-valuenow="35"
                                    aria-valuemin="0"
                                    aria-valuemax="100"/>
                            </div>
                        </div>
                    }
                />

                <CardModule
                    body={true}
                    content={
                        <div>
                            <div className="media">
                                <div className="media-body text-left">
                                    <h3 className="info">{props.newCustomers}</h3>
                                    <span>New Customers</span>
                                </div>
                                <div className="media-right media-middle">
                                    <i className="ft-users info font-large-2 float-right"/>
                                </div>
                            </div>

                            <div className="progress mt-1 mb-0" style={{ height: '7px' }}>
                                <div className="progress-bar bg-success" role="progressbar"
                                    style={{ width: '35%' }} aria-valuenow="35"
                                    aria-valuemin="0"
                                    aria-valuemax="100"/>
                            </div>
                        </div>
                    }
                />
            </Col>
            <Col className="pl-0" md={6}>
                <CardModule
                    body={true}
                    hCenter={true}
                    header={
                        <React.Fragment>
                            <span className="success darken-1">Total Budget</span>
                            <h3 className="font-large-2 grey darken-1 text-bold-200">{props.totalBudget}</h3>
                        </React.Fragment>
                    }
                    content={
                        <React.Fragment>
                            <input type="text" value="75"
                                className="knob hide-value responsive angle-offset"
                                data-angleOffset="0" data-thickness=".15"
                                data-linecap="round" data-width="150"
                                data-height="150" data-inputColor="#e1e1e1"
                                data-readOnly="true" data-fgColor="#37BC9B"
                                data-knob-icon="ft-trending-up"/>

                            <ul className="list-inline clearfix mt-2 mb-0">
                                <li className="border-right-grey border-right-lighten-2 pr-2">
                                    <h2 className="grey darken-1 text-bold-400">75%</h2>
                                    <span className="success">Completed</span>
                                </li>
                                <li className="pl-2">
                                    <h2 className="grey darken-1 text-bold-400">25%</h2>
                                    <span className="danger">Remaining</span>
                                </li>
                            </ul>
                        </React.Fragment>
                    }
                />
            </Col>
            {/* <Col md={6}> */}
            {/*    <CardModule */}
            {/*        body={false} */}
            {/*        content={ */}
            {/*            <div className="earning-chart position-relative"> */}
            {/*                <div className="chart-title position-absolute mt-2 ml-2"> */}
            {/*                    <h1 className="font-large-2 grey darken-1 text-bold-200">{props.totalEarnt}</h1> */}
            {/*                    <span className="text-muted">Total Earning</span> */}
            {/*                </div> */}
            {/*                <div className="chartjs height-400"> */}
            {/*                    <canvas id="earning-chart" className="height-400 block"/> */}
            {/*                </div> */}
            {/*                <div */}
            {/*                    className="chart-stats position-absolute position-bottom-0 position-right-0 mb-2 mr-3"> */}
            {/*                    <a href="#" className="btn bg-info mr-1 white">Statistics <i */}
            {/*                        className="ft-bar-chart"/></a> <span */}
            {/*                        className="text-muted">for the <a */}
            {/*                            href="#">last year.</a></span> */}
            {/*                </div> */}
            {/*            </div> */}
            {/*        } */}
            {/*    /> */}
            {/* </Col> */}
        </Row>

        {/* <Row className="match-height"> */}
        {/*    <Col className="col-xl-6" lg={12}> */}
        {/*        <CardModule */}
        {/*            body={true} */}
        {/*            header={ */}
        {/*                <React.Fragment> */}
        {/*                    <h4 className="card-title">Deals Funnel <span */}
        {/*                        className="text-muted text-bold-400">This Month</span></h4> */}
        {/*                    <a className="heading-elements-toggle"><i */}
        {/*                        className="ft-more-horizontal font-medium-3"/></a> */}
        {/*                    <div className="heading-elements"> */}
        {/*                        <ul className="list-inline mb-0"> */}
        {/*                            <li><a data-action="reload"><i className="ft-rotate-cw"/></a> */}
        {/*                            </li> */}
        {/*                        </ul> */}
        {/*                    </div> */}
        {/*                </React.Fragment> */}
        {/*            } */}
        {/*            content={ */}
        {/*                <ReactEcharts option={this.getOption()}/> */}
        {/*            } */}
        {/*        /> */}

        {/*    </Col> */}
        {/*    <Col className="col-xl-6" lg={12}> */}
        {/*        <CardModule */}
        {/*            cardHeight='410px' */}
        {/*            body={true} */}
        {/*            header={ */}
        {/*                <React.Fragment> */}
        {/*                    <h4 className="card-title">Deals <span className="text-muted text-bold-400">- Won 5</span> */}
        {/*                    </h4> */}
        {/*                    <a className="heading-elements-toggle"><i */}
        {/*                        className="ft-more-horizontal font-medium-3"/></a> */}
        {/*                    <div className="heading-elements"> */}
        {/*                        <ul className="list-inline mb-0"> */}
        {/*                            <li><a data-action="reload"><i className="ft-rotate-cw"/></a> */}
        {/*                            </li> */}
        {/*                        </ul> */}
        {/*                    </div> */}
        {/*                </React.Fragment> */}
        {/*            } */}
        {/*            content={ */}
        {/*                <div style={{ */}
        {/*                    height: '300px', */}
        {/*                    overflowY: 'auto' */}
        {/*                }} id="deals-list-scroll" */}
        {/*                className="card-body height-350 position-relative ps-container ps-theme-default" */}
        {/*                data-ps-id="6205b797-6d0d-611f-25fd-16195eadda29"> */}
        {/*                    {leads} */}
        {/*                </div> */}
        {/*            } */}
        {/*        /> */}
        {/*    </Col> */}
        {/* </Row> */}

        <Row className="match-height">
            {/* <Col className="col-xl-8" lg={12}> */}
            {/*    <StatsCard/> */}
            {/* </Col> */}

            <Col md={12}>
                <CardModule
                    body={true}
                    header={
                        <React.Fragment>
                            <h4 className="card-title">Sources <span
                                className="text-muted text-bold-400">This Month</span></h4>
                            <a className="heading-elements-toggle"><i
                                className="ft-more-horizontal font-medium-3"/></a>
                            <div className="heading-elements">
                                <ul className="list-inline mb-0">
                                    <li><a data-action="reload"><i
                                        className="ft-rotate-cw"/></a>
                                    </li>
                                </ul>
                            </div>
                        </React.Fragment>
                    }
                    content={
                        <ReactEcharts
                            option={pie_options}
                            style={{ height: 150 }}
                            onChartReady={props.onChartReady}
                            onEvents={onEvents}
                        />
                    }
                />
            </Col>
        </Row>

        <Row>
            <Button color="danger" onClick={props.toggleModal}>Configure Dashboard</Button>
        </Row>
    </>
}
