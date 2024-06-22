<template>
    <form action="#" @submit.prevent="submitForm" method="post" class="form-wrapper">
        <div v-if="!token">
            <h4>Register</h4>
            <p>Enter your details below to continue</p>
            <div class="form-group">
                <label>Your Name</label>
                <input type="text" class="form-control" name="name" v-model="name" required>
            </div>
            <div class="form-group">
                <label>Your Email</label>
                <input type="email" class="form-control" name="email" v-model="email" required>
            </div>
            <div class="form-group">
                <label>Your Password</label>
                <input type="password" class="form-control" name="password" v-model="password" required>
            </div>
            <div class="form-group">
                <label>Repeat Password</label>
                <input type="password" class="form-control" name="repeatedPassword" v-model="repeatedPassword" required>
            </div>
            <button type="submit" v-bind:class="{'btn': true, 'btn-primary btn-lg mt-3':true, 'disabled': this.checking}" @click="$event.target.blur()">
                <span class="spinner-border" v-show="this.checking"></span>
                <span :class="{'opacity-0': this.checking}">Continue</span>
            </button>
            <div class="alert alert-danger mt-4" v-show="this.error">{{ this.error }}</div>
        </div>
    </form>
</template>

<script>
import repository from "@/repository/repository";
import { useMainStore } from "@/store";

export default {
    name: 'RegisterForm',
    props: ['token'],
    setup() {
        const store = useMainStore();
        return { store }
    },
    data() {
        return {
            name: "",
            email: "",
            password: "",
            repeatedPassword: "",
            checking: false,
            error: null,
        }
    },
    computed: {
        isValidEmail() {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailPattern.test(this.email);
        },
        emailError() {
            return "Please enter a valid email address.";
        },
        isValidPassword() {
            const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
            return passwordPattern.test(this.password);
        },
        passwordError() {
            return "Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character.";
        },
        repeatPasswordError() {
            return "Passwords do not match. Please try again.";
        }
    },
    created() {
        // Pre-authenticate user if token is provided
        if(this.token) {
            repository.get("/me", {
                headers: { 
                    'Authorization': 'Bearer ' + this.token, 
                }
            })
                .then(() => {
                    this.store.updateUserToken(this.token);
                    this.$router.replace("/");
                }).catch(() => {
                    console.log("Couldn't find this account");
                    this.store.logoutUser();
                    this.$router.replace("/login");
                })
        }
    },
    methods: {
        submitForm() {
            this.startLoading();
            this.error = false;

            if (!this.isValidEmail) {
                this.error = this.emailError;
                this.endLoading();
                return;
            }

            if (!this.isValidPassword) {
                this.error = this.passwordError;
                this.endLoading();
                return;
            }

            if (this.password !== this.repeatedPassword) {
                this.error = this.repeatPasswordError;
                this.endLoading();
                return;
            }

            repository.post("/register", {
                name: this.name,
                email: this.email,
                password: this.password,
            })
                .then(response => {
                    console.log(response);
                    this.$router.replace("/login");
                })
                .catch(error => {
                    let message = error.response.data.message;
                    this.error = message + " Please try again.";
                    this.endLoading();
                })
        },
        startLoading() {
            this.checking = true;
        },
        endLoading() {
            this.checking = false;
        },
    }
}
</script>


<style lang="scss" scoped>
@import "src/scss/variables";

form {

    @include smartphone {
        box-shadow: none;
        padding: 35px 0;
    }

    h4 {
        font-size: 2rem;
        font-weight: bold;
        color: $dark;
    }

    p {
        font-size: 1.1rem;
        font-weight: 400;
        color: $dark;
        margin: 0 0 1.5rem 0;
    }

    .form-group {

        label {
            font-size: 1rem;
            color: $dark;
            font-weight: 600;
            display: block;
            margin-bottom: 0;
        }

        .form-control {
            position: relative;
            padding: 15px 10px;
            height: auto;
            border-radius: 0.75rem;
            background: $background-white;
            border-color: $dark;
            @extend .animated;

            @include smartphone {
                font-size: 16px;
            }

            &:focus {
                box-shadow: none;
                outline: none;
                background: $active-grey;
                border-color: $active-grey;
            }
        }
    }
}
</style>