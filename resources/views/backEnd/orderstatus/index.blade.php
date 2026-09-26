@extends('backEnd.layouts.master')
@section('title', 'অর্ডার স্ট্যাটাস')

@section('css')
<link href="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />
@include('backEnd.orderstatus.partials.os_styles')
@endsection

@section('content')
<div class="container-fluid order-status-shell order-status-page">

    <div class="os-page-header">
        <div>
            <h4>অর্ডার স্ট্যাটাস <span class="os-badge-count">{{ $data->count() }}</span></h4>
            <p class="os-sub">অর্ডার ট্র্যাকিংয়ের জন্য স্ট্যাটাস পরিচালনা (যেমন: পেন্ডিং, শিপড, ডেলিভার্ড)</p>
        </div>
        <a href="{{ route('orderstatus.create') }}" class="os-btn-primary">
            <i class="fas fa-plus"></i> নতুন স্ট্যাটাস
        </a>
    </div>

    <div class="os-card">
        <div class="os-card-head">
            <span class="os-card-icon"><i class="fas fa-flag"></i></span>
            <h6>স্ট্যাটাস তালিকা</h6>
        </div>
        <div class="os-card-body os-dt-wrap">
            <div class="os-table-rail">
                <table id="os-datatable" class="table os-table mb-0 w-100">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>স্ট্যাটাস নাম ও স্লাগ</th>
                            <th class="text-center" style="width: 110px;">সংযুক্ত অর্ডার</th>
                            <th class="text-center" style="width: 100px;">অবস্থা</th>
                            <th class="text-end" style="width: 160px;">অ্যাকশন</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $value)
                        <tr>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border px-2 py-1 fw-bold">#{{ $value->id }}</span>
                            </td>
                            <td>
                                <div class="os-status-name fw-bold text-dark">
                                    <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                    {{ $value->name }}
                                </div>
                                <small class="text-muted font-monospace" style="font-size: 11px;">{{ $value->slug }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ ($value->orders_count ?? 0) > 0 ? 'bg-primary-subtle text-primary border border-primary-subtle' : 'bg-light text-muted border' }} px-2 py-1">
                                    {{ $value->orders_count ?? 0 }} টি
                                </span>
                            </td>
                            <td class="text-center">
                                @if($value->status == 1)
                                    <span class="os-pill-active">সক্রিয়</span>
                                @else
                                    <span class="os-pill-inactive">নিষ্ক্রিয়</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="os-row-actions justify-content-end d-flex align-items-center gap-1">
                                    @if($value->status == 1)
                                        <form method="post" action="{{ route('orderstatus.inactive') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                            <button type="submit" class="os-act-btn os-act-toggle-on" title="নিষ্ক্রিয় করুন">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form method="post" action="{{ route('orderstatus.active') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                            <button type="submit" class="os-act-btn os-act-toggle-off" title="সক্রিয় করুন">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('orderstatus.edit', $value->id) }}" class="os-act-btn os-act-edit" title="সম্পাদনা">
                                        <i class="fas fa-pen"></i>
                                    </a>

                                    <form method="post" action="{{ route('orderstatus.destroy') }}" class="d-inline" onsubmit="return confirm('আপনি কি নিশ্চিত যে এই ডুপ্লিকেট/অপ্রয়োজনীয় স্ট্যাটাসটি মুছে ফেলতে চান?');">
                                        @csrf
                                        <input type="hidden" value="{{ $value->id }}" name="hidden_id">
                                        <button type="submit" class="os-act-btn text-danger border-0 bg-transparent" title="মুছে ফেলুন" style="background: #fee2e2; color: #dc2626; width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-trash-alt" style="font-size: 11px;"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="os-empty">
                                    <i class="fas fa-flag"></i>
                                    <p class="mb-0">কোনো স্ট্যাটাস নেই</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="{{ asset('/public/backEnd/') }}/assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
<script>
$(function () {
    if ($.fn.DataTable && $('#os-datatable tbody tr').length > 0 && !$('#os-datatable tbody tr td[colspan]').length) {
        $('#os-datatable').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: 'খুঁজুন:',
                lengthMenu: '_MENU_ টি দেখান',
                info: '_TOTAL_ এর মধ্যে _START_–_END_',
                paginate: { previous: '‹', next: '›' },
                emptyTable: 'কোনো ডেটা নেই',
                zeroRecords: 'মিল পাওয়া যায়নি'
            },
            columnDefs: [{ orderable: false, targets: -1 }]
        });
    }
});
</script>
@endsection
