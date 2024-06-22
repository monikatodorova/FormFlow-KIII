<template>
    <div class="projects page-wrapper">
      <div class="row">
        <div class="col-md-5">
          <ProjectForm></ProjectForm>
        </div>
      </div>
    </div>
  </template>
  
  <script>
import ProjectForm from '@/components/forms/ProjectForm.vue';

import { useMainStore } from '@/store';

  export default {
    name: 'ProjectsView',
    props: ['token'],
    components: {ProjectForm},
    setup() {
        const store = useMainStore();
        return {
            store
        }
    },
    computed: {
        projects() {
            this.$root.$emit('reloadProjects');
            return this.store.getProjects;
        },
        currentProject() {
            return this.store.getCurrentProject;
        }
    }
  }
  </script>
  