<template>
    <div :class="{'project-box': true, 'loading': !loaded}">
        <div class="project-details">
			<div class="project-name">{{ projectName }}</div>
			<div class="project-website">{{ projectWebsite ? projectWebsite : 'No website' }}</div>
		</div>
		<div class="project-statistics">
			<div class="row">
				<div class="col-6">
					<strong>{{ totalProjectForms }}</strong>
					<span>Forms</span>
				</div>
				<div class="col-6">
					<strong>{{ totalProjectSubmissions }}</strong>
					<span>Submissions</span>
				</div>
			</div>
		</div>
		<div class="actions">
			<div class="btn btn-primary" @click.prevent="editProject(project)">Edit</div>
			<div class="btn btn-secondary" @click.prevent="deleteProject(project)">Delete</div>
		</div>
    </div>
</template>

<script>
import repository from '@/repository/repository';
import { useEventBus } from '@/EventBus';

export default {
    name: "ProjectBox",
	props: ['project', 'detailed'],
	data() {
		return {
			loaded: this.project !== undefined,
		}
	},
	methods: {
		editProject(project) {
			this.$router.replace("/projects/" + project.hashId);
		},
		deleteProject() {
			this.$confirm({
				title: "Delete " + this.project.name + "?",
				message: "Please note that this action will permanently remove the " + this.project.name + " and all linked forms. This action cannot be undone.",
				button: {
					yes: "Yes, continue",
					no: "No, cancel"
				},
				callback: confirm => {
					if (confirm) {
						repository.delete("/projects/" + this.project.hashId)
							.then(() => {
                                useEventBus().emit('reloadProjects');
                                useEventBus().emit('reloadCurrentProject');
							})
							.catch(error => {
								this.$confirm({
									title: error.response.data.message,
									button: { yes: "Continue" }
								})
							})
					}
				}
			})
		},
	},
	computed: {
		projectName() {
			if (!this.loaded) return "Loading..";
			return this.project.name;
		},
		projectWebsite() {
			if (!this.loaded) return "Loading..";
			return this.project.website;
		},
		totalProjectForms() {
			if (!this.loaded) return 0;
			console.log(this.project);
			return this.project.forms_count;
		},
		totalProjectSubmissions() {
			if (!this.loaded) return 0;
			return this.project.submissions_count;
		}
	}
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.project-box {
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

	.project-details {

		.project-name {
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

		.project-website {
			font-size: 0.8rem;
			display: block;
			color: $grey-text;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
		}
	}

	.project-statistics {
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

		.project-details {
			.project-name {
				color: transparent;
				background: $hover-grey;
				border-radius: $box-border-radius;
				position: relative;
			}

			.project-website {
				color: transparent;
				background: $hover-grey;
				border-radius: $box-border-radius;
				position: relative;
				margin-top: 5px;
			}
		}

		.project-statistics {
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