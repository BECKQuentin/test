<script>

import L from 'leaflet';
import 'leaflet.markercluster/dist/leaflet.markercluster';

export default {
  template: `#template_maps`,
  delimiters: ['${', '}'],
  props: {
    initUser: {type: String},
    initLocale: {type: String},
  },
  data() {
    return {
      user: JSON.parse(this.initUser),
      locale: this.initLocale,
      map: null,
      mapCenter: { lat: 0, lng: 0 },
      zoom: 10,
      nearbyUsers: [],

      alertSuccess: '',
      alertError: '',
    }
  },
  mounted() {
    this.initMap();
  },
  methods: {
    initMap() {

      this.map = L.map('map').setView([this.user.coordinates.longitude, this.user.coordinates.latitude], 13);

      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '© OpenStreetMap'
        }).addTo(this.map);
      },

    searchNearbyUsers() {
      const currentCenter = this.map.getCenter();
      this.$http.get(this.$path('maps_search_closest', {longitude: currentCenter.lat, latitude: currentCenter.lng}))
          .then(({data}) => {
            console.log(data);
            const nearbyUsers = response.data;

            console.log(nearbyUsers);
            // Ajouter les marqueurs des utilisateurs à proximité sur la carte
            this.addUserMarkers(nearbyUsers);
          })
          .finally(() => {
            // this.isConversationLoading = false;
          })
    },

    addUserMarkers(users) {
      users.forEach(user => {
        const marker = L.circleMarker([user.latitude, user.longitude], {
          color: 'red',
          fillColor: 'red',
          fillOpacity: 1,
          radius: 6,
        }).addTo(this.map);

        // Vous pouvez ajouter des informations supplémentaires au marqueur ici, par exemple un popup
        marker.bindPopup(`Utilisateur : ${user.username}`);
      });
    },




  },
}
</script>