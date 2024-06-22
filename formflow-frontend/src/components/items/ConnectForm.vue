<template>
	<div class="form-integration">
        <div class="row">
            <div class="col-xl-8">
                <p class="mb-2"><b>Connect your website form to start receiving submissions for your project.</b></p>
                <p>To set up your form correctly, please use this link as the <b>action</b> attribute and set the <b>method</b> attribute to <b class="v2">POST</b>. Additionally, ensure that all fields in your form have a <b>name</b> attribute, and that at least one field is named <b>email</b>.</p>

                <!-- Endpoint -->
                <CopyField class="" label="Form Endpoint" button="Copy link" :content="formLink"></CopyField>
                <p class="small mt-2 mb-4">Please note that there is a limit of 20 input fields per form. If you submit more than 20 fields in a single form, only the first 20 fields will be saved.</p>

                <!-- Example Code -->
                <CodeField class="pt-4" label="Example Code" v-if="formLink" :code="formExample" type="HTML"></CodeField>
            </div>
        </div>
	</div>
</template>

<script>
import CopyField from "@/components/widgets/CopyField";
import CodeField from "@/components/widgets/CodeField";

export default {
    name: 'ConnectForm',
    components: {CodeField, CopyField},
    props: ['formId'],
    data() {
        return {
            loaded: false,
        }
    },
    computed: {
        formLink() {
            if(!this.formId) return "";
            return "http://localhost/FormFlow/formflow-landing/f/" + this.formId;
        },
        formExample() {
            return '<!-- Simple HTML form that accepts Name, Email and Phone number -->\n' +
                '<form action="' + this.formLink + '" method="POST">\n' +
                '  <div class="form-group">\n' +
                '    <label class="form-label" for="name">Your Name</label>\n' +
                '    <input class="form-control" type="text" name="name" id="name" required>\n' +
                '  </div>\n' +
                '  <div class="form-group">\n' +
                '    <label class="form-label" for="email">Your Email</label>\n' +
                '    <input class="form-control" type="email" name="email" id="email" required>\n' +
                '  </div>\n' +
                '  <div class="form-group">\n' +
                '    <label class="form-label" for="phone">Your Phone</label>\n' +
                '    <input class="form-control" type="text" name="phone" id="phone">\n' +
                '  </div>\n' +
                '  <button type="submit" class="btn btn-primary">Submit form</button>\n' +
                '</form>';
        }
    }
}

</script>
