@extends('backEnd.layouts.master')
@section('title', 'Customer Management')

@section('css')
<style>
    /* Premium Design System matching Vendor list */
    .cust-header-card {
        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
        border-radius: 16px;
        padding: 24px;
        color: #ffffff;
        margin-bottom: 20px;
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.15);
    }
    .metric-badge-box {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 12px;
        padding: 12px 18px;
        text-align: center;
        min-width: 110px;
    }
    .metric-badge-box h3 {
        color: #ffffff;
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 2px;
    }
    .metric-badge-box span {
        color: rgba(255, 255, 255, 0.85);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }

    /* Filter Card */
    .filter-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
        padding: 18px 22px;
    }
    .form-label-modern {
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }
    .form-control-modern, .form-select-modern {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 9px;
        padding: 8px 12px;
        font-size: 13.5px;
        color: #0f172a;
        transition: all 0.2s;
    }
    .form-control-modern:focus, .form-select-modern:focus {
        background: #ffffff;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.12);
        outline: none;
    }

    /* Modern Table */
    .table-card-modern {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .table-modern {
        margin-bottom: 0;
        vertical-align: middle;
    }
    .table-modern thead th {
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
        font-size: 11.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1.5px solid #e2e8f0;
        padding: 13px 16px;
        white-space: nowrap;
    }
    .table-modern tbody td {
        padding: 13px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        color: #1e293b;
    }
    .table-modern tbody tr:hover {
        background: #fafcff;
    }

    /* Customer Avatar */
    .cust-avatar {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        object-fit: cover;
        border: 1.5px solid #e2e8f0;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    }
    .cust-avatar-initials {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(14, 165, 233, 0.2);
    }

    /* Status Pills */
    .badge-soft-verified { background: #dcfce7; color: #15803d; font-weight: 600; padding: 4px 9px; border-radius: 6px; font-size: 11.5px; }
    .badge-soft-rejected { background: #fee2e2; color: #b91c1c; font-weight: 600; padding: 4px 9px; border-radius: 6px; font-size: 11.5px; }

    /* Action Circle Buttons */
    .action-circle-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: #f1f5f9;
        color: #475569;
        transition: all 0.2s;
        text-decoration: none;
        font-size: 13px;
    }
    .action-circle-btn:hover { transform: translateY(-2px); }
    .btn-action-view:hover { background: #e0f2fe; color: #0284c7; }
    .btn-action-edit:hover { background: #e0e7ff; color: #4338ca; }
    .btn-action-login:hover { background: #fef3c7; color: #d97706; }
    .btn-action-del:hover { background: #fee2e2; color: #dc2626; }
    .btn-action-toggle:hover { background: #f3e8ff; color: #7e22ce; }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">

    {{-- HEADER CARD --}}
    <div class="cust-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold">
                    <i class="fe-users me-2"></i> Customers Management
                </h4>
                <p class="mb-0 text-white-50 font-size-13">
                    Manage customer accounts, verify profiles, monitor activity, and login as customer.
                </p>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2">
                <div class="metric-badge-box">
                    <h3>{{ $stats['total'] ?? 0 }}</h3>
                    <span>Total Customers</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['active'] ?? 0 }}</h3>
                    <span>Active</span>
                </div>
                <div class="metric-badge-box">
                    <h3>{{ $stats['inactive'] ?? 0 }}</h3>
                    <span>Inactive</span>
                </div>
                @if($show_data->total() > 0)
                <form method="post" action="{{ route('customers.destroy-all') }}" id="form-delete-all-customers" class="d-inline mb-0">
                    @csrf
                    <input type="hidden" name="confirm" id="customer-delete-all-confirm" value="">
                    <button type="button" class="btn btn-outline-light rounded-pill px-3 shadow-sm font-size-13 fw-bold" onclick="submitDeleteAllCustomers()">
                        <i class="fe-trash-2 me-1"></i> Delete All
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <div class="filter-card-modern">
        <form method="GET" action="{{ route('customers.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label-modern">Search Keyword</label>
                    <input type="text" name="keyword" class="form-control form-control-modern" 
                           placeholder="Customer name, phone number, email..." value="{{ request('keyword') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Account Status</label>
                    <select name="status" class="form-select form-select-modern">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Sort By</label>
                    <select name="sort_by" class="form-select form-select-modern">
                        <option value="latest" {{ request('sort_by') === 'latest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort_by') === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ request('sort_by') === 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label-modern">Per Page</label>
                    <select name="per_page" class="form-select form-select-modern">
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ request('per_page') === 'all' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary rounded-3 w-100 fw-bold font-size-13 py-2">
                            <i class="fe-filter me-1"></i> Filter
                        </button>
                        @if(request()->anyFilled(['keyword', 'status', 'sort_by', 'per_page']))
                            <a href="{{ route('customers.index') }}" class="btn btn-light border rounded-3 px-3 py-2" title="Reset Filters">
                                <i class="fe-rotate-ccw"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- CUSTOMERS TABLE --}}
    <div class="table-card-modern">
        <div class="table-responsive">
            <table class="table table-modern align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Customer Details</th>
                        <th>Phone Number</th>
                        <th>Email Address</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 170px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($show_data as $key => $value)
                    <tr>
                        <td class="text-muted fw-semibold">
                            {{ $loop->iteration + ($show_data->currentPage() - 1) * $show_data->perPage() }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($value->image)
                                    <img src="{{ asset($value->image) }}" alt="" class="cust-avatar" loading="lazy" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='inline-flex';">
                                    <div class="cust-avatar-initials" style="display:none;">
                                        {{ strtoupper(substr($value->name ?: 'C', 0, 1)) }}
                                    </div>
                                @else
                                    <div class="cust-avatar-initials">
                                        {{ strtoupper(substr($value->name ?: 'C', 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-bold text-dark font-size-14">{{ $value->name ?: 'Customer' }}</div>
                                    <span class="text-muted font-size-12">ID: #{{ $value->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">
                                <i class="fe-phone text-muted me-1"></i>
                                <a href="tel:{{ $value->phone }}" class="text-secondary text-decoration-none">{{ $value->phone }}</a>
                            </div>
                        </td>
                        <td>
                            @if($value->email)
                                <div class="text-muted font-size-12">
                                    <i class="fe-mail me-1"></i> {{ $value->email }}
                                </div>
                            @else
                                <span class="text-muted font-size-12">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($value->status == 'active')
                                <form method="post" action="{{ route('customers.inactive') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                    <button type="submit" class="badge rounded-pill border-0 px-2 py-1 font-size-12 bg-success text-white" style="cursor: pointer;" title="Click to deactivate">
                                        Active
                                    </button>
                                </form>
                            @else
                                <form method="post" action="{{ route('customers.active') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                    <button type="submit" class="badge rounded-pill border-0 px-2 py-1 font-size-12 bg-danger text-white" style="cursor: pointer;" title="Click to activate">
                                        Inactive
                                    </button>
                                </form>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                {{-- View Profile --}}
                                <a href="{{ route('customers.profile', ['id' => $value->id]) }}" class="action-circle-btn btn-action-view" title="View Customer Profile">
                                    <i class="fe-eye"></i>
                                </a>

                                {{-- Edit --}}
                                <a href="{{ route('customers.edit', $value->id) }}" class="action-circle-btn btn-action-edit" title="Edit Customer">
                                    <i class="fe-edit"></i>
                                </a>

                                {{-- Login As Customer --}}
                                <form method="post" action="{{ route('customers.adminlog') }}" class="d-inline" target="_blank">
                                    @csrf
                                    <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                    <button type="submit" class="action-circle-btn btn-action-login" title="Login As Customer in New Tab">
                                        <i class="fe-log-in"></i>
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <form method="post" action="{{ route('customers.destroy', $value->id) }}" class="d-inline"
                                      onsubmit="return confirm('Delete this customer account permanently?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-circle-btn btn-action-del" title="Delete Customer">
                                        <i class="fe-trash-2"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fe-users font-size-36 d-block mb-2 text-slate-300"></i>
                            <p class="mb-0 font-size-14 fw-semibold">No customers found matching your criteria.</p>
                            <small class="text-muted">Try changing your search keywords or resetting filters.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($show_data->hasPages() || $show_data->total() > 0)
        <div class="p-3 bg-light border-top d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="text-muted font-size-13">
                Showing <strong>{{ $show_data->firstItem() ?? 0 }}</strong> to <strong>{{ $show_data->lastItem() ?? 0 }}</strong> of <strong>{{ $show_data->total() }}</strong> Customers
            </div>
            <div>
                {{ $show_data->links('pagination::bootstrap-4') }}
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function submitDeleteAllCustomers() {
    var text = prompt('Type DELETE to delete all customers permanently:');
    if (text !== 'DELETE') {
        if (text !== null) {
            alert('Wrong text entered. Action cancelled.');
        }
        return;
    }
    if (!confirm('WARNING! All customer accounts will be deleted permanently. Continue?')) {
        return;
    }
    document.getElementById('customer-delete-all-confirm').value = 'DELETE';
    document.getElementById('form-delete-all-customers').submit();
}
</script>
@endsection