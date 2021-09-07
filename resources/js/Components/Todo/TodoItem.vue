<template>
    <div
        class="flex items-center p-6 space-x-6 text-gray-400 transition group hover:shadow-lg"
        :class="{ 'opacity-50 line-through': isCompleted }"
    >
        <input
            v-model="isCompleted"
            type="checkbox"
            class="w-6 h-6 hover:cursor-pointer"
            @change="
                $emit('toggle', {
                    isCompleted: isCompleted,
                    todo: todo
                })
            "
        />
        <div class="flex-1">
            <div
                v-if="!isInputEnable"
                class="text-xl font-semibold text-gray-500 group-hover:text-gray-800"
                @click="enableInput"
                title="Click to edit this task"
            >
                {{ todo.description }}
            </div>
            <input
                v-else
                v-model="inputBox"
                type="text"
                class="w-full p-0 text-xl font-semibold text-gray-500 border-none focus:outline-none focus:ring-0"
                ref="inputBox"
                @keyup.enter="updateTodo"
                @blur="updateTodo"
            />
            <div v-if="todo.order">{{ todo.order.video.name }}</div>
        </div>
        <button
            class="transition hover:text-red-400 focus:outline-none focus:ring-1"
            @click="$emit('delete', todo.id)"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                class="w-6 h-6"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                />
            </svg>
        </button>
    </div>
</template>

<script>
export default {
    props: {
        todo: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            isCompleted: this.todo.is_completed,
            inputBox: this.todo.description,
            isInputEnable: false
        }
    },

    methods: {
        enableInput() {
            this.isInputEnable = true
            this.$nextTick(() => this.$refs.inputBox.focus())
        },

        updateTodo() {
            this.isInputEnable = false

            this.$emit('update', {
                text: this.inputBox,
                todo: this.todo
            })
        }
    }
}
</script>
