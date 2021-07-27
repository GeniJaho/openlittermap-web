<template>
    <button
        :disabled="checkTags"
        :class="button"
        @click="submit"
    >{{ $t('common.submit') }}</button>
</template>

<script>
export default {
    name: 'SubmitButton',
    props: ['photoId'],
    data() {
        return {
            processing: false,
        }
    },
    computed: {
        /**
         * Show spinner when processing
         */
        button ()
        {
            return 'button is-medium is-success' + (this.processing ? ' is-loading' : '');
        },

        /**
         * Disable button if true
         */
        checkTags ()
        {
            if (this.processing) return true;

            return Object.keys(this.$store.state.litter.tags[this.photoId] || {}).length === 0;
        }
    },
    methods: {
        /**
         * Submit the image for verification
         */
        async submit ()
        {
            this.processing = true;

            await this.$store.dispatch('ADD_TAGS_TO_IMAGE');

            this.processing = false;
        }
    }
};
</script>

<style scoped>

</style>
