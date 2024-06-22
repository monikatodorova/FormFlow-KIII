<template>
    <div class="form-wrapper">
        <h1>Edit Form</h1>

        <div class="alert alert-danger p-0" v-if="error">{{ error }}</div>

        <form action="#" @submit.prevent="saveForm" :class="{'new-project-form': true}" style="min-height: 150px;">

            <!-- Loading effect -->
            <div class="loading" v-if="!projectId || !loaded">
                <span class="spinner-border"></span>
            </div>

            <!-- Form Fields -->
            <div class="fields-wrapper" v-if="loaded">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" class="form-control" placeholder="Enter your form name" v-model="name">
                        </div>
                        <div class="form-group">
                            <label>Color</label>
                            <ColorPicker ref="picker" :value="this.color" @color-selected="handleColorSelected"></ColorPicker>
                        </div>

                        <div class="mt-2 d-none d-md-block">
                            <button type="submit" :class="{'btn': true, 'btn-primary': updated, 'btn-secondary': !updated}" style="width: 180px" :disabled="saving || !projectId || !loaded || !updated">
                                <span class="spinner-border" v-if="saving || !projectId || !loaded"></span>
                                <span :class="{'opacity-0': saving || !projectId || !loaded}" v-if="updated">Save Changes</span>
                                <span :class="{'opacity-0': saving || !projectId || !loaded}" v-if="!updated">Saved</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-2 d-md-none">
                    <button type="submit" :class="{'btn': true, 'btn-primary': updated, 'btn-secondary': !updated}" style="width: 180px" :disabled="saving || !projectId || !loaded || !updated">
                        <span class="spinner-border" v-if="saving || !projectId || !loaded"></span>
                        <span :class="{'opacity-0': saving || !projectId || !loaded}" v-if="updated">Save Changes</span>
                        <span :class="{'opacity-0': saving || !projectId || !loaded}" v-if="!updated">Saved</span>
                    </button>
                </div>
            </div>

        </form>

        <hr class="my-5">

        <div class="delete-form-box">
            <div class="row">
                <div class="col-md-10 col-xl-7">
                    <!-- Delete Form -->
                    <h1>Delete Form</h1>

                    <p>Delete your form and submissions permanently. Once deleted, you cannot restore your form or your submissions on this form. You won't be able to accept any new submissions from any forms that you have connected with this Form.</p>

                    <!-- Delete form -->
                    <a href="#" @click.prevent="deleteForm" :class="{'btn btn-danger': true, 'loading': actions.deleting}">
                        <img src="@/assets/icons/trash.svg" alt="Trash" :class="{'opacity-0': actions.deleting}">
                        <span :class="{'opacity-0': actions.deleting}">Delete Form</span>
                        <span v-show="actions.deleting" class="spinner-border"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { useEventBus } from "@/EventBus";
import ColorPicker from "@/components/widgets/ColorPicker";
import repository from "@/repository/repository";

import { useMainStore } from '@/store';

export default {
    name: "EditForm",
    components: {ColorPicker},
    props: ['formId'],
    setup() {
        const store = useMainStore();
        return {
            store
        }
	},
    data() {
        return {
            name: "",
            recipients: [],
            color: null,
            error: null,
            loaded: false,
            saving: false,
            updated: false,
            actions: {
                deleting: false,
            },
        }
    },
    created() {
        this.loadFormDetails();

        let propertiesToWatch = Object.keys(this.$data).filter(prop => ['loaded', 'saving', 'updated'].indexOf(prop) < 0);
        propertiesToWatch.forEach(prop => {
            this.$watch(prop, () => {
                this.updated = true;
            }, { deep: true });
        });
    },
    methods: {
        loadFormDetails() {
            if(!this.projectId) return;
            repository.get("/projects/" + this.projectId + "/forms/" + this.formId)
                .then(response => {
                    this.name = response.data.name;
                    this.recipients = response.data.recipients;
                    this.color = response.data.color_id;
                    this.loaded = true;
                    this.$nextTick(() => {
                        this.updated = false;
                    });
                })
        },
        handleColorSelected(colorId) {
            this.color = colorId;
        },
        saveForm() {
            if(!this.projectId) return;
            this.startSaving();
            this.setError(null);
            if (!this.name || this.name.trim().length < 3) {
                this.setError("The form name is required, and must have at least 3 characters.");
                this.endSaving();
                return;
            }
            // If everything passes, save the project
            repository.put("/projects/" + this.projectId + "/forms/" + this.formId, {
                name: this.name,
                recipients: this.recipients,
                color_id: this.color,
            })
                .then(() => {
                    // Refresh the forms
                    useEventBus().emit("reloadForms");
                    this.updated = false;
                    this.endSaving();
                })
                .catch(error => {
                    if (error.response.data.message) {
                        this.setError(error.response.data.message);
                    } else {
                        this.setError("An error happened while updating your form.");
                    }
                    this.endSaving();
                })
        },
        setError(error) {
            this.error = error;
        },
        startSaving() {
            this.saving = true;
        },
        endSaving() {
            this.saving = false;
        },
        deleteForm() {
            if (!this.projectId) return;
            if (!this.formId) return;
            if (this.actions.deleting) return;
            this.$confirm({
                title: "Delete " + this.name + "?",
                message: "Please note that this action will permanently remove the "+ this.name +" and all linked submissions. This action cannot be undone.",
                button: {
                    yes: "Yes, continue",
                    no: "No, cancel"
                },
                callback: confirm => {
                    if (confirm) {
                        this.actions.deleting = true;
                        repository.delete("/projects/" + this.projectId + "/forms/" + this.formId)
                            .then(() => {
                                // Refresh the forms
                                this.$root.$emit("reloadForms");

                                // Redirect to home
                                this.$router.replace("/");
                                this.actions.deleting = false;
                            })
                            .catch(error => {
                                console.log(error);
                                this.actions.deleting = false;
                            })
                    }
                }
            })
        },
    },
    computed: {
        projectId() {
            return this.store.getCurrentProject.hashId;
        },
    },
    watch: {
        projectId: function (newValue) {
            if(newValue !== null) this.loadFormDetails();
        },
    },
}
</script>

<style lang="scss" scoped>
@import "../../scss/variables";

hr {
    border: 1px solid #dee2e6;
}

.delete-form-box {
    padding-bottom: 2.5rem;

    .btn {
        display: inline-flex;
        align-items: center;

        img {
            width: 1.25rem;
            height: 1.25rem;
            margin: -0.25rem 0.25rem -0.25rem -0.25rem;
            filter: brightness(0) invert(1);
        }
    }
}

.spam-protection-box {
    padding: 1.5rem;
    background: $background-grey;
    border-radius: $box-border-radius;

    h1 {
        font-size: 1rem;
    }

    h2 {
        font-size: 1.25rem;
        font-weight: bold;
        color: $primary;
        margin-bottom: 1.5rem;
        margin-top: 0;
    }
}

.form-group {

    margin-bottom: 10px;

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
        // border-color: $dark;
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
</style>