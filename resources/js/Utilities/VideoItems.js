export const videoItemStatus = {
    none: 0,
    in_progress: 1,
    needs_review: 2,
    approved: 3,
    paid: 4
}

export const videoItemType = {
    master: 0,
    iteration: 1,
    richContent: 2
}

export function getVideoItemTypeString(type) {
    switch (type) {
        case 0:
            return 'Master'
        case 1:
            return 'Iteration'
        case 2:
            return 'Rich Content'
    }
}
