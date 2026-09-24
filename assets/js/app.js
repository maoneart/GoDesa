/**
 * ============================================================
 * MAONEART GLASSMORPHISM MODAL SYSTEM (Standard)
 * Strict Compliance:
 * 1. Dilarang keras native alert() & confirm()
 * 2. Tombol aksi modal 100% simetris dalam 2-kolom grid (grid grid-cols-2 gap-3)
 *    dengan lebar penuh (w-full) dan tinggi yang sama.
 * ============================================================
 */

function ensureModalContainer() {
  let modalContainer = document.getElementById('maoneartModalContainer');
  if (!modalContainer) {
    modalContainer = document.createElement('div');
    modalContainer.id = 'maoneartModalContainer';
    modalContainer.className = 'maoneart-modal-backdrop';
    document.body.appendChild(modalContainer);
  }
  return modalContainer;
}

/**
 * Custom Confirm Modal (100% Symmetrical 2-Column Action Buttons)
 */
function showConfirmModal(options = {}) {
  const {
    title = 'Konfirmasi Tindakan',
    message = 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
    confirmText = 'Ya, Lanjutkan',
    cancelText = 'Batal',
    isDanger = true,
    icon = 'fa-triangle-exclamation',
    onConfirm = null
  } = options;

  const container = ensureModalContainer();
  const iconClass = isDanger ? 'danger' : 'info';
  const confirmBtnClass = isDanger ? 'danger' : 'primary';

  container.innerHTML = `
    <div class="maoneart-modal-card">
      <div class="maoneart-modal-icon-box ${iconClass}">
        <i class="fa-solid ${icon}"></i>
      </div>
      <h3 class="maoneart-modal-title">${title}</h3>
      <div class="maoneart-modal-message">${message}</div>
      <div class="maoneart-modal-actions">
        <button type="button" class="maoneart-modal-btn cancel" id="maoneartModalCancel">
          ${cancelText}
        </button>
        <button type="button" class="maoneart-modal-btn ${confirmBtnClass}" id="maoneartModalConfirm">
          ${confirmText}
        </button>
      </div>
    </div>
  `;

  setTimeout(() => container.classList.add('active'), 10);

  const close = () => {
    container.classList.remove('active');
  };

  const cancelBtn = container.querySelector('#maoneartModalCancel');
  const confirmBtn = container.querySelector('#maoneartModalConfirm');

  cancelBtn.onclick = close;
  confirmBtn.onclick = () => {
    close();
    if (typeof onConfirm === 'function') {
      onConfirm();
    }
  };

  container.onclick = (e) => {
    if (e.target === container) close();
  };
}

/**
 * Custom Alert Modal
 */
function showAlertModal(options = {}) {
  const {
    title = 'Informasi',
    message = '',
    buttonText = 'Mengerti',
    icon = 'fa-circle-info',
    type = 'info' // info, success, danger
  } = options;

  const container = ensureModalContainer();
  const iconClass = type;
  const btnClass = type === 'danger' ? 'danger' : 'primary';

  container.innerHTML = `
    <div class="maoneart-modal-card">
      <div class="maoneart-modal-icon-box ${iconClass}">
        <i class="fa-solid ${icon}"></i>
      </div>
      <h3 class="maoneart-modal-title">${title}</h3>
      <div class="maoneart-modal-message">${message}</div>
      <div style="width: 100%;">
        <button type="button" class="maoneart-modal-btn ${btnClass}" id="maoneartAlertOk">
          ${buttonText}
        </button>
      </div>
    </div>
  `;

  setTimeout(() => container.classList.add('active'), 10);

  const close = () => {
    container.classList.remove('active');
  };

  const okBtn = container.querySelector('#maoneartAlertOk');
  okBtn.onclick = close;

  container.onclick = (e) => {
    if (e.target === container) close();
  };
}

/**
 * Konfirmasi Hapus Data via MaoneArt Modal
 */
function confirmDelete(url, itemTitle) {
  showConfirmModal({
    title: 'Hapus Data?',
    message: `Apakah Anda yakin ingin menghapus data <strong>"${itemTitle}"</strong>?<br><span style="font-size:0.75rem; color:#dc2626;">Tindakan ini tidak dapat dibatalkan.</span>`,
    confirmText: 'Hapus',
    cancelText: 'Batal',
    isDanger: true,
    icon: 'fa-trash-can',
    onConfirm: () => {
      window.location.href = url;
    }
  });
}

/**
 * Role Switcher Modal (Cepat beralih peran Admin, Lurah, Staff, Warga)
 */
