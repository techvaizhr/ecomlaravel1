<style>
    :root {
        --pf-primary: #4f46e5;
        --pf-primary-hover: #4338ca;
        --pf-primary-light: #eef2ff;
        --pf-primary-border: #c7d2fe;
        --pf-success: #10b981;
        --pf-success-light: #ecfdf5;
        --pf-danger: #ef4444;
        --pf-danger-light: #fef2f2;
        --pf-warning: #f59e0b;
        --pf-warning-light: #fffbeb;
        --pf-dark: #0f172a;
        --pf-gray-600: #475569;
        --pf-gray-500: #64748b;
        --pf-gray-400: #94a3b8;
        --pf-gray-200: #e2e8f0;
        --pf-gray-100: #f8fafc;
        --pf-card-radius: 16px;
        --pf-shadow-sm: 0 2px 8px rgba(15, 23, 42, 0.04);
        --pf-shadow-md: 0 8px 24px rgba(15, 23, 42, 0.06);
        --pf-shadow-lg: 0 16px 36px rgba(15, 23, 42, 0.08);
    }

    body {
        background: #f1f5f9;
    }

    .product-form-page {
        padding: 6px 0 40px;
        padding-left: max(8px, env(safe-area-inset-left));
        padding-right: max(8px, env(safe-area-inset-right));
    }

    /* Page Header */
    .product-form-page .pf-page-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 22px;
        padding: 20px 24px;
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 50%, #eff6ff 100%);
        border-radius: var(--pf-card-radius);
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: var(--pf-shadow-md);
        position: relative;
        overflow: hidden;
    }
    .product-form-page .pf-page-header::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(180deg, var(--pf-primary), #3b82f6);
    }
    .product-form-page .pf-page-header h4 {
        margin: 0;
        font-weight: 800;
        color: var(--pf-dark);
        font-size: 1.45rem;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.02em;
    }
    .product-form-page .pf-page-header h4 .header-icon-badge {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: var(--pf-primary-light);
        color: var(--pf-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .product-form-page .pf-page-header .pf-sub {
        font-size: 13.5px;
        color: var(--pf-gray-500);
        margin-top: 6px;
        line-height: 1.4;
    }
    .product-form-page .pf-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }
    .product-form-page .pf-btn-manage {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        border: 1.5px solid var(--pf-gray-200);
        color: var(--pf-dark) !important;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 999px;
        font-size: 13.5px;
        box-shadow: var(--pf-shadow-sm);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .product-form-page .pf-btn-manage:hover {
        background: var(--pf-primary-light);
        border-color: var(--pf-primary-border);
        color: var(--pf-primary) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(79, 70, 229, 0.15);
    }

    /* Cards */
    .product-form-page .card {
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: var(--pf-card-radius);
        box-shadow: var(--pf-shadow-md);
        background: #ffffff;
        margin-bottom: 22px;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .product-form-page .card:hover {
        border-color: rgba(199, 210, 254, 0.8);
        box-shadow: var(--pf-shadow-lg);
    }
    .product-form-page .card-body {
        padding: 24px 26px;
    }

    /* Section Titles */
    .product-form-page .section-title {
        background: linear-gradient(90deg, #f8fafc 0%, #ffffff 100%);
        padding: 12px 18px;
        border-radius: 12px;
        font-weight: 700;
        color: var(--pf-dark);
        border-left: 4px solid var(--pf-primary);
        border-right: 1px solid var(--pf-gray-200);
        border-top: 1px solid var(--pf-gray-200);
        border-bottom: 1px solid var(--pf-gray-200);
        margin-bottom: 22px;
        font-size: 14.5px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .product-form-page .section-title-left {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .product-form-page .section-title-left i {
        color: var(--pf-primary);
        font-size: 17px;
    }

    /* Form Controls & Labels */
    .product-form-page .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #334155;
        margin-bottom: 7px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .product-form-page .form-label .req {
        color: var(--pf-danger);
        font-weight: 700;
    }
    .product-form-page .form-control,
    .product-form-page .form-select {
        border-radius: 10px;
        border: 1.5px solid var(--pf-gray-200);
        font-size: 13.5px;
        padding: 10px 14px;
        background: #f8fafc;
        color: var(--pf-dark);
        transition: all 0.2s ease;
    }
    .product-form-page .form-control:focus,
    .product-form-page .form-select:focus {
        border-color: var(--pf-primary);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        background: #ffffff;
    }
    .product-form-page .form-control.border-primary {
        border-color: var(--pf-primary-border) !important;
        background: #faf5ff;
    }
    .product-form-page .form-control.is-invalid {
        border-color: var(--pf-danger) !important;
        background: var(--pf-danger-light);
    }
    .product-form-page .invalid-feedback {
        font-size: 12px;
        font-weight: 500;
    }

    /* Select2 Enhancements */
    .product-form-page .select2-container--default .select2-selection--single,
    .product-form-page .select2-container--default .select2-selection--multiple {
        border-radius: 10px !important;
        border: 1.5px solid var(--pf-gray-200) !important;
        min-height: 44px;
        background: #f8fafc !important;
        padding: 4px 6px;
    }
    .product-form-page .select2-container--default.select2-container--focus .select2-selection--single,
    .product-form-page .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: var(--pf-primary) !important;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.12);
        background: #ffffff !important;
    }
    .product-form-page .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: var(--pf-primary-light);
        border: 1px solid var(--pf-primary-border);
        color: var(--pf-primary);
        border-radius: 6px;
        font-weight: 600;
        font-size: 12px;
        padding: 2px 8px;
    }

    /* Summernote Editor */
    .product-form-page .note-editor.note-frame {
        border-radius: 12px;
        border: 1.5px solid var(--pf-gray-200);
        overflow: hidden;
        box-shadow: var(--pf-shadow-sm);
    }
    .product-form-page .note-toolbar {
        background: #f8fafc;
        border-bottom: 1.5px solid var(--pf-gray-200);
    }

    /* Toggle Switches (Modern Apple Style) */
    .product-form-page .switch {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
        flex-shrink: 0;
    }
    .product-form-page .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .product-form-page .slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background: #cbd5e1;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 24px;
    }
    .product-form-page .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background: #ffffff;
        transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.18);
    }
    .product-form-page input:checked + .slider {
        background: linear-gradient(135deg, var(--pf-primary), #3b82f6);
    }
    .product-form-page input:checked + .slider.slider-success {
        background: linear-gradient(135deg, var(--pf-success), #059669);
    }
    .product-form-page input:checked + .slider.slider-warning {
        background: linear-gradient(135deg, var(--pf-warning), #d97706);
    }
    .product-form-page input:checked + .slider.slider-danger {
        background: linear-gradient(135deg, var(--pf-danger), #dc2626);
    }
    .product-form-page input:checked + .slider:before {
        transform: translateX(22px);
    }

    /* Variant & Wholesale Cards */
    .product-form-page .variant-card {
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        border: 1.5px dashed var(--pf-primary-border);
        padding: 18px 20px;
        border-radius: 14px;
        margin-bottom: 16px;
        position: relative;
        transition: all 0.2s ease;
    }
    .product-form-page .variant-card:hover {
        border-color: var(--pf-primary);
        box-shadow: 0 6px 18px rgba(79, 70, 229, 0.08);
    }
    .product-form-page #wholesale_area .variant-card {
        border-style: solid;
        border-color: #a7f3d0;
        background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%);
    }

    /* Custom Video Upload & Size Meter */
    .product-form-page .pf-video-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 12px;
        border: 1px solid var(--pf-gray-200);
    }
    .product-form-page .pf-video-source-pill {
        display: flex;
        background: var(--pf-gray-100);
        padding: 4px;
        border-radius: 10px;
        gap: 4px;
        margin-bottom: 12px;
    }
    .product-form-page .pf-video-source-pill label {
        flex: 1;
        text-align: center;
        padding: 6px 10px;
        margin: 0;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        color: var(--pf-gray-600);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .product-form-page .pf-video-source-pill input[type="radio"] {
        display: none;
    }
    .product-form-page .pf-video-source-pill input[type="radio"]:checked + label {
        background: #ffffff;
        color: var(--pf-primary);
        box-shadow: var(--pf-shadow-sm);
    }
    .product-form-page .pf-video-size-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        background: #fef2f2;
        color: var(--pf-danger);
        border: 1px solid #fecaca;
    }
    .product-form-page .pf-video-size-badge.valid {
        background: #ecfdf5;
        color: var(--pf-success);
        border-color: #a7f3d0;
    }
    .product-form-page .video-error-alert {
        display: none;
        padding: 8px 12px;
        border-radius: 8px;
        background: #fef2f2;
        border: 1px solid #f87171;
        color: #991b1b;
        font-size: 12px;
        font-weight: 600;
        margin-top: 8px;
        animation: shake 0.3s ease;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-4px); }
        75% { transform: translateX(4px); }
    }

    /* SEO SERP Preview Box */
    .product-form-page .serp-preview-card {
        background: #ffffff;
        border: 1px solid var(--pf-gray-200);
        border-radius: 12px;
        padding: 16px 18px;
        margin-bottom: 16px;
        box-shadow: var(--pf-shadow-sm);
    }
    .product-form-page .serp-preview-card .serp-url {
        font-size: 12px;
        color: #202124;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 2px;
    }
    .product-form-page .serp-preview-card .serp-title {
        font-size: 17px;
        font-weight: 500;
        color: #1a0dab;
        line-height: 1.3;
        margin-bottom: 4px;
        cursor: pointer;
    }
    .product-form-page .serp-preview-card .serp-title:hover {
        text-decoration: underline;
    }
    .product-form-page .serp-preview-card .serp-desc {
        font-size: 13px;
        color: #4d5156;
        line-height: 1.4;
    }

    /* Profit & Discount Pill */
    .product-form-page .price-metric-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 11.5px;
        font-weight: 700;
        margin-top: 6px;
    }
    .product-form-page .price-metric-badge.profit-pos {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }
    .product-form-page .price-metric-badge.discount-badge {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }

    /* Sidebar Blocks & Polish */
    .product-form-page .pf-sidebar-panel {
        border-radius: var(--pf-card-radius);
        overflow: hidden;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: var(--pf-shadow-md);
        background: #ffffff;
    }
    .product-form-page .pf-sidebar-panel > .card-body {
        padding: 0;
    }
    .product-form-page .pf-side-block {
        padding: 16px 18px;
        border-bottom: 1px solid #f1f5f9;
    }
    .product-form-page .pf-side-block:last-of-type {
        border-bottom: none;
    }
    .product-form-page .pf-side-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--pf-dark);
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .product-form-page .pf-side-head-left {
        display: flex;
        align-items: center;
        gap: 7px;
    }
    .product-form-page .pf-side-head i {
        color: var(--pf-primary);
        font-size: 15px;
    }
    .product-form-page .pf-side-foot {
        padding: 18px 20px;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
        border-top: 1px solid #e2e8f0;
    }

    /* Gallery uploader */
    .product-form-page .gallery-item-row {
        background: #f8fafc;
        border: 1.5px dashed var(--pf-gray-200);
        border-radius: 10px;
        padding: 10px;
        margin-bottom: 8px;
        transition: all 0.2s ease;
    }
    .product-form-page .gallery-item-row:hover {
        border-color: var(--pf-primary-border);
        background: #f1f5f9;
    }

    /* Submit Button Glow */
    .product-form-page button[type="submit"].btn-publish {
        background: linear-gradient(135deg, var(--pf-primary), #6366f1);
        border: none;
        color: #ffffff;
        font-weight: 700;
        font-size: 15px;
        padding: 14px 24px;
        border-radius: 999px;
        box-shadow: 0 10px 24px rgba(79, 70, 229, 0.35);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
    }
    .product-form-page button[type="submit"].btn-publish:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(79, 70, 229, 0.45);
        background: linear-gradient(135deg, var(--pf-primary-hover), var(--pf-primary));
    }

    /* AI Button */
    .pf-ai-desc-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #ffffff !important;
        background: linear-gradient(135deg, #8b5cf6, #ec4899);
        border: none;
        border-radius: 999px;
        box-shadow: 0 4px 12px rgba(236, 72, 153, 0.25);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .pf-ai-desc-btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(236, 72, 153, 0.35);
    }
    .pf-desc-label-row {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
</style>
