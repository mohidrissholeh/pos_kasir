<?php
// Query untuk mengambil data produk
$query = "SELECT * FROM produk WHERE status_produk = 'y' ORDER BY nama_produk ASC";
$result = mysqli_query($koneksi, $query);
?>

<style>
.product-card {
    cursor: pointer;
    transition: transform 0.2s;
    height: 100%;
}
.product-card:hover {
    transform: scale(1.05);
}
.product-image {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 5px 5px 0 0;
}
.product-info {
    padding: 10px;
}
.product-name {
    font-weight: bold;
    margin-bottom: 5px;
    height: 40px;
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.product-price {
    color: #28a745;
    font-weight: bold;
}
.product-stock {
    font-size: 12px;
    color: #6c757d;
}
.cart-item {
    border-bottom: 1px solid #eee;
    padding: 10px 0;
}
</style>

<div class="row">
    <!-- Kolom Produk -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Produk</h3>
                <div class="card-tools">
                    <div class="input-group input-group-sm" style="width: 200px;">
                        <input type="text" id="search" class="form-control float-right" placeholder="Cari produk...">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-default">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Kategori -->
                <div class="mb-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm active" data-kategori="all">
                            Semua
                        </button>
                        <?php
                        $query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
                        $result_kategori = mysqli_query($koneksi, $query_kategori);
                        while ($kategori = mysqli_fetch_assoc($result_kategori)):
                        ?>
                        <button type="button" class="btn btn-outline-primary" data-kategori="<?php echo $kategori['id_kategori']; ?>">
                            <?php echo $kategori['nama_kategori']; ?>
                        </button>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="row" id="product-list">
                    <?php
                    // Query untuk mengambil data produk
                    $query = "SELECT p.*, k.nama_kategori 
                             FROM produk p 
                             LEFT JOIN kategori k ON p.id_kategori = k.id_kategori 
                             WHERE p.status_produk = 'y' AND p.stok > 0 
                             ORDER BY p.nama_produk ASC";
                    $result = mysqli_query($koneksi, $query);
                    
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <div class="col-6 col-md-3 mb-3 product-item" data-kategori="<?php echo $row['id_kategori']; ?>">
                        <div class="card product-card" onclick="addToCart(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                            <?php if (!empty($row['gambar'])): ?>
                                <img src="/pos_kasir/<?php echo $row['gambar']; ?>" class="product-image" alt="<?php echo $row['nama_produk']; ?>">
                            <?php else: ?>
                                <img src="/pos_kasir/dist/img/no-image.png" class="product-image" alt="No Image">
                            <?php endif; ?>
                            <div class="product-info">
                                <div class="product-name"><?php echo $row['nama_produk']; ?></div>
                                <div class="product-price">Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></div>
                                <div class="product-stock">Stok: <?php echo $row['stok']; ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Keranjang -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Keranjang</h3>
            </div>
            <div class="card-body">
                <div id="cartItems">
                    <!-- Item keranjang akan ditampilkan di sini -->
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <h5>Total:</h5>
                    </div>
                    <div class="col-6 text-right">
                        <h5 id="totalAmount">Rp 0</h5>
                    </div>
                </div>

                <!-- Diskon -->
                <div class="mt-3">
                    <div class="form-group">
                        <label>Diskon</label>
                        <div class="input-group">
                            <select class="form-control form-control-sm" id="diskonSelect" onchange="applyDiskon()">
                                <option value="">Pilih Diskon</option>
                                <?php
                                $query = "SELECT * FROM diskon WHERE status_diskon = 'aktif' AND 
                                         (tanggal_mulai IS NULL OR tanggal_mulai <= CURDATE()) AND 
                                         (tanggal_selesai IS NULL OR tanggal_selesai >= CURDATE())";
                                $result = mysqli_query($koneksi, $query);
                                while ($row = mysqli_fetch_assoc($result)):
                                    $label = $row['nama_diskon'] . ' - ';
                                    if ($row['jenis_diskon'] === 'persentase') {
                                        $label .= $row['nilai_diskon'] . '%';
                                    } else {
                                        $label .= 'Rp ' . number_format($row['nilai_diskon'], 0, ',', '.');
                                    }
                                    
                                    //Tambahkan informasi periode jika ada
                                    // if ($row['tanggal_mulai'] && $row['tanggal_selesai']) {
                                    //     $label .= ' (Periode: ' . date('d/m/Y', strtotime($row['tanggal_mulai'])) . ' - ' . 
                                    //              date('d/m/Y', strtotime($row['tanggal_selesai'])) . ')';
                                    // }
                                ?>
                                <option value="<?php echo $row['id_diskon']; ?>" 
                                        data-jenis="<?php echo $row['jenis_diskon']; ?>"
                                        data-nilai="<?php echo $row['nilai_diskon']; ?>"
                                        data-tipe="<?php echo $row['tipe_diskon']; ?>"
                                        data-kondisi="<?php echo $row['kondisi_diskon']; ?>"
                                        data-nilai-kondisi="<?php echo $row['nilai_kondisi']; ?>"
                                        data-tanggal-mulai="<?php echo $row['tanggal_mulai']; ?>"
                                        data-tanggal-selesai="<?php echo $row['tanggal_selesai']; ?>">
                                    <?php echo $label; ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeDiskon()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="diskonInfo" class="alert alert-info" style="display: none;">
                        <div class="row">
                            <div class="col-6">Diskon:</div>
                            <div class="col-6 text-right" id="diskonAmount">Rp 0</div>
                        </div>
                        <div class="row">
                            <div class="col-6">Total Setelah Diskon:</div>
                            <div class="col-6 text-right" id="totalAfterDiskon">Rp 0</div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12">
                                <small id="diskonPeriode" class="text-muted"></small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Pembayaran -->
                <div class="mt-3">
                    <div class="form-group">
                        <label>Metode Pembayaran</label>
                        <select class="form-control form-control-sm" id="paymentMethod" onchange="togglePaymentInput()">
                            <option value="cash">Tunai</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>

                    <!-- Input Tunai -->
                    <div id="cashInputGroup">
                        <div class="form-group">
                            <label>Jumlah Uang</label>
                            <input type="text" class="form-control form-control-sm" id="cashAmount" onkeyup="formatNumber(this)" onchange="calculateChange()">
                        </div>
                        <div class="form-group">
                            <label>Kembalian</label>
                            <input type="text" class="form-control form-control-sm" id="changeAmount" readonly>
                        </div>
                    </div>

                    <!-- QRIS -->
                    <div id="qrisGroup" style="display: none;">
                        <div class="text-center">
                            <img src="/pos_kasir/assets/img/qris.png" alt="QRIS" style="max-width: 200px;">
                            <p class="mt-2">Scan QRIS untuk pembayaran</p>
                        </div>
                    </div>

                    <!-- Transfer Bank -->
                    <div id="transferGroup" style="display: none;">
                        <p>Transfer ke rekening:</p>
                        <p>Bank BCA: 1234567890</p>
                        <p>a.n. Nama Toko</p>
                    </div>

                    <button class="btn btn-primary btn-sm btn-block mt-3" onclick="completePayment()">
                        Proses Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="/pos_kasir/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="/pos_kasir/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="/pos_kasir/dist/js/adminlte.min.js"></script>

<script>
let cart = [];
let selectedDiskon = null;

$(document).ready(function() {
    // Inisialisasi AdminLTE
    $('[data-widget="pushmenu"]').PushMenu();
    $('[data-widget="treeview"]').Treeview('init');
    
    // Inisialisasi DataTables jika ada
    if ($.fn.DataTable) {
        $('.datatable').DataTable();
    }
    
    // Pastikan sidebar berfungsi
    $('.nav-sidebar .nav-link').on('click', function() {
        $('.nav-sidebar .nav-link').removeClass('active');
        $(this).addClass('active');
    });
});

// Filter berdasarkan kategori
$('.btn-group .btn').click(function() {
    // Hapus class active dari semua tombol
    $('.btn-group .btn').removeClass('active');
    // Tambah class active ke tombol yang diklik
    $(this).addClass('active');
    
    const kategoriId = $(this).data('kategori');
    
    // Filter produk
    if (kategoriId === 'all') {
        $('.product-item').show();
    } else {
        $('.product-item').hide();
        $('.product-item[data-kategori="' + kategoriId + '"]').show();
    }
});

// Fungsi untuk menambahkan produk ke keranjang
function addToCart(product) {
    const existingItem = cart.find(item => item.id === product.id_produk);
    
    if (existingItem) {
        // Cek stok sebelum menambah quantity
        if (existingItem.quantity >= product.stok) {
            alert('Stok tidak mencukupi! Stok tersedia: ' + product.stok);
            return;
        }
        existingItem.quantity++;
    } else {
        // Cek stok untuk item baru
        if (product.stok < 1) {
            alert('Stok tidak mencukupi!');
            return;
        }
        cart.push({
            id: product.id_produk,
            name: product.nama_produk,
            price: parseFloat(product.harga),
            quantity: 1,
            stok: parseInt(product.stok) // Tambahkan informasi stok
        });
    }
    
    updateCartDisplay();
}

// Fungsi untuk memperbarui tampilan keranjang
function updateCartDisplay() {
    const cartContainer = $('#cartItems');
    cartContainer.empty();
    
    let total = 0;
    
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        
        cartContainer.append(`
            <div class="cart-item">
                <div class="row">
                    <div class="col-6">
                        <h6>${item.name}</h6>
                        <p class="text-muted">Rp ${item.price.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="col-3">
                        <div class="input-group input-group-sm">
                            <button class="btn btn-outline-secondary" onclick="updateQuantity(${index}, -1)">-</button>
                            <input type="text" class="form-control text-center" value="${item.quantity}" readonly>
                            <button class="btn btn-outline-secondary" onclick="updateQuantity(${index}, 1)">+</button>
                        </div>
                    </div>
                    <div class="col-3 text-right">
                        <p>Rp ${itemTotal.toLocaleString('id-ID')}</p>
                        <button class="btn btn-sm btn-danger" onclick="removeItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        `);
    });
    
    $('#totalAmount').text(`Rp ${total.toLocaleString('id-ID')}`);
    
    // Update tampilan diskon
    if (selectedDiskon) {
        const diskon = calculateDiskon(total);
        const totalAfterDiskon = total - diskon;
        
        $('#diskonInfo').show();
        $('#diskonAmount').text(`Rp ${diskon.toLocaleString('id-ID')}`);
        $('#totalAfterDiskon').text(`Rp ${totalAfterDiskon.toLocaleString('id-ID')}`);
        
        // Tampilkan periode diskon
        let periodeText = '';
        if (selectedDiskon.tanggalMulai && selectedDiskon.tanggalSelesai) {
            const startDate = new Date(selectedDiskon.tanggalMulai).toLocaleDateString('id-ID');
            const endDate = new Date(selectedDiskon.tanggalSelesai).toLocaleDateString('id-ID');
            periodeText = `Periode diskon: ${startDate} - ${endDate}`;
        }
        $('#diskonPeriode').text(periodeText);
    } else {
        $('#diskonInfo').hide();
    }

    // Cek diskon otomatis setelah update keranjang
    if (!selectedDiskon) {
        checkAutoDiskon();
    }
}

