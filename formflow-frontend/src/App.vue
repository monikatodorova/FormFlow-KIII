<template>
  <div id="app">

    <!-- Body -->
    <div :class="{'page-global-wrapper': true, 'signed-out': !signedIn}">

      <div class="page-global-sidebar">
        <!-- Navidation -->
        <Navigation v-if="signedIn"></Navigation>
      </div>

      <!-- Main Part -->
      <div class="page-global-content">
        <router-view/>
      </div>

    </div>

    <vue3-confirm-dialog></vue3-confirm-dialog>
    
  </div>
</template>

<script>
import { useMainStore } from '@/store';
import Navigation from "@/components/navigation/Navigation.vue"

export default {
  components: {Navigation},
  setup() {
    const store = useMainStore();
    return { store }
  },
  data: function() {
    return {
      modal: null,
    }
  },
  computed: {
    signedIn() {
      return this.store.getToken !== null;
    }
  },
}
</script>

<style lang="scss">
@import 'node_modules/bootstrap/scss/bootstrap';
@import "src/scss/variables";
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

html, body {
	background-color: $white;
	font-family: 'Inter', sans-serif;
	font-size: 14px;

	@include smartphone {
		background: $white;
	}
}

body {
	@include smartphone {
		padding-bottom: 54px;
	}
}

.container-fluid {
	padding-left: 15px !important;
	padding-right: 15px !important;

	@include small-desktop {
		& {
			padding-left: 15px !important;
			padding-right: 15px !important;
		}
	}

	@include tablet {
		& {
			padding-left: 15px !important;
			padding-right: 15px !important;
		}
	}

	@include smartphone {
		& {
			padding-left: 15px !important;
			padding-right: 15px !important;
		}
	}
}

.page-global-wrapper {
    display: flex;
    min-height: 100svh;

    @include tablet {
        display: block;
    }

    @include smartphone {
        display: block;
    }

    .page-global-sidebar {
        background: $white;
        width: 22rem;
        height: 100svh;
        display: flex;
        flex-grow: 0;
        flex-shrink: 0;
        position: sticky;
        top: 0;
        border-right: 1px solid $border-grey;

        @include tablet {
            width: 100%;
            height: auto;
            position: relative;
            z-index: 1;
        }

        @include smartphone {
            width: 100%;
            height: auto;
            position: relative;
            z-index: 1;
        }
    }

    .page-global-content {
        flex-grow: 1;
        width: calc(100% - 22rem);

        @include tablet {
            position: relative;
            z-index: 0;
            width: 100%;
        }

        @include smartphone {
            position: relative;
            z-index: 0;
            width: 100%;
        }
    }

    &.signed-out {
        .page-global-sidebar {
            display: none;
        }
    }
}
</style>
