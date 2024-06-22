<template>
  <div class="forms page-wrapper">
    <div class="row">
      <div class="col-md-5">
        <FormForm></FormForm>
      </div>
    </div>
  </div>
</template>
  
<script>
import FormForm from '@/components/forms/FormForm.vue';

export default {
  name: 'ProjectsView',
  props: ['token'],
  components: {FormForm},
}
</script>
  