// Fungsi untuk memperbarui jumlah item
function updateQuantity(index, change) {
    const newQuantity = cart[index].quantity + change;
    
    // Cek stok sebelum mengupdate quantity
    if (newQuantity > cart[index].stok) {
        alert('Stok tidak mencukupi! Stok tersedia: ' + cart[index].stok);
        return;
    }
    
    if (newQuantity < 1) {
        cart.splice(index, 1);
    } else {
        cart[index].quantity = newQuantity;
    }
    
    updateCartDisplay();
}

// Fungsi untuk menghapus item
function removeItem(index) {
    cart.splice(index, 1);
    updateCartDisplay();
}

// Fungsi untuk toggle input pembayaran
function togglePaymentInput() {
    const method = $('#paymentMethod').val();
    $('#cashInputGroup').hide();
    $('#qrisGroup').hide();
    $('#transferGroup').hide();
    
    if (method === 'cash') {
        $('#cashInputGroup').show();
    } else if (method === 'qris') {
        $('#qrisGroup').show();
    } else if (method === 'transfer') {
        $('#transferGroup').show();
    }
}

// Fungsi untuk format number
function formatNumber(input) {
    // Hapus semua karakter non-digit
    let value = input.value.replace(/\D/g, '');
    
    // Format dengan pemisah ribuan
    if (value !== '') {
        value = parseInt(value, 10).toLocaleString('id-ID');
    }
    
    // Update nilai input
    input.value = value;
    
    // Hitung kembalian
    calculateChange();
}

