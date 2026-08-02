<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>

<!-- CSRF Token meta for AJAX -->
<script>
    const CSRF_TOKEN = '<?= csrf_hash() ?>';
    const BASE_URL = '<?= base_url() ?>';
</script>
