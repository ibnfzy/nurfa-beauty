<!-- Alpine.js -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<!-- Lucide Icons -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        // Re-initialize Lucide icons when DOM changes (for Alpine.js templates)
        let iconTimeout;
        const observer = new MutationObserver(function(mutations) {
            let shouldReinit = false;
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && (node.hasAttribute('data-lucide') || node.querySelector('[data-lucide]'))) {
                            shouldReinit = true;
                        }
                    });
                }
            });
            if (shouldReinit) {
                clearTimeout(iconTimeout);
                iconTimeout = setTimeout(function() {
                    lucide.createIcons();
                }, 50);
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
    });
</script>

<!-- CSRF Token meta for AJAX -->
<script>
    const CSRF_TOKEN = '<?= csrf_hash() ?>';
    const BASE_URL = '<?= base_url() ?>';
</script>
