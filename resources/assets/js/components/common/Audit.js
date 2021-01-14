import moment from 'moment'
import React from 'react'
import { Card, CardBody, ListGroup, ListGroupItem } from 'reactstrap'
import FormatDate from './FormatDate'

var deepDiffMapper = (function () {
    return {
        VALUE_CREATED: 'created',
        VALUE_UPDATED: 'updated',
        VALUE_DELETED: 'deleted',
        VALUE_UNCHANGED: 'unchanged',
        map: function (obj1, obj2) {
            if (this.isFunction(obj1) || this.isFunction(obj2)) {
                throw 'Invalid argument. Function given, object expected.'
            }
            if (this.isValue(obj1) || this.isValue(obj2)) {
                var type = this.compareValues(obj1, obj2)

                if (type !== 'unchanged') {
                    return {
                        type: type,
                        data: (obj1 === undefined) ? obj2 : obj1
                    }
                }

                return null
            }

            var diff = {}
            for (var key in obj1) {
                if (this.isFunction(obj1[key])) {
                    continue
                }

                var value2 = undefined
                if (typeof (obj2[key]) !== 'undefined') {
                    value2 = obj2[key]
                }

                diff[key] = this.map(obj1[key], value2)
            }
            for (var key in obj2) {
                if (this.isFunction(obj2[key]) || (typeof (diff[key]) !== 'undefined')) {
                    continue
                }

                diff[key] = this.map(undefined, obj2[key])
            }

            return diff
        },
        compareValues: function (value1, value2) {
            if (value1 === value2) {
                return this.VALUE_UNCHANGED
            }
            if (this.isDate(value1) && this.isDate(value2) && value1.getTime() === value2.getTime()) {
                return this.VALUE_UNCHANGED
            }
            if (typeof (value1) === 'undefined') {
                return this.VALUE_CREATED
            }
            if (typeof (value2) === 'undefined') {
                return this.VALUE_DELETED
            }

            return this.VALUE_UPDATED
        },
        isFunction: function (obj) {
            return {}.toString.apply(obj) === '[object Function]'
        },
        isArray: function (obj) {
            return {}.toString.apply(obj) === '[object Array]'
        },
        isObject: function (obj) {
            return {}.toString.apply(obj) === '[object Object]'
        },
        isDate: function (obj) {
            return {}.toString.apply(obj) === '[object Date]'
        },
        isValue: function (obj) {
            return !this.isObject(obj) && !this.isArray(obj)
        }
    }
}())

export default function Audit (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''

    const audits = props.audits.map((array, i) => {
        const previous = props.audits[i === 0 ? props.audits.length - 1 : i - 1]
        const diff = deepDiffMapper.map(array.data, previous.data)
        const type = array.notification.type.replace(`App\\Listeners\\${props.entity}\\`, '')
        const time_ago = moment(array.created_at).fromNow()

        return <ListGroupItem key={i} className={`${listClass} list-group-item-action flex-column align-items-start`}>
            <h5 className="mb-1">{type} - {array.notification.author}</h5>
            <p><small>{<FormatDate with_time={true} date={array.created_at}/>} . {time_ago}</small></p>
            {/* <p className="mb-1">{JSON.stringify(diff)}</p> */}
            {/* <small>Donec id elit non mi porta.</small> */}
        </ListGroupItem>
    })

    return (
        <Card className="border-0">
            <CardBody>
                <ListGroup>
                    {audits}
                </ListGroup>
            </CardBody>
        </Card>
    )
}
