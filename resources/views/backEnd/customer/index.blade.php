@extends('backEnd.layouts.master')
@section('title','Customer Manage')

@section('css')
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<style>
    .cust-header-card {
        background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
        border-radius: 14px;
        padding: 22px 26px;
        color: #fff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px rgba(6, 78, 59, 0.15);
    }
    .cust-stat-box {
        background: rgba(255,255,255,0.12);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 10px;
        padding: 10px 18px;
        text-align: center;
    }
    .cust-stat-box h3 { color: #fff; margin: 0; font-size: 20px; font-weight: 700; }
    .cust-stat-box span { color: #a7f3d0; font-size: 11.5px; }

    .card-modern {
        border: none;
        border-radius: 14px;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
        background: #fff;
        overflow: hidden;
    }

    .table-modern thead th {
        background: #f8fafc;
        color: #475569;
        font-size: 11.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 18px;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-modern tbody td {
        vertical-align: middle;
        padding: 14px 18px;
        color: #1e293b;
        font-size: 13.5px;
        border-bottom: 1px solid #f1f5f9;
    }
    .table-modern tbody tr:hover td { background-color: #f8fafc; }

    .cust-avatar-wrap {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
        background: linear-gradient(135deg, #10b981, #059669);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 2px 6px rgba(5, 150, 105, 0.2);
    }
    .cust-avatar-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .status-badge {
        font-size: 11.5px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .status-active { background: #dcfce7; color: #166534; }
    .status-inactive { background: #fee2e2; color: #991b1b; }
    .status-dot-sm { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }

    .action-btn-circle {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: #f1f5f9;
        color: #64748b;
        transition: all 0.2s;
        text-decoration: none;
    }
    .action-btn-circle:hover { transform: translateY(-2px); }
    .btn-view-c:hover { background: #e0f2fe; color: #0284c7; }
    .btn-edit-c:hover { background: #e0e7ff; color: #4338ca; }
    .btn-login-c:hover { background: #fef3c7; color: #d97706; }
    .btn-del-c:hover { background: #fee2e2; color: #ef4444; }
    .btn-deact:hover { background: #fff1f2; color: #e11d48; }
    .btn-act:hover { background: #f0fdf4; color: #16a34a; }

    .btn-delete-all {
        font-size: 12.5px;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 8px;
        border: 1px solid rgba(250, 92, 124, 0.4);
        color: #fff;
        background: rgba(255, 255, 255, 0.15);
        transition: all 0.2s;
        backdrop-filter: blur(6px);
    }
    .btn-delete-all:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
    
    {{-- HEADER CARD --}}
    <div class="cust-header-card">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="text-white mb-1 fw-bold">
                    <i class="fe-users me-2"></i> Customer Management
                </h4>
                <p class="mb-0 text-white-50 font-size-13">
                    নিবন্ধিত কাস্টমারদের প্রোফাইল, কন্টাক্ট ইনফো এবং অ্যাকাউন্ট নিয়ন্ত্রণ।
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="cust-stat-box">
                    <h3>{{ $show_data->total() }}</h3>
                    <span>Total Customers</span>
                </div>
                @if($show_data->total() > 0)
                <form method="post" action="{{ route('customers.destroy-all') }}" id="form-delete-all-customers" class="mb-0">
                    @csrf
                    <input type="hidden" name="confirm" id="customer-delete-all-confirm" value="">
                    <button type="button" class="btn-delete-all" onclick="submitDeleteAllCustomers()">
                        <i class="fe-trash-2 me-1"></i> Delete All Customers
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-body p-4">
            <div id="customer-table-wrapper">
                <div class="table-responsive">
                    <table id="datatable-buttons" class="table table-modern w-100 dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>Customer Name</th>
                                <th>Contact Number</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th class="text-end" style="width: 170px;">Actions</th>
                            </tr>
                        </thead>                
                        <tbody>
                            @foreach($show_data as $key => $value)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="cust-avatar-wrap">
                                            @if($value->image && file_exists(public_path($value->image)))
                                                <img src="{{ asset($value->image) }}" alt="">
                                            @else
                                                {{ strtoupper(substr($value->name ?: 'C', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div>
                                            <strong class="d-block text-dark font-size-14">{{ $value->name ?: 'Customer' }}</strong>
                                            <small class="text-muted">ID: #{{ $value->id }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="text-dark font-size-13"><i class="fe-phone text-muted me-1"></i> {{ $value->phone }}</span>
                                </td>

                                <td>
                                    @if($value->email)
                                        <span class="text-muted font-size-13"><i class="fe-mail me-1"></i> {{ $value->email }}</span>
                                    @else
                                        <span class="text-muted small">N/A</span>
                                    @endif
                                </td>
                                
                                <td>
                                    @if($value->status == 'active')
                                        <span class="status-badge status-active"><span class="status-dot-sm"></span> Active</span> 
                                    @else 
                                        <span class="status-badge status-inactive"><span class="status-dot-sm"></span> {{ ucfirst($value->status) }}</span> 
                                    @endif
                                </td>

                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        
                                        {{-- Status Toggle --}}
                                        @if($value->status == 'active')
                                            <form method="post" action="{{ route('customers.inactive') }}" class="d-inline"> 
                                                @csrf
                                                <input type="hidden" value="{{ $value->id }}" name="hidden_id">        
                                                <button type="submit" class="action-btn-circle btn-deact" title="Deactivate Customer">
                                                    <i class="fe-thumbs-down font-size-13"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="post" action="{{ route('customers.active') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" value="{{ $value->id }}" name="hidden_id">        
                                                <button type="submit" class="action-btn-circle btn-act" title="Activate Customer">
                                                    <i class="fe-thumbs-up font-size-13"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- View Profile --}}
                                        <a href="{{ route('customers.profile', ['id' => $value->id]) }}" class="action-btn-circle btn-view-c" title="View Customer Profile">
                                            <i class="fe-eye font-size-13"></i>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('customers.edit', $value->id) }}" class="action-btn-circle btn-edit-c" title="Edit Customer">
                                            <i class="fe-edit-2 font-size-13"></i>
                                        </a>

                                        {{-- Login As Customer --}}
                                        <form method="post" action="{{ route('customers.adminlog') }}" class="d-inline" target="_blank">
                                            @csrf
                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                            <button type="submit" class="action-btn-circle btn-login-c" title="Login As Customer in New Tab">
                                                <i class="fe-log-in font-size-13"></i>
                                            </button>
                                        </form>

                                        {{-- Delete --}}
                                        <form method="post" action="{{ route('customers.destroy', $value->id) }}" class="d-inline"
                                              onsubmit="return confirm('এই কাস্টমার অ্যাকাউন্ট মুছে ফেলবেন?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn-circle btn-del-c" title="Delete Customer">
                                                <i class="fe-trash-2 font-size-13"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    {{ $show_data->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="{{asset('/public/backEnd/')}}/assets/js/pages/datatables.init.js"></script>

<script>
function submitDeleteAllCustomers() {
    var text = prompt('সকল কাস্টমার মুছতে DELETE লিখুন:');
    if (text !== 'DELETE') {
        if (text !== null) {
            alert('ভুল টেক্সট। কাজটি বাতিল হয়েছে।');
        }
        return;
    }
    if (!confirm('সতর্কতা! সকল কাস্টমার চিরতরে মুছে যাবে। চালিয়ে যাবেন?')) {
        return;
    }
    document.getElementById('customer-delete-all-confirm').value = 'DELETE';
    document.getElementById('form-delete-all-customers').submit();
}

$(document).on('click', '#customer-table-wrapper .pagination a', function (e) {
    e.preventDefault();
    let url = $(this).attr('href');
    $('#customer-table-wrapper').css('opacity','0.5');

    $.get(url, function (response) {
        let html = $(response).find('#customer-table-wrapper').html();
        $('#customer-table-wrapper').html(html);
    }).always(function () {
        $('#customer-table-wrapper').css('opacity','1');
    });
});
</script>
@endsection