<template>
    <div class="team-map-container">
        <supercluster v-if="geojson" :geojson-data="geojson" :geojson-art-data="[]"/>
    </div>
</template>

<script>
import Supercluster from '../../views/global/Supercluster'

export default {
    name: 'TeamMap',
    components: {
        Supercluster
    },
    async created ()
    {
        this.attribution += new Date().getFullYear();
        await this.$store.dispatch('GET_CLUSTERS', {
            zoom: 2,
            team_id: 1
        });
    },
    computed: {

        /**
         * From backend api request
         */
        geojson ()
        {
            return this.$store.state.teams.geojson
                ? this.$store.state.teams.geojson
                : [];
        }
    },

    methods: {

        fullscreenChange (fullscreen)
        {
            this.fullscreen = fullscreen
        },

        toggle ()
        {
            this.$refs['fullscreen'].toggle()
        },
    }
};
</script>

<style lang="scss">
@import '../../styles/variables.scss';

    /* remove padding on mobile */
    .team-map-container {
        height: 750px;
        margin: 0;
        position: relative;
        padding-top: 1em;
    }

    @include media-breakpoint-down (lg)
    {
        .team-map-container {
            height: 500px;
        }
    }

    @include media-breakpoint-down (sm)
    {
        .team-map-container {
            margin-left: -3em;
            margin-right: -3em;
        }

        .temp-info {
            text-align: center;
            margin-top: 1em;
        }
    }
</style>
