<template>
	<div class="register-page">
		<div class="container">
			<div class="row">
				<div class="col-md-8 col-xl-6 mx-auto">

					<!-- Logo -->
					<a href="/" class="logo">
						<img src="@/assets/logo.svg" alt="FormFlow">
					</a>

					<!-- Register form -->
					<RegisterForm></RegisterForm>

                    <!-- Login form link -->
                    <div class="additional-links">
                        Already have an account?
                        <router-link to="/login">Sign in!</router-link>
                    </div>

				</div>
			</div>
		</div>
	</div>
</template>

<script>
import RegisterForm from "@/components/forms/RegisterForm";

export default {
    name: 'RegisterView',
    props: ['token'],
    metaInfo: {
        title: 'Register',
    },
    components: {RegisterForm}
}
</script>


<style lang="scss" scoped>
@import "src/scss/variables";

.register-page {
	min-height: 100vh;
	padding: 50px 0;

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
			height: 2.5rem;
		}
	}
}
</style>