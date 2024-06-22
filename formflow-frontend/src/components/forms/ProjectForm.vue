<template>
    <form action="#" @submit.prevent="submitForm" method="post" class="form-wrapper">
        <div v-if="!token">
            <h4>New Project</h4>
            <p>To create your project, simply enter your project details below.</p>
            <div class="form-group">
                <label>Project Name</label>
                <input type="text" class="form-control" name="name" v-model="name" required>
            </div>
            <div class="form-group">
                <label>Project Website</label>
                <input type="text" class="form-control" name="website" v-model="website" required>
            </div>
            <button type="submit"
                    v-bind:class="{'btn': true, 'btn-primary btn-lg mt-3':true, 'disabled': this.checking}"
                    @click="$event.target.blur()">
                <span class="spinner-border" v-show="this.checking"></span>
                <span :class="{'opacity-0': this.checking}">Continue</span>
            </button>
            <div class="alert alert-danger mt-4" v-show="this.error">{{ this.error }}</div>
        </div>
    </form>
</template>

<script>
import repository from "@/repository/repository";

export default {
    name: 'ProjectForm',
    props: ['token'],
    data() {
        return {
            name: "",
            website: "",
            checking: false,
            error: null,
        }
    },
    created() {
    },
    methods: {
        submitForm() {
            this.startLoading();
            this.error = false;

            repository.post("/projects", {
                name: this.name,
                website: this.website,
            })
                .then(response => {
                    console.log(response);
                    this.$router.replace("/projects");
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

<style>
h4 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 10px 0;
}
</style>