/* eslint-disable no-unused-vars */
import React from 'react'
import ConfirmPassword from '../common/ConfirmPassword'

export default function FileUpload (props) {
    const file = props.file
    const arrImages = ['gif', 'png', 'jpg', 'jpeg']
    const extension = file.name.substr(file.name.lastIndexOf('.') + 1)
    const thumbnail = arrImages.includes(extension) ? file.file_path : 'https://source.unsplash.com/pWkk7iiCoDM/400x300'

    return (
        <div className="col-lg-5 col-md-6 col-8">
            <a href={file.file_path} className="d-block" download>
                <img className="img-fluid img-thumbnail" src={thumbnail}
                    alt=""/>
            </a>
            {file.name}<br/>
            {`${file.user.first_name} ${file.user.last_name}`}
            <br/>
            <ConfirmPassword id={file.id} callback={props.delete} url={`/api/uploads/${file.id}`}/>
        </div>
    )
}
