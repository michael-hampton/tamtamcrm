import React from 'react'
import { Button, Card, CardBody, CardHeader, CustomInput, FormGroup, Input, Label } from 'reactstrap'
import { translations } from '../../utils/_translations'

export default class ImageForm extends React.Component {
    constructor (props) {
        super(props)
        this.state = {}
    }

    hasErrorFor (field) {
        return !!this.props.errors[field]
    }

    renderErrorFor (field) {
        if (this.hasErrorFor(field)) {
            return (
                <span className='invalid-feedback'>
                    <strong>{this.props.errors[field][0]}</strong>
                </span>
            )
        }
    }

    render () {
        return (<Card>
            <CardHeader>Images</CardHeader>
            <CardBody>

                <FormGroup>
                    {this.props.product && this.props.product.cover &&
                        <div className="col-md-3">
                            <div className="row">
                                <img src={`/storage/${this.props.product.cover}`} alt=""
                                    className="img-responsive img-thumbnail"/>
                            </div>
                        </div>
                    }

                </FormGroup>

                <FormGroup>
                    {
                        this.props.images && this.props.images.map((image, index) => {
                            return (<div key={index} className="col-md-3">
                                <div className="row">
                                    <img src={`/${image.src}`} alt=""
                                        className="img-responsive img-thumbnail"/>
                                    <br/> <br/>
                                    {this.props.deleteImage &&
                                        <Button data-src={image.src} color="danger"
                                            onClick={this.props.deleteImage}>Remove</Button>
                                    }
                                    <br/>
                                </div>
                            </div>)
                        })
                    }

                </FormGroup>

                <FormGroup>
                    <Label>{translations.cover}</Label>
                    <CustomInput onChange={this.props.handleFileChange} type="file" id="cover"
                        name="cover"
                        label="Cover!"/>
                </FormGroup>

                <FormGroup>
                    <Label>{translations.thumbnails}</Label>
                    <Input onChange={this.props.onChangeHandler} multiple type="file" id="image"
                        name="image"
                        label="Thumbnail!"/>
                </FormGroup>
            </CardBody>
        </Card>
        )
    }
}
