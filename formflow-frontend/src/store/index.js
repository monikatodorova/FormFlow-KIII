import { defineStore } from 'pinia';

export const useMainStore = defineStore('main', {
  state: () => ({
    user: {
      token: localStorage.getItem("token"),
      name: null,
      email: null,
      avatar: {
        credentials: null,
        color: null,
        text: null,
      },
      loaded: false,
    },
    projects: {
      items: [],
      loaded: false,
    },
    currentProject: {
      hashId: null,
      name: null,
      loaded: false,
    },
    dropdown: null,
    forms: {
      items: [],
      loaded: false,
    },
    tags: {
      items: [],
      loaded: false,
    }
  }),
  getters: {
    getUser: (state) => state.user,
    getToken: (state) => state.user.token,
    getProjects: (state) => state.projects,
    getCurrentProject: (state) => state.currentProject,
    getDropdown: (state) => state.dropdown,
    getForms: (state) => state.forms,
    getTags: (state) => state.tags,
  },
  actions: {
    updateUser(userPayload) {
      this.user.name = userPayload.name;
      this.user.email = userPayload.email;
      this.user.avatar = userPayload.avatar;
      this.user.loaded = true;
    },
    updateUserToken(token) {
      this.user.token = token;
      localStorage.setItem("token", token);
    },
    logoutUser() {
      localStorage.removeItem("token");
      this.user.token = null;
      this.user.name = null;
      this.user.email = null;
      this.user.avatar = null;
      this.projects.loaded = false;
    },
    updateProjects(projectsPayload) {
      this.projects.items = projectsPayload;
      this.projects.loaded = true;
    },
    updateCurrentProject(project) {
      this.currentProject.hashId = project.hashId;
      this.currentProject.name = project.name;
      this.currentProject.loaded = true;
    },
    clearDropdown() {
      this.dropdown = null;
    },
    saveDropdown(dropdown) {
      if(this.dropdown !== null) {
        this.dropdown.hideDropdown();
      }
      this.dropdown = dropdown;
    },
    updateForms(formsPayload) {
      this.forms.items = formsPayload;
      this.forms.loaded = true;
    },
    updateTags(tagsPayload) {
      this.tags.items = tagsPayload;
      this.tags.loaded = true;
    },
    loadingTags() {
      this.tags.items = [];
      this.tags.loaded = false;
    },
    addTag(tag) {
      this.tags.items.push(tag);
    },
    updateTag(tag) {
      let index = this.tags.items.findIndex(t => t.hashId === tag.hashId);
      this.tags.items[index].name = tag.name;
    },
    deleteTag(tag) {
      let index = this.tags.items.findIndex(t => t.hashId === tag.hashId);
      this.tags.items.splice(index, 1);
    },
  }
});
