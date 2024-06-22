<template>
    <header>
        <nav class="navbar navbar-expand navbar-light bg-light">
            
            <!-- Escape link & Logo -->
            <router-link class="navbar-brand" to="/">
                <img src="@/assets/logo.svg" alt="FormFlow" title="FormFlow">
            </router-link>

            <!-- Projects dropdown -->
            <ProjectsDropdown/>

            <!-- Links -->
            <ul class="navbar-nav flex-lg-column main-menu">

                <li class="nav-item">
                    <router-link to="/" class="nav-link" exact>
                        <img src="@/assets/icons/home.svg" alt="Dashboard">
                        <span>Dashboard</span>
                    </router-link>
                </li>

                <FormsDropdown></FormsDropdown>

                <li class="nav-item">
                    <router-link to="/submissions" class="nav-link" exact>
                        <img src="@/assets/icons/submissions.svg" alt="Dashboard">
                        <span>Submissions</span>
                    </router-link>
                </li>

                <li class="nav-item">
                    <router-link to="/tags" class="nav-link" exact>
                        <img src="@/assets/icons/tags.svg" alt="Dashboard">
                        <span>Tags</span>
                    </router-link>
                </li>

            </ul>

            <!-- Profile dropdown -->
            <ProfileDropdown/>
            
        </nav>
    </header>
</template>

<script>
import { useMainStore } from "@/store";
import ProfileDropdown from "@/components/navigation/ProfileDropdown";
import ProjectsDropdown from "@/components/navigation/ProjectsDropdown";
import FormsDropdown from "@/components/navigation/FormsDropdown";

export default {
    name: "AppNavigation",
    components: {ProfileDropdown, ProjectsDropdown, FormsDropdown},
	setup() {
        const store = useMainStore();
        return { store }
    },
    computed: {
        user() {
			return this.store.getUser;
        }
    }
}
</script>

<style lang="scss">
@import "src/scss/variables";

header {
    display: flex !important;
    flex-direction: column;
    flex-grow: 1;
    overflow: hidden;

    @include tablet {
        display: block !important;
        overflow: visible;
    }

    @include smartphone {
        display: block !important;
        overflow: visible;
    }

	.navbar-light {
		background-color: transparent !important;
        flex-direction: column;
        align-items: stretch;
        padding: 1rem 1.5rem;
        flex-grow: 1;
        overflow-x: hidden;

        @include tablet {
            flex-direction: row;
            align-items: center;
            overflow: visible;
            padding: 0.25rem 0.75rem;
        }

        @include smartphone {
            overflow: visible;
            flex-direction: row;
            align-items: center;
            padding: 0.75rem;
        }

		.navbar-brand {
			border-radius: $box-border-radius;
			padding: 0.75rem 0.5rem;
			margin-left: -0.5rem;
            width: calc(100% + 1rem);
			user-select: none;
            display: inline-flex;
            align-items: center;
			@extend .animated;

            @include tablet {
                width: auto;
            }

            @include smartphone {
                padding: 0;
                margin: 0;
                width: auto;
            }

			&:hover {
				background: $hover-grey;

				@include smartphone {
					background: none;
				}
			}

			&:active {
				background: $active-grey;

				@include smartphone {
					background: rgba($white, 0.1);
				}
			}

			img {
                height: 1.5rem;
				display: block;
			}

		}

		.navbar-nav {
			align-items: stretch;

            @include smartphone {
                justify-content: stretch;
            }

			&.ml-auto {
				justify-content: flex-end;
			}

			.nav-item {

                @include tablet {
                    margin-left: 0.5rem;
                }

                @include smartphone {
                    flex-grow: 1;
                    width: 25%;
                }

				.nav-link {
					color: $dark;
					font-size: 1rem;
					font-weight: 600;
					padding: 1rem 0.5rem;
					border-radius: $box-border-radius;
					position: relative;
                    display: flex;
                    align-items: center;
					user-select: none;
                    width: calc(100% + 1rem);
                    margin: 0 -0.5rem;
                    @extend .animated;

                    @include tablet {
                        width: auto;
                        margin: 0;
                    }

                    @include smartphone {
                        font-size: 0.9rem;
                        font-weight: 500;
                        margin: 0;
                        width: 100%;
                        justify-content: center;
                        flex-direction: column;
                        padding: 0.5rem;
                    }

					&:hover {
						background: $hover-grey;
						color: $primary;

                        img {
                            filter: none;
                            opacity: 1;
                        }
					}

					&:active {
						background: $active-grey;
						color: $primary;

                        img {
                            filter: none;
                            opacity: 1;
                        }
					}

					&.router-link-exact-active,
					&.router-link-active {
						color: $primary;

						img {
							filter: none;
							opacity: 1;
						}
					}

					img {
                        display: block;
                        width: 24px;
                        height: 24px;
                        object-fit: contain;
                        filter: brightness(0);
                        margin-right: 0.5rem;

                        @include tablet {
                            display: none;
                        }

                        @include smartphone {
                            margin-right: 0;
                            margin-bottom: 0.15rem;
                        }
					}
				}

				&.open {
					.nav-link {
						background: $hover-grey;
						color: $primary;

                        img {
                            filter: none;
                            opacity: 1;
                        }
					}
				}
			}

			&.main-menu {
				@include smartphone {
					position: fixed;
					bottom: 0;
					left: 0;
					right: 0;
					height: auto;
					background: $white;
					border-radius: 0;
					box-shadow: rgba($dark, 0.1) 0 0 0.25rem;
					justify-content: center;
					width: 100%;
					margin-right: 0 !important;

					.nav-item {
						position: static;

						.nav-link {
							border-radius: 0;
						}
					}
				}
			}
		}
	}
}
</style>