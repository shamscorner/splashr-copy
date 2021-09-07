<template>
    <div class="flex flex-col h-full">
        <div
            v-if="orderId"
            class="flex items-center w-full px-5 py-5 space-x-3 bg-white border-b"
        >
            <input
                v-model="inputBox.text"
                type="text"
                class="flex-1 text-lg rounded"
                :class="{
                    'opacity-50 pointer-events-none': inputBox.processing
                }"
                placeholder="Add a task"
                @keyup.enter="addTask"
            />
            <div class="text-center w-14">
                <button
                    class="p-1 text-sm text-gray-400 transition border-2 border-gray-400 rounded-full hover:text-gray-600 hover:border-gray-600 focus:outline-none focus:ring-1"
                    :class="{
                        'opacity-50 pointer-events-none': inputBox.processing
                    }"
                    @click="addTask"
                >
                    <plus-icon></plus-icon>
                </button>
            </div>
        </div>
        <div
            v-else
            class="flex items-center w-full px-4 py-5 bg-white border-b"
        >
            To add tasks, select any order.
        </div>
        <div
            v-if="isTasksLoading"
            class="flex-1 mt-4 text-center text-gray-600"
        >
            Loading . . .
        </div>
        <div v-else-if="tasks.data.length" class="flex-1 pb-5 overflow-y-auto">
            <div class="flex flex-col divide-y">
                <todo-item
                    v-for="task in inCompletedTasks"
                    :key="task.id"
                    :todo="task"
                    @toggle="toggleTodo"
                    @delete="deleteTodo"
                    @update="updateTodo"
                ></todo-item>
                <todo-item
                    v-for="task in completedTasks"
                    :key="task.id"
                    :todo="task"
                    @toggle="toggleTodo"
                    @delete="deleteTodo"
                    @update="updateTodo"
                ></todo-item>
            </div>
            <!-- load more button -->
            <button-text
                v-if="tasks.paginatorInfo.hasMorePages"
                class="block w-full my-8 text-center text-gray-500"
                :class="{
                    'text-gray-300 pointer-events-none': isLoadingMoreTasks
                }"
                @click.native="fetchTasks(true)"
            >
                {{ isLoadingMoreTasks ? 'Loading' : 'More Tasks' }}
            </button-text>
        </div>
        <div v-else class="flex-1 mt-4 text-center text-gray-600">
            No tasks found.
        </div>
    </div>
</template>

<script>
import TodoItem from '@/Components/Todo/TodoItem'
import PlusIcon from '@/Components/Icons/PlusIcon'
import GetTasksByOrder from '@/Graphql/Order/Queries/GetTasksByOrder.gql'
import GetTasksByUser from '@/Graphql/Order/Queries/GetTasksByUser.gql'
import CreateOrderTaskMutation from '@/Graphql/Order/Mutations/CreateOrderTask.gql'
import UpdateOrderTaskMutation from '@/Graphql/Order/Mutations/UpdateOrderTask.gql'
import DeleteOrderTaskMutation from '@/Graphql/Order/Mutations/DeleteOrderTask.gql'

