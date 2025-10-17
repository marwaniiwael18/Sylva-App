<!-- Social Feed Component for Reports -->
<div x-data="reportFeed({{ $reportId }})" x-init="init()" class="mt-6 bg-white rounded-lg shadow-md p-6">
    <!-- Stats Bar -->
    <div class="flex items-center justify-between border-b pb-4 mb-4">
        <div class="flex items-center space-x-6">
            <!-- Vote Score -->
            <div class="flex items-center space-x-2">
                <button @click="vote(1)" :class="userVote === 1 ? 'text-green-600' : 'text-gray-400'" class="hover:text-green-600 transition">
                    <i data-lucide="arrow-up" class="w-5 h-5"></i>
                </button>
                <span class="font-semibold text-lg" x-text="stats.vote_score"></span>
                <button @click="vote(-1)" :class="userVote === -1 ? 'text-red-600' : 'text-gray-400'" class="hover:text-red-600 transition">
                    <i data-lucide="arrow-down" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Comments Button (Toggle) -->
            <button @click="showComments = !showComments" class="flex items-center space-x-2 text-gray-600 hover:text-green-600 transition">
                <i data-lucide="message-circle" class="w-5 h-5"></i>
                <span x-text="stats.total_comments"></span>
                <span class="text-sm" x-text="showComments ? 'Hide' : 'Comment'"></span>
            </button>

            <!-- Reactions -->
            <div class="flex items-center space-x-2">
                <button @click="react('like')" :class="userReaction === 'like' ? 'text-blue-600' : 'text-gray-400'" class="hover:text-blue-600 transition">
                    <i data-lucide="thumbs-up" class="w-5 h-5"></i>
                </button>
                <button @click="react('love')" :class="userReaction === 'love' ? 'text-red-600' : 'text-gray-400'" class="hover:text-red-600 transition">
                    <i data-lucide="heart" class="w-5 h-5"></i>
                </button>
                <button @click="react('support')" :class="userReaction === 'support' ? 'text-green-600' : 'text-gray-400'" class="hover:text-green-600 transition">
                    <i data-lucide="hand-heart" class="w-5 h-5"></i>
                </button>
                <button @click="react('concern')" :class="userReaction === 'concern' ? 'text-orange-600' : 'text-gray-400'" class="hover:text-orange-600 transition">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                </button>
                <span class="text-gray-600 text-sm ml-2" x-text="stats.total_reactions"></span>
            </div>
        </div>
    </div>

    <!-- Comments Section (Hidden by default) -->
    <div x-show="showComments" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
        <!-- Add Comment -->
        <div class="mb-6">
            <textarea 
                x-model="newComment" 
                @keydown.ctrl.enter="addComment"
                placeholder="Add a comment... (Ctrl+Enter to post)"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none"
                rows="3"
            ></textarea>
            <div class="flex justify-end mt-2">
                <button 
                    @click="addComment" 
                    :disabled="!newComment.trim()"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                >
                    Post Comment
                </button>
            </div>
        </div>

        <!-- Comments Feed -->
        <div class="space-y-4">
            <template x-for="activity in activities" :key="activity.id">
            <div class="border-l-2 pl-4 py-2" :class="activity.is_pinned ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200'">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2">
                            <span class="font-semibold text-gray-900" x-text="activity.user.name"></span>
                            <span class="text-xs text-gray-500" x-text="formatDate(activity.created_at)"></span>
                            <span x-show="activity.is_pinned" class="text-xs bg-yellow-200 text-yellow-800 px-2 py-0.5 rounded">Pinned</span>
                        </div>
                        
                        <!-- Comment Content -->
                        <template x-if="activity.activity_type === 'comment'">
                            <div>
                                <p class="text-gray-700 mt-1" x-text="activity.content"></p>
                                
                                <!-- Comment Actions -->
                                <div class="flex items-center space-x-4 mt-2 text-sm">
                                    <button @click="replyTo(activity)" class="text-gray-500 hover:text-green-600 transition">
                                        <i data-lucide="corner-down-right" class="w-4 h-4 inline mr-1"></i>
                                        Reply
                                    </button>
                                    <button @click="togglePin(activity)" class="text-gray-500 hover:text-yellow-600 transition">
                                        <i data-lucide="pin" class="w-4 h-4 inline mr-1"></i>
                                        <span x-text="activity.is_pinned ? 'Unpin' : 'Pin'"></span>
                                    </button>
                                    <button @click="deleteActivity(activity)" class="text-gray-500 hover:text-red-600 transition">
                                        <i data-lucide="trash-2" class="w-4 h-4 inline mr-1"></i>
                                        Delete
                                    </button>
                                </div>

                                <!-- Replies -->
                                <template x-if="activity.replies && activity.replies.length > 0">
                                    <div class="ml-6 mt-3 space-y-2">
                                        <template x-for="reply in activity.replies" :key="reply.id">
                                            <div class="border-l-2 border-green-200 pl-3 py-1">
                                                <div class="flex items-center space-x-2">
                                                    <span class="font-semibold text-sm text-gray-900" x-text="reply.user.name"></span>
                                                    <span class="text-xs text-gray-500" x-text="formatDate(reply.created_at)"></span>
                                                </div>
                                                <p class="text-gray-700 text-sm mt-1" x-text="reply.content"></p>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </template>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-4">
            <i data-lucide="loader" class="w-6 h-6 animate-spin inline-block text-gray-400"></i>
        </div>

        <!-- Empty State -->
        <div x-show="!loading && activities.length === 0" class="text-center py-8 text-gray-500">
            <i data-lucide="message-circle" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
            <p>No comments yet. Be the first to comment!</p>
        </div>
        </div>
    </div>
