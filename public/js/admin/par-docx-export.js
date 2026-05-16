/**
 * par-docx-export.js
 * Generates a downloadable PAR DOCX using html-docx-js (window.htmlDocx).
 * PDIPB Staff portal only.
 *
 * Page 1-3: Portrait — PAR Form (FM-PDI-02 layout, tight government spacing)
 * Page 4:   Landscape — Annex A budget table (single project)
 *
 * CDN: https://unpkg.com/html-docx-js@0.3.1/dist/html-docx.js
 */

export function initParDocxExport() {
    const btn = document.getElementById('btnDownloadParDocx');
    if (!btn) return;

    btn.addEventListener('click', async () => {
        if (!window.htmlDocx) { alert('DOCX library not loaded. Please hard-refresh (Ctrl+Shift+R).'); return; }
        const orig = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Generating...';
        try { await _generate(); }
        catch (e) { console.error('[PAR DOCX]', e); alert('Failed: ' + e.message); }
        finally { btn.disabled = false; btn.innerHTML = orig; if (window.lucide) window.lucide.createIcons(); }
    });
}

/* ── helpers ─────────────────────────────────────────────────────────────── */
const _dom = n => (document.querySelector(`[name="${n}"]`)?.value || '').trim();
const _cb  = id => !!document.getElementById(id)?.checked;
const _chk = id => _cb(id) ? '☒' : '☐';

function _v(n, fd) { return _dom(n) || (fd?.[n] ? String(fd[n]).trim() : ''); }

async function _b64(path) {
    try { const r = await fetch(path); const b = await r.blob();
        return new Promise(ok => { const f = new FileReader(); f.onloadend = () => ok(f.result); f.readAsDataURL(b); });
    } catch { return ''; }
}

