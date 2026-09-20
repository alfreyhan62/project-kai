(() => {
  const shell = document.getElementById('admin-shell-content');
  if (!shell) return;

  const routes = {
    dashboard: 'dashboard.php',
    users: 'users.php',
    unit: 'unit.php',
    ruangan: 'ruangan.php',
    lemari: 'lemari.php',
    rak: 'rak.php',
    'jenis-surat': 'jenis-surat.php',
    klasifikasi: 'klasifikasi-arsip.php'
  };
  const titles = {
    dashboard: 'Dashboard', users: 'Pengelolaan Pengguna', unit: 'Unit Kerja', ruangan: 'Ruangan',
    lemari: 'Lemari', rak: 'Rak', 'jenis-surat': 'Jenis Surat', klasifikasi: 'Klasifikasi Arsip'
  };
  let currentUrl = new URL(window.location.href);
  const dashboardContent = shell.innerHTML;

  const routeFromHash = () => {
    const value = window.location.hash.slice(1) || 'dashboard';
    const [route, query = ''] = value.split('?');
    return { route: routes[route] ? route : 'dashboard', query };
  };
  const setActive = route => {
    document.querySelectorAll('[data-admin-route]').forEach(item => {
      const active = item.dataset.adminRoute === route;
      item.classList.toggle('nav-active', active);
      if (item.tagName === 'A') item.setAttribute('aria-current', active ? 'page' : 'false');
    });
    const title = document.querySelector('[data-admin-title]');
    if (title) title.textContent = titles[route] || 'Administrasi';
  };
  const loading = () => { shell.setAttribute('aria-busy', 'true'); shell.classList.add('admin-content-loading'); };
  const complete = () => { shell.removeAttribute('aria-busy'); shell.classList.remove('admin-content-loading'); };
  const error = () => { shell.innerHTML = '<section class="panel admin-fragment-state"><h2>Konten tidak dapat dimuat</h2><p>Silakan coba lagi.</p></section>'; complete(); };
  const contentFrom = text => {
    const documentResponse = new DOMParser().parseFromString(text, 'text/html');
    return documentResponse.querySelector('.admin-content');
  };
  const load = async () => {
    const { route, query } = routeFromHash();
    setActive(route);
    if (route === 'dashboard' && !query) { shell.innerHTML = dashboardContent; shell.dataset.source = routes.dashboard; complete(); return; }
    currentUrl = new URL(routes[route], window.location.href);
    currentUrl.search = query;
    loading();
    try {
      const response = await fetch(currentUrl, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!response.ok) throw new Error('Request failed');
      const content = contentFrom(await response.text());
      if (!content) throw new Error('Fragment missing');
      shell.innerHTML = content.innerHTML;
      shell.dataset.source = currentUrl.href;
      complete();
    } catch (_) { error(); }
  };
  const routeForUrl = url => Object.entries(routes).find(([, path]) => new URL(path, window.location.href).pathname === url.pathname)?.[0];
  const navigate = url => {
    const route = routeForUrl(url);
    if (!route) return false;
    const hash = `${route}${url.search}`;
    if (window.location.hash.slice(1) === hash) load(); else window.location.hash = hash;
    return true;
  };

  document.addEventListener('click', event => {
    const link = event.target.closest('#admin-shell-content a, [data-admin-route]');
    if (!link || link.target || link.hasAttribute('download') || event.metaKey || event.ctrlKey) return;
    const href = link.getAttribute('href');
    if (!href || href.startsWith('#')) return;
    const url = new URL(href, shell.dataset.source || window.location.href);
    if (url.origin !== window.location.origin || !navigate(url)) return;
    event.preventDefault();
  });
  document.addEventListener('submit', async event => {
    const form = event.target.closest('#admin-shell-content form');
    if (!form) return;
    const url = new URL(form.getAttribute('action') || shell.dataset.source || window.location.href, shell.dataset.source || window.location.href);
    const route = routeForUrl(url);
    if (!route) return;
    event.preventDefault();
    const method = (form.method || 'get').toUpperCase();
    const formData = new FormData(form);
    if (method === 'GET') new URLSearchParams(formData).forEach((value, key) => url.searchParams.set(key, value));
    loading();
    try {
      const response = await fetch(url, { method, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' }, body: method === 'GET' ? undefined : formData });
      if (!response.ok) throw new Error('Request failed');
      const content = contentFrom(await response.text());
      if (!content) throw new Error('Fragment missing');
      shell.innerHTML = content.innerHTML;
      shell.dataset.source = url.href;
      if (method === 'GET') history.replaceState(null, '', `#${route}${url.search}`);
      complete();
    } catch (_) { error(); }
  });
  window.addEventListener('hashchange', load);
  if (!window.location.hash) history.replaceState(null, '', '#dashboard');
  setActive(routeFromHash().route);
  if (routeFromHash().route !== 'dashboard') load();
})();
