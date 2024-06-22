<template>
	<div :class="{'tag-item': true, 'deleting': deleting}">
		<strong>{{ data.name }}</strong>
		<button class="btn" @click.prevent="removeTag"></button>
	</div>
</template>

<script>
import repository from "../../repository/repository";

export default {
    name: "TagWidget",
    props: ['data', 'submissionId', 'deleteTag'],
    data() {
        return {
            deleting: false,
        }
    },
    methods: {
        removeTag() {
            this.deleting = true;
            repository.delete("/submissions/" + this.submissionId + "/tags/" + this.tagId)
                .then(() => {
                    this.deleteTag(this.data);
                    this.deleting = false;
                })
                .catch(error => {
                    console.log(error);
                    this.deleting = false;
                })
        }
    },
    computed: {
        tagId() {
            if (!this.data || !this.data.hashId) return null;
            return this.data.hashId;
        }
    },
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.tag-item {
	display: inline-block;
	vertical-align: middle;
	padding: 7.5px 15px;
	border-radius: 25px;
	background: $hover-grey;
	margin-right: 5px;
	margin-bottom: 5px;
	user-select: none;
	position: relative;

	@extend .animated;

    @include smartphone {
        padding: 5px 10px;
    }

	&:hover {
		background: $active-grey;
	}

	strong {
		color: $dark;
		font-weight: 600;
		font-size: 0.9rem;
		display: inline-block;
		vertical-align: middle;
		margin-right: 10px;
		padding-right: 10px;
		border-right: 1px solid $border-dark-grey;
	}

	button.btn {
		padding: 0;
		margin: 0 -5px 0 0;
		display: inline-block;
		vertical-align: middle;
		position: relative;
		width: 14px;
		height: 14px;
		opacity: 0.75;

		&:hover {
			opacity: 1;
		}

		&:before {
			position: absolute;
			top: 0;
			left: 0;
			width: 100%;
			height: 2px;
			background: $border-darker-grey;
			content: ' ';
			transform-origin: top left;
			transform: rotate(45deg);
		}

		&:after {
			position: absolute;
			top: 0;
			left: -5px;
			width: 100%;
			height: 2px;
			background: $border-darker-grey;
			content: ' ';
			transform-origin: top right;
			transform: rotate(-45deg);
		}
	}

	&.deleting {
		&:after {
			position: absolute;
			bottom: 0;
			left: 0;
			width: 100%;
			height: 100%;
			content: ' ';
			z-index: 1;
			background: $background-grey;
			border-radius: 25px;
		}

		&:before {
			position: absolute;
			top: 0;
			bottom: 0;
			left: 0;
			right: 0;
			width: 15px;
			height: 15px;
			border-radius: 50%;
			border: 2px solid $border-darker-grey;
			border-right-color: transparent;
			content: ' ';
			margin: auto;
			animation: spinner-border 0.75s linear infinite;
			z-index: 2;
		}
	}
}
</style>