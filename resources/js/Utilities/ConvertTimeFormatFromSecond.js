export function getTimeFormat(time) {
    const seconds = Math.floor(time)
    const minutes = Math.floor(seconds / 60)
    const hours = Math.floor(minutes / 60)

    if (hours) {
        return `${hours
            .toString()
            .padStart(2, '0')}:${minutes
            .toString()
            .padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
    } else {
        return `${minutes
            .toString()
            .padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`
    }
}
