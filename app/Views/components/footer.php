<!-- Footer -->
<footer class="bg-gray-50 border-t border-gray-200 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div class="md:col-span-1">
                <h3 class="text-xl font-playfair font-bold text-primary mb-2">Nurfa Beauty</h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-4">
                    Produk kecantikan berkualitas untuk perawatan kulit, rambut, dan tubuh Anda.
                </p>
                <div class="flex items-center gap-3">
                    <a href="#" class="text-gray-400 hover:text-primary transition-colors" aria-label="Instagram">
                        <i data-lucide="camera" class="w-4 h-4"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-primary transition-colors" aria-label="Facebook">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-primary transition-colors" aria-label="Twitter/X">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="text-xs font-poppins font-semibold uppercase tracking-widest text-gray-400 mb-4">Navigasi</h4>
                <ul class="space-y-2">
                    <li><a href="<?= base_url() ?>" class="text-sm text-gray-600 hover:text-primary transition-colors">Beranda</a></li>
                    <li><a href="<?= base_url('catalog') ?>" class="text-sm text-gray-600 hover:text-primary transition-colors">Katalog</a></li>
                    <li><a href="<?= base_url('cart') ?>" class="text-sm text-gray-600 hover:text-primary transition-colors">Keranjang</a></li>
                </ul>
            </div>

            <!-- Account -->
            <div>
                <h4 class="text-xs font-poppins font-semibold uppercase tracking-widest text-gray-400 mb-4">Akun</h4>
                <ul class="space-y-2">
                    <li><a href="<?= base_url('profile') ?>" class="text-sm text-gray-600 hover:text-primary transition-colors">Profil Saya</a></li>
                    <li><a href="<?= base_url('transactions') ?>" class="text-sm text-gray-600 hover:text-primary transition-colors">Riwayat Pesanan</a></li>
                    <li><a href="<?= base_url('wishlist') ?>" class="text-sm text-gray-600 hover:text-primary transition-colors">Wishlist</a></li>
                    <li><a href="<?= base_url('loyalty') ?>" class="text-sm text-gray-600 hover:text-primary transition-colors">Poin Loyalitas</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="text-xs font-poppins font-semibold uppercase tracking-widest text-gray-400 mb-4">Kontak</h4>
                <ul class="space-y-3">
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i data-lucide="mail" class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400"></i>
                        info@nurfaBeauty.com
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i data-lucide="phone" class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400"></i>
                        +62 812-3456-7890
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600">
                        <i data-lucide="map-pin" class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400"></i>
                        Makassar, Indonesia
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-200 mt-10 pt-6 text-center">
            <p class="text-xs text-gray-400">&copy; <?= date('Y') ?> Nurfa Beauty. All rights reserved.</p>
        </div>
    </div>
</footer>
