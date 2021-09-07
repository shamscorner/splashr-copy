<template>
    <button
        class="flex items-center space-x-1 sm:space-x-2 group focus:outline-none focus:ring-1 focus:ring-offset-4"
    >
        <img src="/images/coin.png" width="25" height="25" alt="Coin" />
        <p class="text-sm sm:text-base">23 credits</p>
        <button-plus
            class="text-white bg-splashr-violet-light group-hover:bg-splashr-violet-deep"
        ></button-plus>
    </button>
</template>

<script>
import ButtonPlus from '@/Components/ButtonPlus'

export default {
    components: {
        ButtonPlus
    }
}
</script>
