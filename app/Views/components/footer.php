<!-- Footer -->
<footer class="bg-white border-t border-gray-100 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div class="md:col-span-1">
                <h3 class="text-xl font-playfair font-bold text-primary mb-3">Nurfa Beauty</h3>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Temukan produk kecantikan berkualitas untuk perawatan kulit, rambut, dan tubuh Anda.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-semibold text-gray-800 mb-3 text-sm">Navigasi</h4>
                <ul class="space-y-2">
                    <li><a href="<?= base_url() ?>" class="text-sm text-gray-500 hover:text-primary transition-colors">Beranda</a></li>
                    <li><a href="<?= base_url('catalog') ?>" class="text-sm text-gray-500 hover:text-primary transition-colors">Katalog</a></li>
                    <li><a href="<?= base_url('cart') ?>" class="text-sm text-gray-500 hover:text-primary transition-colors">Keranjang</a></li>
                </ul>
            </div>

            <!-- Account -->
            <div>
                <h4 class="font-semibold text-gray-800 mb-3 text-sm">Akun</h4>
                <ul class="space-y-2">
                    <li><a href="<?= base_url('profile') ?>" class="text-sm text-gray-500 hover:text-primary transition-colors">Profil Saya</a></li>
                    <li><a href="<?= base_url('transactions') ?>" class="text-sm text-gray-500 hover:text-primary transition-colors">Riwayat Pesanan</a></li>
                    <li><a href="<?= base_url('wishlist') ?>" class="text-sm text-gray-500 hover:text-primary transition-colors">Wishlist</a></li>
                    <li><a href="<?= base_url('loyalty') ?>" class="text-sm text-gray-500 hover:text-primary transition-colors">Poin Loyalitas</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="font-semibold text-gray-800 mb-3 text-sm">Kontak</h4>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-gray-500">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                        info@nurfaBeauty.com
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-500">
                        <i data-lucide="phone" class="w-4 h-4"></i>
                        +62 812-3456-7890
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-500">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        Makassar, Indonesia
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-100 mt-8 pt-6 text-center">
            <p class="text-sm text-gray-400">&copy; <?= date('Y') ?> Nurfa Beauty. All rights reserved.</p>
        </div>
    </div>
</footer>
