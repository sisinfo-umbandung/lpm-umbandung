<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
requireAdmin();

$pdo = getPDO();
$user = getCurrentUser();
$siteTitle = getSetting('site_title', 'LPM UMB Bandung');

// Handle artikel actions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'create_artikel') {
            $judul = trim($_POST['judul'] ?? '');
            $konten = trim($_POST['konten'] ?? '');
            $status = $_POST['status'] ?? 'draft';
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $judul));
            $slug = trim($slug, '-') . '-' . time();
            
            if ($judul) {
                $stmt = $pdo->prepare("INSERT INTO artikel (judul, slug, konten, status, user_id) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$judul, $slug, $konten, $status, $user['id']]);
                $message = 'Artikel berhasil ditambahkan';
                $messageType = 'success';
            }
        } elseif ($_POST['action'] === 'update_artikel') {
            $id = (int)($_POST['id'] ?? 0);
            $judul = trim($_POST['judul'] ?? '');
            $konten = trim($_POST['konten'] ?? '');
            $status = $_POST['status'] ?? 'draft';
            
            if ($id && $judul) {
                $stmt = $pdo->prepare("UPDATE artikel SET judul = ?, konten = ?, status = ? WHERE id = ?");
                $stmt->execute([$judul, $konten, $status, $id]);
                $message = 'Artikel berhasil diupdate';
                $messageType = 'success';
            }
        } elseif ($_POST['action'] === 'delete_artikel') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id) {
                $stmt = $pdo->prepare("DELETE FROM artikel WHERE id = ?");
                $stmt->execute([$id]);
                $message = 'Artikel berhasil dihapus';
                $messageType = 'success';
            }
        } elseif ($_POST['action'] === 'update_settings') {
            $settings = [
                'site_title' => trim($_POST['site_title'] ?? ''),
                'site_description' => trim($_POST['site_description'] ?? ''),
            ];
            foreach ($settings as $key => $value) {
                $stmt = $pdo->prepare("UPDATE pengaturan SET nilai = ? WHERE kunci = ?");
                $stmt->execute([$value, $key]);
            }
            $message = 'Pengaturan berhasil disimpan';
            $messageType = 'success';
            $siteTitle = $settings['site_title'];
        }
    }
}

// Get articles
$stmt = $pdo->query("SELECT a.*, u.nama as author_name FROM artikel a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC");
$artikels = $stmt->fetchAll();

// Get settings
$stmt = $pdo->query("SELECT * FROM pengaturan");
$settings = [];
foreach ($stmt->fetchAll() as $row) {
    $settings[$row['kunci']] = $row['nilai'];
}

// Get stats
$stats = [
    'total_artikel' => $pdo->query("SELECT COUNT(*) FROM artikel")->fetchColumn(),
    'published' => $pdo->query("SELECT COUNT(*) FROM artikel WHERE status = 'published'")->fetchColumn(),
    'draft' => $pdo->query("SELECT COUNT(*) FROM artikel WHERE status = 'draft'")->fetchColumn(),
    'total_users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
];

