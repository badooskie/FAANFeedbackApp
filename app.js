document.addEventListener('DOMContentLoaded', () => {
  try {
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
  } catch (_) {}

  const navbar = document.querySelector('.navbar');
  const applyNavbarShadow = () => {
    if (!navbar) return;
    if (window.scrollY > 20) navbar.classList.add('shadow');
    else navbar.classList.remove('shadow');
  };
  applyNavbarShadow();
  window.addEventListener('scroll', applyNavbarShadow);

  const backTop = document.createElement('button');
  backTop.type = 'button';
  backTop.setAttribute('aria-label', 'Back to top');
  backTop.textContent = '↑';
  backTop.className = 'btn btn-primary';
  backTop.style.position = 'fixed';
  backTop.style.bottom = '20px';
  backTop.style.right = '20px';
  backTop.style.zIndex = '1050';
  backTop.style.borderRadius = '50%';
  backTop.style.width = '44px';
  backTop.style.height = '44px';
  backTop.style.display = 'none';
  backTop.style.alignItems = 'center';
  backTop.style.justifyContent = 'center';
  backTop.style.boxShadow = '0 6px 16px rgba(0,0,0,.15)';
  document.body.appendChild(backTop);
  const toggleBackTop = () => {
    backTop.style.display = window.scrollY > 300 ? 'inline-flex' : 'none';
  };
  toggleBackTop();
  window.addEventListener('scroll', toggleBackTop);
  backTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
      const id = anchor.getAttribute('href').slice(1);
      const target = document.getElementById(id);
      if (target) {
        e.preventDefault();
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        history.replaceState(null, '', '#' + id);
      }
    });
  });

  const galleryImages = document.querySelectorAll('img[src^="static/gallery"]');
  if (galleryImages.length) {
    const modalEl = document.createElement('div');
    modalEl.className = 'modal fade';
    modalEl.id = 'imageModal';
    modalEl.innerHTML = `
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark">
          <div class="modal-body p-0">
            <img class="img-fluid w-100" alt="Gallery image">
          </div>
        </div>
      </div>
    `;
    document.body.appendChild(modalEl);
    const modal = new bootstrap.Modal(modalEl);
    const modalImg = modalEl.querySelector('img');
    galleryImages.forEach(img => {
      img.style.cursor = 'zoom-in';
      img.addEventListener('click', () => {
        modalImg.src = img.src;
        modalImg.alt = img.alt || 'Gallery image';
        modal.show();
      });
    });
  }

  const comment = document.getElementById('comment');
  if (comment) {
    const maxLen = 500;
    comment.setAttribute('maxlength', String(maxLen));
    const counter = document.createElement('small');
    counter.className = 'text-muted';
    counter.style.float = 'right';
    counter.style.marginTop = '4px';
    comment.parentElement.appendChild(counter);
    const updateCount = () => {
      counter.textContent = `${comment.value.length}/${maxLen}`;
    };
    comment.addEventListener('input', updateCount);
    updateCount();
  }
});
