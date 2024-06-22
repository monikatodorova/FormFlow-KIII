<template>
	<form class="tag-form-wrapper" @submit.prevent="createTag">
		<div :class="{'tag-form': true, 'saving': saving}">
			<input type="text" v-model="newTag" class="form-control" placeholder="Create new tag.." maxlength="25">
			<button>Press <strong>Enter</strong> to create tag</button>
		</div>
		<div class="alert alert-danger p-0 m-0 mt-2" v-show="error !== null">{{ error }}</div>
	</form>
</template>

<script>
import repository from "../../repository/repository";
import { useMainStore } from "@/store";

export default {
    name: "NewTag",
	setup() {
        const store = useMainStore();
        return { store }
    },
    data() {
        return {
            newTag: "",
            error: null,
            saving: false,
        }
    },
    methods: {
        createTag() {
            if (!this.newTag || this.newTag.trim().length < 3 || this.newTag.trim().length > 25) {
                this.error = "The tag must be between 3 and 25 characters. Special symbols are not allowed.";
                return false;
            }
            this.saving = true;
            repository.post("/tags", {name: this.newTag})
                .then(response => {
                    this.newTag = "";
                    this.error = null;
                    this.saving = false;
					this.store.addTag(response.data.tag);
                })
                .catch(error => {
                    this.saving = false;
                    this.error = error.response.data.message;
                })
        },
    },
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.tag-form-wrapper {
	margin-bottom: 1.5rem;
}

.tag-form {
	background: $white;
    border: 1px solid $border-grey;
	border-radius: $box-border-radius;
	position: relative;
	@extend .animated;

    &:hover,
    &:focus-within {
        background: $hover-grey;
        border-color: $hover-grey;

		button {
			opacity: 1;
		}
	}

	.form-control {
		height: auto;
		padding: 15px 15px;
		border: none;
		background: none;
		box-shadow: none;
		outline: none;
		border-radius: $box-border-radius;
		z-index: 1;
	}

	button {
		position: absolute;
		top: 0;
		bottom: 0;
		right: 0;
		padding: 0 10px;
		font-size: 0.9rem;
		color: $grey-text;
		border-radius: $box-border-radius;
		border: none;
		background: none;
		opacity: 0.25;
		outline: none;
		z-index: 2;
		@extend .animated;
	}

	&.saving {
		&:before {
			position: absolute;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			width: 100%;
			height: 100%;
			background: rgba($background-white, 0.85);
			z-index: 3;
			border-radius: $box-border-radius;
			content: ' ';
		}

		&:after {
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			width: 20px;
			height: 20px;
			border-radius: 50%;
			border: 3px solid $border-darker-grey;
			border-right-color: transparent;
			content: ' ';
			margin: auto;
			animation: spinner-border 0.75s linear infinite;
			z-index: 4;
		}
	}
}
</style>