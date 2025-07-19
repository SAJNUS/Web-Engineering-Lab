function toggleDropdown() {
    const menu = document.getElementById('dropdownMenu');
    if (menu.style.display === 'flex') {
        menu.style.display = 'none';
    } else {
        menu.style.display = 'flex';
    }
}
