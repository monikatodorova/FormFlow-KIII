<template>
    <div class="form-group recipients-field">
        <label>Recipients</label>
        <form action="#" method="post" class="mb-3" @submit.prevent="saveRecipient" v-if="recipientsEmails.length <= 2">
            <label for="recipient" class="recipients-label" v-if="recipientsEmails.length <= 2"></label>
            <input type="email" class="form-control" placeholder="Enter recipient email" v-model="email" id="recipient">
            <button type="submit" class="btn btn-sm btn-keyboard" v-if="email && email.length > 0">↳ Enter</button>
        </form>
        <ul class="recipients-list" v-if="recipientsEmails.length > 0">
            <li v-for="(email, key) in recipientsEmails" :key="key">
                <strong>{{ email }}</strong>
                <button class="btn btn-icon btn-sm" @click.prevent="deleteRecipient(email)">
                    <img src="@/assets/icons/trash.svg" alt="Delete">
                </button>
            </li>
        </ul>
        <p class="small">Please enter up to 3 email addresses of the recipients who should be notified when a submission is received through this form.</p>
    </div>
</template>

<script>
export default {
    name: "RecipientsField",
    props: [ 'value' ],
    data() {
        return {
            email: null,
            recipients: this.value,
        }
    },
    methods: {
        deleteRecipient(email) {
            this.recipients.splice(this.recipients.findIndex(recipient => recipient.email === email), 1);
            // TODO: Propagate changes to main component
            // this.$emit('input', this.recipients);
        },
        saveRecipient() {
            if(!this.email || this.email.trim().length <= 0) {
                return;
            }
            this.recipients.push({
                id: -1,
                email: this.email,
            });
            this.email = null;
            // TODO: Propagate changes to main component
            // this.$emit('input', this.recipients);
        },
    },
    computed: {
        recipientsEmails() {
            if(!this.recipients) return [];
            return this.recipients.map(recipient => recipient.email);
        },
    },
}
</script>

<style lang="scss" scoped>
@import "src/scss/variables";

.recipients-field {
    .recipients-list {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        margin: 0;
        padding: 0;

        li {
            display: block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            background: rgba($dark, 0.1);
            margin-bottom: 0.25rem;
            margin-right: 0.25rem;
            position: relative;
            z-index: 1;
            @extend .disable-selection;

            strong {
                font-weight: 600;
                font-size: 0.9rem;
                color: $dark;
            }

            .btn.btn-icon {
                padding: 0;
                margin-left: 0.25rem;
                margin-top: -0.15rem;
                opacity: 0.25;
                line-height: 1;
                transition: 0.15s ease all;

                &:hover {
                    opacity: 0.75;
                }

                img {
                    width: 1rem;
                    height: 1rem;
                    filter: grayscale(1);
                }
            }
        }
    }

    form {
        .btn.btn-keyboard {
            position: absolute;
            top: 2.65rem;
            right: 0.5rem;
            background: darken($background-grey, 2.5%);
            color: $dark;
            font-size: 0.8rem;
            font-weight: 600;
            border-radius: 0.5rem;
            text-transform: uppercase;

            &:hover {
                background: darken($background-grey, 5%);
            }

            &:active {
                background: darken($background-grey, 7.5%);
                transform-origin: center;
                transform: scale(0.9);
            }
        }
    }
}
</style>