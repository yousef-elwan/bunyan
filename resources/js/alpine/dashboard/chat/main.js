import Alpine from 'alpinejs';
import { getRoute, translate } from '../../utils/helpers';
import axios from 'axios';
import Swal from 'sweetalert2';

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function chatComponent() {
    return {
        // --- STATE ---
        currentView: 'chats',      // 'chats' or 'topics' - Controls what's inside the sidebar
        mobileView: 'chats',       // 'chats', 'topics', or 'messages' - Controls the visible panel on mobile
        chats: [],
        chatsPagination: { currentPage: 0, lastPage: 1, isLoadingMore: false, perPage: 30 },
        userId: null,
        currentChat: null,
        topics: [],
        currentTopic: null,
        messages: [],
        messagesPagination: { currentPage: 0, lastPage: 1, isLoadingMore: false, perPage: 50 },
        newMessage: '',
        isLoadingMessages: false,
        isSendingMessage: false,
        showScrollToBottomButton: false,
        isSelectionMode: false,
        selectedMessages: new Set(),
        echoInstance: null,
        isTopicClosed: false,
        searchQuery: '',

        // --- INITIALIZATION ---
        async init() {
            this.userId = parseInt(window.AppConfig.user.id, 10);

            window.addEventListener('popstate', () => this.handleHashChange());
            window.addEventListener('resize', () => this.handleResize());

            this.setupEcho();
            await this.handleHashChange();
            this.handleResize();

            this.$watch('searchQuery', debounce(() => this.searchConversations(), 300));
            this.$watch('newMessage', () => {
                this.$nextTick(() => {
                    const textarea = this.$refs.messageInput;
                    if (textarea) {
                        textarea.style.height = 'auto';
                        textarea.style.height = `${Math.min(textarea.scrollHeight, 128)}px`;
                    }
                });
            });
        },

        // --- MOBILE & VIEW HELPERS ---
        isMobile() {
            return window.innerWidth < 768;
        },
        handleResize() {
            if (this.isMobile()) {
                if (this.currentTopic) {
                    this.mobileView = 'messages';
                } else if (this.currentChat) {
                    this.mobileView = 'topics';
                } else {
                    this.mobileView = 'chats';
                }
            } else {
                // On desktop, reset mobile view state, CSS handles the rest
                this.mobileView = '';
            }
        },

        // --- NAVIGATION LOGIC ---
        switchToChatsView() {
            this.currentView = 'chats';
            this.currentChat = null;
            this.currentTopic = null;
            this.mobileView = 'chats';
            if (this.chats.length === 0 && !this.chatsPagination.isLoadingMore) {
                this.loadMoreChats();
            }
            this.updateURLHash(null, null);
        },
        async switchToTopicsView(chat) {
            if (!chat) chat = this.currentChat;
            if (!chat) {
                this.switchToChatsView();
                return;
            }

            if (this.currentChat?.id === chat.id && this.currentView === 'topics' && this.mobileView === 'topics') return;

            this.currentChat = chat;
            this.currentView = 'topics';
            this.currentTopic = null; // Important to clear the selected topic
            this.mobileView = 'topics';
            await this.loadTopics(chat.id);
            this.updateURLHash(chat.id, null);
        },
        async loadTopic(topic) {
            if (this.isLoadingMessages || this.currentTopic?.id === topic.id) return;

            this.isLoadingMessages = true;
            this.currentTopic = topic;
            this.isTopicClosed = topic.is_closed;
            this.isSelectionMode = false;
            this.selectedMessages.clear();
            this.messages = [];
            this.messagesPagination = { currentPage: 0, lastPage: 1, isLoadingMore: false, perPage: 50 };

            await this.loadMoreMessagesForCurrentTopic();
            if (topic.unread_messages_count > 0) this.markAsReadOptimistically(topic);

            this.leaveCurrentTopicChannel();
            this.listenToTopicChannel();
            this.updateURLHash(this.currentChat.id, topic.id);
            this.mobileView = 'messages';

            this.isLoadingMessages = false;
        },

        // --- URL HASHING & DEEP-LINKING ---
        async handleHashChange() {
            const params = new URLSearchParams(window.location.hash.substring(1));
            const chatId = parseInt(params.get('chat'));
            const topicId = parseInt(params.get('topic'));

            if (!chatId) {
                this.switchToChatsView();
                return;
            }

            if (this.chats.length === 0) await this.loadMoreChats();
            const chat = this.chats.find(c => c.id === chatId);

            if (chat) {
                await this.loadTopics(chat.id);
                this.currentChat = chat;
                this.currentView = 'topics';

                if (topicId) {
                    const topic = this.topics.find(t => t.id === topicId);
                    if (topic) {
                        this.currentTopic = topic;
                        await this.loadMoreMessagesForCurrentTopic();
                        if (topic.unread_messages_count > 0) this.markAsReadOptimistically(topic);
                        this.leaveCurrentTopicChannel();
                        this.listenToTopicChannel();
                    }
                }
            } else {
                this.switchToChatsView();
            }
            this.handleResize();
        },
        updateURLHash(chatId, topicId) {
            let hash = '';
            if (chatId) {
                hash = `chat=${chatId}`;
                if (topicId) {
                    hash += `&topic=${topicId}`;
                }
            }
            const newUrl = window.location.pathname + (hash ? '#' + hash : '');
            if (window.location.href !== newUrl) {
                // Use pushState for user actions, but for programmatic changes, replaceState might be better
                // to avoid bloating browser history. Let's stick to pushState for navigability.
                history.pushState({ chatId, topicId }, '', newUrl);
            }
        },

        // --- DATA FETCHING ---
        async loadMoreChats() {
            if (this.chatsPagination.isLoadingMore || this.chatsPagination.currentPage >= this.chatsPagination.lastPage) return;
            this.chatsPagination.isLoadingMore = true;
            try {
                const response = await axios.get(getRoute('api.chat.conversations.fetch'), {
                    params: {
                        page: this.chatsPagination.currentPage + 1,
                        perPage: this.chatsPagination.perPage,
                        query: this.searchQuery
                    }
                });
                this.chats.push(...response.data.data);
                this.chatsPagination.currentPage = response.data.pagination.current_page;
                this.chatsPagination.lastPage = response.data.pagination.last_page;
            } catch (error) {
                console.error('Failed to load chats:', error);
            }
            finally {
                this.chatsPagination.isLoadingMore = false;
            }
        },
        async searchConversations() {
            this.chats = [];
            this.chatsPagination = { currentPage: 0, lastPage: 1, isLoadingMore: false, perPage: 30 };
            await this.loadMoreChats();
        },
        async loadTopics(conversationId) {
            try {
                const response = await axios.get(getRoute('api.chat.topics.fetch', { conversation: conversationId }));
                this.topics = response.data.data;
            } catch (error) {
                console.error('Failed to load topics:', error);
                this.topics = [];
            }
        },
        async loadMoreMessagesForCurrentTopic() {
            if (!this.currentTopic || this.messagesPagination.isLoadingMore || this.messagesPagination.currentPage >= this.messagesPagination.lastPage) return;
            this.messagesPagination.isLoadingMore = true;
            const oldScrollHeight = this.$refs.messagesArea?.scrollHeight || 0;
            try {
                const response = await axios.get(getRoute('api.chat.messages.show', { topic: this.currentTopic.id }), {
                    params: {
                        page: this.messagesPagination.currentPage + 1,
                        perPage: this.messagesPagination.perPage
                    }
                });
                const newMessages = response.data.data.messages.reverse();
                this.messages.unshift(...newMessages);
                this.messagesPagination.currentPage = response.data.pagination.current_page;
                this.messagesPagination.lastPage = response.data.pagination.last_page;
                this.$nextTick(() => {
                    if (this.messagesPagination.currentPage === 1) {
                        this.scrollToBottom(true);
                    } else {
                        this.$refs.messagesArea.scrollTop = this.$refs.messagesArea.scrollHeight - oldScrollHeight;
                    }
                });
            } catch (error) {
                console.error('Failed to load messages:', error);
            }
            finally {
                this.messagesPagination.isLoadingMore = false;
            }
        },

        // --- REAL-TIME & ACTIONS ---
        setupEcho() {
            this.echoInstance = window.Echo;
            if (!this.echoInstance) {
                console.error("Laravel Echo not found. Real-time features will be disabled.");
                return;
            }

            this.echoInstance.private(`App.Models.User.${this.userId}`)
                .listen('.message.sent', (e) => this.handleNewMessage(e))
                .listen('.topic.closed', (e) => this.handleTopicStatusChange(e, true))
                .listen('.topic.reopened', (e) => this.handleTopicStatusChange(e, false))
                .listen('topic.created', (e) => {
                    // This assumes a user-specific event, or a general event we filter
                    this.handleNewTopic(e.topic);
                });
        },
        handleNewTopic(topic) {
            // Is the user currently viewing the parent conversation of the new topic?
            if (this.currentChat?.id === topic.conversation_id) {
                // Add the new topic to the list if it's not already there
                if (!this.topics.some(t => t.id === topic.id)) {
                    this.topics.unshift(topic);
                }
            }
        },

        listenToTopicChannel() {
            this.leaveCurrentTopicChannel();
            if (this.currentTopic?.id) {
                this.echoInstance.private(`topic.${this.currentTopic.id}`)
                    .listen('.message.sent', (e) => this.handleNewMessage(e, true))
                    .listen('.messages.read', (e) => {
                        if (e.reader_id !== this.userId) this.handleMessagesReadByOtherUser();
                    })
                    .listen('.messages.deleted', (e) => {
                        if (e.deleter_id !== this.userId) this.handleMessagesDeletedByOtherUser(e.message_ids);
                    });
            }
        },
        leaveCurrentTopicChannel() {
            if (this.currentTopic?.id) {
                this.echoInstance.leave(`topic.${this.currentTopic.id}`);
            }
        },
        // handleNewMessage(event, inCurrentTopic = false) {
        //     const isAtBottom = this.$refs.messagesArea ? (this.$refs.messagesArea.scrollHeight - this.$refs.messagesArea.scrollTop - this.$refs.messagesArea.clientHeight < 100) : false;

        //     if (inCurrentTopic && !this.messages.some(m => m.id === event.id || (m.temp_id && m.temp_id === event.temp_id))) {
        //         this.messages.push(event);
        //         if (event.user_id !== this.userId) {
        //             this.markAsReadOptimistically(this.currentTopic);
        //         }
        //         if (isAtBottom) {
        //             this.$nextTick(() => this.scrollToBottom(true));
        //         }
        //     }

        //     const chat = this.chats.find(c => c.id === event.conversation_id);
        //     if (chat) {
        //         chat.last_message = event;
        //         if (!inCurrentTopic && event.user_id !== this.userId) {
        //             chat.unread_messages_count = (chat.unread_messages_count || 0) + 1;
        //         }
        //         this.chats.sort((a, b) => new Date(b.last_message?.created_at ?? 0) - new Date(a.last_message?.created_at ?? 0));
        //     }

        //     const topicInList = this.topics.find(t => t.id === event.topic_id);
        //     if (topicInList) {
        //         topicInList.last_message = event;
        //         if (!inCurrentTopic && event.user_id !== this.userId) {
        //             topicInList.unread_messages_count = (topicInList.unread_messages_count || 0) + 1;
        //         }
        //     }
        // },
        handleNewMessage(event) {
            // Are we currently viewing this topic?
            const inCurrentTopic = event.topic_id === this.currentTopic?.id;
            const isAtBottom = this.$refs.messagesArea ? (this.$refs.messagesArea.scrollHeight - this.$refs.messagesArea.scrollTop - this.messagesArea.clientHeight < 100) : false;

            if (inCurrentTopic) {
                if (!this.messages.some(m => m.id === event.id)) {
                    this.messages.push(event);
                    if (event.user_id !== this.userId) {
                        // Mark as read immediately because we are viewing it
                        this.markTopicAsRead(event.topic_id);
                    }
                    if (isAtBottom) this.$nextTick(() => this.scrollToBottom(true));
                }
            }

            // --- REAL-TIME COUNTER & LIST UPDATE ---
            const chat = this.chats.find(c => c.id === event.conversation_id);
            if (chat) {
                chat.last_message = event; // Update last message

                // If the user is not viewing this chat, increment unread count
                if (!inCurrentTopic && event.user_id !== this.userId) {
                    chat.unread_messages_count = (chat.unread_messages_count || 0) + 1;
                }

                // Move the chat to the top of the list
                this.chats.sort((a, b) => new Date(b.last_message?.created_at ?? 0) - new Date(a.last_message?.created_at ?? 0));
            }

            const topic = this.topics.find(t => t.id === event.topic_id);
            if (topic) {
                topic.last_message = event;
                if (!inCurrentTopic && event.user_id !== this.userId) {
                    topic.unread_messages_count = (topic.unread_messages_count || 0) + 1;
                }
            }
        },
        async createTopic() {
            const title = this.newTopicTitle.trim();
            if (!title || !this.currentChat) return;

            try {
                // The request to create the topic. The backend will broadcast the event.
                const response = await axios.post(
                    getRoute('api.chat.topics.store', { conversation: this.currentChat.id }),
                    { title: title }
                );

                // --- Optimistic Update for the CREATOR ---
                // The backend can return the newly created topic resource
                this.topics.unshift(response.data.data);
                this.newTopicTitle = '';

            } catch (error) {
                console.error('Failed to create topic:', error);
                Swal.fire('خطأ!', 'فشل إنشاء الموضوع.', 'error');
            }
        },
        handleMessagesReadByOtherUser() {
            this.messages.forEach(msg => {
                if (msg.user_id === this.userId) msg.status = 'read';
            });
        },
        handleMessagesDeletedByOtherUser(deletedIds) {
            this.messages = this.messages.filter(msg => !deletedIds.includes(msg.id));
        },
        handleTopicStatusChange(event, isClosed) {
            const topic = this.topics.find(t => t.id === event.topic_id);
            if (topic) topic.is_closed = isClosed;
            if (this.currentTopic?.id === event.topic_id) this.isTopicClosed = isClosed;
        },
        markAsReadOptimistically(topic) {
            if (!topic) return;
            axios.post(getRoute('api.chat.messages.read', { topic: topic.id })).catch(console.error);
            const chat = this.chats.find(c => c.id === this.currentChat?.id);
            if (chat && topic) {
                chat.unread_messages_count = Math.max(0, (chat.unread_messages_count || 0) - (topic.unread_messages_count || 0));
            }
            topic.unread_messages_count = 0;
        },
        async sendMessage() {
            const msgContent = this.newMessage.trim();
            if (!msgContent || this.isSendingMessage || this.isTopicClosed) return;
            this.isSendingMessage = true;

            const tempId = `temp_${Date.now()}`;
            const optimisticMessage = { id: tempId, message: msgContent, created_at: new Date().toISOString(), user: window.AppConfig.user, user_id: this.userId, status: 'sent', temp_id: tempId };

            this.messages.push(optimisticMessage);
            this.newMessage = '';
            this.$nextTick(() => { this.$refs.messageInput.style.height = 'auto'; this.scrollToBottom(); });

            try {
                const response = await axios.post(getRoute('api.chat.messages.store', { topic: this.currentTopic.id }), { message: msgContent });
                const msgIndex = this.messages.findIndex(m => m.id === tempId);
                if (msgIndex > -1) {
                    this.messages.splice(msgIndex, 1, { ...response.data.data, temp_id: tempId });
                }
            } catch (error) {
                console.error('Message send failed:', error);
                const msgIndex = this.messages.findIndex(m => m.id === tempId);
                if (msgIndex > -1) this.messages[msgIndex].status = 'failed';
            } finally { this.isSendingMessage = false; }
        },
        async closeTopic(topicId) {
            const result = await Swal.fire({ title: 'هل أنت متأكد؟', text: "سيتم إغلاق هذا الموضوع.", icon: 'warning', showCancelButton: true, confirmButtonText: 'نعم, قم بإغلاقه!', cancelButtonText: 'إلغاء', confirmButtonColor: '#d33', cancelButtonColor: '#3085d6' });
            // if (result.isConfirmed) {
            //     try {
            //         await axios.post(getRoute('api.chat.topics.close', { topic: topicId }));
            //         this.handleTopicStatusChange({ topic_id: topicId }, true);
            //         Swal.fire('تم!', 'تم إغلاق الموضوع.', 'success');
            //     } catch (error) { Swal.fire('خطأ!', 'فشل إغلاق الموضوع.', 'error'); }
            // }
            if (!topicId) return;

            Swal.fire({
                title: translate('close_topic_title'),
                text: translate('close_topic_text'),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: translate('close_topic_confirm_button'),
                cancelButtonText: translate('close_topic_cancel_button')
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        await axios.post(getRoute('api.chat.topics.close', { topic: topicId }));
                        this.handleTopicStatusChange({ topic_id: topicId }, true);
                        Swal.fire(
                            translate('close_topic_success_title'),
                            translate('close_topic_success_text'),
                            'success'
                        );
                    } catch (error) {
                        console.error('Failed to close topic:', error);
                        Swal.fire(
                            translate('close_topic_error_title'),
                            translate('close_topic_error_text'),
                            'error'
                        );
                    }
                }
            });
        },
        async reopenTopic(topicId) {
            try {
                await axios.post(getRoute('api.chat.topics.reopen', { topic: topicId }));
                this.handleTopicStatusChange({ topic_id: topicId }, false);
            } catch (error) {
                Swal.fire(translate('close_topic_error_title'), translate('reopen_topic_error_text'), 'error');
            }
        },
        // async deleteSelectedMessages() {
        //     const ids = Array.from(this.selectedMessages);
        //     if (ids.length === 0) return;
        //     const result = await Swal.fire({ title: 'هل أنت متأكد؟', text: `سيتم حذف ${ids.length} رسالة نهائياً!`, icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'نعم, قم بالحذف!', cancelButtonText: 'إلغاء' });
        //     if (result.isConfirmed) {
        //         try {
        //             await axios.delete(getRoute('api.chat.messages.bulk-delete'), { data: { message_ids: ids } });
        //             this.messages = this.messages.filter(m => !ids.includes(m.id));
        //             this.isSelectionMode = false; this.selectedMessages.clear();
        //             Swal.fire('تم الحذف!', 'تم حذف الرسائل.', 'success');
        //         } catch (error) { Swal.fire('خطأ!', 'فشل حذف الرسائل.', 'error'); }
        //     }
        // },
        async deleteSelectedMessages() {
            if (this.selectedMessages.size === 0) return;
            const messageIds = Array.from(this.selectedMessages);

            Swal.fire({
                title: translate('delete_messages_title'),
                // Here we use the replacement feature
                text: translate('delete_messages_text', { count: messageIds.length }),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: translate('delete_messages_confirm_button'),
                cancelButtonText: translate('delete_messages_cancel_button')
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        // ... (axios request)
                        this.messages = this.messages.filter(m => !messageIds.includes(m.id));
                        this.isSelectionMode = false;
                        this.selectedMessages.clear();
                        Swal.fire(
                            translate('delete_messages_success_title'),
                            translate('delete_messages_success_text'),
                            'success'
                        );
                    } catch (error) {
                        console.error('Failed to delete messages:', error);
                        Swal.fire(
                            translate('delete_messages_error_title'),
                            translate('delete_messages_error_text'),
                            'error'
                        );
                    }
                }
            });
        },

        // --- UI HELPERS & FORMATTERS ---
        handleChatsScroll() {
            const el = this.$refs.chatsList;
            if (el.scrollTop + el.clientHeight >= el.scrollHeight - 100) {
                this.loadMoreChats();
            }
        },
        handleMessagesScroll() {
            const el = this.$refs.messagesArea;
            if (!el) return;
            if (el.scrollTop < 100 && this.messagesPagination.currentPage < this.messagesPagination.lastPage) {
                this.loadMoreMessagesForCurrentTopic();
            }
            this.checkScrollPosition();
        },
        scrollToBottom(force = false) {
            this.$nextTick(() => {
                const el = this.$refs.messagesArea;
                if (el) el.scrollTo({ top: el.scrollHeight, behavior: force ? 'auto' : 'smooth' });
            });
        },
        checkScrollPosition() {
            this.$nextTick(() => {
                const el = this.$refs.messagesArea;
                if (!el) return;
                this.showScrollToBottomButton = el.scrollHeight - el.scrollTop - el.clientHeight > 300;
            });
        },
        handleMessageInteraction(message) {
            if (this.isSelectionMode) {
                this.toggleMessageSelection(message);
            }
        },
        handleMessageContextMenu(message) {
            if (message.user_id === this.userId) {
                event.preventDefault();
                this.isSelectionMode = true;
                this.selectedMessages.add(message.id);
            }
        },
        toggleMessageSelection(message) {
            if (this.selectedMessages.has(message.id)) {
                this.selectedMessages.delete(message.id);
            } else {
                this.selectedMessages.add(message.id);
            }
            if (this.selectedMessages.size === 0) {
                this.isSelectionMode = false;
            }
        },
        formatTime: (date) => date ? dayjs(date).format('h:mm A') : '',
        formatTimeAgo: (date) => date ? dayjs(date).fromNow() : '',
        formatDateSeparator(date) {
            const targetDate = dayjs(date).startOf('day');
            const today = dayjs().startOf('day');
            if (targetDate.isSame(today)) return 'Today';
            if (targetDate.isSame(today.subtract(1, 'day'))) return 'Yesterday';
            return targetDate.format('DD/MM/YYYY');
        },
        shouldShowDateSeparator(message, index) {
            if (index === 0) return true;
            const prevDate = dayjs(this.messages[index - 1].created_at).startOf('day');
            const currentDate = dayjs(message.created_at).startOf('day');
            return !currentDate.isSame(prevDate);
        },
        shouldShowMessageHeader(message, index) {
            if (index === 0) return true;
            if (this.shouldShowDateSeparator(message, index)) return true;
            const prevMessage = this.messages[index - 1];
            return prevMessage.user.id !== message.user.id;
        }
    };
}

Alpine.data('chat', chatComponent);