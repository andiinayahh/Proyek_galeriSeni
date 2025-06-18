<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'config.php';
$nama = $_SESSION['nama'];
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$stmt_conversations = $pdo->prepare("
    SELECT c.id AS conversation_id, 
           u1.nama AS user1, 
           u2.nama AS user2, 
           m.message AS last_message, 
           m.sent_at AS last_sent_at
    FROM conversations c
    JOIN users u1 ON c.user1_id = u1.id
    JOIN users u2 ON c.user2_id = u2.id
    JOIN messages m ON m.conversation_id = c.id
    WHERE (c.user1_id = ? OR c.user2_id = ?)
    AND m.sent_at = (
        SELECT MAX(m2.sent_at) 
        FROM messages m2 
        WHERE m2.conversation_id = c.id
    )
    ORDER BY m.sent_at DESC
");
$stmt_conversations->execute([$user_id, $user_id]);
$conversations = $stmt_conversations->fetchAll(PDO::FETCH_ASSOC);
$stmt_artworks = $pdo->prepare("
    SELECT DISTINCT k.id, k.path_gambar, k.judul, k.deskripsi, k.kategori, k.harga, u.nama AS nama_seniman, 
           k.id_seniman, t.status AS transaction_status
    FROM karya_seni k
    JOIN users u ON k.id_seniman = u.id
    LEFT JOIN transaction_items ti ON ti.artwork_id = k.id
    LEFT JOIN transactions t ON t.transaction_id = ti.transaction_id
    WHERE t.status = 'completed' OR t.transaction_id IS NULL
");
$stmt_artworks->execute();
$artworks = $stmt_artworks->fetchAll(PDO::FETCH_ASSOC);
if (!$artworks) {
    echo "<p>Tidak ada karya seni yang ditemukan.</p>";
}


?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Karya Seni</title>
    <link rel="stylesheet" href="beranda.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
 <head>
    <!-- Kode lainnya yang sudah ada -->
    <script>
        // Blokir klik kanan di seluruh halaman
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            alert('Fitur klik kanan dinonaktifkan untuk melindungi karya seni');
        });

        // Blokir drag gambar
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.artwork-image').forEach(img => {
                img.addEventListener('dragstart', function(e) {
                    e.preventDefault();
                });
            });
        });

        // Blokir pintasan keyboard
        document.addEventListener('keydown', function(e) {
            if (e.keyCode === 44 ||    // Print Screen
                e.keyCode === 123 ||   // F12
                (e.ctrlKey && e.shiftKey && e.keyCode === 73) // Ctrl+Shift+I
            ) {
                e.preventDefault();
                alert('Pintasan keyboard dinonaktifkan');
            }
        });
        
    </script>
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
        <div class="notification-container">
            <i class="fas fa-bell" id="notificationIcon"></i>
            <div class="notification-dropdown" id="notificationDropdown">
                <ul>
                    <?php if (empty($conversations)): ?>
                        <li class="no-conversations">Tidak ada percakapan</li>
                    <?php else: ?>
                        <?php foreach ($conversations as $conversation): ?>
                            <li>
                                <a href="#" class="openChatButton"
                                    data-conversation-id="<?= $conversation['conversation_id']; ?>"
                                    data-seniman-name="<?= htmlspecialchars($conversation['user1']); ?>">
                                    <strong><?= htmlspecialchars($conversation['user1']); ?></strong><br>
                                    <small><?= htmlspecialchars($conversation['last_message']); ?></small><br>
                                    <small><em><?= $conversation['last_sent_at']; ?></em></small>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="cart-container">
            <i class="fas fa-shopping-cart" id="cartIcon"></i>
            <div class="cart-dropdown" id="cartDropdown" style="display: none;">
                <ul id="cartItemsList">
                </ul>
                <div class="cart-summary">
                    <p><strong>Total Harga: </strong><span id="totalPrice">0</span></p>
                    <p><strong>Total Jumlah: </strong><span id="totalQuantity">0</span></p>
                    <button id="checkoutButton">Checkout</button>
                </div>
            </div>
        </div>
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
                <?php if ($role == 'seniman'): ?>
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
                <?php endif; ?>
                <a href="logout.php" class="upload-btn">Log Out</a>
            </div>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <h1>Selamat Datang <?php echo htmlspecialchars($nama); ?>! di Galeri Seni Digital</h1>
            <p>Temukan karya-karya seni terbaik dari berbagai seniman berbakat dan jelajahi pameran seni terbaru di
                sekitar Anda</p>
        </div>
    </section>

    <main class="main-content">
        <section>
            <h2 class="section-title">Karya Terbaru</h2>
            <div class="gallery-container">
                <?php foreach ($artworks as $artwork): ?>
                    <div class="art-card">
                        <div class="artwork-image-container">
                            <img src="<?= htmlspecialchars($artwork['path_gambar']); ?>"
                                alt="<?= htmlspecialchars($artwork['judul']); ?>" class="artwork-image">
                            <div class="watermark">galerseni.com</div>
                        </div>
                        <div class="art-info">
                            <h3 class="artwork-title"><?= htmlspecialchars($artwork['judul']); ?></h3>
                            <h3 class="artwork-price">Rp. <?= number_format($artwork['harga'], 0, ',', '.'); ?></h3>
                            <p class="artwork-description"><?= htmlspecialchars($artwork['deskripsi']); ?></p>
                            

                            <?php
                            $isSold = false;
                            if (isset($artwork['transaction_status']) && $artwork['transaction_status'] == 'completed') {
                                $isSold = true;
                            }
                            ?>

                            <?php if ($isSold): ?>
                                <span class="sold-tag">Terjual</span>
                            <?php endif; ?>

                            <?php if ($role == 'pengunjung' && !$isSold): ?>
                                <button type="button" class="buy-button" data-artwork-id="<?= $artwork['id']; ?>"
                                    data-artwork-title="<?= htmlspecialchars($artwork['judul']); ?>"
                                    data-artwork-price="<?= $artwork['harga']; ?>"
                                    data-artwork-image="<?= htmlspecialchars($artwork['path_gambar']); ?>"
                                    onclick="openBuyModal(this)">Beli</button>
                            <?php endif; ?>

                            <?php if ($role == 'seniman'): ?>
                                <button type="button" class="view-detail-button"
                                    onclick="openDetailModal('<?= htmlspecialchars($artwork['judul']); ?>', '<?= htmlspecialchars($artwork['deskripsi']); ?>')">
                                    Lihat Detail
                                </button>
                            <?php endif; ?>

                            <?php if ($role == 'pengunjung' && !$isSold): ?>
                                <button type="button" class="start-conversation-btn"
                                    data-seniman-id="<?= $artwork['id_seniman']; ?>" data-artwork-id="<?= $artwork['id']; ?>"
                                    onclick="startConversation(this)">
                                    Chat Seniman
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>


        <section>
            <h2 class="section-title">Rekomendasi Pameran Offline</h2>
            <div class="exhibitions-container">
                <div class="exhibition-card">
                    <img src="https://cdn1.katadata.co.id/media/images/2022/07/26/Mengunjungi_Pameran_Seni_Rupa_Kontemporer_Indonesia_Transposisi_di_Gakeri_Nasional_Indonesia-2022_07_26-16_11_06_59d9c35ff58ec0694f94a94783bbe45b.jpg" alt="Pameran Seni Kontemporer">
                    <div class="exhibition-info">
                        <h3>Art Jakarta 2023</h3>
                        <p>Pameran seni kontemporer terbesar di Indonesia</p>
                        <div class="date-location">
                            <span class="date">15-20 Nov 2023</span>
                            <span class="location">Jakarta Convention Center</span>
                        </div>
                    </div>
                </div>

                <div class="exhibition-card">
                    <img src="https://img.harianjogja.com/posts/2021/10/09/1085099/biennale-oke.jpg" alt="Pameran Seni Rupa">
                    <div class="exhibition-info">
                        <h3>Biennale Jogja XVI</h3>
                        <p>Pameran seni rupa internasional dengan tema "Equator"</p>
                        <div class="date-location">
                            <span class="date">1 Okt - 30 Nov 2023</span>
                            <span class="location">Jogja National Museum</span>
                        </div>
                    </div>
                </div>

                <div class="exhibition-card">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTqTwmztJDusG5nW_tWfxb7xJfEOI83AtR9rA&s" alt="Pameran Fotografi">
                    <div class="exhibition-info">
                        <h3>Moments in Time</h3>
                        <p>Pameran fotografi dokumenter oleh 20 fotografer Asia</p>
                        <div class="date-location">
                            <span class="date">5-25 Des 2023</span>
                            <span class="location">Galeri Nasional, Jakarta</span>
                        </div>
                    </div>
                </div>

                <div class="exhibition-card">
                    <img src="https://asset-2.tstatic.net/tribunnews/foto/bank/images/graffiti-nature.jpg" alt="Pameran Seni Digital">
                    <div class="exhibition-info">
                        <h3>Digital Art Experience</h3>
                        <p>Eksplorasi seni digital dan interaktif</p>
                        <div class="date-location">
                            <span class="date">10-30 Jan 2024</span>
                            <span class="location">Senayan City, Jakarta</span>
                        </div>
                    </div>
                </div>

                <div class="exhibition-card">
                    <img src="https://jatengprov.go.id/wp-content/uploads/2024/08/IMG-20240801-WA0145-scaled.jpg" alt="Pameran Seni Tradisional">
                    <div class="exhibition-info">
                        <h3>Warisan Nusantara</h3>
                        <p>Pameran seni tradisional dari berbagai daerah di Indonesia</p>
                        <div class="date-location">
                            <span class="date">15 Feb - 15 Mar 2024</span>
                            <span class="location">Museum Nasional, Jakarta</span>
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
                <p>Galeri Seni Digital adalah platform untuk memamerkan karya seni dari berbagai seniman berbakat di
                    seluruh Indonesia.</p>
            </div>

            <div class="copyright">
                <p>&copy; 2023 Galeri Seni Digital. Semua Hak Dilindungi.</p>
            </div>
    </footer>
