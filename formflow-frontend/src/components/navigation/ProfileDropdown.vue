<template>
    <div :class="{'profile-item': true, 'loading': !user.loaded}">
        <Dropdown v-if="user.loaded" class="profile-dropdown" position="bottom">

            <template v-slot:toggle>
                <div class="profile-dropdown-toggle">
                    <div class="profile-link" v-if="user.avatar !== undefined">
                        <span>{{ userCredentials }}</span>
                    </div>
                    <div class="profile-info d-none d-lg-block">
                        <strong>{{ user.name }}</strong>
                        <i>{{ user.email }}</i>
                    </div>
                </div>
            </template>

            <template v-slot:content>
                <div class="d-flex d-lg-none">
                    <div class="profile-link" v-if="user.avatar !== undefined">
                        <span>{{ userCredentials }}</span>
                    </div>
                    <div class="profile-info">
                        <strong>{{ user.name }}</strong>
                        <i>{{ user.email }}</i>
                    </div>
                </div>
                <hr class="d-lg-none">
				<router-link to="/projects" class="dropdown-link">Projects</router-link>
				<router-link to="/logout" class="dropdown-link">Sign out</router-link>
            </template>

        </Dropdown>

        <div class="profile-link loading-link" v-if="!user.loaded">
            <span class="spinner-border"></span>
        </div>
    </div>
</template>

<script>
import { useMainStore } from '@/store';
import repository from "@/repository/repository";
import Dropdown from "@/components/widgets/Dropdown.vue";

export default {
    name: "ProfileDropdown",
    components: {Dropdown},
    setup() {
        const store = useMainStore();
        return { store }
    },
    created() {
        this.loadUser();
    },
    methods: {
        loadUser() {
            repository.get("/me")
                .then(response => {
                    this.store.updateUser(response.data.user);
                })
                .catch(error => {
                    console.log(error);
                    this.store.logoutUser();
                    if(this.$route.path !== "/login") {
                        this.$router.replace("/login");
                    }
                })
        },
    },
    computed: {
        user() {
            return this.store.getUser;
        },
        userCredentials() {
            if(this.user.avatar == null) return "";
            return this.user.avatar.credentials;
        },
        avatarBackground() {
            if (this.user.avatar == null) return null;
            return this.user.avatar.color;
        },
        avatarTextDark() {
            if (this.user.avatar == null) return false;
            return this.user.avatar.text === "dark";
        }
    }
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.profile-item {
    margin: auto 0 1rem 0;

    @include tablet {
        margin: 0 0 0 auto;
    }

    @include smartphone {
        margin: 0;
    }

	&.loading {
		user-select: none;
		pointer-events: none;
	}

    .profile-dropdown-toggle {
        display: flex;
        align-items: center;
    }

	.profile-link {
		width: 36px;
		height: 36px;
		display: block;
		position: relative;
		line-height: 36px;
		text-align: center;
		background: $primary;
		padding: 0;
		border-radius: 50%;
		text-decoration: none;
        flex-shrink: 0;
		@extend .animated;

		span {
			font-weight: 600;
			color: $white;

			&.dark {
				color: $dark;
			}
		}

		span.spinner-border {
			width: 20px;
			height: 20px;
			border-color: $white;
            opacity: 0.25;
            border-width: 2px;
			border-right-color: transparent;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
		}
	}

    .profile-info {
        padding-left: 0.5rem;
        overflow: hidden;
        text-overflow: ellipsis;

        strong {
            display: block;
        }

        i {
            font-style: normal;
            font-size: 0.9rem;
            color: rgba($dark, 0.5);
            overflow: hidden;
        }
    }

    .profile-dropdown {
        @include smartphone {
            position: static;
        }

        .dropdown-link {
            font-weight: 600;
            display: flex;
            align-items: center;

            img {
                width: 1.25rem;
                height: 1.25rem;
                margin-right: 0.25rem;
            }
        }
    }
}
</style>