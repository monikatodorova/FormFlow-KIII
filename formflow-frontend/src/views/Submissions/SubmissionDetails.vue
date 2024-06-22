<template>
    <div class="submission-details-page">

        <div class="error alert alert-danger" v-if="error">{{ error }}</div>

        <div class="loading-view" v-if="!loaded">
            <div class="spinner-border"></div>
        </div>

        <div class="row align-items-center" v-if="loaded">
            <div class="col-xl-6">
                <div :class="{'submission-avatar': true, 'text-dark': avatar.text === 'dark' }" :style="{'background': avatar.color }">
                    {{ avatar.credentials }}
                </div>
                <div class="submission-details">
                    <h1 class="submission-name">{{ name }}</h1>
                    <div class="submission-email">{{ email }}</div>
                </div>
            </div>
            <div class="col-xl-6 mt-4 mt-xl-0 text-xl-right d-none d-md-block" style="text-align: right;">
                <div class="submission-actions">
                    <a :href="'mailto:' + email" class="btn btn-primary">
                        <span>New Email</span>
                        <img src="@/assets/icons/forward.svg">
                    </a>
                </div>
            </div>
        </div>

        <div class="submission-spam-detected" v-if="spam">
            <strong>Spam Detected</strong>
            <span>The following submission appears to be spam. You may disregard it.</span>
        </div>

        <div class="submission-fields" v-if="loaded">
            <h3>
                <img src="@/assets/icons/forms.svg" alt="Fields">
                <span>Submission Details</span>
            </h3>
            <div class="fields-wrapper">
                <div class="field-item" v-for="(value, field) in fields" :key="field">
                    <label>{{ prepareFieldName(field) }}:</label>
                    <div class="form-control" contenteditable="true">
                        {{ fields[field] }}
                    </div>
                </div>
                <div class="field-item">
                    <label>Submitted:</label>
                    <div class="form-control" contenteditable="true">
                        {{ submissionCreatedAt }}
                    </div>
                </div>
                <div class="field-item">
                    <label>Form:</label>
                    <router-link :to="formLink" class="form-link">
                        <i class="project-color" :style="{'backgroundColor': form.color}"></i>
                        {{ form.name }}
                    </router-link>
                </div>
            </div>
            <div class="no-fields" v-if="fields === null || fields.length === 0 || Object.keys(fields).length === 0">
                This submission doesn't contain any fields.
            </div>
        </div>

        <div class="submission-fields" v-if="loaded">
            <h3>
                <img src="@/assets/icons/tags.svg" alt="Tags">
                <span>Submission Tags</span>
            </h3>
            <TagsWidget :tags="tags" :submission-id="id" :add-tag="addTag" :delete-tag="deleteTag" v-if="loaded"></TagsWidget>
        </div>
    </div>
</template>

<script>
import repository from "@/repository/repository";
import TagsWidget from "@/components/widgets/TagsWidget";

