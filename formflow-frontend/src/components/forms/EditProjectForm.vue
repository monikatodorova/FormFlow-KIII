<template>
    <div class="form-wrapper">
        <div class="row">
            <div class="col-md-5">
                <h1>Edit Project</h1>

                <div class="alert alert-danger p-0" v-if="error">{{ error }}</div>

                <form action="#" @submit.prevent="submitForm" method="post" class="form-wrapper">
                    <p>To make updates, modify the project details below.</p>
                    <div class="form-group">
                        <label>Project Name</label>
                        <input type="text" class="form-control" name="name" v-model="name" required>
                    </div>
                    <div class="form-group">
                        <label>Project Website</label>
                        <input type="text" class="form-control" name="website" v-model="website" required>
                    </div>
                    <button type="submit"
                            v-bind:class="{'btn': true, 'btn-primary btn-lg mt-3':true, 'disabled': this.saving}"
                            @click="$event.target.blur()">
                        <span class="spinner-border" v-show="this.saving"></span>
                        <span :class="{'opacity-0': this.saving}">Save changes</span>
                    </button>
                    <div class="alert alert-danger mt-4" v-show="this.error">{{ this.error }}</div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import repository from "@/repository/repository";
import { useEventBus } from "@/EventBus";

export default {
    name: "EditProjectForm",
    props: ['projectId'],
    data() {
        return {
            name: "",
            website: "",
            canEditProject: false,
            error: null,
            saving: false,
        }
    },
    created: function () {
        this.loadProjectData();
    },
    methods: {
        loadProjectData() {
            if(!this.projectId) return;
            repository.get("/projects/" + this.projectId)
                .then(response => {
                    this.name = response.data.name;
                    this.website = response.data.website;
                    this.canEditProject = true;
                })
                .catch(() => {
                    this.$router.replace("/projects");
                })
        },
        submitForm() {
            this.startSaving();
			this.setError(null);

			if (!this.name || this.name.trim().length < 3) {
				this.setError("The project name is required, and must have at least 3 characters.");
				this.endSaving();
				return;
			}
			if (!this.website || !this.verifyWebsite(this.website)) {
				this.setError("A valid website link is required.");
				this.endSaving();
				return;
			}

			// If everything passes, save the project
			repository.put(`/projects/${this.projectId}`, {
				name: this.name,
				website: this.website,
			})
				.then(response => {
                    console.log(response);

					useEventBus().emit("reloadProjects");
					useEventBus().emit('reloadCurrentProject');

					// Reset fields
					this.name = "";
					this.website = "";
                    this.canEditProject = false;
					this.endSaving();

                    this.$router.replace("/projects");
				})
				.catch(error => {
					if (error.response.data.message) {
						this.setError(error.response.data.message);
					} else {
						this.setError("An error happened while saving your project.");
					}
					this.endSaving();
				})
		},
		setError(error) {
			this.error = error;
		},
		verifyWebsite(link) {
			let expression = /[-a-zA-Z0-9@:%._+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_+.~#?&//=]*)?/gi;
			let regex = new RegExp(expression);
			return link.match(regex);
		},
        startSaving() {
            this.saving = true;
        },
        endSaving() {
            this.saving = false;
        },
    }
}
</script>

<style lang="scss" scoped>

</style>