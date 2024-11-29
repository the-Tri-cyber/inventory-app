
<asside class="sidebar sidebar-toggle d-flex flex-column flex-shrink-0 p-3 text-bg-dark" id="sidebar" style="width: 280px; height: auto;">
    <a href="/inventory-app/public" class=" d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none mx-auto" style="padding-top: 5rem;">
      <span class="fs-4 text-capitalize"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
    </a>
    <hr>
    <ul class="sidebar-nav nav nav-pills flex-column mb-auto">
      <li>
        <a href="/inventory-app/modules/dashboard.php" class="nav-link text-white d-flex align-items-center">
          <i class="bi bi-house me-2 fs-4"></i>
          <p class="m-0 p-0" id="ts">Home</p>
        </a>
      </li>
      <li>
        <a href="/inventory-app/modules/barang" class="nav-link text-white d-flex align-items-center">
          <i class="bi bi-database me-2 fs-4"></i>
          <p class="m-0 p-0" id="ts">Barang</p>
        </a>
      </li>
      <li>
        <a href="/inventory-app/modules/transaksi" class="nav-link text-white d-flex align-items-center">
          <i class="bi bi-grid me-2 fs-4"></i>
            <p class="m-0 p-0" id="ts">Transaksi</p>
        </a>
      </li>
    </ul>
  </asside>