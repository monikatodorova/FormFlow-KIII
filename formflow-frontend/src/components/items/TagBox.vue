<template>
    <div :class="{'tag-box': true, 'loading': !loaded}">
        <div class="loading" v-if="!loaded">
            <div class="spinner-border"></div>
        </div>
        <div v-if="loaded">
            <input v-show="loaded" type="text" v-model="tag.name" class="form-control" @change="updateTag" :size="tag.name.length + 1" minlength="3" maxlength="25">
            <button type="button" class="btn btn-icon" @click.prevent="deleteTag">
                <img src="@/assets/icons/trash.svg" alt="Delete">
            </button>
        </div>
    </div>
</template>

<script>
import repository from "../../repository/repository";

import { useMainStore } from "@/store";

export default {
    name: "TagBox",
    props: ['data'],
    setup() {
        const store = useMainStore();
        return {
            store
        }
    },
    data() {
        return {
            tag: this.data,
            deleting: false,
            updating: false,
        }
    },
    methods: {
        updateTag() {
            if (this.updating) return;
            this.updating = true;
            repository.put("/tags/" + this.tag.hashId, {name: this.tag.name})
                .then((response) => {
                    this.updating = false;
                    // this.$store.commit("updateTag", {id: this.tag.hashId, name: response.data.tag.name});
                    this.store.updateTag({id: this.tag.hashId, name: response.data.tag.name});
                })
                .catch(error => {
                    console.log(error);
                    this.updating = false;
                })
        },
        deleteTag() {
            if (this.deleting) return;
            this.$confirm({
                title: "Delete #" + this.tag.name + "?",
                message: "This will permanently remove the tag from the system and all of the leads that you have connected it to.",
                button: {
                    yes: "Yes, continue",
                    no: "No, cancel"
                },
                callback: confirm => {
                    if (confirm) {
                        this.deleting = true;
                        repository.delete("/tags/" + this.tag.hashId)
                            .then(() => {
                                this.deleting = false;
                                // this.$store.commit("deleteTag", this.tag);
                                this.store.deleteTag(this.tag);
                            })
                            .catch(error => {
                                console.log(error);
                                this.deleting = false;
                            })
                    }
                }
            })
        }
    },
    computed: {
        loaded() {
            return this.tag !== undefined;
        },
    },
    watch: {
        data: function (newData) {
            this.tag = newData;
        }
    },
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.tag-box {
    display: inline-flex;
    flex-direction: row;
    align-items: center;
    justify-content: space-between;
    position: relative;
    border-radius: $box-border-radius;
    background: $background-grey;
    padding: 0;
    margin: 0 10px 10px 0;
    @extend .disable-selection;
    @extend .animated;

    &:hover {
        background: darken($background-grey, 2.5%);

        button.btn.btn-icon {
            opacity: 1;
            visibility: visible;
        }
    }

    .loading {
        padding: 10px 25px;

        .spinner-border {
            width: 16px;
            height: 16px;
            border-color: $border-dark-grey;
            border-right-color: transparent;
        }
    }

    button.btn.btn-icon {
        position: absolute;
        top: 0;
        bottom: 0;
        right: 5px;
        margin: auto;
        padding: 2px;
        width: 24px;
        height: 24px;
        border-radius: $box-border-radius;
        text-align: center;
        line-height: 16px;
        opacity: 0;
        visibility: hidden;
        @extend .animated;

        &:hover {
            background: $hover-grey;
        }

        &:active {
            background: $active-grey;
        }

        img {
            width: 14px;
            filter: grayscale(1);
            opacity: 0.5;
        }
    }

    input.form-control {
        margin: 0;
        padding: 10px 15px;
        font-size: 1rem;
        font-weight: 600;
        color: $dark;
        border: none;
        outline: none;
        background: none;
        height: auto;
        width: auto;
        min-width: auto;
        display: inline-block;
        @extend .animated;

        &:focus {
            box-shadow: none;
            outline: none;
            color: $primary;
        }
    }
}
</style>