<template>
    <Dropdown :class="{'nav-item project-item': true, 'loading': !projects.loaded || !currentProject.loaded}">

        <template v-slot:toggle>
            <div class="nav-link">
                <span class="spinner-border" v-if="!projects.loaded || !currentProject.loaded"></span>
				<span v-if="projects.loaded && currentProject.loaded">{{ currentProject.name }}</span>
			</div>
        </template>

        <template v-slot:content>
            <h4>Your projects</h4>
			<div class="spinner-border" v-if="!projects.loaded || !currentProject.loaded"></div>
            <div class="projects-wrapper">
                <div @click.prevent="changeCurrentProject(project)" :class="{'dropdown-link': true, 'selected': project.hashId === currentProject.hashId}" v-for="(project, key) in projects.items" v-bind:key="key">
                    <div class="project-color" :style="{'background': getColor(project)}"></div>
                    <span>{{ project.name }}</span>
                </div>
            </div>
			<router-link to="/projects/new" class="btn btn-primary d-block w-100 mt-2 mb-2">Create new project</router-link>
			<router-link to="/projects" class="btn btn-outline-dark d-block w-100 mt-2 mb-2">Manage projects</router-link>
        </template>

    </Dropdown>
</template>

<script>
import { useEventBus } from "@/EventBus";
import { useMainStore } from '@/store';
import repository from "@/repository/repository";
import Dropdown from "@/components/widgets/Dropdown";

export default {
    name: "ProjectsDropdown",
    components: {Dropdown},
    data() {
        return {}
    },
    setup() {
        const store = useMainStore();
        return { store }
    },
    created() {
        if(!this.projects.loaded) {
            this.loadProjects();
        }
        
        if(!this.currentProject.loaded) {
            this.loadCurrentProject();
        }

        useEventBus().on('reloadCurrentProject', () => {
            this.loadCurrentProject();
        });

        useEventBus().on('reloadProjects', () => {
            this.loadProjects();
        })
    },
    computed: {
        projects() {
            return this.store.getProjects;
        },
        currentProject() {
            return this.store.getCurrentProject;
        },
    },
    methods: {
        getColor(project) {
            if (!project.color) return "rgba(0,0,0,0.1)";
            return project.color.color;
        },
        loadProjects() {
            repository.get("/projects")
                .then(response => {
                    this.store.updateProjects(response.data.projects);
                })
                .catch(error => {
                    console.log(error);
                })
        },
        loadCurrentProject() {
            repository.get("/me")
                .then(response => {
                    console.log(response);
                    this.store.updateCurrentProject(response.data.user.default_project);
                    useEventBus().emit('reloadForms');
                })
        },
        changeCurrentProject(project) {
            this.store.updateCurrentProject(project);
            repository.post("/default-project", { projectId: project.hashId })
                .then(() => {
                    useEventBus().emit('reloadForms');
                });
        },
    },
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.project-item {
    width: 100%;
    margin-top: 1rem;
    margin-bottom: 1rem;

    @include tablet {
        width: auto;
        margin: 0;
        max-width: 14rem;
    }

    @include smartphone {
        margin: 0 auto 0 1rem;
        flex-grow: 0;
        position: static;
        flex-shrink: 1;
        max-width: 10rem;
    }

    .nav-link {
        font-weight: 600;
        color: $primary;
        width: 100%;
        padding: 1rem 1rem;
        border-radius: 0.75rem;
        background: lighten($primary, 50%);
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        min-height: 3.65rem;

        @include tablet {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            min-height: 2.5rem;
        }

        @include smartphone {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            min-height: 2.5rem;
        }

        &:hover {
            background: lighten($primary, 48%);
        }

        &:after {
            margin-left: auto !important;
            display: block !important;
            z-index: 3;
            flex-shrink: 0;
        }

        span {
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            padding-right: 1rem;
        }

        .spinner-border {
            width: 20px;
            height: 20px;
            border-color: $dark;
            border-width: 2px;
            opacity: 0.2;
            border-right-color: transparent;
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 1;

			@include smartphone {
				border-color: rgba($white, 0.5);
				border-right-color: transparent;
			}
        }
    }

    &.loading {
        .nav-link:after {
            opacity: 0 !important;
        }
    }
}

.dropdown-wrapper {
    .dropdown-menu {
        min-width: 100%;
        max-width: 100%;
        width: 100%;

        .projects-wrapper {
            max-height: 200px;
            overflow-x: hidden;
            padding: 0 20px;
            width: calc(100% + 40px);
            margin: 0 -20px;
        }

        .dropdown-link {
            cursor: pointer;
            padding: 8px 10px;

            .project-color {
                display: inline-block;
                vertical-align: middle;
                width: 16px;
                height: 16px;
                border-radius: $box-border-radius;
            }

            span {
                display: inline-block;
                vertical-align: middle;
                width: calc(100% - 21px);
                padding-left: 5px;
                white-space: nowrap;
                text-overflow: ellipsis;
                overflow: hidden;
            }

            &.selected {
                background: rgba($primary, 0.15);
                color: $primary;
                border-radius: 10px;

                .project-color {
                    background: $primary !important;
                }
            }
        }
    }
}
</style>