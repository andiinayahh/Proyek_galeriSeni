<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Karya Seni</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
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
        
        .auth-buttons a {
            margin-left: 15px;
            padding: 8px 20px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .login {
            color: #333;
            border: 1px solid #333;
        }
        
        .login:hover {
            background-color: #f1f1f1;
        }
        
        .register {
            background-color: #FF6B00;
            color: white;
            border: 1px solid #FF6B00;
        }

        
        .hero {
            height: 400px;
            background-image: url('https://source.unsplash.com/random/1600x900/?art,gallery');
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
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }
        
        .gallery {
            padding: 50px;
            text-align: center;
            background-color: #f9f9f9;
        }
        
        .gallery h2 {
            margin-bottom: 30px;
            font-size: 2rem;
            color: #333;
        }
        
        .artworks {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 30px;
        }
        
        .artwork {
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .artwork:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .artwork img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }
        
        .artwork-info {
            padding: 15px;
        }
        
        .artwork-info h3 {
            margin: 0 0 10px 0;
            font-size: 1.1rem;
            color: #222;
        }
        
        .artwork-info p {
            margin: 0 0 15px 0;
            color: #666;
            font-size: 0.9rem;
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
        }
        
        .view-button:hover {
            background-color: #E05D00;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            animation: fadeIn 0.3s;
            overflow-y: auto;
        }

        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 30px;
            border-radius: 8px;
            width: 400px;
            max-width: 90%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            text-align: center;
            position: relative;
        }

        .modal-content h2 {
            color: #FF6B00;
            margin-top: 0;
            margin-bottom: 15px;
        }

        .modal-content p {
            margin-bottom: 25px;
            color: #555;
            line-height: 1.5;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .modal-button {
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            transition: all 0.3s;
            border: none;
            font-size: 0.9rem;
        }

        .masuk-button {
            background-color: #FF6B00;
            color: white;
        }

        .masuk-button:hover {
            background-color: #E05D00;
        }

        .cancel-button {
            background-color: #f1f1f1;
            color: #333;
        }

        .cancel-button:hover {
            background-color: #e1e1e1;
        }

        .close {
            color: #aaa;
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s;
        }

        /* Style untuk tombol masuk dan daftar di header */
.auth-buttons a.masuk {
    color: #333;
    border: 1px solid #333;
    padding: 8px 20px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
}

.auth-buttons a.masuk:hover {
    background-color: #f1f1f1;
    color: #333;
}

.auth-buttons a.daftar {
    background-color: #FF6B00;
    color: white;
    padding: 8px 20px;
    border-radius: 4px;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.3s ease;
    border: 1px solid #FF6B00;
}

.auth-buttons a.daftar:hover {
    background-color: #E05D00;
    border-color: #E05D00;
}

/* Style untuk tombol masuk dan daftar di modal (jika diperlukan) */
.modal-button.masuk-button {
    background-color: #FF6B00;
    color: white;
}

.modal-button.masuk-button:hover {
    background-color: #E05D00;
}

.modal-button.daftar-button {
    background-color: #333;
    color: white;
}

.modal-button.daftar-button:hover {
    background-color: #222;
}

        .close:hover {
            color: #333;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            header {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }
            
            .auth-buttons {
                width: 100%;
                display: flex;
                justify-content: center;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .gallery {
                padding: 30px 20px;
            }
            
            .artworks {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 480px) {
            .modal-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .modal-button {
                width: 100%;
            }
            
            .artworks {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <h1>Galeri Seni</h1>
        </div>
        <div class="auth-buttons">
            <a href="login.html" class="masuk">Masuk</a>
            <a href="daftar.html" class="daftar">Daftar</a>
        </div>
    </header>
    
    <section class="hero">
        <div class="hero-content">
            <h1>Selamat Datang</h1>
            <p>Jelajahi koleksi karya seni terbaik dari berbagai seniman berbakat di galeri kami. Temukan inspirasi dalam setiap karya.</p>
        </div>
    </section>
    
    <section class="gallery">
        <h2>Koleksi Karya Seni</h2>
        <div class="artworks">
            <!-- Karya Seni akan di-generate oleh JavaScript -->
        </div>
    </section>

    <!-- Modal Login -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Login Diperlukan</h2>
            <p>Untuk melihat detail karya seni, silakan login terlebih dahulu atau daftar jika Anda belum memiliki akun.</p>
            <div class="modal-buttons">
                <button class="modal-button masuk-button" onclick="redirectToLogin()">Login</button>
                <button class="modal-button cancel-button" onclick="closeModal()">Nanti Saja</button>
            </div>
        </div>
    </div>

    <script>
        // Data karya seni lengkap
        const artworks = [
            { title: "Pemandangan Senja", artist: "Seniman A", img: "data:image/jpeg;base64,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" },
            {title: "Abstraksi Warna", artist: "Seniman B", img: "https://png.pngtree.com/thumb_back/fh260/background/20230705/pngtree-autumn-forest-landscape-in-vibrant-sunset-colors-abstract-watercolor-painting-with-image_3799344.jpg" },
            { title: "Potret Diri", artist: "Seniman C", img: "https://www.bentarabudaya.com/assets/photo/2021/09/04/61331c88aab34.jpg" },
            { title: "Harmoni Alam", artist: "Seniman D", img: "https://opiniremaja.com/wp-content/uploads/2024/12/0a9f32bb-7284-4238-b200-3c4c0738822e.jpeg" },
            { title: "Ekspresi Bebas", artist: "Seniman E", img: "https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEhVb9Plwgp2-5iD1JLcYnBXlrQi7IQx04XFW-4KQqgGNczUapabqPAEbDuCGJuwtAzhw-XZ1zF7BO7csUfarIseYb6jGiq1CMsMpWJgYXrkUiLFHJ0eYvt5OAlW8h2Jily_dpqOTjlr8ba_/s1600/in+the+rain+wip+1.jpg " },
            { title: "Kota Tua", artist: "Seniman F", img: "https://media.istockphoto.com/id/1219872926/id/vektor/pemandangan-gambar-lukisan-cat-air-kota-tua-katedral-di-situs-warisan-dunia-siena-di-italia.jpg?s=1024x1024&w=is&k=20&c=aC0Ves-0mPGXMYTrVuHwwShVFbtQe1ODnNvxp9h6X10=" },
            { title: "Lautan Biru", artist: "Seniman G", img: "https://kopikeliling.com/wp-content/uploads/2015/04/blue-sea-jim-romeo-.jpg" },
            { title: "Bunga Musim Semi", artist: "Seniman H", img: "https://img.lovepik.com/photo/40242/5606.jpg_wh860.jpg" },
            { title: "Figur Manusia", artist: "Seniman I", img: "https://png.pngtree.com/thumb_back/fw800/background/20241224/pngtree-painting-of-a-person-capturing-the-essence-humanity-and-emotions-through-image_16853705.jpg" },
            { title: "Geometri Warna", artist: "Seniman J", img: "https://serupa.id/wp-content/uploads/2019/07/kubisme-pengertian-contoh-ciri-tokoh-analisis.jpg" },
            { title: "Pohon Kehidupan", artist: "Seniman K", img: "https://png.pngtree.com/png-clipart/20240930/original/pngtree-tree-of-life-watercolor-painting-png-image_16128007.png" },
            { title: "Impresi Kota", artist: "Seniman L", img: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTRaDUSeW9Wu6J9B4VcePRUypYqBmioFTWGsw&s" },
            { title: "Air Terjun", artist: "Seniman M", img: "https://images.tokopedia.net/img/cache/700/VqbcmM/2020/12/1/aae9584f-df7f-4793-9b0a-f7b7195aadef.jpg "},
            { title: "Wajah-wajah", artist: "Seniman N", img: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQKtvCON1qcutVMrKowjlNFBUavQ5OwlEMhjg&s" },
            { title: "Pola Abstrak", artist: "Seniman O", img: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTehMjWbAoqDWksJIWSw-NAMXCLMZ7gl9MgYA&s" },
            { title: "Senja di Pantai", artist: "Seniman P", img: "https://i.pinimg.com/736x/20/28/c3/2028c3928c8c08bc075684030340802b.jpg" }
        ];

        // Fungsi untuk menampilkan karya seni
        function displayArtworks() {
            const artworksContainer = document.querySelector('.artworks');
            artworksContainer.innerHTML = '';
            
            artworks.forEach((artwork, index) => {
                const artworkElement = document.createElement('div');
                artworkElement.className = 'artwork';
                artworkElement.innerHTML = `
                    <img src="${artwork.img}" alt="${artwork.title}" loading="lazy">
                    <div class="artwork-info">
                        <h3>${artwork.title}</h3>
                        <p>${artwork.artist}</p>

                    </div>
                `;
                artworksContainer.appendChild(artworkElement);
            });
        }

        // Fungsi untuk menampilkan modal login
        function showLoginModal() {
            document.getElementById('loginModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Mencegah scrolling latar belakang
        }

        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('loginModal').style.display = 'none';
            document.body.style.overflow = 'auto'; // Mengembalikan scrolling
        }

        // Fungsi untuk redirect ke halaman login
        function redirectToLogin() {
            // Ganti dengan URL halaman login yang sesuai
            window.location.href = '#login';
            closeModal();
        }

        // Event listener untuk menutup modal dengan berbagai cara
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('loginModal');
            if (event.target === modal) {
                closeModal();
            }
        });

        // Tutup modal dengan tombol ESC
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // Inisialisasi halaman saat DOM sudah loaded
        document.addEventListener('DOMContentLoaded', function() {
            displayArtworks();
            
            // Event listener untuk tombol login di header
            document.querySelector('.login').addEventListener('click', function(e) {
                e.preventDefault();
                redirectToLogin();
            });
            
            // Event listener untuk tombol register di header
            document.querySelector('.register').addEventListener('click', function(e) {
                e.preventDefault();
                // Ganti dengan URL halaman register yang sesuai
                window.location.href = '#register';
            });
        });

        // Fungsi untuk lazy loading images (opsional enhancement)
        function setupLazyLoading() {
            const images = document.querySelectorAll('img[loading="lazy"]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src || img.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                images.forEach(img => imageObserver.observe(img));
            }
        }
    </script>
</body>
</html>