export default function formatDuration (duration, showSeconds = false) {
    // const time = duration.toString().split('.')[0]

    if (!duration) {
        return null
    }

    if (showSeconds) {
        return time
    } else {
        console.log('time', duration)
        const parts = duration.toString().split('.')

        console.log('parts', parts)

        return `${zeroPad(parts[0], 2)}:${zeroPad(parts[1], 2)}`
    }
}

export function zeroPad (num, places) {
    if (!num) {
        return 0
    }

    var zero = places - num.toString().length + 1
    return Array(+(zero > 0 && zero)).join('0') + num
}

export function formatPercentage (number) {
    return parseFloat(number).toFixed(2) + '%'
}

export function formatSecondsToTime (seconds) {
    return !seconds ? null : new Date(seconds * 1000).toISOString().substr(11, 8)
}

export function convertTimeToSeconds (time) {
    const a = time.split(':') // split it at the colons

    return (+a[0]) * 60 * 60 + (+a[1]) * 60 + (+a[2])
}
