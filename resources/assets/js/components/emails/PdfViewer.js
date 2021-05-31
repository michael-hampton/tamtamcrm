import React from "react";

export default function PdfViewer(props) {
    return <div style={{minHeight: '600px'}} className="embed-responsive embed-responsive-21by9">
        <iframe style={{width: `${props.width}`, height: props.height || '400px'}}
                className="embed-responsive-item" id="viewer"
                src={props.pdf}/>
    </div>
}