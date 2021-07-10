export function buildPdf (data) {
    // decode base64 string, remove space for IE compatibility
    const binary = atob(data.data.replace(/\s/g, ''))
    const len = binary.length
    const buffer = new ArrayBuffer(len)
    const view = new Uint8Array(buffer)
    for (let i = 0; i < len; i++) {
        view[i] = binary.charCodeAt(i)
    }

    // create the blob object with content-type "application/pdf"
    const blob = new Blob([view], { type: 'application/pdf' })
    return URL.createObjectURL(blob)
}
