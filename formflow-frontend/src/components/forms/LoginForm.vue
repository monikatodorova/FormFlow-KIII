<template>
    <form action="#" @submit.prevent="submitForm" method="post" class="form-wrapper">
        <div v-if="!token">
            <h4>Login</h4>
            <p>Please enter your credentials to continue</p>
            <div class="form-group">
                <label>Your Email</label>
                <input type="email" class="form-control" name="email" v-model="email" required>
            </div>
            <div class="form-group">
                <label>Your Password</label>
                <input type="password" class="form-control" name="password" v-model="password" required>
            </div>
            <button type="submit" v-bind:class="{'btn': true, 'btn-primary btn-lg mt-3':true, 'disabled': this.checking}" @click="$event.target.blur()">
                <span class="spinner-border" v-show="this.checking"></span>
                <span :class="{'opacity-0': this.checking}">Continue</span>
            </button>
            <div class="alert alert-danger mt-4" v-show="this.error">{{ this.error }}</div>
        </div>

        <div class="spinner-border mx-auto my-5 d-block opacity-25" v-if="token"></div>
    </form>
</template>

<script>
import repository from "@/repository/repository";
import { useMainStore } from '@/store';

export default {
    name: 'LoginForm',
    props: ['token'],
    setup() {
        const store = useMainStore();
        return { store }
    },
    data() {
        return {
            email: "",
            password: "",
            checking: false,
            error: null,
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
                })
                .catch(() => {
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

            repository.post("/login", {
                email: this.email,
                password: this.password,
            })
                .then(response => {
                    console.log(response);
                    this.store.updateUserToken(response.data.token);
                    this.$router.replace("/");
                })
                .catch(error => {
                    console.log(error);
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
        padding: 25px 0 0 0;
    }

    h4 {
        font-size: 2rem;
        font-weight: bold;
        color: $dark;

        @include smartphone {
            font-size: 1.5rem;
        }
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