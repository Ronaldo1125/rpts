<section id="projects" class="page-content container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">Manage Projects</h2>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 px-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="text-secondary small">Show</span>
                    <select class="form-select form-select-sm border-0 bg-light" style="width: 70px;">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                    <span class="text-secondary small">entries</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                     <span class="text-secondary small fw-bold">Search:</span>
                     <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" class="form-control rounded-pill bg-light border-0 px-3" placeholder="Search projects...">
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle" data-sortable="true">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary">Project Name</th>
                            <th class="small text-secondary" data-sort-skip="true">Description</th>
                            <th class="small text-secondary">Funding Category</th>
                            <th class="small text-secondary">Created At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="clickable-row cursor-pointer" style="cursor: pointer;" data-agency="Bicol University (BU)">
                            <td style="width: 20%; color: inherit !important;">BU Tabaco Campus Medical & Dental Clinic</td>
                            <td class="small" style="width: 30%;">One building completed</td>
                            <td class="small">No Funding Category Yet</td>
                            <td><span class="badge bg-indigo-50 text-indigo-700 fw-normal small">2 weeks ago</span></td>
                        </tr>
                        <tr class="clickable-row cursor-pointer" style="cursor: pointer;" data-agency="DPWH Region V">
                            <td style="color: inherit !important;">Conduct of RP-FP classes in the FDS...</td>
                            <td class="small">Capacitate local implementers on the conduct...</td>
                            <td class="small">No Funding Category Yet</td>
                            <td><span class="badge bg-indigo-50 text-indigo-700 fw-normal small">2 weeks ago</span></td>
                        </tr>
                        <tr class="clickable-row cursor-pointer" style="cursor: pointer;" data-agency="DICT - Bicol">
                            <td style="color: inherit !important;">Refresher Course on Pre-Marriage Counseling</td>
                            <td class="small">A two-day training on how to run a Pre-...</td>
                            <td class="small">No Funding Category Yet</td>
                            <td><span class="badge bg-indigo-50 text-indigo-700 fw-normal small">2 weeks ago</span></td>
                        </tr>
                        <tr class="clickable-row cursor-pointer" style="cursor: pointer;" data-agency="DPWH Region V">
                            <td style="color: inherit !important;">Upgrading/Rehab of Facilities at Main Hospital...</td>
                            <td class="small">Rehabilitation/renovation of Building J-OB...</td>
                            <td class="small">No Funding Category Yet</td>
                            <td><span class="badge bg-indigo-50 text-indigo-700 fw-normal small">2 weeks ago</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="text-muted small">Showing 1 to 4 of 4 entries</span>
                <nav>
                    <ul class="custom-pagination mb-0">
                        <li class="page-item disabled"><a class="page-link" href="#">&laquo;</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">&lsaquo;</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">&rsaquo;</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</section>
<style>
    .bg-indigo-50 { background-color: #e8f0fe !important; }
    .text-indigo-700 { color: #0032A6 !important; }
</style>
