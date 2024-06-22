<template>
	<div class="page-wrapper">
        <h1>Your tags</h1>

        <!-- New Tags -->
        <NewTag/>

        <!-- Loading -->
        <div class="tags-wrapper" v-if="!tags.loaded">
            <TagBox/>
            <TagBox/>
            <TagBox/>
        </div>

        <!-- Tags -->
        <div class="tags-wrapper" v-if="tags.loaded">
            <TagBox :data="tag" v-for="(tag) in tags.items" :key="tag.hashId"></TagBox>
            <p v-if="tags.items.length === 0">You can create custom tags based on your specific needs to easily categorize and organize your submissions.</p>
        </div>
	</div>
</template>

<script>
import { useMainStore } from "@/store";
import repository from "../../repository/repository";
import TagBox from "../../components/items/TagBox";
import NewTag from "./NewTag";

export default {
    name: "TagsView",
    setup() {
        const store = useMainStore();
        return { store }
    },
    metaInfo: {
        title: "Tags"
    },
    components: {NewTag, TagBox},
    created() {
        this.loadTags();
    },
    methods: {
        loadTags() {
            this.store.loadingTags();
            repository.get("/tags")
                .then(response => {
                    this.store.updateTags(response.data.tags);
                })
                .catch(error => {
                    console.log(error);
                })
        },
    },
    computed: {
        tags() {
            return this.store.getTags;
        }
    },
}
</script>


<style lang="scss" scoped>
@import "src/scss/variables";

.page-wrapper {
    @include smartphone {
        background: $white;

        &:after {
            background: $white;
        }
    }
}
</style>