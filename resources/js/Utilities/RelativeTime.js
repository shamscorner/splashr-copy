import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'

dayjs.extend(relativeTime)

export function diffForHumansTime(time) {
    return dayjs(time).fromNow()
}

export function simpleDate(time) {
    return dayjs(time).format('DD/MM/YYYY')
}
