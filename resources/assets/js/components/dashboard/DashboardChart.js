import {
    Button,
    ButtonGroup,
    ButtonToolbar,
    Card,
    CardBody,
    CardFooter,
    CardTitle,
    Col,
    Progress,
    Row
} from 'reactstrap'
import FormatMoney from '../common/FormatMoney'
import moment from 'moment'
import Line from 'react-chartjs-2'
import React from 'react'
import { CustomTooltips } from '@coreui/coreui-plugin-chartjs-custom-tooltips'

const mainChartOpts = {
    tooltips: {
        enabled: false,
        custom: CustomTooltips,
        intersect: true,
        mode: 'index',
        position: 'nearest',
        callbacks: {
            labelColor: function (tooltipItem, chart) {
                return { backgroundColor: chart.data.datasets[tooltipItem.datasetIndex].borderColor }
            }
        }
    },
    maintainAspectRatio: false,
    legend: {
        display: false
    },
    scales: {
        xAxes: [
            {
                gridLines: {
                    drawOnChartArea: false
                }
            }],
        yAxes: [
            {
                ticks: {
                    beginAtZero: true,
                    maxTicksLimit: 5,
                    stepSize: Math.ceil(250 / 5),
                    max: 250
                }
            }]
    },
    elements: {
        point: {
            radius: 0,
            hitRadius: 10,
            hoverRadius: 4,
            hoverBorderWidth: 3
        }
    }
}

export default function DashboardChart (props) {
    return props.charts.map((entry, index) => {
        const buttons = Object.keys(entry.buttons).map((key, value) => {
            return <Button key={value}
                color="outline-secondary"
                onClick={() => this.onRadioBtnClick(key, entry.name)}
                active={props.radioSelected === key}>{key} <FormatMoney
                    amount={entry.buttons[key].value}/></Button>
        })

        const footerButtons = Object.keys(entry.buttons).map((key, value) => {
            return <Col key={value} sm={12} md
                className="mb-sm-2 mb-0">
                <div
                    className="text-muted">{key}
                </div>
                <strong>Avg {entry.buttons[key].avg}
                    ({entry.buttons[key].pct}%)</strong>
                <Progress
                    className="progress-xs mt-2"
                    color="warning" value={entry.buttons[key].pct}/>
            </Col>
        })

        return (<Row key={index}>
            <Col style={{ height: '600px' }}>
                <Card>
                    <CardBody>
                        <Row>
                            <Col sm="5">
                                <CardTitle
                                    className="mb-0"><h3>{entry.name}</h3></CardTitle>
                                <h5> {`${moment(props.start_date).format('Do MMMM YYYY')} - ${moment(props.end_date).format('Do MMMM YYYY')}`}
                                </h5>
                            </Col>
                            <Col sm="7"
                                className="d-none d-sm-inline-block">
                                <Button color="primary" onClick={props.doExport}
                                    className="float-right"><i
                                        className="icon-cloud-download"/></Button>
                                <ButtonToolbar
                                    className="float-right mt-5"
                                    aria-label="Toolbar with button groups">
                                    <ButtonGroup className="mr-3"
                                        aria-label="First group">
                                        {buttons}
                                    </ButtonGroup>
                                </ButtonToolbar>
                            </Col>
                        </Row>
                        <div className="chart-wrapper"
                            style={{
                                height: 300 + 'px',
                                marginTop: 40 + 'px'
                            }}>
                            <Line data={entry}
                                options={mainChartOpts}
                                height={300} type="bar"/>
                        </div>
                    </CardBody>
                    <CardFooter>
                        <Row className="text-center">
                            {footerButtons}
                        </Row>
                    </CardFooter>
                </Card>
            </Col>
        </Row>)
    })
}
