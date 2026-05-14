/**
 * reports-loader.js
 * Handles initialization and dynamic features for the Admin Reports page.
 * Supports: column toggles, custom row filters (sector, year, location, duty bearer).
 */

// ── Bicol Region LGU data (province → cities/municipalities) ─────────────────
const BICOL_LGUS = {
    'albay': [
        'Bacacay','Camalig','Daraga','Guinobatan','Jovellar','Legazpi City',
        'Libon','Ligao City','Malilipot','Malinao','Manito','Oas','Pio Duran',
        'Polangui','Rapu-Rapu','Santo Domingo','Tabaco City','Tiwi'
    ],
    'camarines norte': [
        'Basud','Capalonga','Daet','Jose Panganiban','Labo','Mercedes',
        'Paracale','San Lorenzo Ruiz','San Vicente','Santa Elena','Talisay',
        'Vinzons'
    ],
    'camarines sur': [
        'Baao','Balatan','Bato','Bombon','Buhi','Bula','Cabusao','Calabanga',
        'Camaligan','Canaman','Caramoan','Del Gallego','Gainza','Garchitorena',
        'Goa','Iriga City','Lagonoy','Libmanan','Lupi','Magarao','Milaor',
        'Minalabac','Nabua','Naga City','Ocampo','Pamplona','Pasacao',
        'Pili','Presentacion','Ragay','Sagñay','San Fernando','San Jose',
        'Sipocot','Siruma','Tigaon','Tinambac'
    ],
    'catanduanes': [
        'Bagamanoc','Baras','Bato','Caramoran','Gigmoto','Pandan','Panganiban',
        'San Andres','San Miguel','Viga','Virac'
    ],
    'masbate': [
        'Aroroy','Baleno','Balud','Batuan','Cataingan','Cawayan','Claveria',
        'Dimasalang','Esperanza','Mandaon','Masbate City','Milagros','Mobo',
        'Monreal','Palanas','Pio V. Corpuz','Placer','San Fernando',
        'San Jacinto','San Pascual','Uson'
    ],
    'sorsogon': [
        'Barcelona','Bulan','Bulusan','Casiguran','Castilla','Donsol',
        'Gubat','Irosin','Juban','Magallanes','Matnog','Pilar','Prieto Diaz',
        'Santa Magdalena','Sorsogon City'
    ]
};

export function initReportsFilter() {
    const reportTable = document.getElementById('reportTable');
    if (!reportTable) return;

    // ── 1. Column visibility toggles (Columns dropdown) ──────────────────────
    document.querySelectorAll('.col-toggle-chk').forEach(chk => {
        chk.addEventListener('change', e => {
            const colClass = e.target.value;
            reportTable.querySelectorAll(`.${colClass}`).forEach(el => {
                el.classList.toggle('d-none', !e.target.checked);
            });
        });
    });

    // ── 2. Report type → show/hide panels ────────────────────────────────────
    const typeSelect        = document.getElementById('report-type-select');
    const yearContainer     = document.getElementById('year-select-container');
    const yearSelect        = document.getElementById('report-year-select');
    const customColContainer= document.getElementById('custom-report-columns');

    if (typeSelect) {
        typeSelect.addEventListener('change', () => {
            const v = typeSelect.value;
            // AIP: show single-year picker
            yearContainer?.classList.toggle('d-none', v !== 'aip');
            yearContainer?.classList.toggle('d-flex', v === 'aip');
            // Custom: show full filter panel
            if (v === 'custom') {
                customColContainer?.classList.remove('d-none');
                if (window.lucide) window.lucide.createIcons();
            } else {
                customColContainer?.classList.add('d-none');
            }
        });
    }

    // ── 3. Location: province → city/municipality cascade ────────────────────
    const provinceSelect = document.getElementById('rpt-filter-province');
    const citySelect     = document.getElementById('rpt-filter-city');

    if (provinceSelect && citySelect) {
        provinceSelect.addEventListener('change', () => {
            const prov = provinceSelect.value;
            citySelect.innerHTML = '<option value="">— All Cities / Municipalities —</option>';
            if (prov && BICOL_LGUS[prov]) {
                BICOL_LGUS[prov].forEach(lgu => {
                    const opt = document.createElement('option');
                    opt.value = lgu.toLowerCase();
                    opt.textContent = lgu;
                    citySelect.appendChild(opt);
                });
                citySelect.disabled = false;
            } else {
                citySelect.disabled = true;
            }
        });
    }

    // ── 4. Apply custom row filters ──────────────────────────────────────────
    const applyBtn = document.getElementById('btn-apply-custom-filters');
    if (applyBtn) {
        applyBtn.addEventListener('click', () => _applyCustomFilters(reportTable));
    }

    // ── 5. Reset custom filters ──────────────────────────────────────────────
    const resetBtn = document.getElementById('btn-reset-custom-filters');
    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            // Sectors — check all
            document.querySelectorAll('.report-sector-chk').forEach(c => c.checked = true);
            // Years — check all
            document.querySelectorAll('.report-year-chk').forEach(c => c.checked = true);
            // Location
            if (provinceSelect) provinceSelect.value = '';
            if (citySelect) {
                citySelect.innerHTML = '<option value="">— All Cities / Municipalities —</option>';
                citySelect.disabled = true;
            }
            // Duty bearer
            const db = document.getElementById('rpt-filter-duty-bearer');
            if (db) db.value = '';
            // Restore all rows
            reportTable.querySelectorAll('tbody tr').forEach(r => r.style.display = '');
            // Restore all year columns
            _restoreAllYearCols(reportTable);
            if (window.showSimpleAlert) window.showSimpleAlert('Filters have been reset.', 'info');
        });
    }

    // ── 6. Export button ─────────────────────────────────────────────────────
    const exportBtn = document.getElementById('btn-export-report');
    if (exportBtn && typeSelect) {
        exportBtn.addEventListener('click', () => _handleExport(typeSelect, yearSelect));
    }

    console.log('[Reports] Filter initialized.');
}

