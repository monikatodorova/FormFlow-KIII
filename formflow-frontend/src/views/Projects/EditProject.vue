<template>
    <div class="projects page-wrapper">
        <EditProjectForm :project-id="projectId"></EditProjectForm>
    </div>
</template>

<script>
import EditProjectForm from '@/components/forms/EditProjectForm.vue';

export default {
    name: 'ProjectsView',
    components: {EditProjectForm},
    data() {
        return {
            projectId: this.$route.params.id,
        }
    },
    watch: {
        "$route.params.id"(val) {
            this.projectId = val;
        },
    },
}
</script>
  