export default {
    components: {
        TodoItem,
        PlusIcon,
        ButtonText: () =>
            import(
                /* webpackChunkName: 'ButtonText' */ '@/Components/ButtonText'
            )
    },

    props: {
        orderId: {
            type: String,
            required: false,
            default: null
        },
        tabKey: {
            type: Number,
            required: false,
            default: 1
        }
    },

    data() {
        return {
            isTasksLoading: false,
            isLoadingMoreTasks: false,
            inputBox: {
                text: '',
                processing: false
            },
            tasks: {
                data: [],
                paginatorInfo: {
                    currentPage: 1
                }
            }
        }
    },

    computed: {
        inCompletedTasks() {
            return this.tasks.data.filter(task => !task.is_completed)
        },
        completedTasks() {
            return this.tasks.data.filter(task => task.is_completed)
        }
    },

    watch: {
        tabKey: {
            immediate: true,
            handler() {
                // reset data
                this.tasks.data = []
                this.tasks.paginatorInfo.currentPage = 1

                this.fetchTasks(false)
            }
        }
    },

    methods: {
        async fetchTasks(isLoadMoreComments) {
            if (!isLoadMoreComments) {
                // show the main loading
                this.isTasksLoading = true
            } else {
                this.isLoadingMoreTasks = true
                this.tasks.paginatorInfo.currentPage += 1
            }

            let query = GetTasksByOrder
            let variables = {
                orderId: this.orderId,
                userId: this.$page.user.id,
                page: this.tasks.paginatorInfo.currentPage
            }
            if (!this.orderId) {
                // fetch tasks by user with order data
                query = GetTasksByUser
                variables = {
                    userId: this.$page.user.id,
                    page: this.tasks.paginatorInfo.currentPage
                }
            }

            try {
                const response = await this.$apollo.query({
                    // Query
                    query: query,
                    // Parameters
                    variables: variables,
                    fetchPolicy: 'network-only'
                })

                if (response.data.tasksByOrder) {
                    this.setTasksData(
                        response.data.tasksByOrder,
                        isLoadMoreComments
                    )
                } else if (response.data.tasksByUser) {
                    this.setTasksData(
                        response.data.tasksByUser,
                        isLoadMoreComments
                    )
                }
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.isTasksLoading = false
                this.isLoadingMoreTasks = false
            }
        },

        setTasksData(data, isLoadMoreComments) {
            if (isLoadMoreComments) {
                this.tasks.data = this.tasks.data.concat(data.data)
            } else {
                this.tasks.data = data.data
            }

            this.tasks.paginatorInfo = {
                currentPage: data.paginatorInfo.currentPage,
                hasMorePages: data.paginatorInfo.hasMorePages,
                lastPage: data.paginatorInfo.lastPage
            }
        },

        async addTask() {
            if (!this.inputBox.text) {
                return
            }

            try {
                this.inputBox.processing = true
                // submit
                const response = await this.$apollo.mutate({
                    // mutation
                    mutation: CreateOrderTaskMutation,
                    // parameters
                    variables: {
                        task: {
                            description: this.inputBox.text,
                            order_id: this.orderId,
                            user_id: this.$page.user.id
                        }
                    }
                })

                if (response.data.createTask) {
                    this.tasks.data.unshift(response.data.createTask)
                    // clear the input
                    this.inputBox.text = ''
                }
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            } finally {
                this.inputBox.processing = false
            }
        },

        async toggleTodo(payload) {
            this.tasks.data.find(task => {
                if (task.id === payload.todo.id) {
                    task.is_completed = payload.isCompleted
                }
            })

            // submit
            try {
                await this.$apollo.mutate({
                    mutation: UpdateOrderTaskMutation,
                    variables: {
                        id: payload.todo.id,
                        task: {
                            order_id: this.orderId
                                ? this.orderId
                                : payload.todo.order.id,
                            user_id: this.$page.user.id,
                            is_completed: payload.isCompleted
                        }
                    }
                })
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        },

        async deleteTodo(todoId) {
            this.tasks.data = this.tasks.data.filter(task => task.id !== todoId)

            // submit
            try {
                await this.$apollo.mutate({
                    mutation: DeleteOrderTaskMutation,
                    variables: {
                        id: todoId
                    }
                })
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        },

        async updateTodo(payload) {
            this.tasks.data.find(task => {
                if (task.id === payload.todo.id) {
                    task.description = payload.text
                }
            })

            // submit
            try {
                await this.$apollo.mutate({
                    mutation: UpdateOrderTaskMutation,
                    variables: {
                        id: payload.todo.id,
                        task: {
                            order_id: this.orderId
                                ? this.orderId
                                : payload.todo.order.id,
                            user_id: this.$page.user.id,
                            description: payload.text
                        }
                    }
                })
            } catch (error) {
                // Error
                console.error(error)
                alert('Oops!!! Something went wrong')
            }
        }
    }
}
</script>