</body>
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeModal">&times;</span>
        <h2>Karya Seni</h2>
        <form id="updateForm" action="upload.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="artworkId" name="artworkId">

            <label for="judul">Judul:</label>
            <input type="text" id="judul" name="judul" required><br><br>

            <label for="deskripsi">Deskripsi:</label>
            <textarea id="deskripsi" name="deskripsi"></textarea><br><br>

            <label for="kategori">Kategori:</label>
            <input type="text" id="kategori" name="kategori"><br><br>

            <label for="harga">Harga:</label>
            <input type="number" step="0.01" id="harga" name="harga"><br><br>

            <label for="gambar">Unggah Gambar:</label>
            <input type="file" id="gambar" name="gambar" accept="image/*"><br><br>

            <button type="submit" name="submit">Submit</button>
        </form>
    </div>
</div>


<div id="chatModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeChatModal">&times;</span>
        <h2><span id="senimanName"></span></h2>
        <div id="chatMessages" class="chat-messages">

        </div>
        <form id="chatForm">
            <input type="hidden" name="conversation_id" id="conversationId">
            <textarea name="message" id="messageInput" placeholder="Ketik pesan..." required></textarea><br>
            <button type="submit">Kirim Pesan</button>
        </form>
    </div>
</div>


<div id="buyModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closeBuyModal">&times;</span>
        <h2>Detail Pembelian Karya Seni</h2>
        <div class="modal-body">
            <div class="modal-image">
                <img id="buyArtworkImage" src="" alt="Artwork Image" />
            </div>
            <form id="buyForm" action="process_purchase.php" method="POST">
                <input type="hidden" id="buyArtworkId" name="artworkId">

                <label for="buyArtworkTitle">Judul Karya:</label>
                <input type="text" id="buyArtworkTitle" name="artworkTitle" readonly><br><br>

                <label for="buyArtworkPrice">Harga:</label>
                <input type="text" id="buyArtworkPrice" name="artworkPrice" readonly><br><br>

                <label for="quantity">Jumlah Pembelian:</label>
                <input type="number" id="quantity" name="quantity" required min="1"><br><br>

                <label for="totalPrice">Total Harga:</label>
                <input type="number" id="totalPrice" name="totalPrice" readonly><br><br>

                <label for="paymentMethod">Metode Pembayaran:</label>
                <select id="paymentMethod" name="paymentMethod" required>
                    <option value="transfer_bca">Transfer BCA</option>
                    <option value="gopay">GoPay</option>
                    <option value="ovo">OVO</option>
                    <option value="cash_on_delivery">Cash on Delivery</option>
                </select><br><br>

                <button type="submit" name="submit">Proses Pembelian</button>
            </form>
        </div>
    </div>
