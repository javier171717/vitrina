(() => {
  const header = document.querySelector('.mkt-header');
  const home = document.body.dataset.home || '/';

  const scrollToId = (id) => {
    const el = document.getElementById(id);
    if (!el) return false;
    const offset = (header?.offsetHeight || 0) + 8;
    const top = el.getBoundingClientRect().top + window.scrollY - offset;
    window.scrollTo({ top: Math.max(0, top), behavior: 'smooth' });
    history.replaceState(null, '', '#' + id);
    return true;
  };

  const goToSection = (id) => {
    if (!id) return;
    if (scrollToId(id)) return;
    sessionStorage.setItem('mkt-scroll', id);
    window.location.href = home;
  };

  document.querySelectorAll('[data-mkt-section]').forEach((link) => {
    link.addEventListener('click', (event) => {
      event.preventDefault();
      goToSection(link.dataset.mktSection);
    });
  });

  const pending = sessionStorage.getItem('mkt-scroll') || window.location.hash.replace('#', '');
  if (pending) {
    sessionStorage.removeItem('mkt-scroll');
    window.setTimeout(() => scrollToId(pending), 50);
  }
})();