// Fungsi untuk menghitung kembalian
function calculateChange() {
    const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const diskon = calculateDiskon(total);
    const totalAfterDiskon = total - diskon;
    const cashAmount = parseFloat($('#cashAmount').val().replace(/\./g, '')) || 0;
    const change = cashAmount - totalAfterDiskon;
    
    if (change >= 0) {
        $('#changeAmount').val('Rp ' + change.toLocaleString('id-ID'));
    } else {
        $('#changeAmount').val('Uang tidak mencukupi');
    }
}

// Fungsi untuk menghitung diskon
function calculateDiskon(total) {
    if (!selectedDiskon) return 0;
    
    let diskon = 0;
    if (selectedDiskon.jenis === 'persentase') {
        diskon = total * (selectedDiskon.nilai / 100);
    } else {
        diskon = selectedDiskon.nilai;
    }
    
    return diskon;
}

// Fungsi untuk menghitung total
function calculateTotal() {
    let total = 0;
    cart.forEach(item => {
        total += item.price * item.quantity;
    });
    return total;
}

// Fungsi untuk menghitung total setelah diskon
function calculateTotalAfterDiskon(total) {
    const diskon = calculateDiskon(total);
    return total - diskon;
}

// Fungsi untuk menghitung total setelah diskon
function applyDiskon() {
    const diskonSelect = $('#diskonSelect');
    const selectedOption = diskonSelect.find('option:selected');
    
    if (selectedOption.val()) {
        selectedDiskon = {
            id: selectedOption.val(),
            jenis: selectedOption.data('jenis'),
            nilai: parseFloat(selectedOption.data('nilai')),
            tipe: selectedOption.data('tipe'),
            kondisi: selectedOption.data('kondisi'),
            nilaiKondisi: selectedOption.data('nilai-kondisi'),
            tanggalMulai: selectedOption.data('tanggal-mulai'),
            tanggalSelesai: selectedOption.data('tanggal-selesai')
        };
        
        // Cek periode diskon
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Set waktu ke awal hari
        
        const startDate = selectedDiskon.tanggalMulai ? new Date(selectedDiskon.tanggalMulai) : null;
        if (startDate) startDate.setHours(0, 0, 0, 0);
        
        const endDate = selectedDiskon.tanggalSelesai ? new Date(selectedDiskon.tanggalSelesai) : null;
        if (endDate) endDate.setHours(23, 59, 59, 999); // Set waktu ke akhir hari
        
        if (startDate && today < startDate) {
            alert('Diskon belum berlaku. Periode diskon dimulai pada ' + startDate.toLocaleDateString('id-ID'));
            removeDiskon();
            return;
        }
        
        if (endDate && today > endDate) {
            alert('Diskon sudah berakhir. Periode diskon berakhir pada ' + endDate.toLocaleDateString('id-ID'));
            removeDiskon();
            return;
        }
        
        // Cek kondisi diskon
        if (selectedDiskon.tipe === 'otomatis') {
            if (selectedDiskon.kondisi === 'pembayaran') {
                const paymentMethod = $('#paymentMethod').val();
                if (paymentMethod !== selectedDiskon.nilaiKondisi) {
                    alert('Diskon hanya berlaku untuk pembayaran ' + selectedDiskon.nilaiKondisi);
                    removeDiskon();
                    return;
                }
            } else if (selectedDiskon.kondisi === 'total_belanja') {
                const total = calculateTotal();
                if (total < parseFloat(selectedDiskon.nilaiKondisi)) {
                    alert('Diskon hanya berlaku untuk pembelian minimal Rp ' + 
                          parseFloat(selectedDiskon.nilaiKondisi).toLocaleString('id-ID'));
                    removeDiskon();
                    return;
                }
            }
        }
        
        updateCartDisplay();
    } else {
        removeDiskon();
    }
}

