<template>
  <div class="page-wrapper background-grey">

    <h4>Project Statistics</h4>
    <div class="row md-smaller-margin">
			<div class="col-6 col-xl-3">
				<div :class="{'stats-box small-box': true, 'loading': !loaded}">
					<img src="@/assets/icons/submissions-today.svg" class="icon" alt="Total Submission">
					<strong>{{ submissions.today }}</strong>
					<span>Submissions Today</span>
				</div>
			</div>
			<div class="col-6 col-xl-3">
				<div :class="{'stats-box small-box': true, 'loading': !loaded}">
					<img src="@/assets/icons/submissions-week.svg" class="icon">
					<strong>{{ submissions.week }}</strong>
					<span>Last 7 days</span>
				</div>
			</div>
			<div class="col-6 col-xl-3">
				<div :class="{'stats-box small-box': true, 'loading': !loaded}">
					<img src="@/assets/icons/submissions-month.svg" class="icon">
					<strong>{{ submissions.month }}</strong>
					<span>Last 30 days</span>
				</div>
			</div>
			<div class="col-6 col-xl-3">
				<div :class="{'stats-box small-box': true, 'loading': !loaded}">
					<img src="@/assets/icons/submissions-total.svg" class="icon">
					<strong>{{ submissions.total }}</strong>
					<span>Total Submission</span>
				</div>
			</div>
		</div>

    <h4>Your Forms</h4>
    <div class="row">

      <!-- Loading effect -->
      <div class="col-md-4 col-lg-4 col-xl-3" v-if="!forms.loaded">
        <FormBox></FormBox>
      </div>

      <div class="col-md-4 col-lg-4 col-xl-3" v-for="(form, key) in forms.items" :key="key">
        <FormBox :form="form"></FormBox>
      </div>

    </div>
  </div>
</template>

<script>
import repository from '@/repository/repository';
import { useEventBus } from '@/EventBus';
import FormBox from "@/components/items/FormBox";

import { useMainStore } from '@/store';

export default {
  name: 'HomeView',
  components: {FormBox},
  setup() {
        const store = useMainStore();
        return {
            store
        }
	},
  data() {
    return {
      submissions: {
        total: 0,
        week: 0,
        month: 0,
        today: 0,
      },
      daily: [],
      loaded: false,
    }
  },
  created() {
    useEventBus().emit('reloadCurrentProject');
    useEventBus().emit('reloadProjects');

    this.loadStatistics(this.currentProject.hashId);

    useEventBus().on('reloadStatistics', () => {
      this.loadStatistics(this.currentProject.hashId);
    })
  },
  methods: {
    loadStatistics(projectId) {
      this.loaded = false;
      if (!projectId) return;
      repository.get("/projects/" + projectId + "/statistics")
        .then(response => {
          this.submissions = response.data.submissions;
          this.daily = response.data.daily;
          this.loaded = true;
        })
        .catch(error => {
          console.log(error);
        });
    }
  },
  computed: {
      currentProject() {
        useEventBus().emit('reloadCurrentProject');
        // return this.$store.getters.currentProject;
		return this.store.getCurrentProject;
      },
      forms() {
        // return this.$store.getters.forms;
		return this.store.getForms;
      }
  },
  watch: {
      "store.getCurrentProject.hashId"(val) {
          this.loadStatistics(val);
      }
  },
}
</script>

<style lang="scss">
@import "src/scss/variables";

