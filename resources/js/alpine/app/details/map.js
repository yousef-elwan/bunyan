export const MapUI = (function () {
    let map;
    let marker;

    function init() {
        const mapContainer = document.getElementById('leafletMapContainerV3');
        if (!mapContainer) {
            return;
        }

        const latitude = window.AppConfig.pageData.latitude;
        const longitude = window.AppConfig.pageData.longitude;

        if (latitude === null || longitude === null) {
            console.warn('Property latitude or longitude is not set.');
            return;
        }


        map = L.map('leafletMapContainerV3').setView([latitude, longitude], 17);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        marker = L.marker([latitude, longitude]).addTo(map);
        // marker.bindPopup(`<b>Property Location</b><br>Latitude: ${latitude}<br>Longitude: ${longitude}`).openPopup();
    }

    return {
        init: init
    };
})();
