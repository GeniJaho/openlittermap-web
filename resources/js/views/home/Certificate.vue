<template>
    <div>
        <div id="map" ref="map"/>
    </div>
</template>

<script>
import L from 'leaflet';
import 'leaflet-timedimension'
import "leaflet-timedimension/dist/leaflet.timedimension.control.css"
import {mapHelper} from '../../maps/mapHelpers';

export default {
    name: 'Certificate',
    data() {
        return {
            certificate: null,
            map: null,
            pointsLayer: null,
            timeLayer: null,
            player: null
        }
    },
    async mounted ()
    {
        await this.load();

        /** 1. Create map object */
        this.map = L.map('map', {
            center: [0, 0],
            zoom: 2,
            scrollWheelZoom: false,
            smoothWheelZoom: true,
            smoothSensitivity: 1,
        });

        // /** 2. Add attribution to the map */
        const date = new Date();
        const year = date.getFullYear();
        let mapLink = '<a href="https://openstreetmap.org">OpenStreetMap</a>';
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'Map data &copy; ' + mapLink + ' & Contributors',
            maxZoom: 20,
            minZoom: 1,
        }).addTo(this.map);

        this.map.attributionControl.addAttribution('Litter data &copy; OpenLitterMap & Contributors ' + year);

        // Time player settings
        let timeDimension = new L.TimeDimension({});
        this.map.timeDimension = timeDimension;
        this.player = new L.TimeDimension.Player({
            transitionTime: 1000,
            loop: true
        }, timeDimension);
        this.player.on('play', () => {
            if (this.map?.hasLayer(this.pointsLayer)) {
                this.map.removeLayer(this.pointsLayer);
            }
        })
        this.map.addControl(new L.Control.TimeDimension({
            player: this.player,
            timeDimension: timeDimension,
            timeSliderDragUpdate: true,
            loopButton: true,
            autoPlay: false,
        }));

        this.pointsLayer = L.geoJSON(this.certificate.geojson, {
            pointToLayer: (feature, latLng) => {
                return L.marker([latLng.lng, latLng.lat])
            },
            onEachFeature: (feature, layer) => {
                layer.on('click', (e) => {
                    L.popup(mapHelper.popupOptions)
                        .setLatLng(feature.geometry.coordinates)
                        .setContent(mapHelper.getMapImagePopupContent(feature.properties))
                        .openOn(this.map);
                });
            }
        });

        this.timeLayer = L.timeDimension.layer.geoJson(this.pointsLayer, {
            updateTimeDimension: true,
            updateTimeDimensionMode: 'replace',
        });

        this.pointsLayer.addTo(this.map);
        this.timeLayer.addTo(this.map);
    },
    methods: {
        async load () {
            await axios.get('/certificates/1')
                .then(response =>
                {
                    this.certificate = response.data;
                })
                .catch(error =>
                {
                    console.error('get_clusters', error);
                });
        }
    }
}
</script>

<style scoped>
#map {
    height: 100vh;
    margin: 0;
    position: relative;
}
</style>
