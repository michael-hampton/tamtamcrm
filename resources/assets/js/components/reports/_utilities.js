generateAndDownloadCSV(props, content) {
    const encoding = props.encoding ? props.encoding : 'UTF-8';
    const csvType = { encoding, type: `text/plain;charset=${encoding}` };
    const filename = props.filename ? props.filename : 'logResults.csv';
    
    let csvContent = '';
    const data = content;
    const headers = [];

    content.forEach((rowObj) => {
      if (headers === undefined || headers.length === 0) {
        for (const property in rowObj) {
          if (rowObj.hasOwnProperty(property)) {
            headers.push(property);
          }
        }
      } else {
        for (const property in rowObj) {
          if (rowObj.hasOwnProperty(property)) {
            if (headers.indexOf(property) == -1) {
              headers.push(property);
            }
          }
        }
      }

      const rowData = [];

      for (const i in headers) {
        let data = rowObj[headers[i]];
        if (data && typeof data === 'string' && data.indexOf(',') >= 0) {
          data = `"${data.replace(/"/g, '""')}"`;
        }

        rowData.push(data);

      }

      const row = rowData.join(',');
      csvContent += `${row}\r\n`;
    });

    const row = headers.join(',');
    csvContent = `${row}\r\n${csvContent}`;
  }

  renderDownload(props) {
      const buttonStyle = props.downloadButtonStyle ? props.downloadButtonStyle : {};
      
      return (
        <div className="csvFileDownloader">
          <button
            style={buttonStyle}
            download={props.csvFileName}
            onClick={props.export}
          >
            <i className="fa fa-download"
            style={{width: '30px'}} />
            {props.downloadName ? props.downloadName : 'Download Table Data'}
          </button>
        </div>
      );
  }
