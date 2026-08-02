<?php

/**
 * Table Helper
 *
 * Helper untuk generate HTML table rows yang bisa dipakai
 * bersama component table.
 */

if (!function_exists('table_rows')) {
    /**
     * Generate HTML table rows dari data array.
     *
     * Column definition:
     * - 'key'    => nama field di data (string)
     * - 'label'  => label header (opsional, untuk referensi)
     * - 'class'  => extra class untuk td (opsional)
     * - 'render' => callback fn($row, $no) untuk custom render (opsional)
     *
     * Special keys:
     * - '#no' => auto numbering dengan pager support
     *
     * @param array       $data    Array data dari model/controller
     * @param array       $columns Array definisi kolom
     * @param object|null $pager   Pager object (opsional)
     * @return string HTML string dari <tr> elements
     */
    function table_rows(array $data, array $columns, ?object $pager = null): string
    {
        if (empty($data)) {
            return '';
        }

        $output = '';
        $no = $pager ? ($pager->getCurrentPage() - 1) * $pager->getPerPage() + 1 : 1;

        foreach ($data as $row) {
            $output .= '<tr class="hover:bg-cream/50 transition-colors">';

            foreach ($columns as $col) {
                $class = $col['class'] ?? '';
                $output .= '<td class="py-3 px-4 ' . esc($class, 'html', 'UTF-8') . '">';

                if (isset($col['render']) && is_callable($col['render'])) {
                    $output .= $col['render']($row, $no);
                } elseif (isset($col['key']) && $col['key'] === '#no') {
                    $output .= $no++;
                } elseif (isset($col['key'])) {
                    $value = $row[$col['key']] ?? '';
                    $output .= $value !== '' ? esc($value) : '<span class="text-gray-400 italic">-</span>';
                }

                $output .= '</td>';
            }

            $output .= '</tr>';
        }

        return $output;
    }
}

if (!function_exists('table_rows_with_template')) {
    /**
     * Generate table rows menggunakan template tags (foreach langsung).
     * Fungsi ini dijalankan di dalam view context sehingga bisa akses variabel view.
     *
     * @param callable $renderer Callback yang berisi template HTML
     * @return string
     */
    function table_rows_with_template(callable $renderer): string
    {
        ob_start();
        $renderer();
        return ob_get_clean();
    }
}
