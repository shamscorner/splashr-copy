import { diffForHumansTime } from '@/Utilities/RelativeTime.js'

export default {
    methods: {
        getLastUpdatedTime(insertedTime, updatedTime) {
            if (insertedTime == updatedTime) {
                return diffForHumansTime(insertedTime)
            }
            return diffForHumansTime(updatedTime) + ' (edited)'
        }
    }
}