/* ── generate ────────────────────────────────────────────────────────────── */
async function _generate() {
    // 1) Saved PAR from localforage
    const parId = sessionStorage.getItem('par_edit_id');
    let par = null, fd = {};
    if (parId && window.localforage) {
        const all = await localforage.getItem('project_assessments') || [];
        par = all.find(p => p.id === parId);
        fd  = par?.formData || {};
    }

    // 2) Logos (small — 45×45 px)
    const [r1, r2] = await Promise.all([_b64('/assets/images/rnp.png'), _b64('/assets/images/rdc.png')]);
    const iR = r1 ? `<img src="${r1}" width="45" height="45">` : '';
    const iD = r2 ? `<img src="${r2}" width="45" height="45">` : '';

    // 3) Values
    const title  = _v('projectTitle', fd) || par?.projectTitle || '';
    const agency = _v('implementingAgency', fd) || par?.proponent || '';
    const cov    = _v('project-coverage', fd);
    let loc = cov || 'Regionwide';
    if (cov === 'Location-Specific') {
        const p = [_v('province',fd),_v('district',fd),_v('municipality',fd),_v('barangay',fd)].filter(Boolean);
        if (p.length) loc = p.join(', ');
    } else if (cov === 'Inter-Province') {
        const tags = [...document.querySelectorAll('#f-provinces-box .tag-badge')].map(t=>t.textContent.trim()).join(', ');
        loc = 'Inter-Province: ' + (tags || fd.interProvince || '');
    }

    const bg   = _v('par_background', fd);
    const comp = _v('par_components', fd);
    const spa  = _v('par_spatial', fd);
    const qual = _v('par_qualitative', fd);
    const rec  = _v('par_recommendations', fd);
    const frec = _v('par_final_recs', fd);
    const adsc = _v('annex_desc', fd);

    const pN = _v('prepBy_name',fd), pP = _v('prepBy_pos',fd);
    const rN = _v('revBy_name',fd),  rP = _v('revBy_pos',fd);
    const aN = _v('appBy_name',fd),  aP = _v('appBy_pos',fd);

    const yrs = [2024,2025,2026,2027,2028,2029];
    const bv  = yrs.map(y => parseFloat(_v(`annex_${y}`,fd)) || 0);
    const tot = parseFloat(_v('annex_total',fd)) || bv.reduce((a,b)=>a+b,0);
    const fmt = n => n.toLocaleString('en-PH',{minimumFractionDigits:2});

    const findings = par?.findings || [];

    console.log('[PAR DOCX] Data:', {title, agency, bg:bg?.substring(0,30), pN});

    // ── checkbox row helper (compact) ────────────────────────────────────
    const C = (id,label,ind) => `<tr><td style="width:18px;padding:0 2px;font-size:9pt;vertical-align:top;font-family:'Segoe UI Symbol',sans-serif;">${_chk(id)}</td><td style="padding:0 0 0 ${ind||0}px;font-size:9pt;line-height:1.35;">${label}</td></tr>`;

    // ── field row (label + underline value) ──────────────────────────────
    const F = (lbl,val) => `<p style="font-size:9pt;font-weight:bold;margin:4px 0 0;">${lbl}:</p><div style="border-bottom:1px solid #000;padding:1px 3px 2px;font-size:9.5pt;min-height:16px;">${val||'&nbsp;'}</div>`;

    // ── text box ─────────────────────────────────────────────────────────
    const T = (lbl,val) => `<p style="font-size:9pt;margin:4px 0 1px;">${lbl}</p><div style="border:1px solid #999;min-height:42px;padding:3px 6px;font-size:9pt;margin-bottom:5px;white-space:pre-wrap;">${val||'&nbsp;'}</div>`;

    // ── header block (reusable for both pages) ───────────────────────────
    const hdr = `<table style="width:100%;border-collapse:collapse;"><tr>
      <td style="width:100px;vertical-align:middle;">${iR}&nbsp;${iD}</td>
      <td style="text-align:center;vertical-align:middle;font-size:8pt;line-height:1.4;"><b>REPUBLIC OF THE PHILIPPINES</b><br><b>REGIONAL DEVELOPMENT COUNCIL</b><br><b>BICOL REGION</b></td>
      <td style="text-align:right;vertical-align:middle;font-size:7.5pt;line-height:1.5;width:170px;">FM-PDI-02 | PAR Form | Revision No. 00<br>Effectivity Date: August 1, 2022</td>
    </tr></table>`;

    // ═════════════════════════════════════════════════════════════════════
    //  PAGE 1–3: Portrait PAR form (tight government spacing)
    // ═════════════════════════════════════════════════════════════════════
    const parPages = `
${hdr}
<div style="background:#154A9A;color:#fff;text-align:center;padding:7px 10px;margin:6px 0 10px;">
  <div style="font-weight:bold;font-size:12pt;">PROJECT ASSESSMENT REPORT</div>
  <div style="font-size:8.5pt;margin-top:1px;">(Programs, Activities and Projects for Inclusion in the RDIP)</div>
</div>

${F('Project Title', title)}
${F('Proponent', agency)}
${F('Location', loc)}

<p style="font-weight:bold;font-size:9.5pt;margin:10px 0 2px;">I.&nbsp;&nbsp;Documentary requirements: (please place tick mark)</p>
<table style="width:100%;border-collapse:collapse;">
${C('doc1_f',"Official request for the project's inclusion in the RDIP")}
${C('doc2_f','Comprehensive Project Profile / Feasibility Study / Pre-Feasibility Study')}
${C('doc3_f','Endorsements (any of the following when applicable)')}
${C('endo1_f','Sangguniang Panlalawigan Resolution approving the PAP',14)}
${C('endo2_f','Sangguniang Panlungsod resolution or ordinance approving the PAPs',14)}
${C('endo3_f','Endorsement of the Board of Trustees/Regents for projects to be implemented by state universities and colleges',14)}
${C('endo4_f','Other endorsements (pls specify) ______________________________',14)}
</table>

<p style="font-weight:bold;font-size:9.5pt;margin:8px 0 2px;">II.&nbsp;&nbsp;Criteria for inclusion in the RDIP</p>
<p style="font-weight:bold;font-size:9pt;margin:4px 0 1px;">&nbsp;&nbsp;1.&nbsp;&nbsp;Typology</p>
<table style="width:100%;border-collapse:collapse;">
${C('type1_f','Capital investment PAP')}
${C('type1a_f','For ICT PAPs, capital outlay components of the Information Systems Strategic Plan of the agency',14)}
${C('type1b_f','For culture PAPs, capital outlay components are required for the conservation of cultural properties as defined by RA 10066, S. 2009 or at the National Cultural Heritage Act of 2009',14)}
${C('type1c_f','Resiliency to withstand natural calamities is factored into infrastructure capital investments',14)}
${C('type1d_f','Requirements for pre-investment activities (e.g., master plans, FS, etc.) must be undertaken',14)}
${C('type1e_f','Timelines and costs on the right of way, resettlement shall be included in the project cost',14)}
${C('type2_f','Technical assistance, institutional development, human resource capacity building or system/process improvement PAPs')}
${C('type3_f','Relending PAPs to LGUs or other target beneficiaries')}
${C('type4_f',"Government facilities that are part of the agency's development strategies and contribute to the outcome and output targets contained in the RDP-Results Matrices")}
</table>

<p style="font-weight:bold;font-size:9pt;margin:6px 0 1px;">&nbsp;&nbsp;2.&nbsp;&nbsp;Responsiveness</p>
<table style="width:100%;border-collapse:collapse;">
${C('resp1_f','Responsiveness to the Bicol RDP')}
${C('resp2_f','Included in any of the following:')}
${C('inc1_f','National Expenditure Program',14)}
${C('inc2_f','Multi-Year Obligational Authority / Multi-Year Contracting Authority',14)}
${C('inc3_f','Existing masterplan/sector studies/procurement plan',14)}
${C('inc4_f','List of RDC-endorsed NG PAPs',14)}
${C('inc5_f','Signed agreements (e.g., peace agreements, etc.)',14)}
${C('inc6_f','Existing laws, rules and regulations',14)}
${C('inc7_f','Regional programs (e.g., HFEP, PAMANA)',14)}
${C('inc8_f','<s>Balik Probinsya Bagong Pag-asa Program</s>',14)}
${C('inc9_f','Regional Recovery Program',14)}
${C('inc10_f','Other programs endorsed by the RDC. Please specify: ___________________',14)}
</table>

<p style="font-weight:bold;font-size:9pt;margin:6px 0 1px;">&nbsp;&nbsp;3.&nbsp;&nbsp;Readiness</p>
<table style="width:100%;border-collapse:collapse;">
${C('ready1_f','With completed project preparation documents')}
${C('ready2_f','For inclusion in the NEP for the next fiscal year')}
${C('ready3_f','With project preparation document currently being prepared and to be completed in the current fiscal year')}
${C('ready4_f','For inclusion in the NEP for the succeeding fiscal year')}
${C('ready5_f','With project preparation documents for completion in the next fiscal year')}
${C('ready6_f','For inclusion in the NEP for beyond fiscal year of the current administration')}
${C('ready7_f','With completed Right of Way acquisition and Resettlement Action Plan (when applicable)')}
${C('ready8_f','With ongoing Right of Way acquisition and Resettlement Action Plan (when applicable)')}
${C('ready9_f','Without Right of Way acquisition and Resettlement Action Plan (when applicable)')}
</table>

<p style="font-weight:bold;font-size:9.5pt;margin:10px 0 2px;">III.&nbsp;&nbsp;Assessment of the PAP</p>
<p style="font-weight:bold;font-size:9pt;margin:4px 0 1px;">&nbsp;&nbsp;1.&nbsp;&nbsp;Brief of the PAP</p>
${T('A.&nbsp;&nbsp;Background', bg)}
${T('B.&nbsp;&nbsp;Project components, Cost and Financing, and Implementation Schedule', comp)}

<p style="font-weight:bold;font-size:9pt;margin:6px 0 1px;">&nbsp;&nbsp;2.&nbsp;&nbsp;Assessment</p>
${T("A.&nbsp;&nbsp;Project's Regional and Spatial Context", spa)}
${T('B.&nbsp;&nbsp;Qualitative Technical, Market, Economic, Social, and Environmental Evaluation', qual)}

<p style="font-weight:bold;font-size:9pt;margin:6px 0 1px;">&nbsp;&nbsp;3.&nbsp;&nbsp;Recommendations</p>
<div style="border:1px solid #999;min-height:36px;padding:3px 6px;font-size:9pt;margin-bottom:5px;white-space:pre-wrap;">${rec||'&nbsp;'}</div>

<p style="font-weight:bold;font-size:9pt;margin:6px 0 1px;">&nbsp;&nbsp;4.&nbsp;&nbsp;Final Recommendations</p>
<div style="border:1px solid #999;min-height:36px;padding:3px 6px;font-size:9pt;margin-bottom:8px;white-space:pre-wrap;">${frec||'&nbsp;'}</div>

${findings.length ? `
<p style="font-weight:bold;font-size:9.5pt;margin:8px 0 2px;">Findings &amp; Recommendations</p>
${findings.map((f,i)=>`<p style="font-weight:bold;font-size:9pt;margin:3px 0 1px;">Finding ${i+1}:</p><p style="font-size:9pt;margin:0 0 2px 10px;">${f.finding||''}</p>${f.recommendation?`<p style="font-size:9pt;margin:0 0 4px 10px;"><em>Rec:</em> ${f.recommendation}</p>`:''}`).join('')}
` : ''}

<!-- SIGNATORIES -->
<table style="width:100%;border-collapse:collapse;margin-top:24px;">
  <tr>
    <td style="width:40%;vertical-align:top;padding-right:10px;">
      <p style="font-size:9.5pt;margin:0 0 24px;">Prepared by:</p>
      <div style="border-top:1px solid #000;text-align:center;font-weight:bold;font-size:9.5pt;padding-top:2px;">${pN||'&nbsp;'}</div>
      <div style="text-align:center;font-size:8.5pt;color:#333;">${pP||'&nbsp;'}</div>
    </td>
    <td style="width:4%;"></td>
    <td style="width:40%;vertical-align:top;">
      <p style="font-size:9.5pt;margin:0 0 24px;">Reviewed by:</p>
      <div style="border-top:1px solid #000;text-align:center;font-weight:bold;font-size:9.5pt;padding-top:2px;">${rN||'&nbsp;'}</div>
      <div style="text-align:center;font-size:8.5pt;color:#333;">${rP||'&nbsp;'}</div>
    </td>
    <td style="width:16%;"></td>
  </tr>
  <tr><td colspan="4" style="height:8px;"></td></tr>
  <tr>
    <td colspan="2" style="vertical-align:top;">
      <p style="font-size:9.5pt;margin:0 0 24px;">Approved by:</p>
      <div style="border-top:1px solid #000;text-align:center;font-weight:bold;font-size:9.5pt;padding-top:2px;width:80%;">${aN||'&nbsp;'}</div>
      <div style="text-align:center;font-size:8.5pt;color:#333;width:80%;">${aP||'&nbsp;'}</div>
    </td>
  </tr>
</table>`;

    // ═════════════════════════════════════════════════════════════════════
    //  PAGE 4: Annex A — Portrait budget table (1 project only)
    // ═════════════════════════════════════════════════════════════════════
    const annexPage = `
${hdr}
<p style="text-align:center;font-weight:bold;font-size:11pt;margin:8px 0 10px;">Annex A</p>

<table style="width:100%;border-collapse:collapse;border:1px solid #000;font-size:8.5pt;">
  <thead>
    <tr>
      <th rowspan="3" style="border:1px solid #000;padding:4px;text-align:center;width:30px;">No.</th>
      <th rowspan="3" style="border:1px solid #000;padding:4px;text-align:center;width:140px;">Project Title</th>
      <th rowspan="3" style="border:1px solid #000;padding:4px;text-align:center;width:110px;">Brief Description</th>
      <th rowspan="3" style="border:1px solid #000;padding:4px;text-align:center;width:80px;">Location</th>
      <th colspan="${yrs.length + 1}" style="border:1px solid #000;padding:4px;text-align:center;font-weight:bold;">BUDGETARY REQUIREMENT</th>
    </tr>
    <tr>
      <th colspan="${yrs.length + 1}" style="border:1px solid #000;padding:2px;text-align:center;font-weight:bold;">(in PHP Million)</th>
    </tr>
    <tr>
      <th style="border:1px solid #000;padding:3px;text-align:center;font-weight:bold;">TOTAL</th>
      ${yrs.map(y=>`<th style="border:1px solid #000;padding:3px;text-align:center;">${y}</th>`).join('')}
    </tr>
  </thead>
  <tbody>
    <!-- Agency row -->
    <tr>
      <td colspan="${4 + yrs.length + 1}" style="border:1px solid #000;padding:4px 6px;font-weight:bold;font-size:9pt;">
        ${agency || 'Agency'}
      </td>
    </tr>
    <!-- Single project row -->
    <tr>
      <td style="border:1px solid #000;padding:4px;text-align:center;">1</td>
      <td style="border:1px solid #000;padding:4px;">${title || '&nbsp;'}</td>
      <td style="border:1px solid #000;padding:4px;">${adsc || '&nbsp;'}</td>
      <td style="border:1px solid #000;padding:4px;text-align:center;">${loc || '&nbsp;'}</td>
      <td style="border:1px solid #000;padding:4px;text-align:right;font-weight:bold;">${fmt(tot)}</td>
      ${bv.map(v=>`<td style="border:1px solid #000;padding:4px;text-align:right;">${v ? fmt(v) : ''}</td>`).join('')}
    </tr>
  </tbody>
</table>`;

    // ═════════════════════════════════════════════════════════════════════
    //  Assemble full HTML — All portrait A4, 4 pages total
    // ═════════════════════════════════════════════════════════════════════
    const html = `<!DOCTYPE html><html><head><meta charset="UTF-8">
<style>
  @page { size: 595pt 842pt; margin: 0.6in 0.55in 0.5in 0.7in; }
  body { font-family: Arial, sans-serif; font-size: 9.5pt; color: #000; margin: 0; padding: 0; }
</style>
</head><body>
${parPages}
<br clear="all" style="mso-special-character:line-break;page-break-before:always">
${annexPage}
</body></html>`;

    // ── Generate and download ────────────────────────────────────────────
    const blob = window.htmlDocx.asBlob(html, {
        orientation: 'portrait',
        margins: { top: 720, bottom: 600, left: 850, right: 680 }
    });
    const url = URL.createObjectURL(blob);
    const a   = document.createElement('a');
    const safe = (title||'Assessment').replace(/[^a-zA-Z0-9\s]/g,'').trim().replace(/\s+/g,'_');
    a.href = url;
    a.download = `PAR_${safe}_${new Date().toISOString().slice(0,10)}.docx`;
    document.body.appendChild(a);
    a.click();
    setTimeout(() => { URL.revokeObjectURL(url); a.remove(); }, 1500);
}

