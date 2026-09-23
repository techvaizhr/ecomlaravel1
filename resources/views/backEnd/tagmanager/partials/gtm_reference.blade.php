<div class="card card-modern border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary bg-gradient p-2 text-white rounded-3">
                <i class="fas fa-book-open"></i>
            </span>
            <div>
                <h5 class="mb-0 fw-bold text-dark">GTM DataLayer & Custom Event Triggers Guide (চিটশিট ও গাইড)</h5>
                <small class="text-muted">সহজে Google Tag Manager-এ Triggers এবং DataLayer Variables কনফিগার করার রেডিমেড গাইড।</small>
            </div>
        </div>
        <ul class="nav nav-pills card-header-pills" id="gtmGuideTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active py-1 px-3 small fw-semibold" id="triggers-tab" data-bs-toggle="tab" data-bs-target="#gtm-triggers" type="button" role="tab">
                    <i class="fas fa-bolt me-1 text-warning"></i> Event Triggers
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-1 px-3 small fw-semibold" id="variables-tab" data-bs-toggle="tab" data-bs-target="#gtm-variables" type="button" role="tab">
                    <i class="fas fa-database me-1 text-info"></i> DataLayer Variables
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link py-1 px-3 small fw-semibold" id="steps-tab" data-bs-toggle="tab" data-bs-target="#gtm-steps" type="button" role="tab">
                    <i class="fas fa-tasks me-1 text-success"></i> Setup Steps (কিভাবে করবেন)
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="tab-content" id="gtmGuideTabContent">
            {{-- TAB 1: EVENT TRIGGERS --}}
            <div class="tab-pane fade show active" id="gtm-triggers" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="small text-muted">
                        <i class="fas fa-info-circle text-primary me-1"></i> GTM-এ Triggers তৈরি করার সময় Trigger Type দিন <strong>"Custom Event"</strong> এবং নিচের Event Name কপি করে পেস্ট করুন।
                    </div>
                    <span class="badge bg-soft-primary text-primary border border-primary border-opacity-25 px-2 py-1">
                        GA4 E-commerce Standard Compliant
                    </span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 220px;">Event Name (Custom Event)</th>
                                <th style="width: 150px;">Trigger Type</th>
                                <th>কখন ফায়ার হয় (Triggering Point)</th>
                                <th>প্রধান DataLayer প্যারামিটার</th>
                                <th class="text-end" style="width: 100px;">কপি</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <code class="fw-bold text-success fs-6">purchase</code>
                                </td>
                                <td><span class="badge bg-soft-info text-info">Custom Event</span></td>
                                <td>অর্ডার সফলভাবে সম্পন্ন হলে (Order Success / Thank You পেজে)</td>
                                <td><code>transaction_id</code>, <code>value</code>, <code>currency</code>, <code>items</code></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('purchase', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code class="fw-bold text-primary fs-6">begin_checkout</code>
                                </td>
                                <td><span class="badge bg-soft-info text-info">Custom Event</span></td>
                                <td>কাস্টমার চেকআউট পেজে আসলে (Checkout Page Load)</td>
                                <td><code>value</code>, <code>currency</code>, <code>items</code></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('begin_checkout', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code class="fw-bold text-primary fs-6">add_payment_info</code>
                                </td>
                                <td><span class="badge bg-soft-info text-info">Custom Event</span></td>
                                <td>চেকআউট ফর্মে পেমেন্ট মেথড সিলেক্ট করে সাবমিট দিলে</td>
                                <td><code>payment_type</code>, <code>value</code>, <code>items</code></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('add_payment_info', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code class="fw-bold text-primary fs-6">add_to_cart</code>
                                </td>
                                <td><span class="badge bg-soft-info text-info">Custom Event</span></td>
                                <td>প্রোডাক্ট পেজ, ক্যাটালগ কার্ড বা কুইকভিউ থেকে Add to Cart এ ক্লিক করলে</td>
                                <td><code>currency</code>, <code>value</code>, <code>items</code> (id, name, price)</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('add_to_cart', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code class="fw-bold text-primary fs-6">view_cart</code>
                                </td>
                                <td><span class="badge bg-soft-info text-info">Custom Event</span></td>
                                <td>কার্ট ড্রয়ার ওপেন করলে বা কার্ট পেজ ভিজিট করলে</td>
                                <td><code>currency</code>, <code>value</code>, <code>items</code></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('view_cart', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code class="fw-bold text-primary fs-6">remove_from_cart</code>
                                </td>
                                <td><span class="badge bg-soft-info text-info">Custom Event</span></td>
                                <td>কার্ট থেকে কোনো প্রোডাক্ট রিমুভ করলে</td>
                                <td><code>currency</code>, <code>value</code>, <code>items</code></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('remove_from_cart', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code class="fw-bold text-primary fs-6">view_item</code>
                                </td>
                                <td><span class="badge bg-soft-info text-info">Custom Event</span></td>
                                <td>প্রোডাক্ট ডিটেইলস পেজ ওপেন করলে (Single Product View)</td>
                                <td><code>currency</code>, <code>value</code>, <code>items</code></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('view_item', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code class="fw-bold text-primary fs-6">view_item_list</code>
                                </td>
                                <td><span class="badge bg-soft-info text-info">Custom Event</span></td>
                                <td>শপ, ক্যাটাগরি, সাব-ক্যাটাগরি বা ব্র্যান্ড পেজ ভিজিট করলে</td>
                                <td><code>item_list_name</code>, <code>items</code></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('view_item_list', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <code class="fw-bold text-primary fs-6">view_search_results</code>
                                </td>
                                <td><span class="badge bg-soft-info text-info">Custom Event</span></td>
                                <td>সার্চ পেজে ফলাফল দেখানো হলে</td>
                                <td><code>search_term</code>, <code>items</code></td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('view_search_results', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: DATALAYER VARIABLES --}}
            <div class="tab-pane fade" id="gtm-variables" role="tabpanel">
                <div class="p-3 bg-light border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="small text-muted">
                        <i class="fas fa-info-circle text-primary me-1"></i> GTM-এ Variables > User-Defined Variables > New > <strong>"Data Layer Variable"</strong> সিলেক্ট করে নিচের নামগুলো ব্যবহার করুন।
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 270px;">Data Layer Variable Name</th>
                                <th style="width: 160px;">GTM Variable Type</th>
                                <th>বিবরণ ও উদাহরণ</th>
                                <th class="text-end" style="width: 100px;">কপি</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><code class="fw-bold text-dark fs-6">ecommerce.transaction_id</code></td>
                                <td><span class="badge bg-soft-secondary text-secondary">Data Layer Variable</span></td>
                                <td>অর্ডার ইনভয়েস / আইডি (যেমন: <code>100254</code>)</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('ecommerce.transaction_id', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><code class="fw-bold text-dark fs-6">ecommerce.value</code></td>
                                <td><span class="badge bg-soft-secondary text-secondary">Data Layer Variable</span></td>
                                <td>মোট টাকার পরিমাণ (যেমন: <code>1250</code>)</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('ecommerce.value', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><code class="fw-bold text-dark fs-6">ecommerce.currency</code></td>
                                <td><span class="badge bg-soft-secondary text-secondary">Data Layer Variable</span></td>
                                <td>কারেন্সি কোড (হিসাবে <code>BDT</code> পাঠায়)</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('ecommerce.currency', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><code class="fw-bold text-dark fs-6">ecommerce.shipping</code></td>
                                <td><span class="badge bg-soft-secondary text-secondary">Data Layer Variable</span></td>
                                <td>ডেলিভারি চার্জ (যেমন: <code>60</code> বা <code>120</code>)</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('ecommerce.shipping', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><code class="fw-bold text-dark fs-6">ecommerce.items</code></td>
                                <td><span class="badge bg-soft-secondary text-secondary">Data Layer Variable</span></td>
                                <td>প্রোডাক্টের সম্পূর্ণ অ্যারে (item_id, item_name, price, quantity, category)</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('ecommerce.items', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><code class="fw-bold text-dark fs-6">user_data.phone</code></td>
                                <td><span class="badge bg-soft-secondary text-secondary">Data Layer Variable</span></td>
                                <td>কাস্টমারের ফোন নাম্বার (E.164 স্ট্যান্ডার্ড: <code>8801XXXXXXXXX</code>)</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('user_data.phone', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><code class="fw-bold text-dark fs-6">user_data.name</code></td>
                                <td><span class="badge bg-soft-secondary text-secondary">Data Layer Variable</span></td>
                                <td>কাস্টমারের নাম</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('user_data.name', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><code class="fw-bold text-dark fs-6">user_data.city</code></td>
                                <td><span class="badge bg-soft-secondary text-secondary">Data Layer Variable</span></td>
                                <td>কাস্টমারের জেলা / শহর</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('user_data.city', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td><code class="fw-bold text-dark fs-6">user_data.address</code></td>
                                <td><span class="badge bg-soft-secondary text-secondary">Data Layer Variable</span></td>
                                <td>কাস্টমারের সম্পূর্ণ ডেলিভারি ঠিকানা</td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary copy-btn" onclick="copyGtmHelper('user_data.address', this)" title="কপি করুন">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 3: STEP-BY-STEP SETUP GUIDE --}}
            <div class="tab-pane fade" id="gtm-steps" role="tabpanel">
                <div class="p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold text-primary mb-3">
                                    <span class="badge bg-primary me-2">১</span> কন্টেইনার আইডি সেটআপ (GTM Container ID)
                                </h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li><a href="https://tagmanager.google.com" target="_blank" class="fw-bold text-decoration-underline">tagmanager.google.com</a> এ প্রবেশ করে আপনার অ্যাকাউন্টে একটি Web কন্টেইনার তৈরি করুন।</li>
                                    <li>উপরে ডানপাশে দেখতে পাবেন <code>GTM-XXXXXXX</code> ফরম্যাটের একটি আইডি।</li>
                                    <li>আইডিটি কপি করে এই অ্যাডমিন প্যানেলের <strong>"Add GTM Container"</strong> এ পেস্ট করে <strong>Active</strong> রেখে সেভ করুন।</li>
                                    <li>ওয়েবসাইটে অটোমেটিক হেড সেকশনে স্ক্রিপ্ট লোড হয়ে যাবে, কোনো থিম কোড এডিট করার প্রয়োজন নেই।</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold text-success mb-3">
                                    <span class="badge bg-success me-2">২</span> ট্রিগার তৈরি (Creating Triggers in GTM)
                                </h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li>GTM ড্যাশবোর্ডে বাম পাশের মেনু থেকে <strong>Triggers</strong> এ যান এবং <strong>New</strong> বাটনে ক্লিক করুন।</li>
                                    <li>Trigger Configuration-এ ক্লিক করে Trigger Type দিন <strong>"Custom Event"</strong>।</li>
                                    <li>Event name বক্সে প্রথম ট্যাব থেকে নাম কপি করে দিন (যেমন: <code>purchase</code>, <code>add_to_cart</code>, <code>begin_checkout</code>)।</li>
                                    <li>ট্রিগারের নাম দিয়ে সেভ করুন।</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold text-info mb-3">
                                    <span class="badge bg-info me-2">৩</span> GA4 ই-কমার্স ট্যাগ কনফিগারেশন
                                </h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li>GTM-এ <strong>Tags</strong> > <strong>New</strong> এ ক্লিক করুন।</li>
                                    <li>Tag Configuration দিন <strong>Google Analytics: GA4 Event</strong>।</li>
                                    <li>Measurement ID দিন (আপনার GA4 এর <code>G-XXXXXXXXXX</code>)।</li>
                                    <li>Event Name এ দিন <code>@{{Event}}</code> অথবা নির্দিষ্ট ইভেন্ট (যেমন: <code>purchase</code>)।</li>
                                    <li>More Settings > <strong>Ecommerce</strong> এ গিয়ে <strong>"Send Ecommerce data"</strong> চেকমার্ক দিন এবং Data source দিন <strong>"Data Layer"</strong>।</li>
                                    <li>Triggering এ তৈরি করা Custom Event ট্রিগার সিলেক্ট করে সেভ করুন।</li>
                                </ol>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="border rounded-3 p-3 h-100 bg-light">
                                <h6 class="fw-bold text-dark mb-3">
                                    <span class="badge bg-dark me-2">৪</span> টেস্ট ও পাবলিশ (Preview & Publish)
                                </h6>
                                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.8;">
                                    <li>GTM-এর উপরের ডানপাশের <strong>Preview</strong> বাটনে ক্লিক করে আপনার ওয়েবসাইটের লিংক দিন।</li>
                                    <li>Tag Assistant উইন্ডোতে ওয়েবসাইটে বিভিন্ন প্রোডাক্ট ভিউ, অ্যাড টু কার্ট এবং একটি টেস্ট অর্ডার করুন।</li>
                                    <li>বাম পাশের ইভেন্ট লিস্টে <code>view_item</code>, <code>add_to_cart</code>, <code>purchase</code> আসছে কিনা ও Data Layer ট্যাবে ভ্যালু দেখাচ্ছে কিনা চেক করুন।</li>
                                    <li>সব ঠিক থাকলে GTM-এর নীল <strong>Submit</strong> বাটনে ক্লিক করে <strong>Publish</strong> করে দিন।</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function copyGtmHelper(text, btn) {
        navigator.clipboard.writeText(text).then(function() {
            if (typeof toastr !== 'undefined') {
                toastr.success('"' + text + '" কপি করা হয়েছে!');
            }
            var originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check text-success"></i>';
            setTimeout(function() {
                btn.innerHTML = originalHtml;
            }, 1500);
        }).catch(function() {
            prompt('নিচের কোডটি কপি করুন:', text);
        });
    }
</script>
