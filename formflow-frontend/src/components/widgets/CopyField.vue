<template>
	<div class="form-group copy-field">
		<label>{{ label }}:</label>
		<input type="text" class="form-control" readonly :value="content" ref="input">
		<button class="btn btn-primary" @click="copy">
			{{ buttonText }}
		</button>
	</div>
</template>

<script>
export default {
    name: "CopyField",
    props: ['label', 'button', 'content'],
    data() {
        return {
            copied: false,
        }
    },
    methods: {
        copy() {
            this.$refs.input.select();
            document.execCommand('copy');
            this.copied = true;
            let app = this;
            setTimeout(function () {
                app.copied = false;
            }, 2000);
        }
    },
    computed: {
        buttonText() {
            if (this.copied) return "Copied!";
            return this.button;
        }
    },
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.copy-field {
	position: relative;

	label {
		display: block;
		font-size: 1rem;
		font-weight: 600;
		margin-bottom: 3px;
		color: $dark;
	}

	.form-control {
		padding: 15px 130px 15px 10px;
		height: auto;
        border-radius: $box-border-radius;
		border: 1px solid $border-grey;
		background: $border-grey;

		&:focus {
			box-shadow: none;
			border-color: $primary;
		}
	}

	.btn {
		position: absolute;
		bottom: 5px;
		right: 5px;
		width: 120px;
	}
}
</style>