function openRoleSwitcher() {
  const container = ensureModalContainer();
  container.innerHTML = `
    <div class="maoneart-modal-card" style="text-align: left;">
      <div style="text-align: center; margin-bottom: 12px;">
        <div class="maoneart-modal-icon-box info">
          <i class="fa-solid fa-users-gear"></i>
        </div>
        <h3 class="maoneart-modal-title">Ganti Peran Pengguna</h3>
        <p class="maoneart-modal-message" style="margin-bottom: 14px;">Pilih peran untuk menguji aplikasi GoDesa secara langsung:</p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 16px;">
        <a href="?switch_role=admin" class="role-select-item" style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 12px; border: 1px solid #E2E8F0; text-decoration: none; color: inherit; background: #FFF;">
          <div style="width: 38px; height: 38px; border-radius: 10px; background: #F1F5F9; display: flex; align-items: center; justify-content: center; color: #475569; font-size: 1.1rem;">
            <i class="fa-solid fa-shield-halved"></i>
          </div>
          <div>
            <div style="font-size: 0.85rem; font-weight: 800; color: #0F172A;">Admin Desa</div>
            <div style="font-size: 0.72rem; color: #64748B;">Kelola pengguna, berita & sistem</div>
          </div>
        </a>

        <a href="?switch_role=lurah" class="role-select-item" style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 12px; border: 1px solid #E2E8F0; text-decoration: none; color: inherit; background: #FFF;">
          <div style="width: 38px; height: 38px; border-radius: 10px; background: #DCFCE7; display: flex; align-items: center; justify-content: center; color: #16A34A; font-size: 1.1rem;">
            <i class="fa-solid fa-user-tie"></i>
          </div>
          <div>
            <div style="font-size: 0.85rem; font-weight: 800; color: #0F172A;">Lurah / Kepala Desa</div>
            <div style="font-size: 0.72rem; color: #64748B;">Approval surat, TTD digital & monitoring</div>
          </div>
        </a>

        <a href="?switch_role=staff" class="role-select-item" style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 12px; border: 1px solid #E2E8F0; text-decoration: none; color: inherit; background: #FFF;">
          <div style="width: 38px; height: 38px; border-radius: 10px; background: #DBEAFE; display: flex; align-items: center; justify-content: center; color: #2563EB; font-size: 1.1rem;">
            <i class="fa-solid fa-id-badge"></i>
          </div>
          <div>
            <div style="font-size: 0.85rem; font-weight: 800; color: #0F172A;">Staff Pelayanan</div>
            <div style="font-size: 0.72rem; color: #64748B;">Verifikasi dokumen & tangani laporan desa</div>
          </div>
        </a>

        <a href="?switch_role=rw" class="role-select-item" style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 12px; border: 1px solid #E2E8F0; text-decoration: none; color: inherit; background: #FFF;">
          <div style="width: 38px; height: 38px; border-radius: 10px; background: #EEF2FF; display: flex; align-items: center; justify-content: center; color: #4F46E5; font-size: 1.1rem;">
            <i class="fa-solid fa-sitemap"></i>
          </div>
          <div>
            <div style="font-size: 0.85rem; font-weight: 800; color: #0F172A;">Ketua RW 001</div>
            <div style="font-size: 0.72rem; color: #64748B;">Koordinasi RT se-RW 001 & pantau warga</div>
          </div>
        </a>

        <a href="?switch_role=rt" class="role-select-item" style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 12px; border: 1px solid #E2E8F0; text-decoration: none; color: inherit; background: #FFF;">
          <div style="width: 38px; height: 38px; border-radius: 10px; background: #CCFBF1; display: flex; align-items: center; justify-content: center; color: #0D9488; font-size: 1.1rem;">
            <i class="fa-solid fa-people-roof"></i>
          </div>
          <div>
            <div style="font-size: 0.85rem; font-weight: 800; color: #0F172A;">Ketua RT 003</div>
            <div style="font-size: 0.72rem; color: #64748B;">Verifikasi pengantar RT warga RT 003 / RW 001</div>
          </div>
        </a>

        <a href="?switch_role=warga" class="role-select-item" style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; border-radius: 12px; border: 1px solid #E2E8F0; text-decoration: none; color: inherit; background: #FFF;">
          <div style="width: 38px; height: 38px; border-radius: 10px; background: #FEF3C7; display: flex; align-items: center; justify-content: center; color: #D97706; font-size: 1.1rem;">
            <i class="fa-solid fa-user"></i>
          </div>
          <div>
            <div style="font-size: 0.85rem; font-weight: 800; color: #0F172A;">Warga (Hermawan)</div>
            <div style="font-size: 0.72rem; color: #64748B;">Warga Desa Cibuntu RT 003 / RW 001</div>
          </div>
        </a>
      </div>

      <div style="width: 100%;">
        <button type="button" class="maoneart-modal-btn cancel" id="maoneartCloseRole">Tutup</button>
      </div>
    </div>
  `;

  setTimeout(() => container.classList.add('active'), 10);
  const close = () => container.classList.remove('active');
  container.querySelector('#maoneartCloseRole').onclick = close;
  container.onclick = (e) => {
    if (e.target === container) close();
  };
}

/**
 * Filter Search Functionality
 */
function setupSearchFilter(inputId, itemSelector, textSelector) {
  const input = document.getElementById(inputId);
  if (!input) return;

  input.addEventListener('input', (e) => {
    const query = e.target.value.toLowerCase().trim();
    const items = document.querySelectorAll(itemSelector);

    items.forEach(item => {
      const textElem = item.querySelector(textSelector);
      const text = textElem ? textElem.textContent.toLowerCase() : item.textContent.toLowerCase();
      if (text.includes(query)) {
        item.style.display = '';
      } else {
        item.style.display = 'none';
      }
    });
  });
}

// Global Search initialization
document.addEventListener('DOMContentLoaded', () => {
  setupSearchFilter('globalAppSearch', '.card-item, .banner-card', 'h3, h4, p, span');
});