export default {
    name: "SubmissionDetails",
    components: {TagsWidget},
    data() {
        return {
            id: null,
            loaded: false,
            error: false,
            spam: false,
            name: "Loading...",
            email: "Loading...",
            created_at: null,
            updated_at: null,
            status: null,
            fields: {},
            tags: [],
            avatar: {
                credentials: null,
                color: null,
                text: null,
            },
            form: {
                hashId: null,
                name: "Loading...",
                color: null,
            },
        }
    },
    created() {
        this.loadSubmission(this.$route.params.id);
    },
    methods: {
        loadSubmission(submissionId) {
            if (submissionId === undefined) return;
            this.loaded = false;
            this.spam = false;
            repository.get("/submissions/" + submissionId)
                .then(response => {
                    let submission = response.data;
                    this.id = submission.hashId;
                    this.name = submission.name;
                    this.email = submission.email;
                    this.created_at = submission.created_at;
                    this.updated_at = submission.updated_at;
                    this.status = submission.status;
                    this.fields = submission.fields;
                    this.tags = submission.tags;
                    this.avatar.credentials = submission.avatar.credentials;
                    this.avatar.color = submission.avatar.color;
                    this.avatar.text = submission.avatar.text;
                    this.form.hashId = submission.form.hashId;
                    this.form.name = submission.form.name;
                    this.form.color = submission.form.color.color;
                    this.spam = submission.spam;
                    this.loaded = true;
                })
                .catch(error => {
                    console.log(error);
                    this.error = error;
                    // this.$router.replace("/submissions");
                });
        },
        prepareFieldName(field) {
            if (field === null) return "N\\A";
            return field.split("-").map(word => word.charAt(0).toUpperCase() + word.substring(1)).join(" ");
        },
        deleteSubmission() {
            let app = this;
            this.$confirm(
                {
                    title: "Remove " + this.name + " permanently?",
                    message: "This will remove " + this.name + " permanently from your project.",
                    button: {
                        no: "No, cancel",
                        yes: "Yes, continue",
                    },
                    callback: confirm => {
                        if (confirm) {
                            this.loaded = false;
                            repository.delete("/submissions/" + this.submissionId)
                                .then(() => {
                                    app.$router.back();
                                })
                                .catch(error => {
                                    app.error = error;
                                })
                        }
                    }
                }
            )
        },
        addTag(tag) {
            this.tags.push(tag);
        },
        deleteTag(tag) {
            let index = this.tags.findIndex(t => t.hashId === tag.hashId);
            this.tags.splice(index, 1);
        }
    },
    computed: {
        submissionId() {
            return this.$route.params.id;
        },
        formLink() {
            if (!this.loaded || this.form.hashId === null) return "";
            return "/forms/" + this.form.hashId;
        },
        submissionCreatedAt() {
            if (!this.loaded) return "Loading..";
            if (this.created_at === null) return "Error..";

            let today = new Date();
            let accessedDate = new Date(this.created_at);
            let difference = Math.abs(today - accessedDate) / 1000; // Difference in seconds
            let returnString = "";

            const secondsInMinute = 60;
            const secondsInHour = 3600;
            const secondsInDay = 86400;
            const secondsInMonth = 2629800; // Average seconds in a month

            const months = Math.floor(difference / secondsInMonth);
            difference %= secondsInMonth;

            const days = Math.floor(difference / secondsInDay);
            difference %= secondsInDay;

            const hours = Math.floor(difference / secondsInHour);
            difference %= secondsInHour;

            const minutes = Math.floor(difference / secondsInMinute);
            const seconds = Math.floor(difference % secondsInMinute);

            if (months === 1) returnString += months + " month";
            else if (months > 1) returnString += months + " months";
            else if (days === 1) returnString += days + " day";
            else if (days > 1) returnString += days + " days";
            else if (hours === 1) returnString += hours + " hour";
            else if (hours > 1) returnString += hours + " hours";
            else if (minutes === 1) returnString += minutes + " minute";
            else if (minutes > 1) returnString += minutes + " minutes";
            else if (seconds === 1) returnString += seconds + " second";
            else if (seconds > 1) returnString += seconds + " seconds";

            return returnString + " ago";
        },
    },
    watch: {
        "$route.params.id"(val) {
            this.loadSubmission(val);
        },
    },
}
</script>


<style lang="scss" scoped>
@import "src/scss/variables";

