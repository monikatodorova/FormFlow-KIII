import { createRouter, createWebHistory } from 'vue-router'
import { useMainStore } from '@/store';
import HomeView from '../views/HomeView.vue'
import Login from '@/views/LoginView.vue'
import Register from '@/views/RegisterView.vue'
import Projects from '@/views/Projects/ProjectsView.vue'
import CreateProject from '@/views/Projects/CreateProject.vue'
import EditProject from '@/views/Projects/EditProject.vue'
import CreateForm from '@/views/Forms/CreateForm.vue'
import FormSubmissions from '@/views/Forms/FormSubmissions.vue'
import SubmissionsView from '@/views/Submissions/SubmissionsView.vue'
import NoSubmissionSelected from '@/views/Submissions/NoSubmissionSelected.vue'
import SubmissionDetails from '@/views/Submissions/SubmissionDetails.vue'
import TagsView from '@/views/Tags/TagsView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: {
      requireAuth: true,
      requireGuest: false,
    }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: {
      requireAuth: false,
      requireGuest: true,
    },
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: {
      requireAuth: false,
      requireGuest: true,
    },
  },
  {
    path: '/logout',
    name: 'logout',
    component: {
      template: '<div>Logging out...</div>',
      beforeRouteEnter(to, from, next) {
        const store = useMainStore();
        store.logoutUser();
        next('/login');
      }
    },
    meta: {
      requireAuth: true,
      requireGuest: false,
    }
  },
  {
    path: '/projects',
    name: 'projects',
    component: Projects,
    meta: {
      requireAuth: true,
      requireGuest: false,
    },
  },
  {
    path: '/projects/:id',
    name: 'Edit Project',
    component: EditProject,
    meta: {
      requireAuth: true,
      requireGuest: false,
    },
  },
  {
    path: '/projects/new',
    name: 'New Project',
    component: CreateProject,
    meta: {
      requireAuth: true,
      requireGuest: false,
    },
  },
  {
    path: '/forms/:id',
    name: 'Form Submissions',
    component: FormSubmissions,
    meta: {
      requireAuth: true,
      requireGuest: false,
    },
  },
  {
    path: '/forms/new',
    name: 'New Form',
    component: CreateForm,
    meta: {
      requireAuth: true,
      requireGuest: false,
    },
  },
  {
    path: '/submissions',
    name: 'Submissions',
    component: SubmissionsView,
    meta: {
      requireAuth: true,
      requireGuest: false,
    },
    children: [
      {
          path: '/submissions',
          name: 'No Submission',
          component: NoSubmissionSelected,
          meta: {
              requireAuth: true,
              requireGuest: false,
          },
      },
      {
          path: '/submissions/:id',
          name: 'Submission Details',
          component: SubmissionDetails,
          meta: {
              requireAuth: true,
              requireGuest: false,
          },
      },
  ],
  },
  {
    path: '/tags',
    name: 'Tags',
    component: TagsView,
    meta: {
      requireAuth: true,
      requireGuest: false,
    },
  },
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach((to, from, next) => {
  const store = useMainStore();

  if (to.matched.some(record => record.meta.requireAuth)) {
    if(!store.getToken) {
      next({ path: '/login' });
    } else {
      next();
    }
  } else if (to.matched.some(record => record.meta.requireGuest)) {
    if (store.getToken) {
      next({ path: '/' });
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
