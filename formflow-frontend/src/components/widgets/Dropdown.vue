<template>
    <div :class="{'dropdown-wrapper': true, 'open': open}" v-click-away="handleDropdownClick">
        <div class="dropdown-toggle" @click.prevent="toggleDropdown">
            <slot name="toggle"></slot>
        </div>
        <div :class="{'dropdown-menu': true, 'position-right': position === 'right', 'position-bottom': position === 'bottom'}" @click="hideDropdown">
            <slot name="content"></slot>
        </div>
    </div>
</template>

<script>
import { useMainStore } from '@/store';

export default {
    name: "DropdownWidget",
    props: ['position', 'elementClass'],
	setup() {
        const store = useMainStore();
        return { store }
    },
    data() {
        return {
            open: false,
        }
    },
    methods: {
        toggleDropdown() {
            if(!this.open) {
                this.showDropdown();
            } else {
                this.hideDropdown();
            }
        },
        showDropdown() {
            this.open = true;
			this.store.saveDropdown(this);
        },
        hideDropdown() {
            this.open = false;
			this.store.clearDropdown();
        },
        handleDropdownClick(event) {
            if (!this.$el.contains(event.target)) {
                this.hideDropdown();
            }
        },
    }
}
</script>

<style lang="scss">
@import "src/scss/variables";

.dropdown-wrapper {
    position: relative;

    .dropdown-toggle {
        position: relative;
        display: block;
        cursor: pointer;
        user-select: none;
    }

    .dropdown-menu {
		opacity: 0;
        display: block;
		position: absolute;
		top: calc(100% - 10px);
		left: 0;
		height: auto;
        min-width: 16rem;
        max-width: 100%;
        width: 100%;
		max-height: 450px;
		overflow-x: hidden;
		padding: 10px 20px 10px 20px;
		border-color: transparent;
		box-shadow: $box-shadow-color 0 2px 5px;
		border-radius: $box-border-radius;
		visibility: hidden;
		user-select: none;

        @extend .animated;

        &.position-right {
            left: auto;
            right: 0;
        }

        &.position-bottom {
            left: auto;
            right: 0;
            top: auto;
            bottom: calc(100% + 15px);

            @include tablet {
                top: calc(100% - 10px);
                bottom: auto;
            }

            @include smartphone {
                top: calc(100% - 10px);
                bottom: auto;
            }
        }

        h4 {
            font-size: 0.9rem;
            color: $dark;
            font-weight: 600;
            margin: 10px 0;
        }

        hr {
            margin: 5px 0;
            opacity: 0.75;
        }

        .spinner-border {
            width: 18px;
            height: 18px;
            margin-bottom: 10px;
            border-width: 2px;
            border-color: $border-darker-grey;
            border-right-color: transparent;
        }

        .dropdown-link {
			display: block;
			width: calc(100% + 20px);
			margin-left: -10px;
			margin-right: -10px;
			padding: 8px 10px;
			text-decoration: none;
			background: none;
			border: none;
			outline: none;
			color: $grey-text;
			text-align: left;
            border-radius: $box-border-radius;

			@extend .animated;

			&:hover {
				background: $hover-grey;
				color: $primary;
			}

			&:active {
				background: $active-grey;
				color: $primary;
			}
		}
    }

    &.open {
        .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(10px);
        }
    }

    // Dropdown types
	&.profile-dropdown {
		.dropdown-toggle:after {
			display: none;
		}
	}

    &.nav-item {
		.dropdown-toggle {
			&:after {
				display: none;
			}

			.nav-link:after {
				display: inline-block;
				vertical-align: middle;
				width: 6px;
				height: 6px;
				transform-origin: center;
				transform: rotate(45deg);
				border-bottom: 2px solid $dark;
				border-right: 2px solid $dark;
				content: ' ';
				margin-left: auto;
				margin-top: -2px;
				opacity: 0.25;

                @include tablet {
                    margin-left: 0.25rem;
                }

				@include smartphone {
					display: none;
				}
			}
		}
	}

    &.bottom-item {

        @include smartphone {
            .dropdown-menu {
                bottom: 100%;
                top: auto;
                transform: translateY(2px);
                width: 100%;
                max-width: 100%;
                border-radius: 0;
                box-shadow: rgba($dark, 0.25) 0 -1.5rem 2.5rem;
                border-bottom: 1px solid $border-grey;
                z-index: 0;
            }
        }
    }

    &.tags-dropdown {
		.dropdown-toggle {
			&:after {
				display: none;
			}

			&:hover {
				color: $primary;
			}
		}

		.dropdown-menu {
			left: -2px;
			right: auto;
            top: auto;
            bottom: calc(100% + 12px);
            max-height: 200px;
            min-width: 200px;

			@include smartphone {
				max-height: 250px;
				top: auto;
				bottom: 100%;
			}
		}
	}

    &.color-picker {
		.dropdown-toggle {
			&:after {
				display: none;
			}
		}
        .dropdown-menu {
            max-height: 250px;
            min-width: 200px;
        }
	}
}


</style>