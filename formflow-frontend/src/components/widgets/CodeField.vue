<template>
	<div class="form-group">
		<label v-if="label !== undefined && label !== null">{{ label }}:</label>
        <pre v-html="escapedFormHtml"></pre>
	</div>
</template>

<script>

export default {
    name: "CodeField",
    props: ['code', 'label'],
    data() {
        return {
            finalCode: this.code
        }
    },
    computed: {
        escapedFormHtml() {
        return this.escapeHtml(this.finalCode);
        }
    },
    methods: {
        escapeHtml(html) {
            return html
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    },
}
</script>

<style>
</style>

<style lang="scss" scoped>
@import "src/scss/variables";

pre {
  background-color: #f8f9fa;
  padding: 1em;
  border: 1px solid #dee2e6;
  border-radius: 4px;
  white-space: pre-wrap; /* This allows the text to wrap */
  word-wrap: break-word; /* This forces long words to break and wrap */
}

.form-group {
	label {
		color: $dark;
		font-size: 1rem;
		font-weight: 600;
		margin-bottom: 3px;
		display: block;
	}

	.form-control {
		padding: 15px 10px;
		height: auto;
		border: 1px solid $border-darker-grey;
		background: #fff;

		&:focus {
			box-shadow: none;
			border-color: $primary;
		}
	}

}
</style>