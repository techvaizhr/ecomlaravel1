<!-- Global Product Image Zoom Modal -->
<div class="modal fade" id="globalImageZoomModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 550px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden; background: #ffffff;">
            <div class="modal-header py-2 px-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                <h6 class="modal-title mb-0 fw-bold text-dark text-truncate" id="zoomModalTitle" style="font-size: 14px; max-width: 440px;">পণ্য ছবি প্রিভিউ</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-3 text-center d-flex align-items-center justify-content-center" style="min-height: 250px; background: #fafafa;">
                <img id="zoomModalImage" src="" alt="প্রিভিউ" class="img-fluid rounded shadow-sm" style="max-height: 70vh; width: auto; max-width: 100%; object-fit: contain;">
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        function initImageZoom() {
            if (typeof jQuery === 'undefined') return;
            jQuery(document).off('click.zoomImg', '.zoomable-product-img').on('click.zoomImg', '.zoomable-product-img', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var imgSrc = jQuery(this).data('full-img') || jQuery(this).attr('src');
                var title = jQuery(this).data('title') || jQuery(this).attr('alt') || 'পণ্য প্রিভিউ';
                if (!imgSrc) return;

                jQuery('#zoomModalImage').attr('src', imgSrc);
                jQuery('#zoomModalTitle').text(title);

                var modalEl = document.getElementById('globalImageZoomModal');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    bootstrap.Modal.getOrCreateInstance(modalEl).show();
                } else if (jQuery.fn.modal) {
                    jQuery('#globalImageZoomModal').modal('show');
                }
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initImageZoom);
        } else {
            initImageZoom();
        }
    })();
</script>
