<template>
    <div class="page-wrapper h-100">
      <div class="page-inside-wrapper pt-1" v-if="this.loaded">

        <div class="d-flex justify-content-between align-items-center mb-1">
          <h4 class="form-name">{{ selectedForm.name }}</h4>
          <nav>
            <ul class="nav nav-pills">
              <li class="nav-item" v-for="tab in tabs" :key="tab.name">
                <a class="nav-link" :class="{ active: selectedTab === tab.name }" @click="selectedTab = tab.name" href="#">{{ tab.name }}</a>
              </li>
            </ul>
          </nav>
        </div>

        <hr class="mt-4 mb-4">
  
        <div class="submissions-wrapper-box">
          <div class="row">
            <div class="col-md-12">

              <div v-if="selectedTab === 'Submissions'">
                <!-- Form Submissions tab -->
                <SubmissionsTable :form-id="formId" :status="status"></SubmissionsTable>
              </div>

              <div v-else-if="selectedTab === 'Connect'">
                <!-- Connect tab -->
                <ConnectForm :form-id="formId"></ConnectForm>
              </div>

              <div v-else-if="selectedTab === 'Statistics'">
                <!-- Statistics tab -->
                <FormAnalytics :form-id="formId" :project-id="this.projectId"></FormAnalytics>
                <p>Statistics content goes here...</p>
              </div>

              <div v-else-if="selectedTab === 'Settings'">
                <!-- Settings tab -->
                <FormSettings :form-id="formId"></FormSettings>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </template>

<script>
import { useEventBus } from "@/EventBus";
import { useMainStore } from '@/store';
import repository from "@/repository/repository";
import SubmissionsTable from '@/components/items/SubmissionsTable';
import ConnectForm from '@/components/items/ConnectForm';
import FormAnalytics from '@/components/items/FormAnalytics';
import FormSettings from '@/components/items/FormSettings';
  
export default {
  name: 'FormSubmissions',
  props: ['token'],
  components: {SubmissionsTable, ConnectForm, FormAnalytics, FormSettings},
  setup() {
      const store = useMainStore();
      return { store }
	},
  data() {
      return {
          status: this.$route.query.status !== undefined ? this.$route.query.status : "all",
          selectedTab: 'Submissions',
          tabs: [
              { name: 'Submissions' },
              { name: 'Connect' },
              { name: 'Statistics' },
              { name: 'Settings' }
          ],
          selectedForm: null,
          loaded: false
      }
  },
  computed: {
      formId() {
          return this.$route.params.id;
      },
      projectId() {
          return this.store.getCurrentProject.hashId;
      },
  },
  methods: {
      loadForm() {
          repository.get("/projects/" + this.projectId + "/forms/" + this.formId)
          .then(response => {
              this.selectedForm = response.data;
              this.loaded = true;
          })
      }
  },
  created: function () {
    if(this.projectId === null) {
      useEventBus().emit("reloadCurrentProject");
    } else {
      this.loadForm();
    }
  },
  watch: {
    projectId: function (newValue, oldValue) {
      if(oldValue === null && newValue !== null && this.formId) {
          this.loadForm();
      }
    },
  }
}
</script>
  
<style lang="scss" scoped>
@import "../../scss/variables";

hr {
  border: 1px solid #dee2e6;
}

.page-wrapper {
  padding: 1.5rem;
  position: relative;
  min-height: 100%;
}

.nav-pills .nav-link {
  padding: 10px 15px;

  &.active {
    background-color: $primary;
    color: #fff;
  }
}

.form-name {
  color: $dark;
  font-weight: 600;
  font-size: 1.25rem;
  margin: 0;

  @include tablet {
  font-size: 1.15rem;
  }

  @include smartphone {
  font-size: 1.15rem;
  }
}

.form-settings {
  display: block;
  padding: 1.5rem 1.5rem 0 1.5rem;
  background: $white;

  @include smartphone {
      padding: 0.75rem 0.75rem 0 0.75rem;
  }

  form {
    &.loading {
      position: relative;

      &:after {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        content: ' ';
        z-index: 1;
        background: $white;
      }

      &:before {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        border: 3px solid $border-darker-grey;
        border-right-color: transparent;
        content: ' ';
        margin: auto;
        animation: spinner-border 0.75s linear infinite;
        z-index: 2;
      }
    }

    .form-group {
      position: relative;

      label {
        display: block;
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 3px;
        color: $dark;
      }

      .form-control {
        padding: 15px 10px;
        height: auto;
        border: 1px solid $border-darker-grey;

        &:focus {
          box-shadow: none;
          border-color: $primary;
        }
      }
    }

  }
}

  </style>