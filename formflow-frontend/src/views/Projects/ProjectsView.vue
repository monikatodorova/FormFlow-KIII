<template>
  <div class="page-wrapper">
		<h1>Projects</h1>
		<div class="row">

			<!-- Loading effect -->
			<div class="col-md-4 col-lg-4 col-xl-3" v-if="!projects.loaded">
				<ProjectBox></ProjectBox>
			</div>

			<div class="col-md-4 col-lg-4 col-xl-3" v-for="(project, key) in projects.items" :key="key">
				<ProjectBox :project="project"></ProjectBox>
			</div>

		</div>
	</div>
</template>
  
<script>
import { useEventBus } from '@/EventBus';
import { useMainStore } from '@/store';
import ProjectBox from "@/components/items/ProjectBox";


export default {
  name: 'ProjectsView',
  props: ['token'],
  components: {ProjectBox},
  setup() {
        const store = useMainStore();
        return { store }
    },
  computed: {
      projects() {
        return this.store.getProjects;
      },
      currentProject() {
        return this.store.getCurrentProject;
      }
  },
  mounted() {
    useEventBus().emit('reloadProjects');
    useEventBus().emit('reloadCurrentProject');
  }
}
</script>

<style lang="scss" scoped>
.page-wrapper {
  padding: 1.5rem;
  position: relative;
  min-height: 100%;
}
.projects {
  .project-box {
    display: block;
    background: #fff;
    border-radius: .75rem;
    padding: 1.5rem;
    box-shadow: 0 2px 3px hsla(0,0%,4%,.15);
    position: relative;
    overflow: hidden;
  }
}
</style>