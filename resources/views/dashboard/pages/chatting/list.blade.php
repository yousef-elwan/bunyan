@extends('dashboard.layouts.default')

@push('css_or_js')
    {{-- Font Awesome & SweetAlert2 --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f0f2f5;
            --text-primary: #111b21;
            --text-secondary: #667781;
            --border-light: #e9edef;
            --sent-bubble: #dcf8c6;
            --received-bubble: #ffffff;
        }

        main {
            padding: 0px;
        }

        [x-cloak] {
            display: none !important;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        #messages-container {
            background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
            background-size: contain;
        }

        /* === START: MOBILE & DESKTOP LAYOUT LOGIC === */

        /* --- Default Desktop Layout (Flexbox) --- */
        #app-container {
            display: flex;
        }

        #chatSidebar {
            width: 340px;
            flex-shrink: 0;
            display: flex;
            /* Ensures chatSidebar is visible on desktop */
        }

        #main-chat-area {
            flex: 1;
            display: flex;
            /* Ensures chat area is visible on desktop */
        }

        /* --- Mobile Layout Overrides --- */
        @media (max-width: 767px) {
            #app-container {
                position: relative;
                overflow: hidden;
            }

            #chatSidebar,
            #main-chat-area {
                width: 100%;
                height: 100%;
                position: absolute;
                top: 0;
                transition: transform 0.3s ease-in-out;
            }

            /* Both panels are off-screen to the right (for RTL) by default */
            #chatSidebar,
            #main-chat-area {
                transform: translateX(100%);
            }

            /* The active panel is brought into view */
            #app-container.mobile-view-chats #chatSidebar,
            #app-container.mobile-view-topics #chatSidebar,
            #app-container.mobile-view-messages #main-chat-area {
                transform: translateX(0);
            }
        }

        /* === END: MOBILE & DESKTOP LAYOUT LOGIC === */
    </style>

    <script>
        window.AppConfig = window.AppConfig || {};
        window.AppConfig.user = @json(auth()->user());
        window.AppConfig.routes = {
            'api.chat.conversations.fetch': "{{ route('api.chat.conversations.fetch') }}",
            'api.chat.topics.fetch': "{{ route('api.chat.topics.fetch', ['conversation' => ':conversation']) }}",
            'api.chat.topics.store': "{{ route('api.chat.topics.store', ['conversation' => ':conversation']) }}",
            'api.chat.messages.show': "{{ route('api.chat.messages.show', ['topic' => ':topic']) }}",
            'api.chat.messages.store': "{{ route('api.chat.messages.store', ['topic' => ':topic']) }}",
            'api.chat.messages.read': "{{ route('api.chat.messages.read', ['topic' => ':topic']) }}",
            'api.chat.topics.close': "{{ route('api.chat.topics.close', ['topic' => ':topic']) }}",
            'api.chat.topics.reopen': "{{ route('api.chat.topics.reopen', ['topic' => ':topic']) }}",
            'api.chat.messages.bulk-delete': "{{ route('api.chat.messages.bulk-delete') }}"
        };
        Object.assign(window.AppConfig.i18n, @json(__('dashboard/chat') ?? []));
    </script>
    @vite(['resources/js/alpine/dashboard/chat/main.js'])
@endpush

