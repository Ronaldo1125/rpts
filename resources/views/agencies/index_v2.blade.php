@extends('layouts.app_v2')
@section('content')
<section id="agency" class="page-content active container-fluid py-4 text-dark">
    <div class="d-flex justify-content-between align-items-center mb-4 px-2">
        <div>
            <h2 class="fw-bold mb-0">Manage Agencies</h2>
            <p class="text-muted small mb-0">Configure implementing agencies for the RDC process</p>
        </div>
        <div>
            <button class="btn btn-primary text-white px-4 py-2 fw-medium rounded-pill shadow-sm" style="background-color: #154A9A; border-color: #154A9A;" data-bs-toggle="modal" data-bs-target="#addAgencyModal">
                <i data-lucide="plus" class="me-1" width="18"></i> Create Agency
            </button>
        </div>
    </div>

    <!-- Add Agency Modal -->
    <div class="modal fade" id="addAgencyModal" tabindex="-1" aria-labelledby="addAgencyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
                <div class="modal-accent-primary"></div>
                <div class="modal-header border-0 pt-4 px-4 pb-1">
                    <h5 class="modal-title fw-bold" id="addAgencyModalLabel">Add Agency</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body px-4">
                    <form id="addAgencyForm">
                        @csrf
                        <div class="mb-4">
                            <label for="agencyName" class="form-label small fw-semibold text-secondary mb-1">Agency Name</label>
                            <input type="text" name="agency_name" class="form-control rounded-12" id="agencyName" placeholder="Enter Agency Name" required>
                            <span class="text-danger mt-1 d-none" id="error-agency_name" style="font-size: 0.75rem;"></span>
                        </div>
                        <div class="mb-0">
                            <label for="agencyAcronym" class="form-label small fw-semibold text-secondary mb-1">Agency Acronym</label>
                            <input type="text" name="agency_acronym" class="form-control rounded-12" id="agencyAcronym" placeholder="Enter Agency Acronym" required>
                            <span class="text-danger mt-1 d-none" id="error-agency_acronym" style="font-size: 0.75rem;"></span>
                        </div>
                    </form>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3" data-bs-dismiss="modal">Close</button>
                    <button type="submit" id="saveAgencyBtn" form="addAgencyForm" class="btn btn-primary px-4 py-2 fw-semibold rounded-12 shadow-sm" style="background-color: #154A9A; border-color: #154A9A;">
                        <span class="btn-text">Save Agency</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-16">
        <div class="card-body d-flex flex-column" style="min-height: 300px; padding: 1.5rem 1.25rem 0.5rem 1.25rem;">
            @include('partials.table-header')

            <div class="table-responsive table-overflow-visible">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="small text-secondary ps-3">Agency Name</th>
                            <th class="small text-secondary">Agency Acronym</th>
                            <th class="small text-secondary">Created At</th>
                            <th class="small text-secondary text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="agency-list-tbody">
                        @foreach($agencies as $agency)
                        <tr>
                            <td class="py-3 ps-3 fw-medium">
                              {{ $agency->agency_name }}
                            </td>
                            <td class="py-3">
                                <span class="badge-soft-pill badge-soft-blue">{{ $agency->agency_acronym }}</span>
                            </td>
                            <td class="py-3"><span class="badge-soft-pill badge-soft-blue">{{ $agency->created_at->diffForHumans() }}</span></td>
                            <td class="py-3 text-end pe-3">
                              <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-link text-dark p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                  <i data-lucide="more-vertical" width="20"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-12">
                                  <a class="dropdown-item py-2" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#editAgencyModal{{ $agency->id }}">
                                    <i data-lucide="edit-2" class="me-2 text-primary" width="16"></i> Edit
                                  </a>
                                  <a class="dropdown-item py-2 text-danger delete-agency-btn" href="javascript:void(0);" data-id="{{ $agency->id }}" data-name="{{ $agency->agency_name }}">
                                    <i data-lucide="trash-2" class="me-2" width="16"></i> Delete
                                  </a>
                                </div>
                              </div>

                              <!-- Edit Modal -->
                              <div class="modal fade" id="editAgencyModal{{ $agency->id }}" tabindex="-1" aria-hidden="true">
                                  <div class="modal-dialog modal-dialog-centered">
                                      <div class="modal-content border-0 shadow-lg rounded-20 overflow-hidden">
                                          <div class="modal-accent-primary"></div>
                                          <div class="modal-header border-0 pt-4 px-4 pb-1">
                                              <h5 class="modal-title fw-bold">Edit Agency</h5>
                                              <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                                          </div>
                                          <div class="modal-body px-4 text-start">
                                              <form id="editAgencyForm{{ $agency->id }}" class="edit-agency-form" data-id="{{ $agency->id }}">
                                                  @csrf
                                                  @method('PUT')
                                                  <div class="mb-4">
                                                      <label class="form-label small fw-semibold text-secondary d-block mb-1">Agency Name</label>
                                                      <input type="text" name="agency_name" class="form-control rounded-12" value="{{ $agency->agency_name }}" required>
                                                      <span class="text-danger mt-1 d-none error-agency_name" style="font-size: 0.75rem;"></span>
                                                  </div>
                                                  <div class="mb-0">
                                                      <label class="form-label small fw-semibold text-secondary d-block mb-1">Agency Acronym</label>
                                                      <input type="text" name="agency_acronym" class="form-control rounded-12" value="{{ $agency->agency_acronym }}" required>
                                                      <span class="text-danger mt-1 d-none error-agency_acronym" style="font-size: 0.75rem;"></span>
                                                  </div>
                                              </form>
                                          </div>
                                          <div class="modal-footer border-0 px-4 pb-4">
                                              <button type="button" class="btn btn-link text-secondary text-decoration-none small fw-medium px-3" data-bs-dismiss="modal">Close</button>
                                              <button type="submit" form="editAgencyForm{{ $agency->id }}" class="btn btn-primary px-4 py-2 fw-semibold rounded-12 shadow-sm" style="background-color: #154A9A; border-color: #154A9A;">Update Agency</button>
                                          </div>
                                      </div>
                                  </div>
                              </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @include('partials.table-pagination', ['paginator' => $agencies])
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Validation Listeners
        const validateUrl = "{{ route('v2.agencies.validate') }}";
        
        RPTS.validation.attach(validateUrl, document.getElementById('agencyName'), 'agency_name', document.getElementById('error-agency_name'));
        RPTS.validation.attach(validateUrl, document.getElementById('agencyAcronym'), 'agency_acronym', document.getElementById('error-agency_acronym'));

        document.querySelectorAll('.edit-agency-form').forEach(form => {
            const id = form.getAttribute('data-id');
            const nameInput = form.querySelector('input[name="agency_name"]');
            const acronymInput = form.querySelector('input[name="agency_acronym"]');
            
            RPTS.validation.attach(validateUrl, nameInput, 'agency_name', form.querySelector('.error-agency_name'), id);
            RPTS.validation.attach(validateUrl, acronymInput, 'agency_acronym', form.querySelector('.error-agency_acronym'), id);
        });

        // Add Agency AJAX
        document.getElementById('addAgencyForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const saveBtn = document.getElementById('saveAgencyBtn');
            saveBtn.disabled = true;
            saveBtn.querySelector('.btn-text').classList.add('d-none');
            saveBtn.querySelector('.spinner-border').classList.remove('d-none');

            try {
                const response = await fetch("{{ route('v2.agencies.store') }}", {
                    method: 'POST',
                    body: new FormData(this),
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if (response.status === 422) {
                    const data = await response.json();
                    for (const [key, messages] of Object.entries(data.errors)) {
                        const errorEl = document.getElementById(`error-${key}`);
                        if (errorEl) { errorEl.textContent = messages[0]; errorEl.classList.remove('d-none'); }
                    }
                    return;
                }

                const data = await response.json();
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addAgencyModal')).hide();
                    RPTS.toast.show('Agency created successfully.', 'success');
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                RPTS.toast.show('Error saving agency.', 'warning');
            } finally {
                saveBtn.disabled = false;
                saveBtn.querySelector('.btn-text').classList.remove('d-none');
                saveBtn.querySelector('.spinner-border').classList.add('d-none');
            }
        });

        // Edit Agency AJAX
        document.querySelectorAll('.edit-agency-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                const id = this.getAttribute('data-id');
                const submitBtn = this.closest('.modal-content').querySelector('button[type="submit"]');
                submitBtn.disabled = true;

                try {
                    const response = await fetch(`/v2/agencies/${id}`, {
                        method: 'POST',
                        body: new FormData(this),
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-HTTP-Method-Override': 'PUT' }
                    });

                    if (response.status === 422) {
                        const data = await response.json();
                        for (const [key, messages] of Object.entries(data.errors)) {
                            const errorEl = this.querySelector(`.error-${key}`);
                            if (errorEl) { errorEl.textContent = messages[0]; errorEl.classList.remove('d-none'); }
                        }
                        return;
                    }

                    const data = await response.json();
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById(`editAgencyModal${id}`)).hide();
                        RPTS.toast.show('Agency updated successfully.', 'info');
                        setTimeout(() => window.location.reload(), 2000);
                    }
                } catch (error) {
                    RPTS.toast.show('Error updating agency.', 'warning');
                } finally {
                    submitBtn.disabled = false;
                }
            });
        });

        // Delete Agency Logic
        RPTS.delete.init(async (id) => {
            try {
                const response = await fetch(`/v2/agencies/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'X-HTTP-Method-Override': 'DELETE'
                    }
                });
                const data = await response.json();
                if (data.success) {
                    RPTS.delete.hide();
                    RPTS.toast.show('Agency deleted successfully.', 'danger');
                    setTimeout(() => window.location.reload(), 2000);
                }
            } catch (error) {
                alert('Could not delete agency.');
            }
        });

        document.querySelectorAll('.delete-agency-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                RPTS.delete.confirm(this.getAttribute('data-id'), this.getAttribute('data-name'));
            });
        });
    });
</script>
@endsection