import React from "react";

export default function HtmlViewer(props) {
    let html = '<div style="background-color: #FFF">' + props.html + '</div>'

    html += '<style>.inline-block { display: inline-block; }.m-0 {\n' +
        '    margin: 0 !important\n' +
        '}\n' +
        '\n' +
        '.mt-0, .my-0 {\n' +
        '    margin-top: 0 !important\n' +
        '}\n' +
        '\n' +
        '.mr-0, .mx-0 {\n' +
        '    margin-right: 0 !important\n' +
        '}\n' +
        '\n' +
        '.mb-0, .my-0 {\n' +
        '    margin-bottom: 0 !important\n' +
        '}\n' +
        '\n' +
        '.ml-0, .mx-0 {\n' +
        '    margin-left: 0 !important\n' +
        '}\n' +
        '\n' +
        '.m-1 {\n' +
        '    margin: .35rem !important\n' +
        '}\n' +
        '\n' +
        '.mt-1, .my-1 {\n' +
        '    margin-top: .35rem !important\n' +
        '}\n' +
        '\n' +
        '.mr-1, .mx-1 {\n' +
        '    margin-right: .35rem !important\n' +
        '}\n' +
        '\n' +
        '.mb-1, .my-1 {\n' +
        '    margin-bottom: .35rem !important\n' +
        '}\n' +
        '\n' +
        '.ml-1, .mx-1 {\n' +
        '    margin-left: .35rem !important\n' +
        '}\n' +
        '\n' +
        '.m-2 {\n' +
        '    margin: .5rem !important\n' +
        '}\n' +
        '\n' +
        '.mt-2, .my-2 {\n' +
        '    margin-top: .10rem !important\n' +
        '}\n' +
        '\n' +
        '.mr-2, .mx-2 {\n' +
        '    margin-right: .10rem !important\n' +
        '}\n' +
        '\n' +
        '.mb-2, .my-2 {\n' +
        '    margin-bottom: .10rem !important\n' +
        '}\n' +
        '\n' +
        '.ml-2, .mx-2 {\n' +
        '    margin-left: .10rem !important\n' +
        '}\n' +
        '\n' +
        '.m-3 {\n' +
        '    margin: 2rem !important\n' +
        '}\n' +
        '\n' +
        '.mt-3, .my-3 {\n' +
        '    margin-top: 3rem !important\n' +
        '}\n' +
        '\n' +
        '.mr-3, .mx-3 {\n' +
        '    margin-right: 3rem !important\n' +
        '}\n' +
        '\n' +
        '.mb-3, .my-3 {\n' +
        '    margin-bottom: 3rem !important\n' +
        '}\n' +
        '\n' +
        '.ml-3, .mx-3 {\n' +
        '    margin-left: 3rem !important\n' +
        '}\n' +
        '\n' +
        '.m-4 {\n' +
        '    margin: 3.5rem !important\n' +
        '}\n' +
        '\n' +
        '.mt-4, .my-4 {\n' +
        '    margin-top: 3.5rem !important\n' +
        '}\n' +
        '\n' +
        '.mr-4, .mx-4 {\n' +
        '    margin-right: 3.5rem !important\n' +
        '}\n' +
        '\n' +
        '.mb-4, .my-4 {\n' +
        '    margin-bottom: 3.5rem !important\n' +
        '}\n' +
        '\n' +
        '.ml-4, .mx-4 {\n' +
        '    margin-left: 3.5rem !important\n' +
        '}\n' +
        '\n' +
        '.m-5 {\n' +
        '    margin: 6rem !important\n' +
        '}\n' +
        '\n' +
        '.mt-5, .my-5 {\n' +
        '    margin-top: 6rem !important\n' +
        '}\n' +
        '\n' +
        '.mr-5, .mx-5 {\n' +
        '    margin-right: 6rem !important\n' +
        '}\n' +
        '\n' +
        '.mb-5, .my-5 {\n' +
        '    margin-bottom: 3rem !important\n' +
        '}\n' +
        '\n' +
        '.ml-5, .mx-5 {\n' +
        '    margin-left: 3rem !important\n' +
        '}\n' +
        '\n' +
        '.p-0 {\n' +
        '    padding: 0 !important\n' +
        '}\n' +
        '\n' +
        '.pt-0, .py-0 {\n' +
        '    padding-top: 0 !important\n' +
        '}\n' +
        '\n' +
        '.pr-0, .px-0 {\n' +
        '    padding-right: 0 !important\n' +
        '}\n' +
        '\n' +
        '.pb-0, .py-0 {\n' +
        '    padding-bottom: 0 !important\n' +
        '}\n' +
        '\n' +
        '.pl-0, .px-0 {\n' +
        '    padding-left: 0 !important\n' +
        '}\n' +
        '\n' +
        '.p-1 {\n' +
        '    padding: .35rem !important\n' +
        '}\n' +
        '\n' +
        '.pt-1, .py-1 {\n' +
        '    padding-top: .35rem !important\n' +
        '}\n' +
        '\n' +
        '.pr-1, .px-1 {\n' +
        '    padding-right: .35rem !important\n' +
        '}\n' +
        '\n' +
        '.pb-1, .py-1 {\n' +
        '    padding-bottom: .35rem !important\n' +
        '}\n' +
        '\n' +
        '.pl-1, .px-1 {\n' +
        '    padding-left: .35rem !important\n' +
        '}\n' +
        '\n' +
        '.p-2 {\n' +
        '    padding: .10rem !important\n' +
        '}\n' +
        '\n' +
        '.pt-2, .py-2 {\n' +
        '    padding-top: .10rem !important\n' +
        '}\n' +
        '\n' +
        '.pr-2, .px-2 {\n' +
        '    padding-right: .10rem !important\n' +
        '}\n' +
        '\n' +
        '.pb-2, .py-2 {\n' +
        '    padding-bottom: .10rem !important\n' +
        '}\n' +
        '\n' +
        '.pl-2, .px-2 {\n' +
        '    padding-left: .5rem !important\n' +
        '}\n' +
        '\n' +
        '.p-3 {\n' +
        '    padding: 2rem !important\n' +
        '}\n' +
        '\n' +
        '.pt-3, .py-3 {\n' +
        '    padding-top: 2rem !important\n' +
        '}\n' +
        '\n' +
        '.pr-3, .px-3 {\n' +
        '    padding-right: 2rem !important\n' +
        '}\n' +
        '\n' +
        '.pb-3, .py-3 {\n' +
        '    padding-bottom: 2rem !important\n' +
        '}\n' +
        '\n' +
        '.pl-3, .px-3 {\n' +
        '    padding-left: 1rem !important\n' +
        '}\n' +
        '\n' +
        '.p-4 {\n' +
        '    padding: 1.5rem !important\n' +
        '}\n' +
        '\n' +
        '.pt-4, .py-4 {\n' +
        '    padding-top: 1.5rem !important\n' +
        '}\n' +
        '\n' +
        '.pr-4, .px-4 {\n' +
        '    padding-right: 1.5rem !important\n' +
        '}\n' +
        '\n' +
        '.pb-4, .py-4 {\n' +
        '    padding-bottom: 1.5rem !important\n' +
        '}\n' +
        '\n' +
        '.pl-4, .px-4 {\n' +
        '    padding-left: 1.5rem !important\n' +
        '}\n' +
        '\n' +
        '.p-5 {\n' +
        '    padding: 3rem !important\n' +
        '}\n' +
        '\n' +
        '.pt-5, .py-5 {\n' +
        '    padding-top: 3rem !important\n' +
        '}\n' +
        '\n' +
        '.pr-5, .px-5 {\n' +
        '    padding-right: 3rem !important\n' +
        '}\n' +
        '\n' +
        '.pb-5, .py-5 {\n' +
        '    padding-bottom: 3rem !important\n' +
        '}\n' +
        '\n' +
        '.pl-5, .px-5 {\n' +
        '    padding-left: 3rem !important\n' +
        '}\n' +
        '.w-100 {' +
        'width: 100%; }.btn-primary {\n' +
        '    color: #fff;\n' +
        '    background-color: #007bff;\n' +
        '    border-color: #007bff;\n' +
        '}\n' +
        '\n' +
        '.btn {\n' +
        '    display: inline-block;\n' +
        '    font-weight: 400;\n' +
        '    text-align: center;\n' +
        '    white-space: nowrap;\n' +
        '    vertical-align: middle;\n' +
        '    -webkit-user-select: none;\n' +
        '    -moz-user-select: none;\n' +
        '    -ms-user-select: none;\n' +
        '    user-select: none;\n' +
        '    border: 1px solid transparent;\n' +
        '    padding: .375rem .75rem;\n' +
        '    font-size: 1rem;\n' +
        '    line-height: 1.5;\n' +
        '    border-radius: .25rem;\n' +
        '    /*transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;*/\n' +
        '}.bg-secondary {\n' +
        '    background-color: #6c757d !important\n' +
        '}.bg-primary {\n' +
        '    background-color: #007bff !important\n' +
        '}.bg-info {\n' +
        '    background-color: #17a2b8 !important\n' +
        '}.bg-warning {\n' +
        '    background-color: #ffc107 !important\n' +
        '}.bg-danger {\n' +
        '    background-color: #dc3545 !important\n' +
        '}.bg-light {\n' +
        '    background-color: #f8f9fa !important\n' +
        '}.bg-dark {\n' +
        '    background-color: #343a40 !important\n' +
        '}.bg-white {\n' +
        '    background-color: #fff !important\n' +
        '}.text-left {\n' +
        '    text-align: left !important\n' +
        '}\n' +
        '\n' +
        '.text-right {\n' +
        '    text-align: right !important\n' +
        '}\n' +
        '\n' +
        '.text-center {\n' +
        '    text-align: center !important\n' +
        '}.border-primary {\n' +
        '    border-color: #007bff !important\n' +
        '}\n' +
        '\n' +
        '.border-secondary {\n' +
        '    border-color: #6c757d !important\n' +
        '}\n' +
        '\n' +
        '.border-success {\n' +
        '    border-color: #28a745 !important\n' +
        '}\n' +
        '\n' +
        '.border-info {\n' +
        '    border-color: #17a2b8 !important\n' +
        '}\n' +
        '\n' +
        '.border-warning {\n' +
        '    border-color: #ffc107 !important\n' +
        '}\n' +
        '\n' +
        '.border-danger {\n' +
        '    border-color: #dc3545 !important\n' +
        '}\n' +
        '\n' +
        '.border-light {\n' +
        '    border-color: #f8f9fa !important\n' +
        '}\n' +
        '\n' +
        '.border-dark {\n' +
        '    border-color: #343a40 !important\n' +
        '}\n' +
        '\n' +
        '.border-white {\n' +
        '    border-color: #fff !important\n' +
        '}\n.border-bottom {\n' +
        '    border-bottom: 1px solid #dee2e6 !important\n' +
        '}.border-top-4 {\n' +
        '    border-top: 4px solid;\n' +
        '}.text-danger {\n' +
        '    color: #dc3545 !important\n' +
        '}.text-warning {\n' +
        '    color: #ffc107 !important\n' +
        '}\n.text-info {\n' +
        '    color: #17a2b8 !important\n' +
        '}\n.text-success {\n' +
        '    color: #28a745 !important\n' +
        '}\n.text-secondary {\n' +
        '    color: #6c757d !important\n' +
        '}\n.text-primary {\n' +
        '    color: #007bff !important\n' +
        '}\n.text-white {\n' +
        '    color: #fff !important\n' +
        '}.border-dashed {\n' +
        '    border: dashed;\n' +
        '}.col-6 {\n' +
        '    width: 50%;\n' +
        '}</style>'


    const content = "data:text/html;charset=utf-8," + escape(html);

    return <div style={{minHeight: '600px'}} className="embed-responsive embed-responsive-21by9">
        <iframe style={{width: `${props.width}`, height: props.height || '400px'}}
                className="embed-responsive-item" id="viewer"
                src={content}/>
    </div>
}
