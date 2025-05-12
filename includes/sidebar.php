<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/" class="brand-link">
        <img src="/dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">POS Kasir</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo $_SESSION['user_name']; ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="/" class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/kasir" class="nav-link <?php echo $current_page === 'kasir' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>Kasir</p>
                    </a>
                </li>
                
                <?php if ($_SESSION['user_role'] === 'admin'): ?>
                <li class="nav-header">MANAJEMEN</li>
                
                <li class="nav-item">
                    <a href="/produk" class="nav-link <?php echo $current_page === 'produk' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Produk</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/kategori" class="nav-link <?php echo $current_page === 'kategori' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Kategori</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/diskon" class="nav-link <?php echo $current_page === 'diskon' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-percent"></i>
                        <p>Diskon</p>
                    </a>
                </li>
                
                <li class="nav-header">KEUANGAN</li>
                
                <li class="nav-item">
                    <a href="/modal" class="nav-link <?php echo $current_page === 'modal' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Modal</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/pemasukan" class="nav-link <?php echo $current_page === 'pemasukan' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-plus-circle"></i>
                        <p>Pemasukan Lain</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/pengeluaran" class="nav-link <?php echo $current_page === 'pengeluaran' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-minus-circle"></i>
                        <p>Pengeluaran</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/kas" class="nav-link <?php echo $current_page === 'kas' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-cash-register"></i>
                        <p>Kas Harian</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/log_laba" class="nav-link <?php echo $current_page === 'laba' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Laba Harian</p>
                    </a>
                </li>
                
                <li class="nav-header">LAPORAN</li>
                
                <li class="nav-item">
                    <a href="/laporan" class="nav-link <?php echo $current_page === 'laporan' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Laporan</p>
                    </a>
                </li>
                
                <li class="nav-header">PENGATURAN</li>
                
                <li class="nav-item">
                    <a href="/user" class="nav-link <?php echo $current_page === 'user' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>User</p>
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a href="/logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside> 