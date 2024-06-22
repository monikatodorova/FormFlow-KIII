<template>
    <div class="login-page">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-xl-6 mx-auto">
                    
                    <!-- Brand -->
                    <a href="/" class="logo">
                        <img src="@/assets/logo.svg" alt="FormFlow">
                    </a>

                    <!-- Login form -->
                    <LoginForm :token="token"></LoginForm>

                    <!-- Register form link -->
                    <div class="additional-links">
                        Do not have an account?
                        <router-link to="/register">Register!</router-link>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>

<script>
import LoginForm from "@/components/forms/LoginForm";

export default {
    name: 'LoginView',
    props: ['token'],
    metaInfo: {
        title: 'Login',
    },
    components: {LoginForm}
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.login-page {
    min-height: 100svh;
    padding: 2.5rem 0;

    @include smartphone {
        align-items: flex-start;
        justify-content: flex-start;
        padding: 25px 0;
        background: $white;
        box-shadow: none;
		min-height: auto;
    }

    .additional-links {
        padding-top: 15px;
        color: $dark;

        a {
            color: $primary;
            font-weight: 600;
        }
    }

    .logo {
        display: block;
        text-decoration: none;
        margin-bottom: 45px;

        @include smartphone {
            margin-bottom: 0;
        }

        img {
            display: inline-block;
            vertical-align: middle;
			height: 2rem;
        }
    }
}
</style>