<!-- Navbar Customer -->
<nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <!-- Logo -->
            <a href="<?= base_url() ?>" class="flex items-center gap-2">
                <span class="text-2xl font-playfair font-bold text-primary">Nurfa Beauty</span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-6">
                <a href="<?= base_url() ?>" class="text-gray-600 hover:text-primary transition-colors text-sm font-medium">Beranda</a>
                <a href="<?= base_url('catalog') ?>" class="text-gray-600 hover:text-primary transition-colors text-sm font-medium">Katalog</a>
                <?php if (session()->get('logged_in')): ?>
                    <a href="<?= base_url('address') ?>" class="text-gray-600 hover:text-primary transition-colors text-sm font-medium">Alamat</a>
                    <a href="<?= base_url('cart') ?>" class="relative text-gray-600 hover:text-primary transition-colors">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        <span id="cart-count" class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                    </a>
                    <!-- Notification Bell -->
                    <div class="relative" x-data="notificationBell()" x-init="fetchNotifications()">
                        <button @click="open = !open" class="relative text-gray-600 hover:text-primary transition-colors">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            <span x-show="unreadCount > 0" x-text="unreadCount" 
                                class="absolute -top-2 -right-2 bg-danger text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"></span>
                        </button>
                        <!-- Dropdown -->
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-100 py-2 max-h-96 overflow-y-auto z-50">
                            <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-800">Notifikasi</h4>
                                <button x-show="unreadCount > 0" @click="markAllRead()" class="text-xs text-primary hover:text-primary-dark">Tandai Dibaca</button>
                            </div>
                            <template x-if="notifications.length === 0">
                                <div class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada notifikasi</div>
                            </template>
                            <template x-for="notif in notifications" :key="notif.id">
                                <a href="#" @click.prevent="markRead(notif)" 
                                    class="block px-4 py-3 hover:bg-cream transition-colors border-b border-gray-50 last:border-0"
                                    :class="notif.is_read == 0 ? 'bg-primary-light/20' : ''">
                                    <p class="text-sm font-medium text-gray-800" x-text="notif.title"></p>
                                    <p class="text-xs text-gray-500 mt-0.5 line-clamp-2" x-text="notif.message"></p>
                                </a>
                            </template>
                        </div>
                    </div>
                    <a href="<?= base_url('transactions') ?>" class="text-gray-600 hover:text-primary transition-colors text-sm font-medium">Pesanan</a>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 text-gray-600 hover:text-primary">
                            <i data-lucide="user" class="w-5 h-5"></i>
                            <span class="text-sm font-medium"><?= session()->get('name') ?></span>
                        </button>
                        <div x-show="open" @click.away="open = false" x-transition
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2">
                            <a href="<?= base_url('profile') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-cream">Profil Saya</a>
                            <a href="<?= base_url('wishlist') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-cream">Wishlist</a>
                            <a href="<?= base_url('loyalty') ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-cream">Poin Loyalitas</a>
                            <hr class="my-1 border-gray-100">
                            <a href="<?= base_url('auth/logout') ?>" class="block px-4 py-2 text-sm text-danger hover:bg-red-50">Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= base_url('auth/login') ?>" class="btn-outline text-sm">Masuk</a>
                    <a href="<?= base_url('auth/register') ?>" class="btn-primary text-sm">Daftar</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <button @click="mobileOpen = !mobileOpen" class="md:hidden text-gray-600">
                <i data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileOpen" x-transition class="md:hidden bg-white border-t border-gray-100">
        <div class="px-4 py-3 space-y-2">
            <a href="<?= base_url() ?>" class="block py-2 text-gray-600 hover:text-primary text-sm">Beranda</a>
            <a href="<?= base_url('catalog') ?>" class="block py-2 text-gray-600 hover:text-primary text-sm">Katalog</a>
            <?php if (session()->get('logged_in')): ?>
                <a href="<?= base_url('address') ?>" class="block py-2 text-gray-600 hover:text-primary text-sm">Alamat</a>
                <a href="<?= base_url('cart') ?>" class="block py-2 text-gray-600 hover:text-primary text-sm">Keranjang</a>
                <a href="#" class="block py-2 text-gray-600 hover:text-primary text-sm flex items-center gap-2" x-data="notificationBell()" x-init="fetchNotifications()">
                    Notifikasi <span x-show="unreadCount > 0" x-text="unreadCount" class="bg-danger text-white text-xs rounded-full px-1.5 py-0.5"></span>
                </a>
                <a href="<?= base_url('transactions') ?>" class="block py-2 text-gray-600 hover:text-primary text-sm">Pesanan</a>
                <a href="<?= base_url('profile') ?>" class="block py-2 text-gray-600 hover:text-primary text-sm">Profil</a>
                <a href="<?= base_url('wishlist') ?>" class="block py-2 text-gray-600 hover:text-primary text-sm">Wishlist</a>
                <a href="<?= base_url('loyalty') ?>" class="block py-2 text-gray-600 hover:text-primary text-sm">Poin Loyalitas</a>
                <a href="<?= base_url('auth/logout') ?>" class="block py-2 text-danger text-sm">Logout</a>
            <?php else: ?>
                <a href="<?= base_url('auth/login') ?>" class="block py-2 text-primary text-sm font-medium">Masuk</a>
                <a href="<?= base_url('auth/register') ?>" class="block py-2 text-primary text-sm font-medium">Daftar</a>
            <?php endif; ?>
        </div>
    </div>

    <script>
    function notificationBell() {
        return {
            open: false,
            notifications: [],
            unreadCount: 0,
            fetchNotifications() {
                fetch('<?= base_url('api/notifications') ?>')
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            this.notifications = data.notifications || [];
                            this.unreadCount = data.unread_count || 0;
                        }
                    })
                    .catch(() => {});
            },
            markRead(notif) {
                if (notif.is_read == 0) {
                    fetch('<?= base_url('api/notifications/read/') ?>' + notif.id, { method: 'POST' })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                notif.is_read = 1;
                                this.unreadCount = Math.max(0, this.unreadCount - 1);
                            }
                        });
                }
            },
            markAllRead() {
                fetch('<?= base_url('api/notifications/read-all') ?>', { method: 'POST' })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            this.notifications.forEach(n => n.is_read = 1);
                            this.unreadCount = 0;
                        }
                    });
            }
        };
    }
    </script>
</nav>