(function () {
    // const savedDirection = localStorage.getItem('uiDirection') || 'ltr';
    // const savedDirection = 'rtl';
    const savedDirection = 'ltr';
    document.documentElement.setAttribute('dir', savedDirection); // Set on <html>
    if (savedDirection === 'rtl') {
        document.documentElement.classList.add('rtl'); // Add class to <html>
        // document.body might not exist yet, so <html> is safer for class
    } else {
        document.documentElement.classList.add('ltr');
    }
})();