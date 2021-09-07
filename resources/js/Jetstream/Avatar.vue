<template>
    <div class="rounded-full">
        <img
            v-if="src"
            class="object-cover w-full h-full rounded-full"
            :src="src"
            :alt="alt"
        />
        <div
            v-else
            class="flex items-center justify-center w-full h-full uppercase rounded-full bg-splashr-violet-light text-gray-50"
            :class="getAvatarBackgroundClass"
        >
            {{ profileInitials }}
        </div>
    </div>
</template>

<script>
export default {
    props: {
        src: {
            type: String,
            required: false,
            default: ''
        },
        alt: {
            type: String,
            required: false,
            default: ''
        },
        name: {
            type: String,
            required: false,
            default: ''
        }
    },

    computed: {
        profileInitials() {
            if (!this.name) {
                return ''
            }

            const nameArr = this.name.split(' ')

            if (nameArr.length > 1) {
                return `${nameArr[0][0]}${nameArr[1][0]}`
            }

            return `${nameArr[0][0]}`
        },

        getAvatarBackgroundClass() {
            if (!this.profileInitials) {
                return ''
            }

            const alphabets = 'abcdefghijklmnopqrstuvwxyz'

            let letterValue =
                alphabets.indexOf(this.profileInitials[0].toLowerCase()) + 1

            if (this.profileInitials.length > 1) {
                letterValue +=
                    alphabets.indexOf(this.profileInitials[1].toLowerCase()) + 1
            }

            return `avatar-init-${letterValue}`
        }
    }
}
</script>

<style>
.avatar-init-2 {
    background: #91091e;
}

.avatar-init-3 {
    background: #99bbad;
}

.avatar-init-4 {
    background: #9a8194;
}

.avatar-init-5 {
    background: #c6a9a3;
}

.avatar-init-6 {
    background: #161d6f;
}

.avatar-init-7 {
    background: #da723c;
}

.avatar-init-8 {
    background: #845ec2;
}

.avatar-init-9 {
    background: #493323;
}

.avatar-init-10 {
    background: #5eaaa8;
}

.avatar-init-11 {
    background: #295939;
}

.avatar-init-12 {
    background: #ff577f;
}

.avatar-init-13 {
    background: #663f3f;
}

.avatar-init-14 {
    background: #16c79a;
}

.avatar-init-15 {
    background: #8b5e83;
}

.avatar-init-16 {
    background: #48426d;
}

.avatar-init-17 {
    background: #184d47;
}

.avatar-init-18 {
    background: #1a508b;
}

.avatar-init-19 {
    color: #184d47;
    background: #96bb7c;
}

.avatar-init-20 {
    background: #51c2d5;
}

.avatar-init-21 {
    background: #9dad7f;
}

.avatar-init-22 {
    background: #91684a;
}

.avatar-init-23 {
    background: #00af91;
}

.avatar-init-24 {
    background: #314e52;
}

.avatar-init-25 {
    background: #62959c;
}

.avatar-init-26 {
    background: #ff9292;
}

.avatar-init-27 {
    background: #555e7b;
}

.avatar-init-28 {
    background: #83af9b;
}

.avatar-init-29 {
    background: #fe4846;
}

.avatar-init-30 {
    color: #00000080;
    background: #a4ebf3;
}

.avatar-init-31 {
    color: #00000080;
    background: #f5d782;
}

.avatar-init-32 {
    color: #00000080;
    background: #e97878;
}

.avatar-init-33 {
    color: #493323;
    background: #f8dc81;
}

.avatar-init-34 {
    background: #ec4646;
}

.avatar-init-35 {
    color: #00000080;
    background: #f58634;
}

.avatar-init-36 {
    color: #00000080;
    background: #ffc75f;
}

.avatar-init-37 {
    color: #00000080;
    background: #94b5c0;
}

.avatar-init-38 {
    color: #00000080;
    background: #ffe227;
}

.avatar-init-39 {
    color: #00000080;
    background: #ff884b;
}

.avatar-init-40 {
    color: #00000080;
    background: #f88f01;
}

.avatar-init-41 {
    color: #00000080;
    background: #b7d968;
}

.avatar-init-42 {
    background: #e04644;
}

.avatar-init-43 {
    color: #00000080;
    background: #fde47f;
}

.avatar-init-44 {
    color: #00000080;
    background: #7ccce5;
}

.avatar-init-45 {
    color: #00000080;
    background: #fe4365;
}

.avatar-init-46 {
    color: #00000080;
    background: #fc9d9a;
}

.avatar-init-47 {
    color: #00000080;
    background: #f9cdad;
}

.avatar-init-48 {
    color: #00000080;
    background: #c8c8a9;
}

.avatar-init-49 {
    color: #00000080;
    background: #fda47b;
}

.avatar-init-50 {
    color: #00000080;
    background: #fbd094;
}

.avatar-init-51 {
    color: #00000080;
    background: #d9cc8e;
}

.avatar-init-52 {
    color: #00000080;
    background: #a9b57c;
}

.avatar-init-53 {
    color: #493323;
    background: #faf3e0;
}

.avatar-init-54 {
    color: #493323;
    background: #eb5e0b;
}

.avatar-init-55 {
    color: #493323;
    background: #d89216;
}

.avatar-init-56 {
    color: #493323;
    background: #e27802;
}
</style>
