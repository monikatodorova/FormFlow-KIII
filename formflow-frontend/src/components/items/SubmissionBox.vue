<template>
    <router-link
        @click="updateStatus"
        :to="submissionLink"
        :class="{'submission-box': true, 'loading': !loaded, 'new': submissionStatus === 'new', 'spam': spamDetected }"
    >
        <div :class="{'submission-avatar': true, 'text-dark': submissionAvatarColor === 'dark' }" :style="{'background': submissionAvatarBackground }">
			{{ submissionCredentials }}
		</div>
		<div class="submission-name">
			<strong>{{ submissionName }}</strong>
			<span>{{ submissionEmail }}</span>
		</div>
		<div :class="{'submission-time': true, 'no-border': true }">
			{{ submissionCreatedAt }}
		</div>
		<div class="submission-project" v-if="data !== undefined && data.form !== undefined && !spamDetected">
			<div :class="{'project-name': true, 'tooltip-like': true }">{{ formName }}</div>
			<div class="project-color" :style="{'background': formColor }"></div>
		</div>
        <div class="submission-spam-detected" v-if="spamDetected">
            Spam
        </div>

    </router-link>
</template>

<script>
import { useEventBus } from '@/EventBus';

export default {
    name: 'SubmissionBox',
    props: ['data'],
    data() {
        return {
            loaded: this.data !== undefined,
        }
    },
    computed: {
        submissionLink() {
            if (!this.loaded) return "";
            return "/submissions/" + this.data.hashId;
        },
        submissionName() {
            if (!this.loaded) return "Loading...";
            return this.data.name;
        },
        submissionEmail() {
            if (!this.loaded) return "Loading...";
            return this.data.email;
        },
        submissionStatus() {
            if (!this.loaded) return "Loading...";
            return this.data.status;
        },
        spamDetected() {
            if (!this.loaded) return false;
            return this.data.spam;
        },
        submissionCreatedAt() {
            if (!this.loaded) return "Loading..";
            if (this.data.created_at === null) return "Error..";

            let today = new Date();
            let accessedDate = new Date(this.data.created_at);
            let timeDifference = today - accessedDate;
            let returnString = "";

            // Convert milliseconds to seconds
            timeDifference = Math.floor(timeDifference / 1000);

            const secondsInMinute = 60;
            const secondsInHour = 3600;
            const secondsInDay = 86400;
            const secondsInMonth = 2629800; // Average seconds in a month

            const months = Math.floor(timeDifference / secondsInMonth);
            const days = Math.floor((timeDifference % secondsInMonth) / secondsInDay);
            const hours = Math.floor((timeDifference % secondsInDay) / secondsInHour);
            const minutes = Math.floor((timeDifference % secondsInHour) / secondsInMinute);
            const seconds = Math.floor(timeDifference % secondsInMinute);

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
        submissionCredentials() {
            if (!this.loaded) return "NA";
            return this.data.avatar.credentials;
        },
        submissionAvatarBackground() {
            if (!this.loaded) return null;
            return this.data.avatar.color;
        },
        submissionAvatarColor() {
            if (!this.loaded) return null;
            return this.data.avatar.text;
        },
        tags() {
            if (!this.loaded) return null;
            if (this.data.tags === undefined) return null;
            if (this.data.tags.length === 0) return null;
            return this.data.tags;
        },
        formName() {
            if (!this.loaded || this.data === undefined || this.data.form === undefined) return "Loading..";
            return this.data.form.name;
        },
        formColor() {
            if (!this.loaded || this.data === undefined || this.data.form === undefined) return null;
            return this.data.form.color.color;
        },
    },
    methods: {
        updateStatus() {
            useEventBus().emit('updateStatus');
        }
    }
}
</script>


<style lang="scss" scoped>
@import "src/scss/variables";

.submission-box {
	display: flex;
	flex-direction: row;
	align-items: center;
	position: relative;
	background: $background-white;
	padding: 10px 25px;
    width: calc(100% + 50px);
    margin: 0 -25px;
    text-decoration: none;

	@include smartphone {
        width: 100%;
        margin: 0;
		box-shadow: none;
        padding: 10px;
	}

	& + .submission-box {
        border-top: 1px solid $border-grey;

		@include smartphone {
			margin-top: 0;
		}
	}

    &.new {
        background: lighten($primary, 52%);

        & + .submission-box.new {
            border-top: 1px solid rgba($primary, 0.1);
        }
    }

    &.spam {
        background: $white;

        * {
            opacity: 0.5;
        }
    }

	&:hover {
        background: $hover-grey;
        transition: 0.15s ease all;

        .submission-avatar {
            box-shadow: rgba($primary, 1) 0 0 0 0.15rem;
        }

		.submission-name {
			strong {
				color: $primary;
                text-decoration: underline;
			}
		}

		@include smartphone {
			box-shadow: none;
			background: $hover-grey;
		}
	}

    &.router-link-exact-active {
        background: lighten($primary, 48%);
    }

    &.loading {
        background: $white;
        user-select: none;
        pointer-events: none;

        .submission-avatar {
            background: $active-grey;
            color: transparent;
            pointer-events: none;
            user-select: none;
        }

        .submission-name {
            pointer-events: none;
            user-select: none;

            strong {
                background: $hover-grey;
                width: 250px;
                border-radius: $box-border-radius;
                color: transparent;
                position: relative;
                overflow: hidden;

                &:before {
                    position: absolute;
                    top: 0;
                    left: -80%;
                    width: 80%;
                    height: 100%;
                    background: $active-grey;
                    content: ' ';
                    -webkit-animation: LOADING-EFFECT 0.65s linear infinite; /* Safari 4+ */
                    -moz-animation: LOADING-EFFECT 0.65s linear infinite; /* Fx 5+ */
                    -o-animation: LOADING-EFFECT 0.65s linear infinite; /* Opera 12+ */
                    animation: LOADING-EFFECT 0.65s linear infinite; /* IE 10+, Fx 29+ */

                    @extend .animated;
                }
            }

            span {
                background: $hover-grey;
                color: transparent;
                border-radius: $box-border-radius;
                width: 150px;
                transform: translateY(3px);
            }
        }

        .submission-time {
            background: $hover-grey;
            color: transparent;
            border-right: none;
            border-radius: $box-border-radius;
        }

        .submission-project {
            background: $hover-grey;
            color: transparent;
            border-right: none;
            border-radius: $box-border-radius;
            width: 200px;

            .project-name {
                color: transparent;
            }
        }

    }

    .submission-link {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        cursor: pointer;
    }

	.submission-avatar {
		width: 30px;
		height: 30px;
		line-height: 30px;
		text-align: center;
		font-weight: 700;
		border-radius: 50%;
		color: $white;
		font-size: 0.7rem;
		text-decoration: none;
		display: block;
		flex-shrink: 0;

		@extend .animated;

		&.text-dark {
			color: $dark;
		}
	}

	.submission-name {
		display: block;
		padding-left: 10px;
		margin-right: auto;
		text-decoration: none;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

		strong {
			display: block;
			color: $dark;
			font-size: 1rem;
			font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;

			@extend .animated;

			@include smartphone {
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				display: block;
			}
		}

		span {
			display: block;
			color: $grey-text;
			font-size: 0.8rem;
			margin-top: 2px;
			position: relative;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;

			@include smartphone {
				margin-top: 0;
				display: block;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
		}

		@include smartphone {
			flex-wrap: wrap;
			max-width: 55%;
		}
	}

	.submission-time {
		font-size: 0.8rem;
		color: $grey-text;
		padding-right: 10px;
		margin-right: 10px;
		border-right: 1px solid $border-darker-grey;
        text-decoration: none;

        @media (max-width: 1400px) {
            display: none;
        }

		@include smartphone {
			font-size: 0.65rem;
			opacity: 0.75;
			flex-shrink: 0;
		}

		&.no-border {
			border-right: none;
			margin-right: 0;
		}
	}

    .submission-status {
        margin-right: 10px;
        width: 80px;
        font-stretch: normal;
        font-size: 0.8rem;
        text-transform: capitalize;
        color: $white;
        background: rgba($primary, 1);
        border-radius: $box-border-radius;
        text-align: center;
        padding: 0.15rem 0.25rem;

        &.seen {
            color: $grey-text;
            background: rgba($dark, 0.05);
        }
    }

	.submission-project {
		position: relative;
        z-index: 2;

		.project-color {
			display: inline-block;
			vertical-align: middle;
			width: 16px;
			height: 16px;
            line-height: 1;
			border-radius: $box-border-radius;
		}

		.project-name {
			display: inline-block;
			vertical-align: middle;
			font-size: 0.8rem;
			color: $grey-text;
			padding-right: 5px;

			&.tooltip-like {
				position: absolute;
				width: auto;
				white-space: nowrap;
                bottom: 0;
                top: 0;
                height: calc(1.2rem + 10px);
                right: calc(100% + 10px);
				z-index: 1;
				opacity: 0;
				visibility: hidden;
				background: $grey-background;
				padding: 5px 10px;
				border-radius: $box-border-radius;
				box-shadow: $box-shadow-color 0 1px 2px;
				color: $white;
				font-weight: 600;

				@extend .animated;
			}

			@include smartphone {
				display: none;
			}
		}

		&:hover {
			.project-name.tooltip-like {
				opacity: 1;
				visibility: visible;
			}
		}
	}

    .submission-spam-detected {
        font-weight: bold;
        font-size: 0.6rem;
        opacity: 1;
        color: $dark;
        text-transform: uppercase;
        background: rgba($dark, 0.1);
        padding: 0.15rem 0.5rem;
        border-radius: 0.5rem;
    }
}
</style>