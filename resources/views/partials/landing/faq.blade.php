<!-- ===== FAQ PAGE ===== -->
<div class="rdip-faq-container">

    <!-- ── HEADER SECTION ── -->
    <div class="faq-header">
        <a href="{{ route('landing') }}" id="btn-back-faq" class="btn-back-minimal">
            <i data-lucide="arrow-left" width="16" height="16"></i>
            Back to Home
        </a>
        <h1 class="faq-main-title">Frequently Asked Questions</h1>
        
        <!-- Category Navigation Pills -->
        <div class="faq-categories">
            <button class="cat-pill active" onclick="toggleFaqCategory(this, 'cat-general')">GENERAL</button>
            <button class="cat-pill" onclick="toggleFaqCategory(this, 'cat-rdip')">RDIP</button>
            <button class="cat-pill" onclick="toggleFaqCategory(this, 'cat-reqs')">REQUIREMENTS</button>
            <button class="cat-pill" onclick="toggleFaqCategory(this, 'cat-criteria')">CRITERIA</button>
        </div>
    </div>

    <!-- ── FAQ CONTENT SECTION ── -->
    <div class="faq-body-content">
        
        <!-- General Category Section -->
        <div id="cat-general" class="faq-category-section">
            <h2 class="faq-section-heading">General Inquiries</h2>
            <div class="faq-grid">
                <div class="faq-item-box">
                    <button class="faq-toggle" onclick="toggleFaqBox(this)">
                        <span class="faq-question">What is Comprehensive Investment Programming Guidelines (CIPG)?</span>
                        <i data-lucide="chevron-down" class="faq-icon" width="20" height="20"></i>
                    </button>
                    <div class="faq-answer-collapse">
                        <div class="faq-answer-inner">
                            <p>The RDC Region 5 developed the CIPG to increase the number of projects being funded. The overall framework of the CIPG aims to synchronize the planning, investment programming, and budgeting processes. It provides the principles, processes, and requirements in the identification and prioritization of PPAs for inclusion in investment programming documents.</p>
                            <p>The CIPG guides the identification of priority programs, projects and activities (PPAs) for inclusion in the Regional Development Investment Program (RDIP) and Agency Investment Program (AIP). It also aims to: (1) present the processes and requirements of the Bicol RDC for the inclusion of PPAs in the RDIP; (2) provide the criteria for inclusion of PPAs in the RDIP; and (3) monitor the status of funding of PPAs in the RDIP.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RDIP Category Section -->
        <div id="cat-rdip" class="faq-category-section" style="display: none;">
            <h2 class="faq-section-heading">About RDIP</h2>
            <div class="faq-grid">
                <div class="faq-item-box">
                    <button class="faq-toggle" onclick="toggleFaqBox(this)">
                        <span class="faq-question">What is Regional Development Investment Program (RDIP)?</span>
                        <i data-lucide="chevron-down" class="faq-icon" width="20" height="20"></i>
                    </button>
                    <div class="faq-answer-collapse">
                        <div class="faq-answer-inner">
                            <p>The RDIP is a rolling document, thus submission of project proposals for inclusion in the RDIP can be done anytime within the year. The RDIP is updated quarterly, as necessary, to include new and expanded ongoing PPAs towards efficient and effective investment programming and budgeting and to ensure consistency with the regional development priorities.</p>
                        </div>
                    </div>
                </div>
                <div class="faq-item-box">
                    <button class="faq-toggle" onclick="toggleFaqBox(this)">
                        <span class="faq-question">When should the agency submit the documents for RDIP inclusion?</span>
                        <i data-lucide="chevron-down" class="faq-icon" width="20" height="20"></i>
                    </button>
                    <div class="faq-answer-collapse">
                        <div class="faq-answer-inner">
                            <p>The RDIP is a rolling document; agencies can submit anytime but must observe the cut-off period, which is one month before the Sectoral Committee meeting. Submission beyond the cut-off period means they will be considered in the next update schedule.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requirements Category Section -->
        <div id="cat-reqs" class="faq-category-section" style="display: none;">
            <h2 class="faq-section-heading">Documentary Requirements</h2>
            <div class="faq-grid">
                <div class="faq-item-box">
                    <button class="faq-toggle" onclick="toggleFaqBox(this)">
                        <span class="faq-question">What are the documentary requirements for proposed programs/projects?</span>
                        <i data-lucide="chevron-down" class="faq-icon" width="20" height="20"></i>
                    </button>
                    <div class="faq-answer-collapse">
                        <div class="faq-answer-inner">
                            <p>For PPAs to be included in the RDIP, the following documents must be submitted by the proponent agency:</p>
                            <ul class="faq-detail-list">
                                <li>Official request for the project's inclusion in the RDIP.</li>
                                <li>Comprehensive Project Profile (CPP) with GAD Checklist.</li>
                                <li>Feasibility study (for ODA/PPP projects).</li>
                                <li>Sangguniang Panlalawigan/Panlungsod Resolution/Ordinance.</li>
                                <li>LGU/SUC Endorsements and Certifications.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="faq-item-box">
                    <button class="faq-toggle" onclick="toggleFaqBox(this)">
                        <span class="faq-question">Who can submit proposed PPAs for RDIP inclusion?</span>
                        <i data-lucide="chevron-down" class="faq-icon" width="20" height="20"></i>
                    </button>
                    <div class="faq-answer-collapse">
                        <div class="faq-answer-inner">
                            <p>Proponents are classified into two (2): direct and indirect.</p>
                            <p><strong style="color:#154A9A;">Direct proponents:</strong> RLAs, SUCs, GOCCs, and GFIs.</p>
                            <p><strong style="color:#154A9A;">Indirect proponents:</strong> Other RLAs, LGUs, and private sector.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Criteria Category Section -->
        <div id="cat-criteria" class="faq-category-section" style="display: none;">
            <h2 class="faq-section-heading">Inclusion & Exclusion Criteria</h2>
            <div class="faq-grid">
                <div class="faq-item-box">
                    <button class="faq-toggle" onclick="toggleFaqBox(this)">
                        <span class="faq-question">Can ALL types of projects be included in the RDIP?</span>
                        <i data-lucide="chevron-down" class="faq-icon" width="20" height="20"></i>
                    </button>
                    <div class="faq-answer-collapse">
                        <div class="faq-answer-inner">
                            <p>No. Projects should satisfy criteria regarding Typology (Technical assistance, Capital investment, etc.) and Responsiveness to the Bicol RDP.</p>
                        </div>
                    </div>
                </div>
                <div class="faq-item-box">
                    <button class="faq-toggle" onclick="toggleFaqBox(this)">
                        <span class="faq-question">What projects are excluded in the RDIP?</span>
                        <i data-lucide="chevron-down" class="faq-icon" width="20" height="20"></i>
                    </button>
                    <div class="faq-answer-collapse">
                        <div class="faq-answer-inner">
                            <p>Excluded: Recurrent spending, Guarantee-related activities, pure LGU-funded projects, acquisition of lots, and stand-alone preparatory activities.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* RPTS Integrated FAQ Design */
