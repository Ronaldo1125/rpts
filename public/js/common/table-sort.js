/**
 * table-sort.js
 * Lightweight, reusable table column sorting for all dashboard tables.
 * Usage: Add data-sortable="true" to any <table> element.
 *        Add data-sort-skip="true" to <th> elements that shouldn't be sortable (e.g., Actions).
 */

(function () {
    'use strict';

    /**
     * Get the plain-text sort key from a cell.
     * Falls back gracefully for badge/span-wrapped content.
     */
    function getCellValue(tr, colIndex) {
        const cell = tr.cells[colIndex];
        if (!cell) return '';
        return (cell.dataset.sortValue || cell.innerText || cell.textContent || '').trim().toLowerCase();
    }

    /**
     * Compare two table rows for a given column.
     */
    function compareRows(rowA, rowB, colIndex, dir) {
        const a = getCellValue(rowA, colIndex);
        const b = getCellValue(rowB, colIndex);
        // Attempt numeric comparison first
        const numA = parseFloat(a.replace(/[^0-9.-]/g, ''));
        const numB = parseFloat(b.replace(/[^0-9.-]/g, ''));
        if (!isNaN(numA) && !isNaN(numB)) {
            return dir === 'asc' ? numA - numB : numB - numA;
        }
        return dir === 'asc' ? a.localeCompare(b) : b.localeCompare(a);
    }

    /**
     * Sort the given table by the specified column index and direction.
     */
    function sortTable(table, colIndex, dir) {
        const tbody = table.querySelector('tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => compareRows(a, b, colIndex, dir));
        rows.forEach(row => tbody.appendChild(row));
    }

    /**
     * Update sort icon states in the thead.
     */
    function updateHeaders(thead, activeIndex, dir) {
        thead.querySelectorAll('th[data-sort-col]').forEach((th, i) => {
            const icon = th.querySelector('.sort-icon');
            if (!icon) return;
            // Find what the actual column index is (stored on th)
            const colIdx = parseInt(th.dataset.sortCol, 10);
            if (colIdx === activeIndex) {
                th.classList.add('sort-active');
                icon.innerHTML = dir === 'asc'
                    ? `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5"/><polyline points="5 12 12 5 19 12"/></svg>`
                    : `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14"/><polyline points="19 12 12 19 5 12"/></svg>`;
            } else {
                th.classList.remove('sort-active');
                icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.35"><path d="M12 5v14"/><polyline points="19 12 12 19 5 12"/><path d="M12 19V5"/><polyline points="5 12 12 5 19 12"/></svg>`;
            }
        });
    }

    /**
     * Inject sort icons and click listeners into all column headers.
     */
    function initTable(table) {
        const thead = table.querySelector('thead');
        if (!thead || table.dataset.sortInit === 'true') return;
        table.dataset.sortInit = 'true';

        const headerCells = thead.querySelectorAll('th');
        let sortState = { colIndex: -1, dir: 'asc' };

        headerCells.forEach((th, i) => {
            // Skip non-sortable columns
            if (th.dataset.sortSkip === 'true') return;

            th.dataset.sortCol = i;
            th.classList.add('sortable-th');

            // Add sort icon SVG (default: both-arrows = unsorted)
            const icon = document.createElement('span');
            icon.className = 'sort-icon ms-1';
            icon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" opacity="0.35"><path d="M12 5v14"/><polyline points="19 12 12 19 5 12"/><path d="M12 19V5"/><polyline points="5 12 12 5 19 12"/></svg>`;
            th.appendChild(icon);

            th.addEventListener('click', () => {
                const newDir = (sortState.colIndex === i && sortState.dir === 'asc') ? 'desc' : 'asc';
                sortState = { colIndex: i, dir: newDir };
                sortTable(table, i, newDir);
                updateHeaders(thead, i, newDir);

                // Re-init Lucide icons if needed (for newly sorted rows)
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        });
    }

    /**
     * Initialize all sortable tables currently in the DOM.
     */
    function initAll() {
        document.querySelectorAll('table[data-sortable="true"]').forEach(initTable);
    }

    // Run on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // Also observe dynamically loaded page sections (SPA support)
    const observer = new MutationObserver(() => {
        initAll();
    });
    observer.observe(document.body, { childList: true, subtree: true });

    // Expose globally for manual initialization if needed
    window.TableSort = { init: initAll, initTable };
})();