// Fungsi untuk mengecek diskon otomatis
function checkAutoDiskon() {
    const total = calculateTotal();
    
    // Ambil semua diskon yang aktif
    const diskonOptions = $('#diskonSelect option');
    let selectedAutoDiskon = null;
    
    diskonOptions.each(function() {
        if ($(this).val()) {
            const diskon = {
                id: $(this).val(),
                jenis: $(this).data('jenis'),
                nilai: parseFloat($(this).data('nilai')),
                tipe: $(this).data('tipe'),
                kondisi: $(this).data('kondisi'),
                nilaiKondisi: $(this).data('nilai-kondisi'),
                tanggalMulai: $(this).data('tanggal-mulai'),
                tanggalSelesai: $(this).data('tanggal-selesai')
            };
            
            // Cek apakah diskon otomatis dan memenuhi kondisi
            if (diskon.tipe === 'otomatis') {
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                const startDate = diskon.tanggalMulai ? new Date(diskon.tanggalMulai) : null;
                if (startDate) startDate.setHours(0, 0, 0, 0);
                
                const endDate = diskon.tanggalSelesai ? new Date(diskon.tanggalSelesai) : null;
                if (endDate) endDate.setHours(23, 59, 59, 999);
                
                // Cek periode
                if ((!startDate || today >= startDate) && (!endDate || today <= endDate)) {
                    // Cek kondisi
                    if (diskon.kondisi === 'total_belanja' && total >= parseFloat(diskon.nilaiKondisi)) {
                        selectedAutoDiskon = diskon;
                        return false; // Break loop
                    }
                }
            }
        }
    });
    
    // Terapkan diskon otomatis jika ditemukan
    if (selectedAutoDiskon) {
        selectedDiskon = selectedAutoDiskon;
        $('#diskonSelect').val(selectedDiskon.id);
        updateCartDisplay();
    }
}