</div>

<div id="paymentSuccessModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" id="closePaymentSuccessModal">&times;</span>
        <h2 class="modal-title">Detail Pembayaran</h2>

        <div class="transaction-header">
            <span>Nomor Transaksi: <strong id="transactionNumber"></strong></span><br>
            <span>Tanggal: <strong id="transactionDate"></strong></span><br>
        </div>

        <table id="transactionTable" class="transaction-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody id="transactionItems">
            </tbody>
        </table>

        <div class="transaction-total">
            <span>Total Pembayaran: </span><strong id="totalAmount">Rp 0</strong>
        </div>
        <form action="process_payment.php" method="POST" id="paymentForm">
            <input type="hidden" name="transactionNumber" id="transactionNumberInput">
            <input type="hidden" name="transactionDate" id="transactionDateInput">
            <input type="hidden" name="totalAmount" id="totalAmountInput">
            <input type="hidden" name="paymentMethod" id="paymentMethodInput">
            <input type="hidden" name="cartItems" id="cartItemsInput">
            <div class="action-buttons">
                <button type="submit" class="btn btn-pay">Bayar Sekarang</button>
            </div>
        </form>
    </div>
</div>



<script>
    const profilePic = document.getElementById('profilePic');
    const profileDropdown = document.getElementById('profileDropdown');
    profilePic.addEventListener('click', function (e) {
        e.stopPropagation();
        profileDropdown.classList.toggle('show');
    });
    document.addEventListener('click', function () {
        profileDropdown.classList.remove('show');
    });
    profileDropdown.addEventListener('click', function (e) {
        e.stopPropagation();
    });
    const viewButtons = document.querySelectorAll('.view-button');
    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            alert('Fitur melihat detail karya akan ditampilkan di sini');
        });
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            profileDropdown.classList.remove('show');
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        var modal = document.getElementById("modal");
        var btn = document.getElementById("openModal");
        var span = document.getElementById("closeModal");

        btn.onclick = function () {
            document.getElementById("artworkId").value = '';
            document.getElementById("judul").value = '';
            document.getElementById("deskripsi").value = '';
            document.getElementById("kategori").value = '';
            document.getElementById("harga").value = '';
            modal.style.display = "block";
        };

        span.onclick = function () {
            modal.style.display = "none";
        };

        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };

        window.openModal = function (artworkId) {
            var artworkImage = document.querySelector(`img[data-id='${artworkId}']`);
            if (!artworkImage) {
                console.error("Gambar tidak ditemukan.");
                return;
            }
            var judul = artworkImage.getAttribute("data-judul");
            var deskripsi = artworkImage.getAttribute("data-deskripsi");
            var kategori = artworkImage.getAttribute("data-kategori");
            var harga = artworkImage.getAttribute("data-harga");
            document.getElementById("artworkId").value = artworkId;
            document.getElementById("judul").value = judul;
            document.getElementById("deskripsi").value = deskripsi;
            document.getElementById("kategori").value = kategori;
            document.getElementById("harga").value = harga;
            modal.style.display = "block";
        };
        window.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        var notificationIcon = document.getElementById("notificationIcon");
        var notificationContainer = document.querySelector(".notification-container");
        notificationIcon.onclick = function () {
            notificationContainer.classList.toggle("active");
        };
        window.onclick = function (event) {
            if (!notificationContainer.contains(event.target) && !event.target.matches("#notificationIcon")) {
                notificationContainer.classList.remove("active");
            }
        };
    });

    document.addEventListener("DOMContentLoaded", function () {
        var modal = document.getElementById("chatModal");
        var closeModalBtn = document.getElementById("closeChatModal");
        var openChatButtons = document.querySelectorAll(".openChatButton");
        var chatForm = document.getElementById("chatForm");
        openChatButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                var conversationId = this.getAttribute("data-conversation-id");
                var senimanName = this.getAttribute("data-seniman-name");
                openChatModal(conversationId, senimanName);
            });
        });
        closeModalBtn.onclick = function () {
            modal.style.display = "none";
        };
        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };


        function openChatModal(conversationId, senimanName) {
            document.getElementById("senimanName").innerText = senimanName;
            document.getElementById("conversationId").value = conversationId;
            fetchMessages(conversationId);
            modal.style.display = "block";
        }
        chatForm.addEventListener("submit", function (e) {
            e.preventDefault();
            sendMessage();
        });

        function sendMessage() {
            var message = document.getElementById("messageInput").value;
            var conversationId = document.getElementById("conversationId").value;
            fetch('send_message.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `conversation_id=${encodeURIComponent(conversationId)}&message=${encodeURIComponent(message)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        appendMessage(data.message, data.sender_name);
                        document.getElementById("messageInput").value = '';
                    } else {
                        alert("Error: " + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error sending message:', error);
                    alert("There was an error sending your message.");
                });
        }

        function appendMessage(message, senderName) {
            var messagesContainer = document.getElementById("chatMessages");
            var messageElement = document.createElement("p");
            messageElement.innerHTML = `<strong>${senderName}:</strong> ${message}`;
            messagesContainer.appendChild(messageElement);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }

        function fetchMessages(conversationId) {
            fetch('get_messages.php?conversation_id=' + conversationId)
                .then(response => response.json())
                .then(data => {
                    var messagesContainer = document.getElementById("chatMessages");
                    messagesContainer.innerHTML = '';

                    data.messages.forEach(function (msg) {
                        appendMessage(msg.message, msg.sender_name);
                    });
                })
                .catch(error => {
                    console.error('Error fetching messages:', error);
                    alert("Error fetching messages");
                });
        }


        function startConversation(senimanId, artworkId) {
            fetch('start_conversation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `seniman_id=${encodeURIComponent(senimanId)}&artwork_id=${encodeURIComponent(artworkId)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        openChatModal(data.conversation_id, 'Seniman');
                    }
                })
                .catch(error => {
                    console.error('Error starting conversation:', error);
                    alert("Terjadi kesalahan dalam memulai percakapan.");
                });
        }

        document.querySelectorAll('.start-conversation-btn').forEach(button => {
            button.addEventListener('click', function () {
                var senimanId = this.getAttribute('data-seniman-id');
                var artworkId = this.getAttribute('data-artwork-id');
                startConversation(senimanId, artworkId);
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        var modal = document.getElementById("buyModal");
        var closeModalBtn = document.getElementById("closeBuyModal");
        var buyButtons = document.querySelectorAll(".buy-button");
        buyButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                var artworkId = this.getAttribute("data-artwork-id");
                var artworkTitle = this.getAttribute("data-artwork-title");
                var artworkPrice = this.getAttribute("data-artwork-price");
                var artworkImage = this.getAttribute("data-artwork-image");
                openBuyModal(artworkId, artworkTitle, artworkPrice, artworkImage);
            });
        });
        closeModalBtn.onclick = function () {
            modal.style.display = "none";
        };
        window.onclick = function (event) {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };

        function openBuyModal(artworkId, artworkTitle, artworkPrice, artworkImage) {
            document.getElementById("buyArtworkId").value = artworkId;
            document.getElementById("buyArtworkTitle").value = artworkTitle;
            document.getElementById("buyArtworkPrice").value = artworkPrice;
            document.getElementById("buyArtworkImage").src = artworkImage;
            document.getElementById("totalPrice").value = artworkPrice;
            modal.style.display = "block";
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        var cartIcon = document.getElementById("cartIcon");
        var cartDropdown = document.getElementById("cartDropdown");
        var checkoutButton = document.getElementById("checkoutButton");
        cartIcon.addEventListener("click", function () {
            cartDropdown.style.display = (cartDropdown.style.display === "block") ? "none" : "block";
            fetchCartItems();
        });
        function fetchCartItems() {
            fetch('get_cart_items.php')
                .then(response => response.json())
                .then(data => {
                    updateCartDropdown(data);
                })
                .catch(error => {
                    console.error('Error fetching cart items:', error);
                    alert("There was an error fetching your cart items.");
                });
        }
        function updateCartDropdown(cartItems) {
            var ul = cartDropdown.querySelector("ul");
            ul.innerHTML = "";
            var totalPrice = 0;
            var totalQuantity = 0;
            if (cartItems.length === 0) {
                ul.innerHTML = "<li class='no-items'>Tidak ada item di keranjang</li>";
            } else {
                cartItems.forEach(function (item) {
                    var li = document.createElement("li");
                    li.innerHTML = `<strong>${item.judul}</strong><br>
                                <small>Harga: ${item.harga} | Jumlah: ${item.quantity}</small><br>`;
                    ul.appendChild(li);

                    totalPrice += item.harga * item.quantity;
                    totalQuantity += item.quantity;
                });
            }
            document.getElementById("totalPrice").innerText = totalPrice.toFixed(2);
            document.getElementById("totalQuantity").innerText = totalQuantity;
        }

    });
    document.getElementById("checkoutButton").addEventListener("click", function () {
        fetch('get_cart_items.php')
            .then(response => response.json())
            .then(cartItems => {
                updatePaymentModal(cartItems);
                document.getElementById("paymentSuccessModal").style.display = "block";
            })
            .catch(error => {
                console.error('Error fetching cart items:', error);
                alert("There was an error fetching your cart items.");
            });
    });


    function updatePaymentModal(cartItems) {
        const transactionNumber = 'TRX-' + Date.now();
        const transactionDate = new Date().toLocaleString();
        document.getElementById("transactionNumber").textContent = transactionNumber;
        document.getElementById("transactionDate").textContent = transactionDate;
        let totalAmount = 0;
        const transactionItemsTable = document.getElementById("transactionItems");
        transactionItemsTable.innerHTML = '';
        cartItems.forEach(item => {
            const subtotal = item.harga * item.quantity;
            totalAmount += subtotal;
            const row = document.createElement('tr');
            row.innerHTML = `
        <td>${item.judul}</td>
        <td>${item.harga}</td>
        <td>${item.quantity}</td>
        <td>${subtotal}</td>
        `;
            transactionItemsTable.appendChild(row);
        });
        document.getElementById("totalAmount").textContent = 'Rp ' + totalAmount.toLocaleString();
        document.getElementById("transactionNumberInput").value = transactionNumber;
        document.getElementById("transactionDateInput").value = transactionDate;
        document.getElementById("totalAmountInput").value = totalAmount;
        document.getElementById("cartItemsInput").value = JSON.stringify(cartItems);
        const paymentMethod = document.getElementById("paymentMethod").textContent || "Transfer Bank";
        document.getElementById("paymentMethod").textContent = paymentMethod;
    }

    document.getElementById("btn-pay").addEventListener("click", function () {
        event.preventDefault();
        const transactionData = {
            transactionNumber: document.getElementById("transactionNumberInput").value,
            transactionDate: document.getElementById("transactionDateInput").value,
            totalAmount: document.getElementById("totalAmountInput").value,
            paymentMethod: document.getElementById("paymentMethodInput").value,
            cartItems: JSON.parse(document.getElementById("cartItemsInput").value)
        };
        fetch('process_payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(transactionData)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Pembayaran berhasil! Nomor transaksi: " + data.transactionId);
                    document.getElementById("paymentSuccessModal").style.display = "none"; // Tutup modal
                } else {
                    alert("Terjadi kesalahan saat pemrosesan pembayaran.");
                }
            })
            .catch(error => {
                console.error('Error during payment processing:', error);
                alert("Terjadi kesalahan saat pemrosesan pembayaran.");
            });
    });

    document.getElementById("backButton").addEventListener("click", function () {
        document.getElementById("paymentSuccessModal").style.display = "none";
    });
    document.getElementById("closePaymentSuccessModal").addEventListener("click", function () {
        document.getElementById("paymentSuccessModal").style.display = "none";
    });
    window.onclick = function (event) {
        if (event.target === document.getElementById("paymentSuccessModal")) {
            document.getElementById("paymentSuccessModal").style.display = "none";
        }
    };




