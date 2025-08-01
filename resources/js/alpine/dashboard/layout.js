// Toggle organization dropdown
const orgInfo = document.getElementById('orgInfoContainer');
const orgDropdown = document.getElementById('orgDropdown');
const orgChevron = document.getElementById('orgChevron');

if (orgInfo) {
    orgInfo.addEventListener('click', function (event) {
        // منع انتشار الحدث، لتجنب إغلاق القائمة فورًا بواسطة المستمع الموجود أدناه
        event.stopPropagation();
        orgDropdown.classList.toggle('hidden');
        orgChevron.classList.toggle('rotate-180');
    });
}

// Close dropdown when clicking outside
document.addEventListener('click', function (event) {
    if (orgInfo && !orgInfo.contains(event.target) && !orgDropdown.classList.contains('hidden')) {
        orgDropdown.classList.add('hidden');
        orgChevron.classList.remove('rotate-180');
    }
});