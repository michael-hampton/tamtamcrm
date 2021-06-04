import React from 'react'
import { ListGroup, Row } from 'reactstrap'
import ViewEntityHeader from '../../common/entityContainers/ViewEntityHeader'
import { translations } from '../../utils/_translations'
import { icons } from '../../utils/_icons'
import FormatMoney from '../../common/FormatMoney'
import InfoItem from '../../common/entityContainers/InfoItem'
import FieldGrid from '../../common/entityContainers/FieldGrid'

export default function Overview (props) {
    const listClass = !Object.prototype.hasOwnProperty.call(localStorage, 'dark_theme') || (localStorage.getItem('dark_theme') && localStorage.getItem('dark_theme') === 'true') ? 'list-group-item-dark' : ''
    const fields = []

    if (props.entity.custom_value1.length) {
        const label1 = props.model.getCustomFieldLabel('Product', 'custom_value1')
        fields[label1] = props.model.formatCustomValue(
            'Product',
            'custom_value1',
            props.entity.custom_value1
        )
    }

    if (props.entity.custom_value2.length) {
        const label2 = props.model.getCustomFieldLabel('Product', 'custom_value2')
        fields[label2] = props.model.formatCustomValue(
            'Product',
            'custom_value2',
            props.entity.custom_value2
        )
    }

    if (props.entity.custom_value3.length) {
        const label3 = props.model.getCustomFieldLabel('Product', 'custom_value3')
        fields[label3] = props.model.formatCustomValue(
            'Product',
            'custom_value3',
            props.entity.custom_value3
        )
    }

    if (props.entity.custom_value4.length) {
        const label4 = props.model.getCustomFieldLabel('Product', 'custom_value4')
        fields[label4] = props.model.formatCustomValue(
            'Product',
            'custom_value4',
            props.entity.custom_value4
        )
    }

    return <React.Fragment>
        <ViewEntityHeader heading_1={translations.cost} value_1={props.entity.cost}
            heading_2={translations.price} value_2={props.entity.price}/>

        <Row>
            <ListGroup className="col-12">
                <InfoItem icon={icons.credit_card}
                    value={<FormatMoney amount={props.entity.price}/>}
                    title={translations.price}/>
                <InfoItem icon={icons.credit_card}
                    value={<FormatMoney amount={props.entity.cost}/>}
                    title={translations.cost}/>
                <InfoItem icon={icons.building} value={props.entity.name}
                    title={translations.name}/>
                <InfoItem icon={icons.building} value={props.entity.description}
                    title={translations.description}/>
                <InfoItem icon={icons.building} value={props.entity.sku}
                    title={translations.sku}/>
                <InfoItem icon={icons.list} value={props.entity.quantity}
                    title={translations.quantity}/>
            </ListGroup>
        </Row>

        <FieldGrid fields={fields}/>
    </React.Fragment>
}
