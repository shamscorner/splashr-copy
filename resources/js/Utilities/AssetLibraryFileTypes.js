export function getFileAcceptType(type) {
    switch (type) {
        case 'logos':
            return 'image/*, .ai, .eps'
        case 'graphics':
            return '.pdf'
        case 'fonts':
            return '.ttf, .otf'
        case 'sounds':
            return 'audio/*'
        case 'videos':
            return 'image/*, video/mp4, video/x-m4v, video/*'
        default:
            return 'image/*'
    }
}

export function getFileAcceptFormat(type) {
    switch (type) {
        case 'logos':
            return ['AI', 'SVG', 'PNG', 'JPG', 'JPEG', 'EPS']
        case 'graphics':
            return ['PDF']
        case 'fonts':
            return ['TTF', 'OTF']
        case 'sounds':
            return ['MP3', 'AAC', 'FLAC', 'WAV', 'WMA']
        case 'videos':
            return ['JPG', 'PNG', 'SVG', 'MPG4', 'MP4']
        default:
            return ['AI', 'SVG', 'PNG', 'JPG', 'JPEG']
    }
}

export function getAcceptTargetByFileType(mimeType) {
    if (mimeType.includes('image')) {
        return 'logos'
    }
    if (mimeType.includes('pdf')) {
        return 'graphics'
    }
    if (mimeType.includes('ttf') || mimeType.includes('otf')) {
        return 'fonts'
    }
    if (mimeType.includes('audio')) {
        return 'sounds'
    }
    if (mimeType.includes('video')) {
        return 'videos'
    }
}

export function getMimetypeByAcceptTarget(acceptTarget) {
    switch (acceptTarget) {
        case 'logos':
            return 'image/*'
        case 'graphics':
            return 'application/pdf, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.oasis.opendocument.presentation, application/vnd.oasis.opendocument.spreadsheet, application/vnd.oasis.opendocument.text, application/vnd.ms-powerpoint, application/vnd.openxmlformats-officedocument.presentationml.presentation, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        case 'fonts':
            return 'font/*, application/vnd.ms-fontobject'
        case 'sounds':
            return 'audio/*'
        case 'videos':
            return 'image/*, video/mp4, video/x-m4v, video/*'
        default:
            return ''
    }
}