.page-wrapper {
	padding: 1.5rem;
	position: relative;
  min-height: 100%;

  @include smartphone {
      padding: 0.75rem;
  }

  &.background-grey {
      background: lighten($background-grey, 1%);
  }

  .stats-box {
		display: block;
		padding: 20px 20px 20px 80px;
		background: $white;
		border-radius: $box-border-radius;
		box-shadow: rgba($dark, 0.1) 0 1px 2px;
		position: relative;
		text-decoration: none;
		margin-bottom: 30px;
        overflow: hidden;

		&.small-box {
			height: calc(100% - 30px);

            @include small-desktop {
                height: calc(100% - 15px);
                margin-bottom: 15px;
            }

            @include tablet {
                height: calc(100% - 15px);
                margin-bottom: 15px;
            }
		}

		@include small-desktop {
			padding: 15px 15px 15px 60px;
		}

		@include tablet {
			padding: 15px 15px 15px 60px;
		}

		@include smartphone {
			margin-bottom: 15px;
            height: auto;
			padding: 15px;

			&.small-box {
				height: auto;
			}
		}

		&.chart-box {
			padding-left: 20px;

			@include smartphone {
				padding: 0;
			}
		}

        &.forms-breakdown-box {
            height: calc(100% - 30px - 2.5rem);
            overflow-y: hidden;

			@include smartphone {
				height: auto;
				padding: 15px;
			}

            .form-breakdown {
                display: flex;
                align-items: center;
                padding: 0.5rem;
                width: calc(100% + 1rem);
                margin: 0 -0.5rem;
                border-radius: $box-border-radius;
                @extend .animated;

                &:hover {
                    background: $background-grey;

                    .form-progress {
                        .progress {
                            background: $white;
                        }
                    }
                }

                .form-color {
                    width: 1.5rem;
                    height: 1.5rem;
                    border-radius: $box-border-radius;
                    background: $background-grey;
                    margin-right: 0.5rem;
                }

                .form-name {
                    width: 55%;
                    flex-shrink: 0;
                    white-space: nowrap;
                    text-overflow: ellipsis;
                    overflow: hidden;
                    font-size: 1rem;
                    color: rgba($dark, 0.75);
                    padding-right: 1.5rem;
                }

                .form-progress {
                    margin-left: auto;
                    width: calc(45% - 2rem);
                    display: flex;
                    align-items: center;

                    .progress {
                        background: $background-grey;
                        border-radius: $box-border-radius;
                        width: 100%;
                        @extend .animated;

                        .progress-bar {
                            border-radius: $box-border-radius;
                        }
                    }

                    span {
                        width: 3rem;
                        flex-shrink: 0;
                        text-align: right;
                        font-size: 0.8rem;
                        font-weight: 600;
                        color: $dark;
                    }
                }
            }
        }

		@extend .animated;

		&:hover {
			box-shadow: $box-shadow-color 0 3px 6px;
		}

		&:before {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 100%;
			background: $white;
			z-index: 1;
			content: ' ';
			opacity: 0;
			visibility: hidden;
			transition: 0.15s ease all;
		}

		&:after {
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			width: 25px;
			height: 25px;
			border: 2px solid $border-darker-grey;
			border-right-color: transparent;
			content: ' ';
			z-index: 2;
			border-radius: 50%;
			margin: auto;
			animation: spinner-border 0.75s linear infinite;
			opacity: 0;
			visibility: hidden;
			transition: 0.15s ease all;
		}

		&.loading {
			&:before,
			&:after {
				opacity: 1;
				visibility: visible;
				transition: none;
			}
		}

		img.icon {
			position: absolute;
			top: 0;
			bottom: 0;
			margin: auto;
			left: 25px;
			width: 40px;
			height: 40px;
			user-select: none;
			pointer-events: none;

			@include small-desktop {
				left: 15px;
				width: 30px;
				height: 30px;
			}

			@include tablet {
				left: 15px;
				width: 25px;
				height: 25px;
			}

			@include smartphone {
				position: relative;
				top: 0;
				left: 0;
				margin: 0;
				width: 25px;
				height: 25px;
			}
		}

		strong {
			font-weight: 700;
			color: $dark;
			display: block;
			font-size: 2rem;
			margin-bottom: -5px;

			@include smartphone {
				font-size: 1.5rem;
			}
		}

		span {
			font-weight: 400;
			color: $dark;
			display: block;
			margin: 0;

			@include smartphone {
				font-size: 0.8rem;
			}
		}

		@include tablet {
			padding: 10px 10px 10px 50px;
		}
	}

	h1 {
		font-weight: 600;
		font-size: 1.25rem;
		color: $dark;
		margin: 0 0 1rem 0;
	}
}

.page-inside-wrapper {
    padding: 0 1.5rem 0 0;
    // background: lighten($background-grey, 1%);

    @include smartphone {
        padding: 0 0.75rem;
    }
}

</style>
