<template>
    <div class="tags-wrapper">
        <TagWidget v-for="(tag, index) in tags" :key="index" :data="tag" :submission-id="submissionId" :delete-tag="deleteTag"/>
		<TagsDropdown :tags="tags" :submission-id="submissionId" :add-tag="addTag"></TagsDropdown>
    </div>
</template>

<script>
import TagWidget from "@/components/widgets/TagWidget";
import TagsDropdown from "@/components/widgets/TagsDropdown";

export default {
    name: "TagsWidget",
    components: {TagsDropdown, TagWidget},
    props: ['tags', 'submissionId', 'addTag', 'deleteTag'],
}
</script>