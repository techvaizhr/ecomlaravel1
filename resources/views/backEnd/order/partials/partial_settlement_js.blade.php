<script>
(function ($) {
"use strict";
if (!$) return;

$(document).ready(function () {
    let currentPartialOrder = null;

    function showPsModal() {
        var el = document.getElementById('partialSettlementModal');
        if (!el) return;

        // Move to body to prevent stacking context & backdrop trapping
        if (el.parentNode !== document.body) {
            document.body.appendChild(el);
        }

        // Close other open modals
        $('.modal').not('#partialSettlementModal').modal('hide');
        $('.modal-backdrop').not('.ps-keep').remove();

        setTimeout(function () {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                try {
                    var modal = bootstrap.Modal.getOrCreateInstance(el, { backdrop: true, keyboard: true });
                    if (modal) { modal.show(); return; }
                } catch(e) {}
            }
            $(el).modal('show');
        }, 100);
    }

    function hidePsModal() {
        var el = document.getElementById('partialSettlementModal');
        if (!el) return;

        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                var modal = bootstrap.Modal.getInstance(el);
                if (modal) { modal.hide(); }
            } catch(e) {}
        }
        $(el).modal('hide');
        setTimeout(function () {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
        }, 300);
    }

    // Open Partial Settlement Modal for a given order ID
    window.openPartialSettlementModal = function (orderId) {
        if (!orderId) return;

        // Show loading state if needed
        $('#ps_order_id').val(orderId);
        $('#ps_items_tbody').html('<tr><td colspan="6" class="text-center py-3 text-muted"><i class="fas fa-spinner fa-spin me-1"></i> তথ্য লোড হচ্ছে...</td></tr>');
        
        showPsModal();

        $.ajax({
            url: "{{ url('admin/order/partial-details') }}/" + orderId,
            type: "GET",
            dataType: "json",
            success: function (res) {
                if (res.status === 'success' && res.order) {
                    currentPartialOrder = res.order;
                    renderPartialModalData(res.order);
                } else {
                    toastr.error('অর্ডারের তথ্য পাওয়া যায়নি');
                    hidePsModal();
                }
            },
            error: function () {
                toastr.error('সার্ভার থেকে তথ্য লোড করতে সমস্যা হয়েছে');
                hidePsModal();
            }
        });
    };

    function renderPartialModalData(order) {
        $('#ps_invoice_badge').text('#' + order.invoice_id);
        $('#ps_customer_name').text(order.customer_name);
        $('#ps_customer_phone').text(order.customer_phone);
        $('#ps_original_amount').text(order.amount);
        $('.ps-calc-orig').text(order.amount);
        $('#ps_ship_charge_label').text(order.shipping_charge);
        $('#ps_delivery_charge_paid_11').val(order.shipping_charge);
        $('#ps_collected_amount_9').val(order.amount);
        $('#ps_shortage_9').text('৳0');
        $('#ps_note').val(order.partial_note || '');

        // Render Table Items for Option 10
        var tbody = '';
        if (order.items && order.items.length > 0) {
            order.items.forEach(function (item) {
                var delQty = item.delivered_qty !== undefined ? item.delivered_qty : item.qty;
                var retQty = item.qty - delQty;
                var lineTotal = delQty * item.sale_price;

                tbody += `
                <tr data-item-id="${item.id}" data-price="${item.sale_price}" data-ordered-qty="${item.qty}" style="border-bottom: 1px solid #f1f5f9;">
                    <td class="ps-2 py-2.5">
                        <div class="fw-bold text-dark text-truncate" style="max-width: 250px;" title="${item.product_name}">${item.product_name}</div>
                        <small class="text-muted" style="font-size: 11px;"><i class="fas fa-layer-group opacity-75"></i> মজুদ: ${item.product_stock} টি</small>
                    </td>
                    <td class="text-center py-2.5 fw-semibold text-secondary">৳${item.sale_price}</td>
                    <td class="text-center py-2.5"><span class="badge bg-secondary-subtle text-secondary border px-2 py-1">${item.qty} টি</span></td>
                    <td class="text-center py-2.5">
                        <div class="input-group input-group-sm mx-auto d-flex align-items-center justify-content-center" style="max-width: 110px;">
                            <button type="button" class="btn btn-outline-secondary btn-sm ps-qty-btn ps-qty-minus" style="width: 28px; height: 28px; padding: 0;"><i class="fas fa-minus" style="font-size: 9px;"></i></button>
                            <input type="number" min="0" max="${item.qty}" step="1" 
                                   name="items[${item.id}][delivered_qty]" 
                                   class="form-control form-control-sm text-center fw-bold ps-item-del-input px-1" 
                                   value="${delQty}" style="height: 28px; max-width: 44px;">
                            <button type="button" class="btn btn-outline-secondary btn-sm ps-qty-btn ps-qty-plus" style="width: 28px; height: 28px; padding: 0;"><i class="fas fa-plus" style="font-size: 9px;"></i></button>
                        </div>
                    </td>
                    <td class="text-center py-2.5">
                        <span class="badge ${retQty > 0 ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-success-subtle text-success border border-success-subtle'} fw-semibold ps-item-ret-badge" style="font-size: 11px;">
                            ${retQty > 0 ? retQty + ' টি ফেরত' : 'সম্পূর্ণ ডেলিভারি'}
                        </span>
                        <input type="hidden" name="items[${item.id}][returned_qty]" class="ps-item-ret-input" value="${retQty}">
                    </td>
                    <td class="text-end pe-2 py-2.5 fw-bold text-success ps-item-line-total">৳${lineTotal.toFixed(2)}</td>
                </tr>`;
            });
        } else {
            tbody = '<tr><td colspan="6" class="text-center py-3 text-muted">কোনো পণ্য পাওয়া যায়নি</td></tr>';
        }
        $('#ps_items_tbody').html(tbody);

        // Pre-select option based on current status or partial_type if already settled
        if (order.current_status === 9 || order.partial_type === 'full_received') {
            $('#ps_opt_9').prop('checked', true).trigger('change');
            if (order.partial_collected_amount) {
                $('#ps_collected_amount_9').val(order.partial_collected_amount);
                calcShortage9();
            }
        } else if (order.current_status === 11 || order.partial_type === 'delivery_charge_only') {
            $('#ps_opt_11').prop('checked', true).trigger('change');
            if (order.partial_collected_amount) {
                $('#ps_delivery_charge_paid_11').val(order.partial_collected_amount);
            }
        } else {
            $('#ps_opt_10').prop('checked', true).trigger('change');
            calcTotal10();
        }
    }

    // Option Cards Change Event
    $('input[name="target_status"]').on('change', function () {
        var val = $(this).val();
        $('.ps-clean-card').removeClass('active');
        $(this).closest('.ps-clean-card').addClass('active');

        $('.ps-dynamic-section').hide();
        $('#ps_section_' + val).stop(true, true).slideDown(200);

        if (val === '9') {
            calcShortage9();
        } else if (val === '10') {
            calcTotal10();
        }
    });

    // Option 9 calculation
    $('#ps_collected_amount_9').on('input', function () {
        calcShortage9();
    });

    function calcShortage9() {
        if (!currentPartialOrder) return;
        var orig = Number(currentPartialOrder.amount) || 0;
        var col = Number($('#ps_collected_amount_9').val()) || 0;
        var diff = orig - col;
        if (diff > 0) {
            $('#ps_shortage_9').text('৳' + diff.toFixed(2)).removeClass('text-success').addClass('text-danger');
        } else if (diff < 0) {
            $('#ps_shortage_9').text('+৳' + Math.abs(diff).toFixed(2) + ' (অতিরিক্ত)').removeClass('text-danger').addClass('text-success');
        } else {
            $('#ps_shortage_9').text('৳0 (সমান)').removeClass('text-danger').addClass('text-success');
        }
    }

    // Option 10 stepper buttons
    $(document).on('click', '.ps-qty-minus', function (e) {
        e.preventDefault();
        var $input = $(this).siblings('.ps-item-del-input');
        var val = Number($input.val()) || 0;
        if (val > 0) {
            $input.val(val - 1).trigger('input');
        }
    });

    $(document).on('click', '.ps-qty-plus', function (e) {
        e.preventDefault();
        var $input = $(this).siblings('.ps-item-del-input');
        var $row = $(this).closest('tr');
        var maxQty = Number($row.data('ordered-qty')) || 1;
        var val = Number($input.val()) || 0;
        if (val < maxQty) {
            $input.val(val + 1).trigger('input');
        }
    });

    // Option 10 calculations
    $(document).on('input change', '.ps-item-del-input', function () {
        var $row = $(this).closest('tr');
        var maxQty = Number($row.data('ordered-qty')) || 1;
        var price = Number($row.data('price')) || 0;
        var val = Number($(this).val()) || 0;

        if (val < 0) val = 0;
        if (val > maxQty) val = maxQty;
        $(this).val(val);

        var retQty = maxQty - val;
        var $badge = $row.find('.ps-item-ret-badge');
        if (retQty > 0) {
            $badge.text(retQty + ' টি ফেরত').removeClass('bg-success-subtle text-success border-success-subtle').addClass('bg-danger-subtle text-danger border-danger-subtle');
        } else {
            $badge.text('সম্পূর্ণ ডেলিভারি').removeClass('bg-danger-subtle text-danger border-danger-subtle').addClass('bg-success-subtle text-success border-success-subtle');
        }
        $row.find('.ps-item-ret-input').val(retQty);
        $row.find('.ps-item-line-total').text('৳' + (val * price).toFixed(2));

        calcTotal10();
    });

    $('#ps_include_shipping').on('change', function () {
        calcTotal10();
    });

    $(document).on('click', '#ps_sync_calc_10', function (e) {
        e.preventDefault();
        calcTotal10(true);
        toastr.info('হিসাব অনুযায়ী টাকার পরিমাণ রিক্যালকুলেট করা হয়েছে');
    });

    function calcTotal10(forceSetInput) {
        if (!currentPartialOrder) return;
        var itemsSum = 0;

        $('#ps_items_tbody tr').each(function () {
            var price = Number($(this).data('price')) || 0;
            var delQty = Number($(this).find('.ps-item-del-input').val()) || 0;
            itemsSum += (price * delQty);
        });

        var ship = $('#ps_include_shipping').is(':checked') ? (Number(currentPartialOrder.shipping_charge) || 0) : 0;
        var discount = Number(currentPartialOrder.discount) || 0;
        var finalTotal = Math.max(0, itemsSum + ship - discount);

        if (forceSetInput || $('#ps_collected_amount_10').val() === '' || $('#ps_collected_amount_10').val() === '0' || forceSetInput === undefined) {
            $('#ps_collected_amount_10').val(finalTotal);
        }
    }

    // Form Submit
    $('#partialSettlementForm').on('submit', function (e) {
        e.preventDefault();
        var $btn = $('#ps_submit_btn');
        var origHtml = $btn.html();
        var targetStatus = $('input[name="target_status"]:checked').val();

        var formData = {
            _token: "{{ csrf_token() }}",
            order_id: $('#ps_order_id').val(),
            target_status: targetStatus,
            note: $('#ps_note').val(),
        };

        if (targetStatus === '9') {
            formData.collected_amount = $('#ps_collected_amount_9').val();
        } else if (targetStatus === '10') {
            formData.include_shipping = $('#ps_include_shipping').is(':checked') ? 1 : 0;
            formData.collected_amount = $('#ps_collected_amount_10').val();
            formData.items = {};
            $('#ps_items_tbody tr').each(function () {
                var id = $(this).data('item-id');
                if (id) {
                    formData.items[id] = {
                        delivered_qty: $(this).find('.ps-item-del-input').val(),
                        returned_qty: $(this).find('.ps-item-ret-input').val()
                    };
                }
            });
        } else if (targetStatus === '11') {
            formData.delivery_charge_paid = $('#ps_delivery_charge_paid_11').val();
        }

        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> প্রসেসিং হচ্ছে...');

        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            data: formData,
            dataType: "json",
            success: function (res) {
                if (res.status === 'success') {
                    toastr.success(res.message || 'সেটেলমেন্ট সফল হয়েছে!');
                    hidePsModal();
                    setTimeout(function () {
                        window.location.reload();
                    }, 600);
                } else {
                    toastr.error(res.message || 'সেটেলমেন্ট ব্যর্থ হয়েছে');
                    $btn.prop('disabled', false).html(origHtml);
                }
            },
            error: function (xhr) {
                var msg = 'সার্ভার ত্রুটি, অনুগ্রহ করে আবার চেষ্টা করুন';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                }
                toastr.error(msg);
                $btn.prop('disabled', false).html(origHtml);
            }
        });
    });

    // Clean up backdrop when partial settlement modal is closed
    $(document).on('hidden.bs.modal', '#partialSettlementModal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
    });

    // Intercept status change triggers if targeting/coming from Partial statuses
    $(document).on('click', '.open-partial-settle-btn', function (e) {
        e.preventDefault();
        var orderId = $(this).data('order-id');
        window.openPartialSettlementModal(orderId);
    });
});

})(window.jQuery);
</script>