function removeDiskon() {
    selectedDiskon = null;
    $('#diskonSelect').val('');
    updateCartDisplay();
}

// Fungsi untuk menyelesaikan pembayaran
function completePayment() {
    if (cart.length === 0) {
        alert('Keranjang masih kosong!');
        return;
    }
    
    // Cek stok sebelum pembayaran
    for (let item of cart) {
        if (item.quantity > item.stok) {
            alert('Stok tidak mencukupi untuk produk: ' + item.name + '\nStok tersedia: ' + item.stok);
            return;
        }
    }
    
    const paymentMethod = $('#paymentMethod').val();
    const cashAmount = $('#cashAmount').val().replace(/\./g, '');
    const total = calculateTotal();
    const diskon = calculateDiskon(total);
    const totalAfterDiskon = total - diskon;
    
    if (paymentMethod === 'cash' && (!cashAmount || parseInt(cashAmount) < totalAfterDiskon)) {
        alert('Jumlah uang tidak mencukupi!');
        return;
    }
    
    // Tampilkan loading
    $('.btn-primary').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
    
    $.ajax({
        url: '/pos_kasir/ajax/process_payment.php',
        type: 'POST',
        data: {
            items: cart,
            payment_method: paymentMethod,
            cash_amount: cashAmount,
            diskon: selectedDiskon ? {
                id: selectedDiskon.id,
                nilai: diskon
            } : null
        },
        success: function(response) {
            try {
                const result = JSON.parse(response);
                if (result.status === 'success') {
                    alert('Pembayaran berhasil!');
                    cart = [];
                    selectedDiskon = null;
                    updateCartDisplay();
                    printReceipt(result.transaction_id);
                    // Reset form
                    $('#paymentMethod').val('cash');
                    $('#cashAmount').val('');
                    $('#changeAmount').val('');
                    $('#diskonSelect').val('');
                    togglePaymentInput();
                } else {
                    alert(result.message || 'Terjadi kesalahan saat memproses pembayaran');
                }
            } catch (e) {
                console.error('Error parsing response:', e);
                alert('Terjadi kesalahan saat memproses pembayaran');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses pembayaran');
        },
        complete: function() {
            // Reset button state
            $('.btn-primary').prop('disabled', false).html('Proses Pembayaran');
        }
    });
}

// Fungsi untuk mencetak struk
function printReceipt(transactionId) {
    window.open(`/pos_kasir/print_receipt.php?id=${transactionId}`, '_blank');
}

// Pencarian produk
$('#search').on('keyup', function() {
    const searchText = $(this).val().toLowerCase();
    const activeKategori = $('.btn-group .btn.active').data('kategori');
    
    $('.product-item').each(function() {
        const productName = $(this).find('.product-name').text().toLowerCase();
        const productKategori = $(this).data('kategori');
        
        // Cek apakah produk sesuai dengan kategori yang aktif
        const kategoriMatch = activeKategori === 'all' || productKategori === activeKategori;
        
        // Cek apakah nama produk sesuai dengan pencarian
        const nameMatch = productName.includes(searchText);
        
        // Tampilkan produk jika sesuai dengan kedua kriteria
        if (kategoriMatch && nameMatch) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});
</script>
</body>
</html> 