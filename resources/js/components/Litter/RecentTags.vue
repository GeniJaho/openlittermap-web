<template>
    <div>
        <div class="relative">
            <div class="absolute inset-0 px-4 flex items-center" aria-hidden="true">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center">
                <span class="px-3 bg-white text-lg font-medium text-gray-900">
                  {{ $t('tags.recently-tags') }}
                </span>
            </div>
        </div>

        <!-- Tags list (only on smallest breakpoint) -->
        <div class="sm:hidden">
            <ul class="mt-3 divide-y divide-gray-100">
                <li v-for="category in Object.keys(recentTags)">
                    <div class="group flex items-center justify-between py-2 hover:bg-gray-50">
                      <span class="flex flex-wrap items-center">
                        <span class="w-2.5 h-2.5 flex-shrink-0 mr-2 my-1 rounded-full bg-pink-600" aria-hidden="true"></span>
                        <span class="font-medium mr-2 my-1 text-sm leading-6">
                          {{ getCategoryName(category) }}
                        </span>
                          <Badge
                              v-for="tag in Object.keys(recentTags[category])"
                              :key="tag"
                              :title="getTagName(category, tag)"
                              :show-icon="false"
                              @click="addTag(category, tag)"
                          ></Badge>
                      </span>
                    </div>
                </li>
            </ul>
        </div>

        <!-- Tags list (small breakpoint and up) -->
        <div class="hidden sm:block">
            <div class="align-middle inline-block min-w-full px-4">
                <table class="min-w-full">
                    <tbody class="bg-white divide-y divide-gray-100">
                    <tr v-for="category in Object.keys(recentTags)">
                        <td class="py-3 max-w-0 w-full whitespace-nowrap text-sm font-medium text-gray-900">

                            <div class="flex flex-wrap items-center">

                                <div class="flex-shrink-0 w-2.5 h-2.5 mr-3 my-1 rounded-full bg-pink-600" aria-hidden="true"></div>
                                <span class="mr-3 my-1">{{ getCategoryName(category) }}</span>

                                <div class="flex flex-wrap items-center">
                                    <Badge
                                        v-for="tag in Object.keys(recentTags[category])"
                                        :key="tag"
                                        :title="getTagName(category, tag)"
                                        :show-icon="false"
                                        @click="addTag(category, tag)"
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
    name: 'RecentTags',
    components: {Badge},
    props: ['recentTags'],
    methods: {
        /**
         * Return translated category name for recent tags
         */
        getCategoryName (category)
        {
            return this.$i18n.t(`litter.categories.${category}`);
        },

        /**
         * Return translated litter.key name for recent tags
         */
        getTagName (category, tag)
        {
            return this.$i18n.t(`litter.${category}.${tag}`);
        },

        addTag(category, tag) {
            this.$emit('addRecentTag', category, tag);
        }
    }
};
</script>

<style scoped>

</style>