.submission-details-page {
	display: block;
	position: relative;
    height: 100svh;
    overflow-x: hidden;
    padding: 2.5rem;
	@extend .animated;

    @include small-desktop {
        padding: 1.5rem;
    }

    @include tablet {
        padding: 1.5rem;
        height: calc(100svh - 56px);
    }

	@include smartphone {
		z-index: 2;
        padding: 0.75rem 0 5rem 0;
        height: auto;
        overflow: visible;
	}

	.submission-avatar {
		width: 60px;
		height: 60px;
		color: $white;
		font-weight: 700;
		text-align: center;
		line-height: 60px;
		font-size: 1.25rem;
		border-radius: 50%;
		display: inline-block;
		vertical-align: middle;
	}

	.submission-details {
		display: inline-block;
		vertical-align: middle;
		width: calc(100% - 65px);
		padding-left: 15px;

		h1.submission-name {
			display: block;
			font-weight: 700;
			color: $dark;
			margin: 0;
			font-size: 1.25rem;
		}

		.submission-email {
			display: block;
			color: $grey-text;
			margin-top: 2px;
		}
	}

	.submission-actions {
		@include smartphone {
			display: flex;
			justify-content: stretch;
			width: calc(100% + 10px);
			margin: 15px -5px 0 -5px;
		}

		.btn {
			@include smartphone {
				margin: 0 5px;
				padding-left: 5px;
				padding-right: 5px;
				width: 50%;
                text-align: center;
                justify-content: center;
			}

			img {
				display: inline-block;
				vertical-align: middle;
				margin: -3px 0 0 5px;
				filter: brightness(0) invert(1);
				width: 16px;
			}

			&.btn-secondary {
				img {
					filter: brightness(0);
					margin-left: -5px;
					margin-top: -2px;
					margin-right: 5px;

					@extend .animated;
				}

				&:hover img {
					filter: none;
				}
			}
		}
	}

	.submission-fields {
		display: block;
		border-radius: $box-border-radius;
		background: $background-white;
		padding: 20px;
        border: 1px solid $border-grey;
		margin-top: 20px;

        @include smartphone {
            box-shadow: none;
            margin-top: 15px;
        }

		h3 {
			font-weight: 600;
			font-size: 1.15rem;
			color: $dark;
			margin: 0 0 1rem 0;
            display: flex;
            align-content: center;

            @include smartphone {
                margin-bottom: 0.5rem;
            }

            img {
                width: 1.25rem;
                height: 1.25rem;
                object-fit: contain;
                filter: grayscale(1);
                margin-right: 0.5rem;
            }
		}

		.field-item {
			display: flex;
			flex-direction: row;
			align-items: flex-start;
			padding: 5px 5px;
			width: calc(100% + 10px);
			margin: 0 -5px;
			border-radius: $box-border-radius;
			position: relative;
			@extend .animated;

            @include small-desktop {
                flex-direction: column;
            }

            @include tablet {
                flex-direction: column;
            }

            @include smartphone {
                flex-direction: column;
            }

			&:not(:last-child):after {
				position: absolute;
				bottom: 0;
				left: 0;
				width: 100%;
				height: 1px;
				content: ' ';
				background: $border-grey;
			}

			label {
				margin: 5px 0 0 0;
				width: 20%;
				text-align: right;
				padding-right: 5px;
				font-weight: 600;
				color: $dark;
				font-size: 0.9rem;
				user-select: none;
				cursor: pointer;
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow: hidden;
                display: block;
                flex-shrink: 0;
                flex-grow: 0;

                @include small-desktop {
                    width: 100%;
                    text-align: left;
                    padding-right: 0;
                }

                @include tablet {
                    width: 100%;
                    text-align: left;
                    padding-right: 0;
                }

                @include smartphone {
                    width: 100%;
                    text-align: left;
                    padding-right: 0;
                }
			}

			.form-control {
				border: none;
				box-shadow: none;
				font-size: 0.9rem;
				height: auto;
				padding: 5px 10px;
                max-width: 100%;
                overflow: hidden;

                @include small-desktop {
                    padding: 5px 0;
                }

                @include tablet {
                    padding: 5px 0;
                }

                @include smartphone {
                    padding: 5px 0;
                }
			}

            .form-link {
                cursor: pointer;
                display: block;
                color: $dark;
                font-size: 0.9rem;
                font-weight: 600;
                white-space: nowrap;
                text-overflow: ellipsis;
                overflow: hidden;
                padding: 5px 10px;

                @include tablet {
                    padding: 5px 0;
                }

                @include smartphone {
                    padding: 5px 0;
                }

                .project-color {
                    width: 16px;
                    height: 16px;
                    border-radius: $box-border-radius;
                    display: inline-block;
                    vertical-align: middle;
                    margin-top: -2px;
                    background: $grey-background;
                }
            }
		}

		.no-fields {
			font-size: 0.9rem;
			color: $grey-text;
		}

		.loading-fields {
			.spinner-border {
				border-width: 3px;
				border-color: $border-darker-grey;
				border-right-color: transparent;
			}
		}
	}

    .submission-spam-detected {
        background: $dark;
        padding: 1.5rem;
        border-radius: $box-border-radius;
        color: $white;
        margin-top: 20px;

        strong {
            display: block;
        }
    }

    .loading-view {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 99;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;

        .spinner-border {
            border-color: $border-grey;
            width: 2.5rem;
            height: 2.5rem;
            border-width: 3px;
            border-right-color: transparent;
        }
    }
}

</style>