.rdip-faq-container {
    background: #ffffff;
    min-height: 100vh;
    font-family: 'Inter', sans-serif;
    color: #1e293b;
    padding-bottom: 10rem; 
}

/* ━━━ HIDE BUILDING BACKGROUND FROM PARENT ━━━ */
#faq-container-wrapper.no-bg-overlay::before {
    display: none !important;
}

/* ━━━ GLOBAL HEADER OVERRIDE FOR FAQ MODE ━━━ */
.top-bar.faq-mode {
    background: #ffffff !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1) !important;
    backdrop-filter: blur(15px) !important;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05) !important;
}

.top-bar.faq-mode .system-title,
.top-bar.faq-mode .system-subtitle,
.top-bar.faq-mode .top-left i,
.top-bar.faq-mode .top-left svg,
.top-bar.faq-mode #menu-toggle i,
.top-bar.faq-mode #search-toggle i {
    color: #0f172a !important;
}

.top-bar.faq-mode .header-logo {
    filter: none !important;
    opacity: 0.85;
}

/* Header Styling */
.faq-header {
    text-align: center;
    padding: 10rem 2rem 5rem; /* PUSH DOWN further to clear fixed header */
    position: relative;
    background: linear-gradient(to bottom, #f8fafc, #ffffff);
}

.btn-back-minimal {
    position: absolute;
    top: 3rem;
    left: 4rem;
    background: transparent;
    border: none;
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: color 0.2s;
}

.btn-back-minimal:hover { color: #154A9A; }

.faq-main-title {
    font-size: 3.5rem;
    font-weight: 800;
    letter-spacing: -0.04em;
    color: #0f172a;
    margin-bottom: 2.5rem;
}

/* Category Pills */
.faq-categories {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap; /* Fixed: Allow pills to wrap on mobile */
    padding: 0 1rem;
}

.cat-pill {
    background: #f1f5f9;
    color: #64748b;
    border: 1px solid #e2e8f0;
    padding: 0.6rem 1.4rem; /* Slightly more compact */
    border-radius: 50px;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.cat-pill:hover { background: #e2e8f0; color: #0f172a; }

.cat-pill.active {
    background: #154A9A;
    color: #ffffff;
    border-color: #154A9A;
    box-shadow: 0 4px 12px rgba(21, 74, 154, 0.2);
}

/* Body Content */
.faq-body-content {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 2rem;
}

.faq-section-heading {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 2.5rem;
    color: #0f172a;
    border-left: 4px solid #154A9A;
    padding-left: 1.25rem;
}

.faq-grid {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}

/* FAQ Box Item */
.faq-item-box {
    border: 1px solid #e2e8f0;
    background: #fff;
    border-radius: 12px;
    transition: all 0.2s ease;
}

.faq-item-box:hover {
    border-color: #154A9A;
    box-shadow: 0 4px 20px rgba(0,0,0,0.04);
}

.faq-toggle {
    width: 100%;
    padding: 1.75rem 2rem;
    background: transparent;
    border: none;
    text-align: left;
    cursor: pointer;
    outline: none;
    display: flex;
    align-items: center;
}

.faq-question {
    font-size: 1.1rem;
    font-weight: 600;
    color: #0f172a;
    line-height: 1.5;
    flex: 1;
    padding-right: 1.5rem;
}

.faq-icon {
    color: #94a3b8;
    transition: transform 0.3s ease;
}

.faq-answer-collapse { max-height: 0; overflow: hidden; transition: max-height 0.4s ease; }

.faq-answer-inner { padding: 0 2rem 2rem; color: #475569; font-size: 0.95rem; line-height: 1.8; }

.faq-item-box.active { border-color: #154A9A; background: #fcfdfe; }

.faq-item-box.active .faq-icon { transform: rotate(180deg); color: #154A9A; }

.faq-detail-list { margin: 1rem 0; padding-left: 1.25rem; list-style-type: disc; }

.faq-detail-list li { margin-bottom: 0.75rem; }

.faq-sub-list { margin-top: 0.75rem; padding-left: 1.5rem; list-style-type: circle; color: #64748b; }

@media (max-width: 768px) {
    .faq-header { padding: 8rem 1.5rem 3rem; }
    .faq-main-title { font-size: 2.2rem; margin-bottom: 2rem; }
    .faq-categories { gap: 8px; }
    .cat-pill { padding: 0.5rem 1.2rem; font-size: 0.65rem; }
    .faq-body-content { padding: 0 1.25rem; }
    .faq-section-heading { font-size: 1.4rem; margin-bottom: 1.5rem; }
    .faq-toggle { padding: 1.25rem 1.5rem; }
    .faq-question { font-size: 1rem; padding-right: 1rem; }
    .faq-answer-inner { padding: 0 1.5rem 1.5rem; font-size: 0.9rem; }
}
</style>

<script>
window.toggleFaqBox = function(btn) {
    const item = btn.parentElement;
    const answer = item.querySelector('.faq-answer-collapse');
    const isActive = item.classList.contains('active');

    // Close all other items
    document.querySelectorAll('.faq-item-box').forEach(other => {
        if (other !== item) {
            other.classList.remove('active');
            other.querySelector('.faq-answer-collapse').style.maxHeight = null;
        }
    });

    if (!isActive) {
        item.classList.add('active');
        answer.style.maxHeight = answer.scrollHeight + "px";
    } else {
        item.classList.remove('active');
        answer.style.maxHeight = null;
    }
    
    // Refresh Icons
    if (window.lucide) lucide.createIcons();
};

</script>

