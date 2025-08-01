import { translate } from "../../utils/helpers";

// js/ui/dynamicContent.js
export function initDynamicContent() {

    const amenities = (window.AppConfig.pageData.amenities || []).map(function (item) {
        return {
            name: item,
            icon: ''
        }
    });

    const nearbyPlaces = window.AppConfig.pageData.nearby_places || [];

    const attributes = (window.AppConfig.pageData.attributes || []).map(function (item) {
        return {
            key: item.name,
            value: item.value,
            icon: ''
        }
    });
    const propertyAttributesListV3 = document.getElementById('propertyAttributesListV3');
    if (propertyAttributesListV3 && typeof attributes !== 'undefined' && typeof ATTRIBUTE_ICON_MAP_V3 !== 'undefined') {
        propertyAttributesListV3.innerHTML = '';
        attributes.forEach(attr => {
            const li = document.createElement('li');
            const iconClass = attr.icon || ATTRIBUTE_ICON_MAP_V3[attr.key] || ATTRIBUTE_ICON_MAP_V3['default'];
            li.innerHTML = `<i class="fas ${iconClass}"></i> <strong>${attr.key || translate('attribute_default_name')}:</strong> <span>${attr.value || '-'}</span>`;
            propertyAttributesListV3.appendChild(li);
        });
    }

    const amenitiesListV3 = document.getElementById('amenitiesListV3');
    if (amenitiesListV3 && typeof amenities !== 'undefined') {
        amenitiesListV3.innerHTML = '';
        amenities.forEach(item => {
            const li = document.createElement('li');
            li.innerHTML = `<i class="fas ${item.icon || 'fa-check-square'}"></i> <span>${item.name || translate('aminity_default_name')}</span>`;
            amenitiesListV3.appendChild(li);
        });
    }

    const nearestPlacesListV3 = document.getElementById('nearestPlacesListV3');
    if (nearestPlacesListV3 && typeof NEAREST_PLACES_DATA_V3 !== 'undefined' && typeof PLACE_ICON_MAP_V3 !== 'undefined') {
        nearestPlacesListV3.innerHTML = '';
        nearbyPlaces.forEach(place => {
            const li = document.createElement('li');
            const iconClass = place.icon || PLACE_ICON_MAP_V3[place.type] || PLACE_ICON_MAP_V3['default'];
            li.innerHTML = `<i class="fas ${iconClass}"></i> <span>${place.name || translate('nearby_place_default_name')}</span> <span class="place-distance-v3">(${place.distance || ''})</span>`;
            nearestPlacesListV3.appendChild(li);
        });
    }
}