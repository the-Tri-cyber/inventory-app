<nav class="fixed-top navbar navbar-dark text-bg-dark bg-dark">
  <div class="container-fluid">
    <button class="navbar-toggler" type="button">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="text-white d-flex">
      <img src="/inventory-app/public/assets/img/logo.png" alt="logo" style="width: 45px; height: 45px; padding-right: 3px;">
      <h2 class="fs-2">Inventory App</h2>
    </div>
    <div class="pe-3">
      <div class="dropdown">
        <a
          href="#"
          class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          <?php if (isset($_SESSION['gambar']) && !empty($_SESSION['gambar'])): ?>
            <img
              src="/inventory-app/modules/uploads/users/<?php echo htmlspecialchars($_SESSION['gambar']); ?>"
              alt="Profile Picture"
              width="32"
              height="32"
              class="rounded-circle me-2"
            />
          <?php else: ?>
            <i class="bi bi-person" style="font-size: 32px; margin-right: 8px;"></i>
          <?php endif; ?>
          <strong class="text-white text-capitalize"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
          <li><a class="dropdown-item" href="/inventory-app/modules/account/">My Account</a></li>
          <li><hr class="dropdown-divider" /></li>
          <li>
            <button
              class="dropdown-item text-danger"
              href="/inventory-app/modules/auth/logout.php"
              data-bs-toggle="modal"
              data-bs-target="#logoutModal"
            >
              Log out
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<!-- Modal -->
<div
  class="modal fade"
  id="logoutModal"
  tabindex="-1"
  aria-labelledby="logoutModalLabel"
  aria-hidden="true"
>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
        <button
          type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          aria-label="Close"
        ></button>
      </div>
      <div class="modal-body">
        Apakah Anda yakin ingin logout dari aplikasi?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
 Batal
        </button>
        <a href="/inventory-app/modules/auth/logout.php" class="btn btn-danger">Logout</a>
      </div>
    </div>
  </div>
</div>