@section('content')
    <div class="h-[calc(100vh-var(--header-height))] bg-gray-100" dir="rtl">

        <div id="app-container" x-data="chat()" x-cloak class="relative w-full h-full shadow-xl"
            :class="{
                'mobile-view-chats': mobileView === 'chats',
                'mobile-view-topics': mobileView === 'topics',
                'mobile-view-messages': mobileView === 'messages'
            }">

            <!-- Sidebar (Contains both Chats & Topics lists) -->
            <aside id="chatSidebar" class="flex flex-col bg-white border-l border-gray-200">
                <!-- Chats View -->
                <div x-show="currentView === 'chats'" class="flex flex-col h-full w-full">
                    <header class="p-3 border-b border-gray-200 flex justify-between items-center flex-shrink-0 bg-gray-50">
                        <h2 class="text-xl font-bold">{{ __('dashboard/chat.title') }}</h2>
                    </header>
                    <div class="p-2 bg-gray-50 flex-shrink-0 border-b border-gray-200">
                        <div class="relative"><input type="text" x-model.debounce.300ms="searchQuery"
                                placeholder="بحث..."
                                class="w-full p-2 pr-8 bg-white rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"><i
                                class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400"></i></div>
                    </div>
                    <div class="flex-1 overflow-y-auto custom-scrollbar" @scroll.debounce.300ms="handleChatsScroll()"
                        x-ref="chatsList">
                        <template x-for="chat in chats" :key="chat.id">
                            <article @click="switchToTopicsView(chat)"
                                class="p-3 cursor-pointer transition-colors hover:bg-gray-100 border-b border-gray-100">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div class="flex-1 overflow-hidden">
                                        <div class="flex justify-between items-center">
                                            <h4 class="font-bold truncate" x-text="chat.name"></h4><span
                                                class="text-xs text-gray-500 flex-shrink-0"
                                                x-text="chat.last_message ? formatTimeAgo(chat.last_message.created_at) : ''"></span>
                                        </div>
                                        <div class="flex justify-between items-center mt-1">
                                            <p class="text-sm text-gray-500 truncate" x-show="chat.last_message"><span
                                                    class="font-semibold text-blue-600"
                                                    x-show="chat.last_message?.topic?.title">#<span
                                                        x-text="chat.last_message.topic.title"></span>: </span><span
                                                    x-text="chat.last_message?.message"></span></p><span
                                                x-show="chat.unread_messages_count > 0" x-text="chat.unread_messages_count"
                                                class="bg-blue-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center flex-shrink-0"></span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </template>
                        <div x-show="chatsPagination.isLoadingMore" class="text-center py-4 text-gray-500">
                            {{ __('dashboard/chat.loading') }}...
                        </div>
                    </div>
                </div>

                <!-- Topics View -->
                <div x-show="currentView === 'topics'" class="flex flex-col h-full w-full">
                    <header
                        class="p-3 border-b border-gray-200 flex items-center justify-start flex-shrink-0 bg-gray-50 gap-2">
                        <button @click="switchToChatsView()" class="text-gray-600 hover:bg-gray-200 p-2 rounded-full"><i
                                class="fas fa-arrow-right"></i></button>
                        <div class="min-w-0">
                            <h2 class="text-lg font-bold truncate" x-text="currentChat?.name"></h2>
                        </div>
                    </header>
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <template x-for="topic in topics" :key="topic.id">
                            <article @click="loadTopic(topic)"
                                class="p-3 cursor-pointer transition-colors hover:bg-gray-100"
                                :class="{
                                    'bg-blue-100 hover:bg-blue-200': topic.id === currentTopic?.id,
                                    'opacity-60': topic
                                        .is_closed
                                }">
                                <div class="flex items-start space-x-4 space-x-reverse">
                                    <div class="flex-shrink-0">
                                        <div
                                            class="w-10 h-10 flex items-center justify-center bg-gray-200 rounded-full text-lg font-bold text-gray-500">
                                            #</div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex justify-between items-center mb-1">
                                            <h4 class="font-bold truncate" x-text="topic.title"></h4><span
                                                x-show="topic.unread_messages_count > 0"
                                                x-text="topic.unread_messages_count"
                                                class="bg-blue-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center flex-shrink-0 ml-2"></span>
                                        </div>
                                        <div class="flex justify-between items-center">
                                            <p class="text-sm text-gray-500 truncate" x-show="topic.last_message"><span
                                                    x-text="topic.last_message?.user?.name ? topic.last_message.user.name + ': ' : ''"></span><span
                                                    x-text="topic.last_message?.message"></span></p><span
                                                x-show="topic.is_closed" class="text-xs text-red-500 flex-shrink-0">
                                                {{ __('dashboard/chat.closed') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </template>
                    </div>
                </div>
            </aside>

            <!-- Main Chat Area -->
            <main id="main-chat-area" class="flex flex-col flex-1 bg-gray-200">
                <div x-show="currentTopic" class="h-full w-full flex flex-col" x-transition>
                    <header id="main-chat-header" x-show="!isSelectionMode"
                        class="p-3 bg-gray-50 flex items-center flex-shrink-0 border-b border-gray-200 gap-2">
                        <button @click="switchToTopicsView()"
                            class="text-gray-600 hover:bg-gray-200 p-2 rounded-full md:hidden"><i
                                class="fas fa-arrow-right"></i></button>
                        <h3 x-text="currentTopic?.title" class="text-sm font-bold"></h3>
                    </header>
                    <header id="selection-header" x-show="isSelectionMode"
                        class="p-3 bg-blue-500 text-white flex justify-between items-center flex-shrink-0">
                        <button @click="isSelectionMode = false; selectedMessages.clear()"
                            class="p-2 hover:bg-blue-600 rounded-full"><i class="fas fa-times"></i></button>
                        <span x-text="`${selectedMessages.size} محدد`" class="font-bold"></span>
                        <button @click="deleteSelectedMessages()" class="p-2 hover:bg-blue-600 rounded-full"><i
                                class="fas fa-trash"></i></button>
                    </header>
                    <div id="messages-container" x-ref="messagesArea" @scroll.debounce.150ms="handleMessagesScroll()"
                        class="flex-1 relative overflow-y-auto p-4 custom-scrollbar bg-gray-200">
                        <div x-show="messagesPagination.isLoadingMore" class="flex justify-center py-4"><i
                                class="fas fa-spinner fa-spin text-2xl text-gray-500"></i></div>
                        <template x-for="(message, index) in messages" :key="message.id">
                            <div>
                                <div x-show="shouldShowDateSeparator(message, index)" class="flex justify-center my-4">
                                    <div class="bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full text-xs text-gray-600 shadow-sm"
                                        x-text="formatDateSeparator(message.created_at)"></div>
                                </div>
                                <article class="message-group flex items-end gap-2.5 my-1"
                                    :class="{
                                        'flex-row-reverse': message.user.id === userId,
                                        'selected': selectedMessages.has(
                                            message.id)
                                    }"
                                    @contextmenu.prevent="handleMessageContextMenu(message)"
                                    @click="handleMessageInteraction(message)">
                                    <img x-show="shouldShowMessageHeader(message, index)" :src="message.user.image_url"
                                        :alt="message.user.name" class="w-8 h-8 rounded-full object-cover">
                                    <div x-show="!shouldShowMessageHeader(message, index)" class="w-8 flex-shrink-0">
                                    </div>
                                    <div class="message-bubble p-2 rounded-lg shadow-sm text-sm max-w-[70%]"
                                        :class="message.user.id === userId ? 'bg-green-100 rounded-br-none' :
                                            'bg-white rounded-bl-none'">
                                        <p x-html="message.message.replace(/\n/g, '<br>')" class="text-gray-800"></p>
                                        <div
                                            class="text-right text-xs text-gray-500 mt-1 flex items-center justify-end gap-1">
                                            <span x-text="formatTime(message.created_at)"></span>
                                            <div x-show="message.user.id === userId"><span
                                                    x-show="message.status === 'sent'"><i
                                                        class="fas fa-check"></i></span><span
                                                    x-show="message.status === 'read'" class="text-blue-500"><i
                                                        class="fas fa-check-double"></i></span><span
                                                    x-show="message.status === 'failed'"><i
                                                        class="fas fa-exclamation-circle text-red-500"></i></span></div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        </template>
                        <div x-show="!isLoadingMessages && messages.length === 0"
                            class="flex items-center justify-center h-full text-gray-500">
                            {{ __('dashboard/chat.closed') }}!</div>
                        <button x-show="showScrollToBottomButton" @click="scrollToBottom()"
                            class="sticky bottom-4 float-right z-10 w-10 h-10 rounded-full shadow-md flex justify-center items-center bg-white text-gray-600 hover:bg-gray-200"><i
                                class="fas fa-arrow-down"></i></button>
                    </div>
                    <div class="bg-gray-50 p-3 flex-shrink-0 border-t border-gray-200">
                        <form @submit.prevent="sendMessage()"
                            class="flex items-center bg-white rounded-lg px-2 border border-gray-300"
                            :class="{ 'opacity-50 cursor-not-allowed': isTopicClosed }">
                            <textarea x-ref="messageInput" x-model="newMessage" @keydown.enter.prevent.exact="sendMessage()"
                                :disabled="isTopicClosed || isSendingMessage" placeholder="{{ __('dashboard/chat.write_message') }}..."
                                class="flex-1 p-2 bg-transparent border-none focus:ring-0 resize-none h-12 max-h-32 disabled:bg-gray-100"></textarea>
                            <button type="submit" x-ref="sendBtn"
                                :disabled="isTopicClosed || isSendingMessage || newMessage.trim() === ''"
                                class="bg-blue-500 text-white w-10 h-10 flex items-center justify-center rounded-full hover:bg-blue-600 ml-2 flex-shrink-0 disabled:bg-blue-300"><i
                                    class="fas fa-paper-plane"></i></button>
                            <button type="button" x-show="currentTopic && !isTopicClosed"
                                @click="closeTopic(currentTopic.id)"
                                class="text-red-500 hover:text-red-700 p-2 ml-2 flex-shrink-0"
                                title="{{ __('dashboard/chat.close_topic_tooltip') }}"><i
                                    class="fas fa-lock"></i></button>
                            <button type="button" x-show="currentTopic && isTopicClosed"
                                @click="reopenTopic(currentTopic.id)"
                                class="text-green-500 hover:text-green-700 p-2 ml-2 flex-shrink-0"
                                title="{{ __('dashboard/chat.reopen_topic_tooltip') }}"><i class="fas fa-lock-open"></i>
                                {{ __('dashboard/chat.closed_topic') }}
                            </button>
                        </form>
                        <p x-show="isTopicClosed" class="text-red-500 text-center text-sm mt-1"></p>
                    </div>
                </div>
                <div x-show="!currentTopic" class="h-full w-full flex items-center justify-center">
                    <div class="text-center text-gray-500"><i class="fas fa-comments text-4xl mb-2"></i>
                        <p>
                            {{ __('dashboard/chat.select_conversation') }}
                        </p>
                    </div>
                </div>
            </main>

        </div>
    </div>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/relativeTime.js"></script>
    <script>
        dayjs.extend(window.dayjs_plugin_relativeTime);
    </script>
@endpush
