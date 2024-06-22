<template>
	<Dropdown class="color-picker">
		<template v-slot:toggle>
			<div class="current-color">
				<div class="spinner-border" v-if="!loaded"></div>
				<span v-if="loaded && current !== null" :style="{'background': current.color}"></span>
			</div>
		</template>
		<template v-slot:content>
			<div class="dropdown-link" @click.prevent="saveColorChoice(color)" v-for="(color, key) in colors" :key="key">
				<div class="project-color" :style="{'background': color.color}"></div>
				<span>{{ color.name }}</span>
			</div>
		</template>
	</Dropdown>
</template>

<script>
import repository from "@/repository/repository";
import Dropdown from "@/components/widgets/Dropdown";

export default {
    name: "ColorPicker",
    props: ['value'],
    components: {Dropdown},
    data() {
        return {
            colors: [],
            loaded: false,
            current: this.value,
        }
    },
    created() {
        repository.get("/colors")
            .then(response => {
                this.colors = response.data;
                this.loaded = true;
                if (this.current === null) {
                    this.current = this.shuffle([...this.colors])[0];
                } else {
                    this.current = this.colors.filter(item => item.id == this.value)[0];
                }
            })
            .catch(error => {
                console.log(error);
            })
    },
    methods: {
        saveColorChoice(color) {
			console.log(color);
            this.current = color;
			this.$emit('color-selected', this.current.id);
        },
        shuffle(a) {
            let j, x, i;
            for (i = a.length - 1; i > 0; i--) {
                j = Math.floor(Math.random() * (i + 1));
                x = a[i];
                a[i] = a[j];
                a[j] = x;
            }
            return a;
        },
    },
    computed: {
        dropdown() {
            return this.$store.getters.dropdown;
        }
    },
	watch: {
    },
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.current-color {
	width: 51px;
	height: 51px;
	border: 1px solid $border-darker-grey;
	border-radius: 0.75rem;
	background: #fff;
	position: relative;

	&:hover,
	&:focus {
		border-color: $primary;
	}

	span {
		position: absolute;
		top: 10px;
		bottom: 10px;
		left: 10px;
		right: 10px;
		border-radius: $box-border-radius;
		display: block;
	}

	.spinner-border {
		position: absolute;
		top: 0;
		bottom: 0;
		left: 0;
		right: 0;
		margin: auto;
		border-color: $border-darker-grey;
		border-right-color: transparent;
	}
}

.dropdown-link {
	cursor: pointer;

	.project-color {
		display: inline-block;
		vertical-align: middle;
		width: 16px;
		height: 16px;
		border-radius: $box-border-radius;
	}

	span {
		display: inline-block;
		vertical-align: middle;
		width: calc(100% - 21px);
		padding-left: 5px;
		white-space: nowrap;
		text-overflow: ellipsis;
		overflow: hidden;
	}
}
</style>