// ── Apply row-level filters ───────────────────────────────────────────────────
function _applyCustomFilters(table) {
    // Collect active sectors
    const activeSectors = Array.from(
        document.querySelectorAll('.report-sector-chk:checked')
    ).map(c => c.value.toLowerCase());

    // Collect active years
    const activeYears = Array.from(
        document.querySelectorAll('.report-year-chk:checked')
    ).map(c => c.value);

    // Location
    const province = document.getElementById('rpt-filter-province')?.value.toLowerCase() || '';
    const city     = document.getElementById('rpt-filter-city')?.value.toLowerCase()     || '';

    // Duty bearer
    const dutyTerm = (document.getElementById('rpt-filter-duty-bearer')?.value || '').trim().toLowerCase();

    let visibleCount = 0;
    table.querySelectorAll('tbody tr').forEach(row => {
        const locCell  = row.querySelector('.col-loc')?.textContent.toLowerCase()  || '';
        const dutyCell = row.querySelector('.col-duty')?.textContent.toLowerCase() || '';
        const secCell  = (row.dataset.sector || '').toLowerCase();

        // Sector check
        const sectorOk = activeSectors.length === 0
            || activeSectors.some(s => secCell.includes(s) || locCell.includes(s));

        // Location check
        const provOk = !province || locCell.includes(province);
        const cityOk = !city     || locCell.includes(city);

        // Duty bearer check
        const dutyOk = !dutyTerm || dutyCell.includes(dutyTerm);

        const show = sectorOk && provOk && cityOk && dutyOk;
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });

    // Year column visibility — show only checked years
    _applyYearColFilter(table, activeYears);

    if (window.showSimpleAlert) {
        window.showSimpleAlert(`Filters applied — ${visibleCount} row(s) shown.`, 'success');
    }
}

// ── Show/hide year sub-columns based on selected years ────────────────────────
function _applyYearColFilter(table, activeYears) {
    const thead = table.querySelector('thead');
    if (!thead) return;
    const rows = thead.querySelectorAll('tr');
    if (rows.length < 2) return;

    const yearRow = rows[1];
    const physHeaders = Array.from(yearRow.querySelectorAll('th.col-physical'));
    const costHeaders = Array.from(yearRow.querySelectorAll('th.col-cost'));

    // Determine which indices to hide
    physHeaders.forEach((th, i) => {
        const yr = th.textContent.trim();
        const hide = activeYears.length > 0 && !activeYears.includes(yr) && yr !== 'Succeeding Years';
        th.classList.toggle('d-none', hide);
        // Hide matching body cells
        table.querySelectorAll('tbody tr').forEach(row => {
            const cells = Array.from(row.querySelectorAll('td.col-physical'));
            if (cells[i]) cells[i].classList.toggle('d-none', hide);
        });
    });

    costHeaders.forEach((th, i) => {
        const yr = th.textContent.trim();
        const hide = activeYears.length > 0 && !activeYears.includes(yr) && yr !== 'Succeeding Years';
        th.classList.toggle('d-none', hide);
        table.querySelectorAll('tbody tr').forEach(row => {
            const cells = Array.from(row.querySelectorAll('td.col-cost'));
            if (cells[i]) cells[i].classList.toggle('d-none', hide);
        });
    });
}

