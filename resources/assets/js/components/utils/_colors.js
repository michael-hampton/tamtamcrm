function rgbToYIQ ({ r, g, b }) {
    return ((r * 299) + (g * 587) + (b * 114)) / 1000
}

function hexToRgb (hex) {
    if (!hex || hex === undefined || hex === '') {
        return undefined
    }

    const result =
        /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex)

    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : undefined
}

export function contrast (colorHex, threshold = 128) {
    if (colorHex === undefined) {
        return '#000'
    }

    const rgb = hexToRgb(colorHex)

    if (rgb === undefined) {
        return '#000'
    }

    return rgbToYIQ(rgb) >= threshold ? '#000' : '#fff'
}
