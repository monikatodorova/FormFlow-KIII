<template>
    <div :class="{'page-wrapper submissions-wrapper': true, 'mobile-view': hasOpenSubmission}">
        <div class="submissions-list-view" id="submissions-list-view">

            <div class="submissions-list-wrapper">
                <h1 class="mb-4">Project Submissions</h1>

                <div class="submissions-list" ref="submissionsList">

                    <!-- Submissions -->
                    <transition-group name="list" tag="div" class="list-wrap">      
                        <SubmissionBox class="list-item" v-for="(submission) in filteredItems" :key="submission.hashId" :data="submission" @click="updateSubmissionStatus(submission.hashId, 'seen')"></SubmissionBox>
                    </transition-group>

                    <!-- Loading Effect -->
                    <div class="loading-submissions" ref="loadingSubmissions" v-if="!loaded || meta.next">
                        <SubmissionBox></SubmissionBox>
                        <SubmissionBox></SubmissionBox>
                        <SubmissionBox></SubmissionBox>
                    </div>

                    <!-- No Submissions Found -->
                    <div class="no-submissions py-4" v-if="loaded && filteredItems.length === 0">
                        <p>You will view your submissions here upon receiving at least one submission through any of your forms.</p>
                    </div>

                </div>

                <!-- Load more -->
                <div class="submissions-meta">
                    <p class="small text-center mr-md-auto mb-0">Showing {{ filteredItems.length }} out of {{ meta.totalSubmissions }} submissions</p>
                    <div class="load-more" v-if="meta.loading">
                        <span class="spinner-border"></span>
                    </div>
                </div>

            </div>

        </div>

        <div class="submission-details-view" id="submissions-details-view">

            <!-- Submission Details -->
            <router-view></router-view>

        </div>
    </div>
</template>

<script>
import repository from '@/repository/repository'
import SubmissionBox from '@/components/items/SubmissionBox'
import { useEventBus } from "@/EventBus";

import { useMainStore } from '@/store';

