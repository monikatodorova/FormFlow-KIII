<template>
    <router-link :to="formLink" :class="{'form-box': true, 'loading': !loaded}">
        <div class="form-details">
            <div class="form-color-line" :style="{'backgroundColor': formColor}"></div>
			<div class="form-name">{{ formName }}</div>
		</div>
		<div class="form-statistics">
			<div class="row">
				<div class="col-6">
					<strong>{{ totalFormSubmissions }}</strong>
					<span>Submissions</span>
				</div>
			</div>
		</div>
        <div class="btn btn-primary">
            View Form
        </div>
    </router-link>
</template>

<script>
// import repository from '@/repository/repository';
// import { useEventBus } from '@/EventBus';

export default {
    name: "FormBox",
	props: ['form'],
	data() {
		return {
			loaded: this.form !== undefined,
		}
	},
	methods: {
		openForm(form) {
			this.$router.replace("/forms/" + form.hashId);
		},
	},
	computed: {
		formName() {
			if (!this.loaded) return "Loading..";
			return this.form.name;
		},
		totalFormSubmissions() {
			if (!this.loaded) return 0;
			return this.form.total_submissions;
		},
        formColor() {
            if (this.form == null || this.form.color == null) return null;
            return this.form.color.color;
        },
        formLink() {
            if (!this.loaded) return "Loading..";
            return "/forms/" + this.form.hashId;
        },
	}
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.form-box {
	display: block;
	background: $background-white;
	border-radius: $box-border-radius;
	padding: 1.5rem;
	box-shadow: $box-shadow-color 0 2px 3px;
	position: relative;
	overflow: hidden;
	margin-bottom: 30px;
	text-decoration: none;
	color: inherit;
	@extend .animated;

	&:hover {
		box-shadow: $box-shadow-color 0 5px 10px;
	}

	.form-details {
        display: flex;
        align-items: center;

		.form-name {
			font-weight: 600;
			color: $dark;
			font-size: 1rem;
			display: block;
			white-space: nowrap;
			text-overflow: ellipsis;
			overflow: hidden;
			margin: 0;
			text-decoration: none;
		}

        .form-color-line {
            width: 2rem;
            height: 2rem;
            display: block;
            margin-right: 0.5rem;
            border-radius: 0.75rem;
            flex-shrink: 0;
        }
	}

	.form-statistics {
		padding: 1rem 0;
		margin: 1rem 0;
		border-top: 1px solid $border-grey;
		border-bottom: 1px solid $border-grey;

		strong {
			display: block;
			font-size: 1.5rem;
			font-weight: 600;
			color: $dark;
		}

		span {
			font-size: 0.8rem;
			display: block;
			color: $grey-text;
			white-space: normal;

			@include smartphone {
				max-width: 100%;
			}
		}
	}

	.actions {
		margin-top: 20px;
		display: flex;
		gap: 10px;

		.btn {
			flex: 1;
		}
	}

	&.loading {
		pointer-events: none;
		user-select: none;

		.form-details {
			.form-name {
				color: transparent;
				background: $hover-grey;
				border-radius: $box-border-radius;
				position: relative;
			}
            .form-color-line {
                background: $active-grey !important;
            }
		}

		.form-statistics {
			border-top-color: transparent;
			border-bottom-color: transparent;

			strong {
				color: transparent;
				background: $hover-grey;
				border-radius: $box-border-radius;
				position: relative;
			}

			span {
				color: transparent;
				background: $hover-grey;
				border-radius: $box-border-radius;
				position: relative;
				transform: translateY(2px);
			}
		}

		.btn {
			color: transparent;
			background: $hover-grey;
			border-radius: $box-border-radius;
			position: relative;
			border-color: transparent;
		}

	}

	@include smartphone {
		height: auto;
		margin-bottom: 15px;
	}
}
</style>