</script>

<style>
    .modal-image {
        width: 100%;
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .modal-image img {
        max-width: 100px;
        height: 100px;
        border-radius: 8px;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.6);
        padding-top: 30px;
        transition: all 0.3s ease-in-out;
    }

    .modal-content {
        background-color: #fff;
        margin: 5% auto;
        padding: 30px;
        border-radius: 10px;
        width: 60%;
        max-width: 600px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        animation: fadeIn 0.5s ease-in-out;
        max-height: 80vh;
        overflow-y: auto;
    }


    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    form {
        flex-direction: column;
        gap: 1px;
    }

    label {
        font-size: 1.1em;
        font-weight: bold;
        color: #333;
    }

    input[type="text"],
    input[type="number"],
    textarea,
    input[type="file"] {
        padding: 10px;
        font-size: 1em;
        border: 2px solid #ccc;
        border-radius: 5px;
        width: 100%;
        box-sizing: border-box;
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    button {
        padding: 12px 20px;
        background-color: #f44336;
        color: white;
        font-size: 1.1em;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #f44336;
    }

    button:focus {
        outline: none;
    }

    @media (max-width: 768px) {
        .modal-content {
            width: 90%;
            padding: 20px;
        }

        .close-btn {
            font-size: 30px;
            top: 5px;
            right: 10px;
        }

        label {
            font-size: 1em;
        }

        button {
            font-size: 1em;
            padding: 10px 18px;
        }
    }

    @media (max-width: 480px) {
        .modal-content {
            width: 95%;
            padding: 15px;
        }

        .close-btn {
            font-size: 28px;
            top: 0;
            right: 5px;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            font-size: 0.9em;
        }

        button {
            font-size: 1em;
            padding: 10px 15px;
        }
    }


    .notification-container {
        position: relative;
    }

    #notificationIcon {
        font-size: 24px;
        cursor: pointer;
        color: #000;
    }

    .notification-dropdown {
        display: none;
        position: absolute;
        top: 30px;
        right: 0;
        background-color: #fff;
        border: 1px solid #ddd;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 250px;
        z-index: 10;
    }

    .notification-dropdown ul {
        list-style: none;
        padding: 10px;
        margin: 0;
    }

    .notification-dropdown ul li a {
        display: block;
        padding: 8px;
        text-decoration: none;
        color: #333;
        border-bottom: 1px solid #ddd;
    }

    .notification-dropdown ul li a:hover {
        background-color: #f4f4f4;
    }

    .notification-container.active .notification-dropdown {
        display: block;
    }


    .chat-modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0, 0, 0);
        background-color: rgba(0, 0, 0, 0.4);
    }

    .chat-modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 600px;
        border-radius: 8px;
    }

    .close-btn {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close-btn:hover,
    .close-btn:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .chat-messages {
        max-height: 300px;
        overflow-y: scroll;
        margin-bottom: 10px;
    }

    .chat-messages p {
        padding: 5px;
        background-color: #f1f1f1;
        margin: 5px 0;
        border-radius: 5px;
    }

    textarea {
        width: 100%;
        height: 60px;
        padding: 10px;
        margin: 10px 0;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    .cart-container {
        position: relative;
        display: inline-block;
    }

    #cartIcon {
        font-size: 24px;
        cursor: pointer;
    }

    .cart-dropdown {
        display: none;
        position: absolute;
        top: 30px;
        right: 0;
        background-color: white;
        border: 1px solid #ddd;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 250px;
        padding: 10px;
        z-index: 10;
    }

    .cart-summary {
        padding-top: 10px;
    }

    .cart-dropdown ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .cart-dropdown ul li {
        padding: 5px 0;
    }

    .cart-container.active .cart-dropdown {
        display: block;
    }

    .sold-tag {
        background-color: #f44336;
        color: white;
        padding: 5px 10px;
        font-size: 14px;
        margin-top: 10px;
        border-radius: 5px;
        font-weight: bold;
        margin-bottom: 15px;
        display: inline-block;
    }


    .button-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }


    .buy-button,
    .view-detail-button,
    .start-conversation-btn {
        background-color: #f44336;
        color: white;
        border: none;
        padding: 10px 15px;
        font-size: 14px;
        border-radius: 5px;
        cursor: pointer;
        width: 100%;
        margin-bottom: 10px;
        transition: background-color 0.3s ease;
    }


    .buy-button:hover,
    .view-detail-button:hover,
    .start-conversation-btn:hover {
        background-color: #f44336;
    }

    .view-detail-button {
        background-color: #28a745;
    }

    .start-conversation-btn {
        background-color: #ff9800;
    }

    /* Kontainer untuk gambar dan watermark */
    .artwork-image-container {
        position: relative;
        width: 100%;
        height: 200px;
    }

    .artwork-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-bottom: 2px solid #f2f2f2;
    }

    /* Watermark text */
    .watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 30px;
        color: rgba(255, 255, 255, 0.5);
        /* Warna putih dengan transparansi */
        font-weight: bold;
        pointer-events: none;
        /* Agar watermark tidak mengganggu klik gambar */
        user-select: none;
        /* Menonaktifkan pemilihan teks */
    }


    .sold-tag {
        background-color: #f44336;
        color: white;
        padding: 5px 10px;
        font-size: 14px;
        border-radius: 5px;
        font-weight: bold;
        margin-bottom: 20px;
        display: inline-block;
    }

    .transaction-header {
        font-size: 16px;
        margin-bottom: 15px;
    }

    .transaction-header span {
        display: block;
        margin-bottom: 5px;
    }

    .transaction-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .transaction-table th, .transaction-table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .transaction-table th {
        background-color: #f2f2f2;
    }

    .transaction-total {
        text-align: right;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 20px;
    }
</style>

</html>