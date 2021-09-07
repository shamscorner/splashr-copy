export default {
    methods: {
        isPriceAvailable(commitment, videoSessionType) {
            if (videoSessionType === 'motion') {
                if (
                    commitment.motion_quantity_master &&
                    commitment.motion_quantity_rich_content
                ) {
                    return true
                }
                if (
                    commitment.motion_quantity_iteration &&
                    commitment.motion_quantity_rich_content
                ) {
                    return true
                }
            } else if (videoSessionType === 'acting') {
                if (
                    commitment.acting_quantity_master &&
                    commitment.acting_quantity_rich_content
                ) {
                    return true
                }
                if (
                    commitment.acting_quantity_iteration &&
                    commitment.acting_quantity_rich_content
                ) {
                    return true
                }
            }

            return false
        }
    }
}