</div>

<script>
function reportFeed(reportId) {
    return {
        reportId: reportId,
        activities: [],
        stats: {
            vote_score: 0,
            total_comments: 0,
            total_reactions: 0,
            upvotes: 0,
            downvotes: 0,
            reaction_breakdown: {}
        },
        newComment: '',
        replyingTo: null,
        userVote: 0, // 1 for upvote, -1 for downvote, 0 for no vote
        userReaction: null, // 'like', 'love', 'support', 'concern', or null
        loading: false,
        showComments: false, // Hide comments by default

        init() {
            this.loadActivities();
            // Reinitialize lucide icons after Alpine renders
            this.$nextTick(() => lucide.createIcons());
        },

        async loadActivities() {
            this.loading = true;
            try {
                const response = await fetch(`/api/reports/${this.reportId}/activities`);
                const data = await response.json();
                
                if (data.success) {
                    this.activities = data.data.activities;
                    this.stats = data.data.stats;
                    this.$nextTick(() => lucide.createIcons());
                }
            } catch (error) {
                console.error('Error loading activities:', error);
            } finally {
                this.loading = false;
            }
        },

        async addComment() {
            if (!this.newComment.trim()) return;

            try {
                const response = await fetch(`/api/reports/${this.reportId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        content: this.newComment,
                        parent_id: this.replyingTo ? this.replyingTo.id : null
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.newComment = '';
                    this.replyingTo = null;
                    await this.loadActivities();
                }
            } catch (error) {
                console.error('Error adding comment:', error);
                alert('Failed to add comment. Please try again.');
            }
        },

        async vote(value) {
            try {
                const response = await fetch(`/api/reports/${this.reportId}/vote`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ vote_value: value })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.stats = { ...this.stats, ...data.data };
                    this.userVote = this.userVote === value ? 0 : value;
                }
            } catch (error) {
                console.error('Error voting:', error);
            }
        },

        async react(type) {
            try {
                const response = await fetch(`/api/reports/${this.reportId}/react`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ reaction_type: type })
                });

                const data = await response.json();
                
                if (data.success) {
                    this.stats = { ...this.stats, ...data.data };
                    this.userReaction = this.userReaction === type ? null : type;
                }
            } catch (error) {
                console.error('Error reacting:', error);
            }
        },

        async togglePin(activity) {
            try {
                const response = await fetch(`/api/activities/${activity.id}/pin`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    await this.loadActivities();
                }
            } catch (error) {
                console.error('Error pinning comment:', error);
            }
        },

        async deleteActivity(activity) {
            if (!confirm('Are you sure you want to delete this?')) return;

            try {
                const response = await fetch(`/api/activities/${activity.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    await this.loadActivities();
                }
            } catch (error) {
                console.error('Error deleting activity:', error);
            }
        },

        replyTo(activity) {
            this.replyingTo = activity;
            this.newComment = `@${activity.user.name} `;
            document.querySelector('textarea').focus();
        },

        formatDate(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diff = Math.floor((now - date) / 1000); // seconds

            if (diff < 60) return 'just now';
            if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
            if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
            if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
            
            return date.toLocaleDateString();
        }
    }
}
</script>
