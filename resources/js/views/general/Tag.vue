<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <div class="py-6">

                <loading v-if="loading" v-model:active="loading" :is-full-page="true"/>

                <div v-else>
                    <!-- No Image available for tagging -->
                    <div v-if="photos.length === 0" class="hero-body">
                        <div class="container has-text-centered">
                            <h3 class="subtitle is-1">
                                {{ $t('tags.no-tags') }}
                            </h3>
                            <h3 class="subtitle button is-medium is-info hov">
                                <router-link to="/submit">
                                    {{ $t('tags.please-upload') }}
                                </router-link>
                            </h3>
                        </div>
                    </div>

                    <!-- Image is available for tagging -->
                    <div v-else>
                        <div v-for="photo in photos" :key="photo.id"
                             class="max-w-3xl mx-auto sm:px-6 lg:max-w-full lg:px-16 xl:px-32 lg:grid lg:grid-cols-12 lg:gap-4 xl:gap-8"
                        >
                            <div class="block lg:col-span-3 xl:col-span-4">
                                <div class="sticky top-6 p-4 xl:p-8 bg-white overflow-hidden shadow rounded-lg">
                                    <div class="space-y-6">
                                        <div>
                                            <div class="block">
                                                <img :src="photo.filename"
                                                     alt="Photo"
                                                     class="object-contain max-h-128 mx-auto rounded-lg overflow-hidden"
                                                >
                                            </div>
                                            <div class="mt-4 flex items-start justify-between">
                                                <div>
                                                    <h2 class="text-lg font-medium text-gray-900">
                                                        {{ $t('tags.taken') }}: {{ getDate(photo.datetime) }}
                                                    </h2>
                                                    <p class="text-sm font-medium text-gray-500">#{{ photo.id }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div>
                                            <dl class="mt-2 divide-y divide-gray-200">
                                                <div class="py-3 flex flex-wrap justify-between text-sm font-medium">
                                                    <dt class="text-gray-500">{{ $t('tags.address') }}</dt>
                                                    <dd class="text-gray-900">{{ photo.display_name }}</dd>
                                                </div>
                                                <div class="py-3 flex flex-wrap justify-between text-sm font-medium">
                                                    <dt class="text-gray-500">{{ $t('tags.coordinates') }}</dt>
                                                    <dd class="text-gray-900">{{ photo.lat }}, {{ photo.lon }}</dd>
                                                </div>
                                                <div class="py-3 flex flex-wrap justify-between text-sm font-medium">
                                                    <dt class="text-gray-500">{{ $t('tags.device') }}</dt>
                                                    <dd class="text-gray-900">{{ photo.model }}</dd>
                                                </div>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <main class="lg:col-span-6">

                                <div class="bg-white overflow-hidden shadow rounded-lg flex flex-col justify-between">
                                    <div class="px-4 py-5 sm:p-6">
                                        <add-tags-new class="w-full" :id="photo.id"/>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-4 sm:px-6">
                                        <div>
                                            <!-- Previous, Next Image-->
                                            <div class="column" style="text-align: center;">
                                                <div class="has-text-centered mt3em">
                                                    <a
                                                        v-show="previous_page"
                                                        class="pagination-previous has-background-link has-text-white"
                                                        @click="previousImage"
                                                    >{{ $t('tags.previous') }}</a>
                                                    <a
                                                        v-show="remaining > current_page"
                                                        class="pagination-next has-background-link has-text-white"
                                                        @click="nextImage"
                                                    >{{ $t('tags.next') }}</a>
                                                </div>
                                            </div>

                                            <!-- Pagination -->
                                            <div class="column">
                                                <nav class="pagination is-centered" role="navigation" aria-label="pagination">
                                                    <ul class="pagination-list">
                                                        <li v-for="i in remaining" :key="i">
                                                            <a
                                                                :class="(i === current_page ? 'pagination-link is-current': 'pagination-link')"
                                                                :aria-label="'page' + current_page"
                                                                :aria-current="current_page"
                                                                @click="goToPage(i)"
                                                            >{{ i }}</a>
                                                        </li>
                                                    </ul>
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </main>
                            <aside class="block lg:col-span-3 xl:col-span-2">

                                <div class="w-full sticky top-6">
                                    <div class="bg-white overflow-hidden shadow rounded-lg">
                                        <div class="px-4 py-5 sm:p-6 space-y-8">

                                            <ul>
                                                <li class="list-group-item">
                                                    {{ $t('tags.to-tag') }}: {{ remaining }}
                                                </li>
                                                <li class="list-group-item">
                                                    {{ $t('tags.total-uploaded') }}: {{ user.photos_count }}
                                                </li>
                                            </ul>

                                            <submit-button :photo-id="photo.id"></submit-button>

                                            <!-- Presence button. was profile8 -->
                                            <div>
                                                <strong>{{ $t('tags.picked-up-title') }}</strong>
                                                <presence/>
                                            </div>

                                            <!-- Delete photo button -->
                                            <profile-delete :photoid="photo.id"/>

                                            <!-- Clear recent tags -->
                                            <div v-show="hasRecentTags">
                                                <button
                                                    type="button"
                                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                    @click="clearRecentTags"
                                                >
                                                    {{ $t('tags.clear-tags-btn') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </aside>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import moment from 'moment';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.css';
import AddTagsNew from '../../components/Litter/AddTagsNew';
import Presence from '../../components/Litter/Presence';
import Tags from '../../components/Litter/Tags';
import ProfileDelete from '../../components/Litter/ProfileDelete';
import SubmitButton from '../../components/Litter/SubmitButton';

export default {
    name: 'Tag',
    components: {
        SubmitButton,
        Loading,
        AddTagsNew,
        Presence,
        Tags,
        ProfileDelete
    },
    async created ()
    {
        this.loading = true;

        await this.$store.dispatch('GET_PHOTOS_FOR_TAGGING');

        this.loading = false;
    },
    data () {
        return {
            loading: true
        };
    },
    computed: {

        /**
         * Get the current page the user is on
         */
        current_page ()
        {
            return this.$store.state.photos.paginate.current_page;
        },

        /**
         * Return true and show Clear Recent Tags button if the user has recent tags
         */
        hasRecentTags ()
        {
            return Object.keys(this.$store.state.litter.recentTags).length > 0;
        },

        /**
         * Paginated array of the users photos where verification = 0
         */
        photos ()
        {
            return this.$store.state?.photos?.paginate?.data;
        },

        /**
         * URL for the previous page, if it exists.
         */
        previous_page ()
        {
            return this.$store.state.photos.paginate?.prev_page_url;
        },

        /**
         * Number of photos the user has left to verify. Verification = 0
         */
        remaining ()
        {
            return this.$store.state.photos.remaining;
        },

        /**
         * Only show Previous button if current page is greater than 1
         * If current page is 1, then we don't need to show the previous page button.
         */
        show_current_page ()
        {
            return this.$store.state.photos.paginate.current_page > 1;
        },

        /**
         * Only show Previous button if next_page_url exists
         */
        show_next_page ()
        {
            return this.$store.state.photos.paginate.next_page_url;
        },

        /**
         * Total number of photos the user has uploaded. Verification = 0-3
         */
        total ()
        {
            return this.$store.state.photos.total;
        },

        /**
         * Currently authenticated user
         */
        user ()
        {
            return this.$store.state.user.user;
        }
    },

    methods: {

        /**
         * Remove the users recent tags
         */
        clearRecentTags ()
        {
            this.$store.commit('initRecentTags', {});

            this.$localStorage.remove('recentTags');
        },

        /**
         * Format date
         */
        getDate (date)
        {
            return moment(date).format('LLL');
        },

        /**
         * Load a specific page
         */
        goToPage (i)
        {
            this.$store.dispatch('SELECT_IMAGE', i);
        },

        /**
         * Load the next image
         */
        nextImage ()
        {
            this.$store.dispatch('NEXT_IMAGE');
        },

        /**
         * Load the previous page
         */
        previousImage ()
        {
            this.$store.dispatch('PREVIOUS_IMAGE');
        }
    }
};
</script>

<style scoped lang="scss">

.tag-container {
    padding: 0 3em;
}

.image-wrapper {
    text-align: center;
    .image-content {
        position: relative;
        display: inline-block;

        .delete-img{
            position: absolute;
            top: -19px;
            right: -17px;
            font-size: 40px;
        }
    }
}

.taken {
    color: #fff;
    font-weight: 600;
    font-size: 2.5rem;
    line-height: 1.25;
    margin-bottom: 1em;
    text-align: center;
}

@media screen and (max-width: 768px)
{
    .img {
        max-height: 15em;
    }

    .tag-container {
        padding: 0 1em;
    }

    .taken {
        display: none;
    }
}

</style>
