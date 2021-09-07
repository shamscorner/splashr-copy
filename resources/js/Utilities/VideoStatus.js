export const videoStatus = {
    doc: 3,
    video: 4,
    slide: 5,
    finished: 6
}

export const videoType = {
    slide: 1,
    doc: 2,
    video: 3
}

export const videoSessionType = {
    motion: 'motion',
    acting: 'acting'
}

export function getVideoStatusLabel(status) {
    switch (status) {
        case 2:
            return 'Brief'
        case 3:
            return 'Creative Ideas'
        case 4:
            return 'Video'
        case 5:
            return 'Storyboard'
        default:
            return ''
    }
}
