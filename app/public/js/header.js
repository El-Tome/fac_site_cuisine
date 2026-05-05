let lastScrollY = 0;
let headerOffsetY = 0;
const header = document.querySelector('header');
const headerHeight = header.offsetHeight;

window.addEventListener('scroll', () => {
    const currentScrollY = window.scrollY;
    const delta = currentScrollY - lastScrollY;

    headerOffsetY = Math.min(0, Math.max(-headerHeight, headerOffsetY - delta));
    header.style.top = headerOffsetY + 'px';

    lastScrollY = currentScrollY;
});
