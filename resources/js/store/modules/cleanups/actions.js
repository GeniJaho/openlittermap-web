import Vue from "vue";
import i18n from "../../../i18n";

export const actions = {
    /**
     * Create a new cleanup event
     */
    async CREATE_CLEANUP_EVENT (context, payload)
    {
        const title = i18n.t('notifications.success');
        const body = "A new cleanup has been created!";

        await axios.post('/cleanups/create', {
            name: payload.name,
            date: payload.date,
            lat: payload.lat,
            lon: payload.lon,
            time: payload.time,
            description: payload.description,
            inviteLink: payload.inviteLink
        })
        .then(response => {
            console.log('create_cleanup_event', response);

            if (response.data.success)
            {
                Vue.$vToastify.success({
                    title,
                    body,
                });
            }
        })
        .catch(error => {
            console.error('create_cleanup_event', error);
        });
    },

    /**
     * Get GeoJson cleanups object
     */
    async GET_CLEANUPS (context)
    {
        await axios.get('/cleanups/get-cleanups')
            .then(response => {
                console.log('get_cleanups', response);

                if (response.data.success)
                {
                    context.commit('setCleanupsGeojson', response.data.geojson);
                }
            })
            .catch(error => {
                console.error('get_cleanups', error);
            });
    },

    /**
     *  Join a cleanup
     */
    async JOIN_CLEANUP (context, payload)
    {
        const title = i18n.t('notifications.success');
        const body = "You have joined the cleanup!";

        await axios.post(`/cleanups/${payload.link}/join`)
            .then(response => {
                console.log('join_cleanup', response);

                if (response.data.success)
                {
                    Vue.$vToastify.success({
                        title,
                        body,
                    });
                }
                else
                {
                    if (response.data.msg === 'already joined')
                    {
                        Vue.$vToastify.error({
                            title: "Not possible",
                            body: "You have already joined this cleanup. Please refresh the page to see!",
                        });
                    }
                }
            })
            .catch(error => {
                console.error('join_cleanup', error);
            });
    },

    /**
     * Leave a cleanup
     */
    async LEAVE_CLEANUP (context, payload)
    {
        const title = i18n.t('notifications.success');
        const body = "You have left the cleanup";

        await axios.post(`/cleanups/${payload.link}/leave`)
            .then(response => {
                console.log('leave_cleanup', response);

                if (response.data.success)
                {
                    Vue.$vToastify.success({
                        title,
                        body,
                    });
                }
                else {
                    if (response.data.msg === 'already left')
                    {
                        Vue.$vToastify.error({
                            title: "Not possible",
                            body: "You have already left this cleanup. Please refresh the page to see!",
                        });
                    }
                }
            })
            .catch(error => {
                console.error('leave_cleanup', error);
            });
    }
}
