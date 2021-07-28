<template>
    <div>
        <div class="relative" v-if="categories.length">
            <div class="absolute inset-0 sm:px-4 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="px-3 bg-white text-lg font-medium text-gray-900">
                  Added tags
                </span>
            </div>
        </div>

        <!-- Tags list (only on smallest breakpoint) -->
        <div v-if="categories.length" class="sm:hidden">
            <ul class="mt-3 divide-y divide-gray-100">
                <li v-for="category in categories">
                    <div class="group flex items-center justify-between py-2 hover:bg-gray-50">
                      <span class="flex flex-wrap items-center">
                        <span class="w-2.5 h-2.5 flex-shrink-0 mr-2 my-1 rounded-full bg-pink-600" aria-hidden="true"></span>
                        <span class="font-medium mr-2 my-1 text-sm leading-6">
                          {{ getCategory(category.category) }}
                        </span>
                          <Badge
                              v-for="tagItem in Object.entries(category.tags)"
                              :key="tagItem.id"
                              :title="getTags(tagItem, category.category)"
                              @click="removeTag(category.category, tagItem[0])"
                          ></Badge>
                      </span>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Tags list (small breakpoint and up) -->
        <div v-if="categories.length" class="hidden sm:block">
            <div class="align-middle inline-block min-w-full px-4">
                <table class="min-w-full">
                    <tbody class="bg-white divide-y divide-gray-100">
                    <tr v-for="category in categories">
                        <td class="py-3 max-w-0 w-full whitespace-nowrap text-sm font-medium text-gray-900">

                            <div class="flex flex-wrap items-center">

                                <div class="flex-shrink-0 w-2.5 h-2.5 mr-3 my-1 rounded-full bg-pink-600" aria-hidden="true"></div>
                                <span class="mr-3 my-1">{{ getCategory(category.category) }}</span>

                                <div class="flex flex-wrap items-center">
                                    <Badge
                                        v-for="tagItem in Object.entries(category.tags)"
                                        :key="tagItem.id"
                                        :title="getTags(tagItem, category.category)"
                                        @click="removeTag(category.category, tagItem[0])"
                                    ></Badge>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
import Badge from './Badge';
export default {
    name: 'TagsNew',
    components: {Badge},
    props: ['photoId'],
    computed: {

        /**
         * Categories from the tags object the user has created
         */
        categories ()
        {
            let categories = [];

            Object.entries(this.$store.state.litter.tags[this.photoId] || {}).map(entries => {
                if (Object.keys(entries[1]).length > 0)
                {
                    categories.push({
                        category: entries[0],
                        tags: entries[1]
                    });
                }
            });

            return categories;
        },
    },
    methods: {

        /**
         * Return translated value for category key
         */
        getCategory (category)
        {
            return this.$i18n.t('litter.categories.' + category);
        },

        /**
         * Return Translated key: value from tags[0]: tags[1]
         */
        getTags (tags, category)
        {
            return this.$i18n.t('litter.' + category + '.' + tags[0]) + ': ' + tags[1];
        },

        /**
         * Remove tag from this category
         * If all tags have been removed, delete the category
         */
        removeTag (category, tag_key)
        {
            this.$store.commit('removeTag', {
                photoId: this.photoId,
                category,
                tag_key
            });
        }
    }
};
</script>

<style scoped>

</style>
