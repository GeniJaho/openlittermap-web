export const actions = {

    /**
     * Get unverified photos for tagging
     */
    async GET_PHOTOS_FOR_TAGGING (context, payload)
    {
        await axios.get('photos')
            .then(response => {
                console.log('get_photos_for_tagging', response);

                context.commit('photosForTagging', response.data);
            })
            .catch(error => {
                console.log('error.photos', error);
            });
    },

    /**
     * Load the next image from pagination
     */
    async NEXT_IMAGE (context)
    {
        await axios.get(context.state.paginate.next_page_url)
            .then(response => {
                console.log('next_image', response);

                context.commit('clearTags');

                context.commit('photosForTagging', response.data);
            })
            .catch(error => {
                console.log('error.next_image', error);
            });
    },

    /**
     * Load the next previous from pagination
     */
    async PREVIOUS_IMAGE (context)
    {
        await axios.get(context.state.paginate.prev_page_url)
            .then(response => {
                console.log('next_image', response);

                context.commit('clearTags');

                context.commit('photosForTagging', response.data);
            })
            .catch(error => {
                console.log('error.next_image', error);
            });
    },

    /**
     * Select page from pagination
     *
     * @payload = pageSelected
     */
    async SELECT_IMAGE (context, payload)
    {
        await axios.get(`/photos?page=${payload}`)
            .then(response => {
                console.log('select_img', response);

                context.commit('clearTags');
                context.commit('photosForTagging', response.data);
            })
            .catch(error => {
                console.log('res', error);
            });
    },
}