// Edit mode
$editArtikel = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM artikel WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editArtikel = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= htmlspecialchars($siteTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar { min-height: 100vh; background: #1e293b; }
        .sidebar .nav-link { color: #94a3b8; padding: 0.75rem 1rem; border-radius: 0.5rem; margin: 0.25rem 0.5rem; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: #334155; }
        .sidebar .nav-link i { width: 1.25rem; text-align: center; }
        .stat-card { border-left: 4px solid; }
        .stat-card.primary { border-left-color: #3b82f6; }
        .stat-card.success { border-left-color: #22c55e; }
        .stat-card.warning { border-left-color: #f59e0b; }
        .stat-card.info { border-left-color: #06b6d4; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse" id="sidebarMenu">
                <div class="position-sticky pt-3">
                    <div class="text-center text-white mb-4">
                        <i class="bi bi-newspaper fs-1"></i>
                        <h5 class="mt-2"><?= htmlspecialchars($siteTitle) ?></h5>
                        <small class="text-muted">Admin Panel</small>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#dashboard" data-bs-toggle="tab">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#artikel" data-bs-toggle="tab">
                                <i class="bi bi-journal-text"></i> Kelola Artikel
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#pengaturan" data-bs-toggle="tab">
                                <i class="bi bi-gear"></i> Pengaturan
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-danger" href="/logout.php">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                    <div class="text-center text-muted small mt-4 px-3">
                        Login sebagai: <strong><?= htmlspecialchars($user['nama']) ?></strong> (<?= $user['role'] ?>)
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="tab-content">
                    <!-- Dashboard Tab -->
                    <div class="tab-pane fade show active" id="dashboard" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2>Dashboard</h2>
                            <a href="/index.php" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-eye me-1"></i> Lihat Website
                            </a>
                        </div>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Stats Cards -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="card stat-card primary">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">Total Artikel</h6>
                                        <h2 class="mb-0"><?= $stats['total_artikel'] ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card success">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">Published</h6>
                                        <h2 class="mb-0"><?= $stats['published'] ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card warning">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">Draft</h6>
                                        <h2 class="mb-0"><?= $stats['draft'] ?></h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card stat-card info">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-1">Total User</h6>
                                        <h2 class="mb-0"><?= $stats['total_users'] ?></h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Articles -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Artikel Terbaru</h5>
                                <a href="#artikel" data-bs-toggle="tab" class="btn btn-sm btn-primary">
                                    <i class="bi bi-plus me-1"></i> Tambah
                                </a>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($artikels)): ?>
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-journal-x display-4"></i>
                                        <p class="mt-2">Belum ada artikel. <a href="#artikel" data-bs-toggle="tab">Buat yang pertama</a></p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Judul</th>
                                                    <th>Status</th>
                                                    <th>Author</th>
                                                    <th>Tanggal</th>
                                                    <th class="text-end">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach (array_slice($artikels, 0, 5) as $art): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($art['judul']) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= $art['status'] === 'published' ? 'success' : 'warning' ?>">
                                                            <?= ucfirst($art['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= htmlspecialchars($art['author_name'] ?? '-') ?></td>
                                                    <td><?= date('d M Y', strtotime($art['created_at'])) ?></td>
                                                    <td class="text-end">
                                                        <a href="#artikel?edit=<?= $art['id'] ?>" data-bs-toggle="tab" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Hapus artikel ini?')">
                                                            <input type="hidden" name="action" value="delete_artikel">
                                                            <input type="hidden" name="id" value="<?= $art['id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Artikel Tab -->
                    <div class="tab-pane fade" id="artikel" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2><?= $editArtikel ? 'Edit Artikel' : 'Kelola Artikel' ?></h2>
                            <?php if ($editArtikel): ?>
                                <a href="/dashboard.php#artikel" class="btn btn-outline-secondary">
                                    <i class="bi bi-x me-1"></i> Batal Edit
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Form Artikel -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0"><?= $editArtikel ? 'Edit' : 'Tambah' ?> Artikel</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="<?= $editArtikel ? 'update_artikel' : 'create_artikel' ?>">
                                    <?php if ($editArtikel): ?>
                                        <input type="hidden" name="id" value="<?= $editArtikel['id'] ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Judul *</label>
                                        <input type="text" class="form-control" name="judul" 
                                               value="<?= htmlspecialchars($editArtikel['judul'] ?? '') ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Konten</label>
                                        <textarea class="form-control" name="konten" rows="10"><?= htmlspecialchars($editArtikel['konten'] ?? '') ?></textarea>
                                        <div class="form-text">Gunakan HTML biasa untuk formatting</div>
                                    </div>
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Status</label>
                                            <select class="form-select" name="status">
                                                <option value="draft" <?= ($editArtikel['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                                                <option value="published" <?= ($editArtikel['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-<?= $editArtikel ? 'check-circle' : 'plus-circle' ?> me-1"></i>
                                        <?= $editArtikel ? 'Update' : 'Simpan' ?> Artikel
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Daftar Artikel -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Daftar Semua Artikel</h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if (empty($artikels)): ?>
                                    <div class="text-center py-5 text-muted">
                                        <i class="bi bi-journal-x display-4"></i>
                                        <p class="mt-2">Belum ada artikel</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 50px;">#</th>
                                                    <th>Judul</th>
                                                    <th>Slug</th>
                                                    <th>Status</th>
                                                    <th>Author</th>
                                                    <th>Dibuat</th>
                                                    <th class="text-end" style="width: 120px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($artikels as $art): ?>
                                                <tr>
                                                    <td><?= $art['id'] ?></td>
                                                    <td><?= htmlspecialchars($art['judul']) ?></td>
                                                    <td><code><?= htmlspecialchars($art['slug']) ?></code></td>
                                                    <td>
                                                        <span class="badge bg-<?= $art['status'] === 'published' ? 'success' : 'warning' ?>">
                                                            <?= ucfirst($art['status']) ?>
                                                        </span>
                                                    </td>
                                                    <td><?= htmlspecialchars($art['author_name'] ?? '-') ?></td>
                                                    <td><?= date('d M Y H:i', strtotime($art['created_at'])) ?></td>
                                                    <td class="text-end">
                                                        <a href="/dashboard.php#artikel?edit=<?= $art['id'] ?>" data-bs-toggle="tab" class="btn btn-sm btn-outline-primary" title="Edit">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus artikel ini?')">
                                                            <input type="hidden" name="action" value="delete_artikel">
                                                            <input type="hidden" name="id" value="<?= $art['id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pengaturan Tab -->
                    <div class="tab-pane fade" id="pengaturan" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2>Pengaturan Website</h2>
                        </div>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
                                <?= htmlspecialchars($message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Informasi Umum</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="action" value="update_settings">
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Judul Website *</label>
                                        <input type="text" class="form-control" name="site_title" 
                                               value="<?= htmlspecialchars($settings['site_title'] ?? '') ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Deskripsi Website</label>
                                        <textarea class="form-control" name="site_description" rows="3"><?= htmlspecialchars($settings['site_description'] ?? '') ?></textarea>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Info Database</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr><td><strong>Database</strong></td><td>lpm_umbandung</td></tr>
                                    <tr><td><strong>Host</strong></td><td>localhost</td></tr>
                                    <tr><td><strong>Tabel</strong></td><td>users, artikel, pengaturan</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-switch tab based on URL hash
        document.addEventListener('DOMContentLoaded', function() {
            var hash = window.location.hash;
            if (hash) {
                var tab = document.querySelector('[href="' + hash + '"]');
                if (tab) {
                    new bootstrap.Tab(tab).show();
                }
            }
            
            // Update URL hash when tab changes
            var tabElements = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabElements.forEach(function(tab) {
                tab.addEventListener('shown.bs.tab', function(e) {
                    history.replaceState(null, null, e.target.getAttribute('href'));
                });
            });
        });
    </script>
</body>
</html>