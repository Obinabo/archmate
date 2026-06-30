// Scroll up
const scrollUp = document.getElementById('scroll-up');
if (scrollUp) {
  window.addEventListener('scroll', () => {
    scrollUp.classList.toggle('show', window.scrollY > 350);
  });
}

// Mobile nav toggle
const navToggle = document.getElementById('nav-toggle');
const navMenu = document.getElementById('nav-menu');
if (navToggle && navMenu) {
  navToggle.addEventListener('click', () => {
    const isOpen = navMenu.style.display === 'flex';
    if (isOpen) {
      navMenu.style.display = '';
    } else {
      Object.assign(navMenu.style, {
        display: 'flex',
        position: 'absolute',
        top: '100%',
        right: '2rem',
        flexDirection: 'column',
        background: 'rgba(7,21,36,0.97)',
        padding: '1.5rem 2rem',
        border: '1px solid rgba(0,197,220,0.18)',
        gap: '1.25rem'
      });
    }
  });
}

// Header divisions dropdown
const divisionWrap = document.querySelector('.nav-dropdown-wrap');
const divisionTrigger = document.querySelector('[data-division-toggle]');
const divisionDropdown = document.querySelector('[data-division-dropdown]');
if (divisionWrap && divisionTrigger && divisionDropdown) {
  const closeDropdown = () => {
    divisionWrap.classList.remove('is-open');
    divisionTrigger.setAttribute('aria-expanded', 'false');
  };

  divisionTrigger.addEventListener('click', (event) => {
    event.preventDefault();
    const isOpen = divisionWrap.classList.toggle('is-open');
    divisionTrigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
  });

  document.addEventListener('click', (event) => {
    if (!divisionWrap.contains(event.target)) {
      closeDropdown();
    }
  });

  divisionDropdown.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      closeDropdown();
    });
  });
}

// Reveal on scroll
const reveals = document.querySelectorAll('.reveal');
if ('IntersectionObserver' in window && reveals.length) {
  const io = new IntersectionObserver((entries) => {
    entries.forEach((e) => {
      if (e.isIntersecting) {
        e.target.classList.add('is-visible');
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.12 });
  reveals.forEach(el => io.observe(el));
}

// Accordion
document.querySelectorAll('.value-accordion-item').forEach((item) => {
  const header = item.querySelector('.value-accordion-header');
  if (!header) return;
  header.addEventListener('click', () => {
    const open = item.classList.contains('open');
    item.parentElement.querySelectorAll('.value-accordion-item').forEach(i => i.classList.remove('open'));
    if (!open) item.classList.add('open');
  });
});

// Properties filter (simple client-side)
document.querySelectorAll('.props-filter button').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.props-filter button').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const f = btn.dataset.filter;
    document.querySelectorAll('.props-all-grid .prop-card').forEach(card => {
      const t = card.dataset.type || '';
      card.style.display = (f === 'all' || t === f) ? '' : 'none';
    });
  });
});
