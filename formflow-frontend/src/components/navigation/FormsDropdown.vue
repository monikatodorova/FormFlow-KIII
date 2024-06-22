<template>
    <div class="forms-dropdown">
        <Dropdown :class="{'nav-item project-item bottom-item': true, 'loading': !forms.loaded}">

            <template v-slot:toggle>
                <div class="nav-link">
                    <img src="@/assets/icons/forms.svg" alt="Forms">
                    <span>Forms</span>
                </div>
            </template>

            <template v-slot:content>
                <h4 class="dropdown-title">Your forms</h4>
                
                <div class="spinner-border" v-if="!forms.loaded"></div>

                <router-link :to="'/forms/' + item.hashId" v-for="(item, key) in forms.items" :key="key" class="form-details">
                    <div class="form-color-line" :style="{'backgroundColor': item.color.color}"></div>
                    <div class="form-name">{{ item.name }}</div>
                </router-link>

                <router-link to="/forms/new" class="btn btn-primary d-block w-100 mt-3 mb-2">Create new form</router-link>
            </template>

        </Dropdown>
    </div>
</template>

<script>
import { useEventBus } from "@/EventBus";
import { useMainStore } from '@/store';
import repository from "@/repository/repository";
import Dropdown from "@/components/widgets/Dropdown";

export default {
    name: "FormsDropdown",
    components: {Dropdown},
    setup() {
        const store = useMainStore();
        return { store }
    },
    created() {
        useEventBus().on('reloadForms', () => {
            this.loadForms();
        });
    },
    computed: {
        forms() {
            return this.store.getForms;
        },
        currentProject() {
            return this.store.getCurrentProject;
        },
    },
    methods: {
        loadForms() {
            if (!this.currentProject.loaded) return;
            repository.get("/projects/" + this.currentProject.hashId + "/forms")
                .then(response => {
                    this.store.updateForms(response.data.forms);
                })
                .catch(error => {
                    console.log(error);
                })
        },
    },
    watch: {
        'store.state.currentProject.hashId': function() {
            if(this.currentProject.loaded) this.loadForms();
        }
    },
    mounted() {
        this.loadForms();
    }
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.forms-dropdown {
    
    .dropdown-title {
        font-weight: 600;
        color: $dark;
        font-size: .9rem;
        margin-top: 15px;
    }

    .form-details {
        display: flex;
        align-items: center;
        padding: 8px 10px;
        margin: 5px -10px;
        border-radius: 0.5rem;
        text-decoration: none;

        .form-name {
            font-weight: 400;
            color: #666;
            font-size: 1rem;
            display: block;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            margin: 0;
            text-decoration: none;
        }

        .form-color-line {
            width: 1.2rem;
            height: 1.2rem;
            display: block;
            margin-right: 0.5rem;
            border-radius: 0.75rem;
            flex-shrink: 0;
        }

        &:hover {
            background: #eee;
            cursor: pointer;
        }
    }
}
</style>