function _restoreAllYearCols(table) {
    table.querySelectorAll('.col-physical, .col-cost').forEach(el => el.classList.remove('d-none'));
}

// ── Export handler (unchanged logic, extended for custom filters) ──────────────
function _handleExport(typeSelect, yearSelect) {
    const reportType = typeSelect.value;
    const reportName = typeSelect.options[typeSelect.selectedIndex].text;
    const year       = yearSelect ? yearSelect.value : '';

    if (!reportType) {
        if (window.showSimpleAlert) window.showSimpleAlert('Please select a report type.', 'warning');
        return;
    }
    if (reportType === 'aip' && !year) {
        if (window.showSimpleAlert) window.showSimpleAlert('Please select a year for the AIP report.', 'warning');
        return;
    }

    if (window.showSimpleAlert) {
        const msg = reportType === 'aip'
            ? `Preparing ${reportName} for Year ${year}…`
            : `Preparing ${reportName} for export…`;
        window.showSimpleAlert(msg, 'info');
    }

    try {
        const table = document.getElementById('reportTable');
        if (!table) throw new Error('Report table not found');

        const clone = table.cloneNode(true);

        if (reportType === 'aip' && year) {
            clone.querySelectorAll('th.col-physical[colspan]').forEach(th => th.setAttribute('colspan', '1'));
            clone.querySelectorAll('th.col-cost[colspan]').forEach(th => th.setAttribute('colspan', '1'));
            const yearHeaderRow = clone.querySelectorAll('thead tr')[1];
            let matchIndex = -1;
            if (yearHeaderRow) {
                const physHeaders = Array.from(yearHeaderRow.querySelectorAll('th.col-physical'));
                matchIndex = physHeaders.findIndex(th => th.textContent.trim() === year);
                if (matchIndex === -1)
                    matchIndex = physHeaders.findIndex(th => th.textContent.includes('Succeeding'));
            }
            if (matchIndex !== -1) {
                if (yearHeaderRow) {
                    Array.from(yearHeaderRow.querySelectorAll('th.col-physical')).forEach((th, i) => { if (i !== matchIndex) th.remove(); });
                    Array.from(yearHeaderRow.querySelectorAll('th.col-cost')).forEach((th, i) => { if (i !== matchIndex) th.remove(); });
                }
                clone.querySelectorAll('tbody tr').forEach(row => {
                    Array.from(row.querySelectorAll('td.col-physical')).forEach((td, i) => { if (i !== matchIndex) td.remove(); });
                    Array.from(row.querySelectorAll('td.col-cost')).forEach((td, i) => { if (i !== matchIndex) td.remove(); });
                });
            }
        }

        if (reportType === 'custom') {
            const selectedCols = Array.from(document.querySelectorAll('.custom-col-chk:checked')).map(cb => cb.value);
            const allColClasses = ['col-title','col-desc','col-ind','col-loc','col-duty','col-fund','col-physical','col-cost','col-remarks'];
            allColClasses.filter(c => !selectedCols.includes(c)).forEach(cls => {
                clone.querySelectorAll(`.${cls}`).forEach(el => el.remove());
            });
            // Remove hidden rows (filtered out)
            clone.querySelectorAll('tbody tr').forEach(r => {
                if (r.style.display === 'none') r.remove();
            });
            // Remove hidden year columns
            clone.querySelectorAll('.d-none').forEach(el => el.remove());
        } else {
            clone.querySelectorAll('.d-none').forEach(el => el.remove());
        }

        clone.querySelectorAll('i, svg, .btn, .dropdown').forEach(el => el.remove());
        clone.querySelectorAll('td, th').forEach(cell => {
            const t = cell.textContent.trim();
            if (['—','–','-','&mdash;'].includes(t)) cell.textContent = '';
        });

        const wb        = XLSX.utils.table_to_book(clone, { sheet: 'Project Reports' });
        const timestamp = new Date().toISOString().split('T')[0];
        const filename  = reportType === 'aip'
            ? `AIP_Report_${year}_${timestamp}.xlsx`
            : `${reportType.toUpperCase()}_Report_${timestamp}.xlsx`;
        XLSX.writeFile(wb, filename);

        if (window.showSimpleAlert) {
            setTimeout(() => {
                const msg = reportType === 'aip'
                    ? `${reportName} for ${year} exported successfully.`
                    : `${reportName} exported successfully.`;
                window.showSimpleAlert(msg, 'success');
            }, 500);
        }
    } catch (err) {
        console.error('[Reports] Export error:', err);
        if (window.showSimpleAlert) window.showSimpleAlert('Failed to export report.', 'danger');
    }
}
