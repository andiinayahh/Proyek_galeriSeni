<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require_once 'config.php';
$nama = $_SESSION['nama'];
$role = $_SESSION['role'];
$id_seniman = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT k.id, k.path_gambar, k.judul, k.deskripsi, k.kategori, k.harga, u.nama AS nama_seniman 
                       FROM karya_seni k 
                       JOIN users u ON k.id_seniman = u.id 
                       WHERE k.id_seniman = ?");
$stmt->execute([$id_seniman]);
$artworks = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Karya Seni - Beranda</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #f9f9f9;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 50px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .logo h1 {
            margin: 0;
            color: #FF6B00;
            font-size: 1.8rem;
        }
        
        nav ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        nav ul li {
            margin-left: 1.5rem;
        }
        
        nav ul li a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        nav ul li a:hover {
            color: #FF6B00;
        }
        
        .profile-container {
            position: relative;
            margin-left: 20px;
        }
        
        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid #FF6B00;
            transition: transform 0.3s;
        }
        
        .profile-pic:hover {
            transform: scale(1.1);
        }
        
        .profile-dropdown {
            position: absolute;
            right: 0;
            top: 50px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 300px;
            padding: 20px;
            display: none;
            z-index: 100;
        }
        
        .profile-dropdown.show {
            display: block;
        }
        
        .hero {
            height: 400px;
            background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRgeJdQq31HvUP4kfah4O_EUYCGBjovIfbnxQ&s');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .hero-content {
            position: relative;
            z-index: 1;
            padding: 0 20px;
            max-width: 800px;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        .search-bar {
            display: flex;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-bar input {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 4px 0 0 4px;
            font-size: 1rem;
        }
        
        .search-bar button {
            padding: 12px 20px;
            background-color: #FF6B00;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        
        .search-bar button:hover {
            background-color: #E05D00;
        }
        
        .main-content {
            padding: 50px;
        }
        
        .section-title {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: #333;
            position: relative;
            padding-bottom: 0.5rem;
            text-align: center;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: #FF6B00;
        }
        
        /* Featured Categories */
        .categories {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .category-card {
            width: 180px;
            height: 180px;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .category-card:hover {
            transform: translateY(-5px);
        }
        
        .category-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .category-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
            padding: 15px;
            color: white;
        }
        
        .category-overlay h3 {
            margin: 0;
            font-size: 1.2rem;
        }
        
        /* Gallery Container */
        .gallery-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 3rem;
        }
        
        .art-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .art-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .art-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }
        
        .art-info {
            padding: 15px;
        }
        
        .art-info h3 {
            margin: 0 0 10px 0;
            font-size: 1.1rem;
            color: #222;
        }
        
        .art-info p {
            margin: 0 0 15px 0;
            color: #666;
            font-size: 0.9rem;
        }
        
        .artist {
            display: flex;
            align-items: center;
            margin-top: 0.5rem;
        }
        
        .artist img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 0.5rem;
            object-fit: cover;
        }
        
        .artist span {
            font-size: 0.8rem;
            color: #555;
        }
        
        .category-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(255, 107, 0, 0.9);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .view-button {
            background-color: #FF6B00;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
            width: 100%;
            margin-top: 10px;
        }
        
        .view-button:hover {
            background-color: #E05D00;
        }
        
        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 2rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        
        .footer-section {
            flex: 1;
            min-width: 250px;
            margin-bottom: 1rem;
            padding: 0 15px;
        }
        
        .footer-section h3 {
            margin-bottom: 1rem;
            color: #FF6B00;
        }
        
        .footer-section p, .footer-section a {
            color: #ddd;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        .footer-section a {
            display: block;
            margin-bottom: 0.5rem;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section a:hover {
            color: #FF6B00;
        }
        
        .copyright {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #444;
            font-size: 0.8rem;
            color: #aaa;
        }
        
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
                flex-wrap: wrap;
            }
            
            nav {
                order: 3;
                width: 100%;
                margin-top: 15px;
            }
            
            nav ul {
                justify-content: center;
            }
            
            .profile-container {
                margin-left: auto;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .main-content {
                padding: 30px 20px;
            }
            
            .gallery-container {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 20px;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
            
            .category-card {
                width: 140px;
                height: 140px;
            }
        }
        
        @media (max-width: 480px) {
            nav ul li {
                margin-left: 1rem;
            }
            
            .hero h1 {
                font-size: 1.5rem;
            }
            
            .hero {
                height: 350px;
            }
            
            .search-bar {
                flex-direction: column;
            }
            
            .search-bar input, .search-bar button {
                border-radius: 4px;
                width: 100%;
            }
            
            .search-bar button {
                margin-top: 10px;
            }
            
            .gallery-container {
                grid-template-columns: 1fr;
            }
            
            .profile-dropdown {
                width: 280px;
                right: -20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <h1>Galeri Seni</h1>
        </div>

        <nav>
            <ul>
                <li><a href="beranda.php">Beranda</a></li>
                <li><a href="galery.php">Gallery</a></li>
                <li><a href="pameran.html">Pameran</a></li>
                <li><a href="tentang.html">Tentang</a></li>
            </ul>
        </nav>

        <div class="profile-container">
            <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profil" class="profile-pic"
                id="profilePic">
            <div class="profile-dropdown" id="profileDropdown">
                <div class="profile-info">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Profil">
                    <div>
                        <h3><?php echo htmlspecialchars($nama); ?></h3>
                        <p><?php echo htmlspecialchars($role); ?></p>
                    </div>
                </div>
                <p class="bio">Saya seorang seniman visual yang berfokus pada lukisan abstrak dan instalasi seni. Karya
                    saya terinspirasi oleh alam dan emosi manusia.</p>
                <div class="profile-artworks">
                    <h4>Karya Saya</h4>
                    <div class="artwork-grid">
                        <?php foreach ($artworks as $artwork): ?>
                            <div class="artwork-item">
                                <img src="<?= htmlspecialchars($artwork['path_gambar']); ?>"
                                    alt="Karya <?= $artwork['id']; ?>" class="artwork-image"
                                    data-id="<?= $artwork['id']; ?>"
                                    data-judul="<?= htmlspecialchars($artwork['judul']); ?>"
                                    data-deskripsi="<?= htmlspecialchars($artwork['deskripsi']); ?>"
                                    data-kategori="<?= htmlspecialchars($artwork['kategori']); ?>"
                                    data-harga="<?= $artwork['harga']; ?>"
                                    data-path="<?= htmlspecialchars($artwork['path_gambar']); ?>"
                                    onclick="openModal(<?= $artwork['id']; ?>)">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="#" class="upload-btn" id="openModal">Unggah Karya Baru</a>
                <a href="logout.php" class="upload-btn">Log Out</a>
            </div>
        </div>
    </header>
    
    <section class="hero">
        <div class="hero-content">
            <h1>Temukan Inspirasi dalam Setiap Karya</h1>
            <p>Jelajahi koleksi seni terbaik dari seniman berbakat di seluruh Indonesia</p>
            <div class="search-bar">
                <input type="text" placeholder="Cari karya seni, pameran, atau kategori...">
                <button>Cari</button>
            </div>
        </div>
    </section>
    
    <main class="main-content">
        <section>
            <h2 class="section-title">Kategori Populer</h2>
            
            <div class="categories">
                <div class="category-card">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTt_YRLaANyut6H3SpJaHsayX75ZX-XGagPuA&s" alt="Lukisan">
                    <div class="category-overlay">
                        <h3>Lukisan</h3>
                    </div>
                </div>
                
                <div class="category-card">
                    <img src="data:image/jpeg;base64,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" alt="Patung">
                    <div class="category-overlay">
                        <h3>Patung</h3>
                    </div>
                </div>
                
                <div class="category-card">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSIZxKQgfoOP-FSqNrr9y71E8jfehPBPZzSOw&s" alt="Fotografi">
                    <div class="category-overlay">
                        <h3>Fotografi</h3>
                    </div>
                </div>
                
                <div class="category-card">
                    <img src="data:image/jpeg;base64,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" alt="Digital Art">
                    <div class="category-overlay">
                        <h3>Digital Art</h3>
                    </div>
                </div>
                
                <div class="category-card">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT4SyX9CdlrodvUGWcyg-XKgj9Bk2HdBWzKrw&s" alt="Seni Tradisional">
                    <div class="category-overlay">
                        <h3>Tradisional</h3>
                    </div>
                </div>
            </div>
        </section>
        
        <section>
            <h2 class="section-title">Karya Terbaru</h2>
            
            <div class="gallery-container">
                <!-- Karya 1 -->
                <div class="art-card">
                    <img src="data:image/jpeg;base64,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" alt="Lukisan Abstrak">
                    <span class="category-badge">Lukisan</span>
                    <div class="art-info">
                        <p>Lukisan abstrak </p>
                        <div class="artist">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQDON9y2KMYyVKWu26GrTum4sz60YWXehXfGxM8Re7U8hnS3ZbSOd8nMOtcTynsYuYgcow&usqp=CAU" alt="Seniman">
                            <span>Andi Wijaya</span>
                        </div>
                    </div>
                </div>
                
                <!-- Karya 2 -->
                <div class="art-card">
                    <img src="data:image/jpeg;base64,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" alt="Patung Modern">
                    <span class="category-badge">Patung</span>
                    <div class="art-info">
                        <p>Patung </p>
                        <div class="artist">
                            <img src="data:image/jpeg;base64,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" alt="Seniman">
                            <span>Kurniawan</span>
                        </div>
                    </div>
                </div>
                
                <!-- Karya 3 -->
                <div class="art-card">
                    <img src="data:image/jpeg;base64,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" alt="Fotografi Konseptual">
                    <span class="category-badge">Fotografi</span>
                    <div class="art-info">
                        <p>fotografi </p>
                        <div class="artist">
                            <img src="data:image/jpeg;base64,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" alt="Seniman">
                            <span>Budi Santoso</span>
                        </div>
                    </div>
                </div>
                
                <!-- Karya 4 -->
                <div class="art-card">
                    <img src="data:image/jpeg;base64,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" alt="Digital Art">
                    <span class="category-badge">Digital Art</span>
                    <div class="art-info">
                        <p>Karya seni digital</p>
                        <div class="artist">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrjTRQNE-9IzMSc1-v340uzJeanzjLtzt-0ug-d3GKcOYwQyXG-DbwKs50WBKXEpQKudk&usqp=CAU" alt="Seniman">
                            <span>Sarah Artista</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Tentang Kami</h3>
                <p>Galeri Seni Digital adalah platform untuk memamerkan karya seni dari berbagai seniman berbakat di seluruh Indonesia.</p>
            </div>
              
    
    <script>
        // Toggle profile dropdown
        const profilePic = document.getElementById('profilePic');
        const profileDropdown = document.getElementById('profileDropdown');
        
        profilePic.addEventListener('click', function(e) {
            e.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            profileDropdown.classList.remove('show');
        });
        
        // Prevent dropdown from closing when clicking inside it
        profileDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        // View button functionality
        const viewButtons = document.querySelectorAll('.view-button');
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Here you can add functionality to view artwork details
                alert('Fitur melihat detail karya akan ditampilkan di sini');
            });
        });
        
        // Close dropdown with ESC key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                profileDropdown.classList.remove('show');
            }
        }); 
    </script>
</body>
</html>