export function calculateStepHref(text) {
    return text
        .split(' ')
        .map(item =>
            item
                .trim()
                .toLowerCase()
                .replace(/\w\S*/g, w => w.replace(/^\w/, c => c.toUpperCase()))
        )
        .join('')
}