export default {
    name: 'SubmissionsView',
    components: {SubmissionBox},
    setup() {
        const store = useMainStore();
        return {
            store
        }
    },
    data() {
        return {
            items: [],
            formId: null,
            loaded: false,
            meta: {
                next: null,
                totalSubmissions: null,
                loading: false,
            }
        }
    },
    created() {
        this.loadSubmissions(true);
    },
    mounted() {
        useEventBus().emit("reloadProjects");
		useEventBus().emit('reloadCurrentProject');
        this.$refs.submissionsList.addEventListener("scroll", this.handleScroll);
    },
    beforeUnmount() {
        this.$refs.submissionsList.removeEventListener("scroll", this.handleScroll)
    },
    methods: {
        loadSubmissions(clear = false) {
            if(clear) this.items = [];
            this.loaded = false;
            if(!this.projectId) return;
            repository.get("/submissions" + (this.formId ? "?form=" + this.formId : ""))
                .then(response => {
                    this.items.push(...response.data.items);
                    this.meta.next = response.data.cursor;
                    this.meta.totalSubmissions = response.data.total;
                    this.loaded = true;
                })
                .catch(error => {
                    console.log(error);
                })
        },
        loadMore() {
            if(!this.projectId) return;
            if (this.meta.loading) return;
            this.meta.loading = true;
            repository.get("/submissions" + "?status=" + this.status + (this.formId ? "&form=" + this.formId : "") + (this.meta.next ? "&cursor=" + this.meta.next : ""))
                .then(response => {
                    this.meta.loading = false;
                    this.items.push(...response.data.items);
                    this.meta.next = response.data.cursor;
                    this.meta.totalSubmissions = response.data.total;
                })
        },
        updateSubmissionStatus(id, status) {
            this.items.filter(item => item.hashId === id)[0].status = status;
        },
        handleScroll() {
            let submissionsList = this.$refs.submissionsList;
            let loadingSubmissions = this.$refs.loadingSubmissions;
            if(this.meta.next && (submissionsList.scrollTop + submissionsList.clientHeight >= ((submissionsList.scrollHeight - loadingSubmissions.clientHeight) / 1.5))) {
                this.loadMore();
            }
        },
    },
    computed: {
        filteredItems() {
            if(!this.loaded) return [];
            return this.items;
        },
        projectId() {
            // return this.$store.getters.currentProject.hashId;
            return this.store.getCurrentProject.hashId;
        },
        hasOpenSubmission() {
            return !this.$route.path.endsWith("/submissions");
        },
    },
    watch: {
        items(val) {
            if (this.loaded && val.length <= 0 && this.meta.next) {
                this.loadMore();
            }
        },
        projectId: function (newValue) {
            if(newValue !== null) this.loadSubmissions(true);
        },
    }
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.submissions-wrapper {
    padding: 0;
    background: $white;
    display: flex;
    justify-content: stretch;

    @include smartphone {
        padding: 0 15px;

        .submissions-list-view {
            width: 100%;
            display: block;
        }

        .submission-details-view {
            width: 100%;
            display: none;
        }

        &.mobile-view {
            .submissions-list-view {
                display: none;
            }
            .submission-details-view {
                display: block;
            }
        }
    }

    .submissions-list-view {
        flex-grow: 1;
        width: 40%;

        @include tablet {
            width: 50%;
        }
    }

    .submission-details-view {
        flex-grow: 1;
        width: 60%;

        @include tablet {
            width: 50%;
        }
    }
}

.submissions-list-wrapper {
    padding: 1.5rem;
    height: 100svh;
    border-right: 1px solid $border-grey;

    @include tablet {
        height: calc(100svh - 56px);
    }

    @include smartphone {
        width: 100%;
        padding: 1.5rem 0 0 0;
        margin: 0;
        height: auto;
        border-right: none;
        overflow: visible;
    }

    .submissions-list {
        height: calc(100vh - 6rem - 3rem);
        width: calc(100% + 3rem);
        border-top: 1px solid $border-grey;
        border-bottom: 1px solid $border-grey;
        margin: 0 -1.5rem;
        padding: 0 1.5rem;
        overflow-x: hidden;

        @include tablet {
            height: calc(100vh - 6rem - 3rem - 3.5rem);
        }

        @include smartphone {
            width: calc(100% + 30px);
            padding: 0;
            margin: 0 -15px;
            height: auto;
        }

        &.form-submissions {
            max-height: inherit;
        }
    }

    .submissions-meta {
        width: calc(100% + 3rem);
        margin: 0 -1.5rem;
        padding: 1.5rem 1.5rem 0 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;

        @include smartphone {
            flex-direction: column;
            justify-content: center;
            text-align: center;
            width: calc(100% + 30px);
            padding: 15px 0 30px 0;
            margin: 0 -15px;
        }

        p {
            line-height: 1.5rem;
            height: 1.5rem;
        }

        .load-more {
            transition: 0.5s ease all;
            user-select: none;
            height: 1.5rem;

            span.toggler {
                cursor: pointer;
                font-weight: 600;
                color: $dark;
                font-size: 0.9rem;

                &:hover {
                    color: $primary;

                    &:after {
                        border-color: $primary;
                    }
                }

                &:after {
                    content: ' ';
                    display: inline-block;
                    vertical-align: middle;
                    width: 8px;
                    height: 8px;
                    margin-top: -5px;
                    margin-left: 5px;
                    border-bottom: 2px solid $dark;
                    border-right: 2px solid $dark;
                    transform-origin: center;
                    transform: rotate(45deg);
                }
            }

            span.spinner-border {
                width: 20px;
                height: 20px;
                border-width: 3px;
                border-color: $border-darker-grey;
                border-right-color: transparent;
            }

            @include smartphone {
                padding-top: 5px;
                padding-bottom: 15px;
            }
        }
    }
}
</style>