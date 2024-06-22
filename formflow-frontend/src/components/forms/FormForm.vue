<template>
    <form action="#" @submit.prevent="submitForm" method="post" class="form-wrapper">
        <div v-if="!token">
            <h4>New Form</h4>
            <p>Enter the following details to create your form. The color you choose is used for better representation of the forms and their analytics.</p>
            <div class="form-group">
                <label>Form Name</label>
                <input type="text" class="form-control" name="name" v-model="name" required>
            </div>
            <div class="form-group">
                <label>Color</label>
                <ColorPicker ref="picker" :value="this.color" @color-selected="handleColorSelected"></ColorPicker>
            </div>
            <button type="submit"
                    v-bind:class="{'btn': true, 'btn-primary btn-lg mt-3':true, 'disabled': this.checking}"
                    @click="$event.target.blur()">
                <span class="spinner-border" v-show="this.checking"></span>
                <span :class="{'opacity-0': this.checking}">Save form</span>
            </button>
            <div class="alert alert-danger mt-4" v-show="this.error">{{ this.error }}</div>
        </div>
    </form>
</template>

<script>
import repository from "@/repository/repository"; 
import { useEventBus } from "@/EventBus";
import { useMainStore } from '@/store';
import ColorPicker from "@/components/widgets/ColorPicker";

export default {
    name: 'FormForm',
    props: ['token'],
    components: {ColorPicker},
    setup() {
        const store = useMainStore();
        return {
            store
        }
	},
    data() {
        return {
            name: "",
            checking: false,
            error: null,
            color: null
        }
    },
    created() {
    },
    computed: {
        currentProject() {
            // return this.$store.getters.currentProject;
            return this.store.getCurrentProject;
        },
    },
    methods: {
        submitForm() {
            this.startLoading();
            this.error = false;

            repository.post("/projects/" + this.currentProject.hashId + "/forms", {
                name: this.name,
                color: this.color,
            })
                .then(response => {
                    console.log(response);
                    useEventBus().emit('reloadForms');
                    this.$router.replace("/");
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

<style scoped>
h4 {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 10px 0;
}
</style>