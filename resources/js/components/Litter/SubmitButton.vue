<template>
    <button
        type="button"
        :disabled="checkTags"
        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-300"
        :class="checkTags ? 'cursor-not-allowed bg-green-300 hover:bg-green-300' : 'bg-green-400 hover:bg-green-500'"
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
            return this.processing ? 'is-loading' : '';
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
