/**
 * JavaScript xử lý form thêm sản phẩm đa phiên bản
 * Sử dụng jQuery để xử lý DOM manipulation
 */

// Biến đếm index cho variants, storage options, promotions
let variantIndex = 0;

// Biến lưu danh sách care plans
let carePlansData = [];

// =============================================================================
// KHỞI TẠO KHI TRANG LOAD
// =============================================================================
$(document).ready(function() {
    console.log('Document ready - initializing form');
    
    // Load danh sách care plans
    loadCarePlans();
    
    // Thêm phiên bản đầu tiên mặc định (chỉ khi không phải edit mode)
    if (typeof isEditMode === 'undefined' || !isEditMode) {
        addVariant();
    }
    
    // Xử lý click nút thêm Care+ cho Series
    $('#addSeriesCarePlanBtn').on('click', function(e) {
        e.preventDefault();
        console.log('=== ADD SERIES CAREPLAN BTN CLICKED ===');
        addSeriesCarePlan();
    });
    
    // Xử lý click nút thêm tất cả Care+ cho Series
    $('#addAllCarePlansBtn').on('click', function(e) {
        e.preventDefault();
        console.log('=== ADD ALL CAREPLANS BTN CLICKED ===');
        addAllCarePlans();
    });
    
    // Xử lý xóa Care+ cho Series
    $(document).on('click', '.delete-series-careplan-btn', function() {
        $(this).closest('.careplan-row').fadeOut(200, function() {
            $(this).remove();
            updateSeriesCarePlanIndexes();
        });
    });
    
    // Xử lý khi chọn Care+ cho Series
    $(document).on('change', '.series-careplan-select', function() {
        const selectedOption = $(this).find(':selected');
        const price = selectedOption.data('price') || 0;
        const $priceDisplay = $(this).closest('.careplan-row').find('.series-careplan-price-display');
        $priceDisplay.val(price > 0 ? formatCurrency(price) : '');
    });
    
    // Xử lý click nút thêm Ưu đãi cho Series
    $('#addSeriesOfferBtn').on('click', function(e) {
        e.preventDefault();
        console.log('=== ADD SERIES OFFER BTN CLICKED ===');
        addSeriesOffer();
    });
    
    // Xử lý click nút thêm mẫu ưu đãi mặc định
    $('#addDefaultOffersBtn').on('click', function(e) {
        e.preventDefault();
        console.log('=== ADD DEFAULT OFFERS BTN CLICKED ===');
        addDefaultOffers();
    });
    
    // Xử lý xóa Ưu đãi cho Series
    $(document).on('click', '.delete-series-offer-btn', function() {
        $(this).closest('.offer-row').fadeOut(200, function() {
            $(this).remove();
            updateSeriesOfferIndexes();
        });
    });
    
    // Xử lý preview ảnh series
    $('#series_image').on('change', function() {
        previewSeriesImage(this);
    });
    
    // Xử lý preview ảnh variants (multiple)
    $(document).on('change', '.variant-images', function() {
        previewVariantImages(this);
    });
    
    // Xử lý preview mã màu khi nhập
    $(document).on('input', '.color-code-input', function() {
        const colorCode = $(this).val();
        const $preview = $(this).closest('.input-group').find('.color-preview-box');
        if (/^#[0-9A-Fa-f]{6}$/.test(colorCode)) {
            $preview.css('background', colorCode);
        }
    });
    
    // Xử lý submit form
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return false;
        }
        
        // Hiển thị loading
        Swal.fire({
            title: 'Đang xử lý...',
            html: 'Vui lòng đợi trong giây lát',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Lấy form data
        const formData = new FormData(this);
        
        // Gửi AJAX request
        $.ajax({
            type: 'POST',
            url: '../Api/ProcessProductSeries.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    html: `
                        <p>${response.message}</p>
                        <ul style="text-align: left; margin-top: 15px;">
                            <li>Tên series: <strong>${response.data.series_name}</strong></li>
                            <li>Số phiên bản: <strong>${response.data.variants_count}</strong></li>
                            <li>Số tùy chọn dung lượng: <strong>${response.data.storage_options_count}</strong></li>
                            <li>Số khuyến mãi: <strong>${response.data.promotions_count}</strong></li>
                        </ul>
                    `,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Reset form
                    $('#productForm')[0].reset();
                    // Xóa hết variants
                    $('#variantsContainer').empty();
                    // Reset index
                    variantIndex = 0;
                    // Thêm 1 variant mới
                    addVariant();
                });
            },
            error: function(xhr) {
                let errorMsg = 'Có lỗi xảy ra khi thêm sản phẩm';
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch(e) {
                    errorMsg = xhr.responseText || errorMsg;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: errorMsg,
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });
});

// =============================================================================
// THÊM PHIÊN BẢN MỚI
// =============================================================================
$('#addVariantBtn').on('click', function() {
    addVariant();
});

function addVariant() {
    // Clone template
    const template = $('#variantTemplate').prop('content');
    const $variantBlock = $(template).clone();
    
    // Update index
    $variantBlock.find('.variant-block').attr('data-variant-index', variantIndex);
    $variantBlock.find('.variant-number').text(variantIndex + 1);
    
    // Update name attributes với index mới
    $variantBlock.find('[name]').each(function() {
        let name = $(this).attr('name');
        name = name.replace('[0]', `[${variantIndex}]`);
        $(this).attr('name', name);
    });
    
    // Append vào container
    $('#variantsContainer').append($variantBlock);
    
    // KHÔNG thêm storage option mặc định - người dùng tự thêm
    
    // Tăng index
    variantIndex++;
    
    // Update số thứ tự hiển thị
    updateVariantNumbers();
}

// =============================================================================
// XÓA PHIÊN BẢN
// =============================================================================
$(document).on('click', '.delete-variant-btn', function() {
    const $variantBlock = $(this).closest('.variant-block');
    const variantCount = $('.variant-block').length;
    
    if (variantCount <= 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Không thể xóa',
            text: 'Phải có ít nhất 1 phiên bản!',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    Swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa phiên bản này?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            $variantBlock.fadeOut(300, function() {
                $(this).remove();
                updateVariantNumbers();
            });
        }
    });
});

// =============================================================================
// THÊM TÙY CHỌN DUNG LƯỢNG
// =============================================================================
$(document).on('click', '.add-storage-btn', function() {
    const $variantBlock = $(this).closest('.variant-block');
    const vIndex = $variantBlock.attr('data-variant-index');
    addStorageOption(vIndex);
});

// Import JSON cho storage options
$(document).on('click', '.import-storage-json-btn', function() {
    const $variantBlock = $(this).closest('.variant-block');
    const vIndex = $variantBlock.attr('data-variant-index');
    currentImportVariantIndex = vIndex;
    
    // Clear previous input
    $('#storageJsonInput').val('');
    $('#storageJsonPreview').html('');
    
    // Show modal
    new bootstrap.Modal(document.getElementById('importStorageJsonModal')).show();
});

// Function import storage options từ JSON
function importStorageOptionsFromJson(variantIndex, storageData) {
    const $variantBlock = $(`.variant-block[data-variant-index="${variantIndex}"]`);
    const $storageContainer = $variantBlock.find('.storage-options-container');
    
    // Xóa các storage options hiện tại
    $storageContainer.empty();
    
    // Thêm từng storage option
    storageData.forEach(function(item, sIndex) {
        // Clone template
        const template = $('#storageTemplate').prop('content');
        const $storageRow = $(template).clone();
        
        // Update name attributes
        $storageRow.find('[name]').each(function() {
            let name = $(this).attr('name');
            name = name.replace('[0][storage][0]', `[${variantIndex}][storage][${sIndex}]`);
            $(this).attr('name', name);
        });
        
        // Fill values
        $storageRow.find('input[name*="[storage]"]:not([name*="[colors]"])').first().val(item.storage || '');
        $storageRow.find('input[name*="[ram]"]').val(item.ram || '');
        $storageRow.find('input[name*="[price]"]').val(item.price || 0);
        $storageRow.find('input[name*="[installment_price]"]').val(item.installment_price || '');
        $storageRow.find('input[name*="[installment_months]"]').val(item.installment_months || 12);
        $storageRow.find('input[name*="[interest_rate]"]').val(item.interest_rate || 0);
        
        $storageContainer.append($storageRow);
        
        // Thêm colors nếu có
        if (item.colors && Array.isArray(item.colors) && item.colors.length > 0) {
            const $newStorageRow = $storageContainer.find('.storage-option-row').last();
            const $colorsContainer = $newStorageRow.find('.colors-container');
            
            item.colors.forEach(function(color, cIndex) {
                const colorTemplate = $('#colorTemplate').prop('content');
                const $colorRow = $(colorTemplate).clone();
                
                // Update name attributes
                $colorRow.find('[name]').each(function() {
                    let name = $(this).attr('name');
                    name = name.replace('[0][storage][0][colors][0]', `[${variantIndex}][storage][${sIndex}][colors][${cIndex}]`);
                    $(this).attr('name', name);
                });
                
                // Fill values
                const colorCode = color.code || '#000000';
                $colorRow.find('input[name*="[color_name]"]').val(color.name || '');
                $colorRow.find('input[name*="[color_code]"]').val(colorCode);
                $colorRow.find('input[name*="[color_stock]"]').val(color.stock || 0);
                
                // Update color preview
                $colorRow.find('.color-preview-box').css('background', colorCode);
                
                $colorsContainer.append($colorRow);
            });
        }
    });
}

function addStorageOption(variantIndex) {
    const $variantBlock = $(`.variant-block[data-variant-index="${variantIndex}"]`);
    const $storageContainer = $variantBlock.find('.storage-options-container');
    const currentStorageCount = $storageContainer.find('.storage-option-row').length;
    
    // Clone template
    const template = $('#storageTemplate').prop('content');
    const $storageRow = $(template).clone();
    
    // Update name attributes
    $storageRow.find('[name]').each(function() {
        let name = $(this).attr('name');
        name = name.replace('[0][storage][0]', `[${variantIndex}][storage][${currentStorageCount}]`);
        $(this).attr('name', name);
    });
    
    // Append
    $storageContainer.append($storageRow);
    
    // Animation
    $storageContainer.find('.storage-option-row').last().hide().slideDown(300);
}

// =============================================================================
// XÓA TÙY CHỌN DUNG LƯỢNG
// =============================================================================
$(document).on('click', '.delete-storage-btn', function() {
    const $storageRow = $(this).closest('.storage-option-row');
    const $variantBlock = $(this).closest('.variant-block');
    const storageCount = $variantBlock.find('.storage-option-row').length;
    
    if (storageCount <= 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Không thể xóa',
            text: 'Mỗi phiên bản phải có ít nhất 1 tùy chọn dung lượng!',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    Swal.fire({
        title: 'Xác nhận xóa',
        text: 'Bạn có chắc muốn xóa tùy chọn dung lượng này?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy'
    }).then((result) => {
        if (result.isConfirmed) {
            $storageRow.slideUp(300, function() {
                $(this).remove();
            });
        }
    });
});

// =============================================================================
// THÊM MÀU SẮC CHO STORAGE OPTION
// =============================================================================
$(document).on('click', '.add-color-btn', function() {
    const $storageRow = $(this).closest('.storage-option-row');
    const $variantBlock = $(this).closest('.variant-block');
    const vIndex = $variantBlock.attr('data-variant-index');
    
    // Tìm index của storage row
    const $storageContainer = $variantBlock.find('.storage-options-container');
    const sIndex = $storageContainer.find('.storage-option-row').index($storageRow);
    
    addColor(vIndex, sIndex, $storageRow);
});

function addColor(variantIndex, storageIndex, $storageRow) {
    const $colorsContainer = $storageRow.find('.colors-container');
    const currentColorCount = $colorsContainer.find('.color-option-row').length;
    
    // Clone template
    const template = $('#colorTemplate').prop('content');
    const $colorRow = $(template).clone();
    
    // Update name attributes
    $colorRow.find('[name]').each(function() {
        let name = $(this).attr('name');
        name = name.replace('[0][storage][0][colors][0]', `[${variantIndex}][storage][${storageIndex}][colors][${currentColorCount}]`);
        $(this).attr('name', name);
    });
    
    // Append
    $colorsContainer.append($colorRow);
    
    // Animation
    $colorsContainer.find('.color-option-row').last().hide().slideDown(300);
}

// =============================================================================
// XÓA MÀU SẮC
// =============================================================================
$(document).on('click', '.delete-color-btn', function() {
    const $colorRow = $(this).closest('.color-option-row');
    $colorRow.slideUp(300, function() {
        $(this).remove();
    });
});

// =============================================================================
// THÊM KHUYẾN MÃI
// =============================================================================
$(document).on('click', '.add-promo-btn', function() {
    const $variantBlock = $(this).closest('.variant-block');
    const vIndex = $variantBlock.attr('data-variant-index');
    addPromotion(vIndex);
});

// Thêm mẫu khuyến mãi mặc định
const defaultPromotions = [
    { type: "GiamGia", description: "Giảm 10% khi thanh toán qua VNPay", value: "" },
    { type: "TraGop", description: "Trả góp 0% lãi suất qua thẻ tín dụng", value: "" },
    { type: "TangPhuKien", description: "Tặng ốp lưng chính hãng trị giá 500.000đ", value: "500000" },
    { type: "BaoHanh", description: "Bảo hành mở rộng 12 tháng", value: "" }
];

$(document).on('click', '.add-default-promos-btn', function() {
    const $variantBlock = $(this).closest('.variant-block');
    const vIndex = $variantBlock.attr('data-variant-index');
    addDefaultPromotions(vIndex);
});

function addDefaultPromotions(variantIndex) {
    const $variantBlock = $(`.variant-block[data-variant-index="${variantIndex}"]`);
    const $promoContainer = $variantBlock.find('.promotions-container');
    
    // Xóa các promo hiện tại
    $promoContainer.empty();
    
    // Thêm các mẫu mặc định
    defaultPromotions.forEach(function(promo, index) {
        const template = $('#promoTemplate').prop('content');
        const $promoRow = $(template).clone();
        
        // Update name attributes
        $promoRow.find('[name]').each(function() {
            let name = $(this).attr('name');
            name = name.replace('[0][promos][0]', `[${variantIndex}][promos][${index}]`);
            $(this).attr('name', name);
        });
        
        // Fill values
        $promoRow.find('select[name*="type"]').val(promo.type);
        $promoRow.find('input[name*="description"]').val(promo.description);
        if (promo.value) {
            $promoRow.find('input[name*="value"]').val(promo.value);
        }
        
        $promoContainer.append($promoRow);
    });
    
    Swal.fire({
        icon: 'success',
        title: 'Đã thêm mẫu!',
        text: `Đã thêm ${defaultPromotions.length} khuyến mãi mẫu`,
        timer: 1500,
        showConfirmButton: false
    });
}

function addPromotion(variantIndex) {
    const $variantBlock = $(`.variant-block[data-variant-index="${variantIndex}"]`);
    const $promoContainer = $variantBlock.find('.promotions-container');
    const currentPromoCount = $promoContainer.find('.promo-row').length;
    
    // Clone template
    const template = $('#promoTemplate').prop('content');
    const $promoRow = $(template).clone();
    
    // Update name attributes
    $promoRow.find('[name]').each(function() {
        let name = $(this).attr('name');
        name = name.replace('[0][promos][0]', `[${variantIndex}][promos][${currentPromoCount}]`);
        $(this).attr('name', name);
    });
    
    // Append
    $promoContainer.append($promoRow);
    
    // Animation
    $promoContainer.find('.promo-row').last().hide().slideDown(300);
}

// =============================================================================
// XÓA KHUYẾN MÃI
// =============================================================================
$(document).on('click', '.delete-promo-btn', function() {
    const $promoRow = $(this).closest('.promo-row');
    $promoRow.slideUp(300, function() {
        $(this).remove();
    });
});

// =============================================================================
// LOAD DANH SÁCH 3AM CARE PLANS
// =============================================================================
function loadCarePlans() {
    $.ajax({
        url: '../Api/ProcessCarePlan.php?action=get_all',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            console.log('Care Plans Response:', response);
            if (response.success) {
                carePlansData = response.data.filter(plan => plan.is_active == 1);
                console.log('Loaded Care Plans:', carePlansData);
            }
        },
        error: function(xhr, status, error) {
            console.log('Lỗi load Care Plans:', error);
            console.log('Response:', xhr.responseText);
        }
    });
}

// =============================================================================
// THÊM 3AM CARE PLAN CHO SERIES
// =============================================================================
function addSeriesCarePlan() {
    console.log('Adding series care plan');
    console.log('Available care plans:', carePlansData);
    
    const $container = $('#seriesCarePlansContainer');
    const currentCount = $container.find('.careplan-row').length;
    
    // Clone template
    const template = document.getElementById('seriesCareplanTemplate');
    if (!template) {
        console.log('ERROR: seriesCareplanTemplate not found!');
        return;
    }
    
    const $careplanRow = $(template.content.cloneNode(true));
    
    // Update name attributes
    $careplanRow.find('[name]').each(function() {
        let name = $(this).attr('name');
        name = name.replace('[0]', `[${currentCount}]`);
        $(this).attr('name', name);
    });
    
    // Populate select options
    const $select = $careplanRow.find('.series-careplan-select');
    $select.empty();
    $select.append('<option value="">-- Chọn gói Care+ --</option>');
    
    if (carePlansData.length === 0) {
        console.log('WARNING: No care plans data available');
        $select.append('<option value="" disabled>Chưa có gói Care+ nào</option>');
    } else {
        carePlansData.forEach(function(plan) {
            const priceText = formatCurrency(plan.final_price);
            const typeLabel = plan.plan_type === 'protection' ? '🛡️' : '📅';
            $select.append(`<option value="${plan.id}" data-price="${plan.final_price}" data-name="${plan.name}">${typeLabel} ${plan.name} - ${priceText}</option>`);
        });
    }
    
    $container.append($careplanRow);
    console.log('Series care plan added successfully');
}

// =============================================================================
// THÊM TẤT CẢ CARE PLANS CHO SERIES
// =============================================================================
function addAllCarePlans() {
    console.log('Adding all care plans');
    
    if (carePlansData.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Không có dữ liệu',
            text: 'Chưa có gói Care+ nào trong hệ thống!'
        });
        return;
    }
    
    // Xóa các care plan hiện tại
    $('#seriesCarePlansContainer').empty();
    
    // Thêm tất cả care plans
    carePlansData.forEach(function(plan, index) {
        const $container = $('#seriesCarePlansContainer');
        
        // Clone template
        const template = document.getElementById('seriesCareplanTemplate');
        if (!template) {
            console.log('ERROR: seriesCareplanTemplate not found!');
            return;
        }
        
        const $careplanRow = $(template.content.cloneNode(true));
        
        // Update name attributes
        $careplanRow.find('[name]').each(function() {
            let name = $(this).attr('name');
            name = name.replace('[0]', `[${index}]`);
            $(this).attr('name', name);
        });
        
        // Populate select options và chọn sẵn
        const $select = $careplanRow.find('.series-careplan-select');
        $select.empty();
        $select.append('<option value="">-- Chọn gói Care+ --</option>');
        
        carePlansData.forEach(function(p) {
            const priceText = formatCurrency(p.final_price);
            const typeLabel = p.plan_type === 'protection' ? '🛡️' : '📅';
            const selected = p.id === plan.id ? 'selected' : '';
            $select.append(`<option value="${p.id}" data-price="${p.final_price}" data-name="${p.name}" ${selected}>${typeLabel} ${p.name} - ${priceText}</option>`);
        });
        
        // Set price display
        const $priceDisplay = $careplanRow.find('.series-careplan-price-display');
        $priceDisplay.val(formatCurrency(plan.final_price));
        
        $container.append($careplanRow);
    });
    
    Swal.fire({
        icon: 'success',
        title: 'Đã thêm tất cả!',
        text: `Đã thêm ${carePlansData.length} gói Care+`,
        timer: 1500,
        showConfirmButton: false
    });
}

// Cập nhật lại index của series care plans sau khi xóa
function updateSeriesCarePlanIndexes() {
    $('#seriesCarePlansContainer .careplan-row').each(function(index) {
        $(this).find('[name]').each(function() {
            let name = $(this).attr('name');
            name = name.replace(/series_careplans\[\d+\]/, `series_careplans[${index}]`);
            $(this).attr('name', name);
        });
    });
}

// =============================================================================
// THÊM ƯU ĐÃI ĐỘC QUYỀN CHO SERIES
// =============================================================================
function addSeriesOffer() {
    console.log('Adding series offer');
    
    const $container = $('#seriesOffersContainer');
    const currentCount = $container.find('.offer-row').length;
    
    // Clone template
    const template = document.getElementById('seriesOfferTemplate');
    if (!template) {
        console.log('ERROR: seriesOfferTemplate not found!');
        return;
    }
    
    const $offerRow = $(template.content.cloneNode(true));
    
    // Update name attributes
    $offerRow.find('[name]').each(function() {
        let name = $(this).attr('name');
        name = name.replace('[0]', `[${currentCount}]`);
        $(this).attr('name', name);
    });
    
    $container.append($offerRow);
    console.log('Series offer added successfully');
}

// =============================================================================
// THÊM MẪU ƯU ĐÃI MẶC ĐỊNH
// =============================================================================
const defaultOffers = [
    {
        badge: "ƯU ĐÃI GOOGLE AI PRO",
        title: "Nhận ngay ưu đãi Google AI Pro (Trị giá 3 Triệu)",
        description: "",
        link_text: "Tìm hiểu thêm"
    },
    {
        badge: "ƯU ĐÃI ĐỘC QUYỀN",
        title: "Nhập code CTHANG12 giảm trực tiếp đến 1.1 triệu",
        description: "Áp dụng khi mua kèm phụ kiện",
        link_text: ""
    },
    {
        badge: "ƯU ĐÃI THANH TOÁN",
        title: "Thanh toán trước giảm 1 triệu",
        description: "Áp dụng thanh toán qua thẻ quốc tế/ ATM/ mã QR/ Ví điện tử",
        link_text: ""
    },
    {
        badge: "3AM CARE+",
        title: "Ưu đãi lên đến 25%",
        description: "Chăm sóc thiết bị chuẩn chuyên gia Samsung.",
        link_text: "Tìm hiểu"
    },
    {
        badge: "ƯU ĐÃI MUA KÈM",
        title: "Thu cũ đổi mới hỗ trợ 1 triệu",
        description: "Áp dụng cho một số sản phẩm nhất định",
        link_text: ""
    }
];

function addDefaultOffers() {
    console.log('Adding default offers');
    
    // Xóa các offer hiện tại
    $('#seriesOffersContainer').empty();
    
    // Thêm các mẫu mặc định
    defaultOffers.forEach(function(offer, index) {
        const $container = $('#seriesOffersContainer');
        
        // Clone template
        const template = document.getElementById('seriesOfferTemplate');
        if (!template) {
            console.log('ERROR: seriesOfferTemplate not found!');
            return;
        }
        
        const $offerRow = $(template.content.cloneNode(true));
        
        // Update name attributes
        $offerRow.find('[name]').each(function() {
            let name = $(this).attr('name');
            name = name.replace('[0]', `[${index}]`);
            $(this).attr('name', name);
        });
        
        // Fill values
        $offerRow.find('[name*="badge"]').val(offer.badge);
        $offerRow.find('[name*="title"]').val(offer.title);
        $offerRow.find('[name*="description"]').val(offer.description);
        $offerRow.find('[name*="link_text"]').val(offer.link_text);
        
        $container.append($offerRow);
    });
    
    Swal.fire({
        icon: 'success',
        title: 'Đã thêm mẫu!',
        text: `Đã thêm ${defaultOffers.length} ưu đãi mẫu`,
        timer: 1500,
        showConfirmButton: false
    });
}

// Cập nhật lại index của series offers sau khi xóa
function updateSeriesOfferIndexes() {
    $('#seriesOffersContainer .offer-row').each(function(index) {
        $(this).find('[name]').each(function() {
            let name = $(this).attr('name');
            name = name.replace(/series_offers\[\d+\]/, `series_offers[${index}]`);
            $(this).attr('name', name);
        });
    });
}

// =============================================================================
// THÊM 3AM CARE PLAN CHO VARIANT (deprecated - giữ lại cho tương thích)
// =============================================================================
function addCarePlan(variantIndex) {
    console.log('Adding care plan for variant:', variantIndex);
    console.log('Available care plans:', carePlansData);
    
    const $variantBlock = $(`.variant-block[data-variant-index="${variantIndex}"]`);
    const $careplanContainer = $variantBlock.find('.careplan-container');
    
    if ($careplanContainer.length === 0) {
        console.log('ERROR: careplan-container not found!');
        return;
    }
    
    const currentCount = $careplanContainer.find('.careplan-row').length;
    
    // Clone template
    const template = document.getElementById('careplanTemplate');
    if (!template) {
        console.log('ERROR: careplanTemplate not found!');
        return;
    }
    
    const $careplanRow = $(template.content.cloneNode(true));
    
    // Update name attributes
    $careplanRow.find('[name]').each(function() {
        let name = $(this).attr('name');
        name = name.replace('[0][careplans][0]', `[${variantIndex}][careplans][${currentCount}]`);
        $(this).attr('name', name);
    });
    
    // Populate select options
    const $select = $careplanRow.find('.careplan-select');
    $select.empty();
    $select.append('<option value="">-- Chọn gói Care+ --</option>');
    
    if (carePlansData.length === 0) {
        console.log('WARNING: No care plans data available');
        $select.append('<option value="" disabled>Chưa có gói Care+ nào</option>');
    } else {
        carePlansData.forEach(function(plan) {
            const priceText = formatCurrency(plan.final_price);
            const typeLabel = plan.plan_type === 'protection' ? '🛡️' : '📅';
            $select.append(`<option value="${plan.id}" data-price="${plan.final_price}" data-name="${plan.name}">${typeLabel} ${plan.name} - ${priceText}</option>`);
        });
    }
    
    // Append
    $careplanContainer.append($careplanRow);
    
    // Animation
    $careplanContainer.find('.careplan-row').last().hide().slideDown(300);
    console.log('Care plan row added successfully');
}

// =============================================================================
// XÓA 3AM CARE PLAN
// =============================================================================
$(document).on('click', '.delete-careplan-btn', function() {
    const $careplanRow = $(this).closest('.careplan-row');
    $careplanRow.slideUp(300, function() {
        $(this).remove();
    });
});

// =============================================================================
// CẬP NHẬT GIÁ KHI CHỌN CARE PLAN
// =============================================================================
$(document).on('change', '.careplan-select', function() {
    const $selected = $(this).find(':selected');
    const price = $selected.data('price') || 0;
    const $priceDisplay = $(this).closest('.careplan-row').find('.careplan-price-display');
    
    if (price > 0) {
        $priceDisplay.val(formatCurrency(price));
    } else {
        $priceDisplay.val('');
    }
});

// =============================================================================
// FORMAT CURRENCY
// =============================================================================
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
}

// =============================================================================
// UPDATE SỐ THỨ TỰ PHIÊN BẢN
// =============================================================================
function updateVariantNumbers() {
    $('.variant-block').each(function(index) {
        $(this).find('.variant-number').text(index + 1);
    });
}

// =============================================================================
// PREVIEW ẢNH SERIES
// =============================================================================
function previewSeriesImage(input) {
    const $preview = $('#series_image_preview');
    $preview.empty();
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $preview.html(`<img src="${e.target.result}" class="image-preview" alt="Series preview">`);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// =============================================================================
// VALIDATION FORM
// =============================================================================
function validateForm() {
    let isValid = true;
    let errors = [];
    
    // 1. Kiểm tra tên series
    const seriesName = $('#series_name').val().trim();
    if (seriesName === '') {
        errors.push('Tên Series không được để trống');
        isValid = false;
    }
    
    // 2. Kiểm tra có ít nhất 1 phiên bản
    const variantCount = $('.variant-block').length;
    if (variantCount === 0) {
        errors.push('Phải có ít nhất 1 phiên bản');
        isValid = false;
    }
    
    // 3. Kiểm tra từng phiên bản
    $('.variant-block').each(function(index) {
        const $variantNameInput = $(this).find('input[name*="[name]"]');
        const variantName = $variantNameInput.length > 0 ? $variantNameInput.val().trim() : '';
        
        // 3.1. Kiểm tra tên phiên bản
        if (variantName === '') {
            errors.push(`Phiên bản ${index + 1}: Tên phiên bản không được để trống`);
            isValid = false;
        }
        
        // 3.2. Kiểm tra có ít nhất 1 tùy chọn dung lượng
        const storageCount = $(this).find('.storage-option-row').length;
        if (storageCount === 0) {
            errors.push(`Phiên bản ${index + 1}: Phải có ít nhất 1 tùy chọn dung lượng`);
            isValid = false;
        }
        
        // 3.3. Kiểm tra thông tin tùy chọn dung lượng
        $(this).find('.storage-option-row').each(function(sIndex) {
            const storage = $(this).find('input[name*="[storage]"]').val().trim();
            const price = $(this).find('input[name*="[price]"]').val().trim();
            
            if (storage === '') {
                errors.push(`Phiên bản ${index + 1}, Tùy chọn ${sIndex + 1}: Dung lượng không được để trống`);
                isValid = false;
            }
            
            if (price === '' || isNaN(price) || parseFloat(price) < 0) {
                errors.push(`Phiên bản ${index + 1}, Tùy chọn ${sIndex + 1}: Giá bán không hợp lệ`);
                isValid = false;
            }
        });
    });
    
    // Hiển thị lỗi nếu có
    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'Lỗi Validation',
            html: '<ul style="text-align: left;">' + errors.map(e => `<li>${e}</li>`).join('') + '</ul>',
            confirmButtonColor: '#dc3545'
        });
    }
    
    return isValid;
}

// =============================================================================
// AUTO CALCULATE INSTALLMENT PRICE
// =============================================================================
// AUTO CALCULATE INSTALLMENT PRICE
// =============================================================================
$(document).on('input', 'input[name*="[price]"], input[name*="[installment_months]"]', function() {
    const $storageRow = $(this).closest('.storage-option-row');
    const price = parseFloat($storageRow.find('input[name*="[price]"]').val()) || 0;
    const months = parseInt($storageRow.find('input[name*="[installment_months]"]').val()) || 12;
    
    if (price > 0 && months > 0) {
        const installmentPrice = Math.round(price / months);
        $storageRow.find('input[name*="[installment_price]"]').val(installmentPrice);
    }
});

// =============================================================================
// TỰ ĐỘNG TÍNH TỔNG GIÁ (BASE + CHÊNH LỆCH)
// =============================================================================
function updateStorageTotalPrice($changedElement) {
    const $variantBlock = $changedElement.closest('.variant-block');
    const basePrice = parseFloat($variantBlock.find('.variant-base-price').val()) || 0;
    
    // Cập nhật tất cả storage options trong variant này
    $variantBlock.find('.storage-option-row').each(function() {
        const $storageRow = $(this);
        const additionalPrice = parseFloat($storageRow.find('.storage-additional-price').val()) || 0;
        const totalPrice = basePrice + additionalPrice;
        
        // Tìm hoặc tạo element hiển thị tổng giá
        let $totalDisplay = $storageRow.find('.total-price-display');
        if ($totalDisplay.length === 0) {
            // Thêm element hiển thị sau ô giá chênh lệch
            const $priceCol = $storageRow.find('.storage-additional-price').closest('.col-md-2');
            $priceCol.append('<div class="total-price-display mt-1" style="font-size: 12px; font-weight: 600; color: #28a745;"></div>');
            $totalDisplay = $storageRow.find('.total-price-display');
        }
        
        // Hiển thị tổng giá
        if (totalPrice > 0) {
            $totalDisplay.text(`→ Tổng: ${totalPrice.toLocaleString('vi-VN')}₫`);
        } else {
            $totalDisplay.text('');
        }
    });
}

// =============================================================================
// PREVIEW NHIỀU ẢNH CHO VARIANT
// =============================================================================
function previewVariantImages(input) {
    const $container = $(input).closest('div');
    let $previewContainer = $container.find('.variant-images-preview');
    
    // Tạo container nếu chưa có
    if ($previewContainer.length === 0) {
        $container.append('<div class="variant-images-preview mt-2" style="display:flex;gap:8px;flex-wrap:wrap;"></div>');
        $previewContainer = $container.find('.variant-images-preview');
    }
    
    $previewContainer.empty();
    
    if (input.files && input.files.length > 0) {
        // Hiển thị số lượng ảnh
        const fileCount = input.files.length;
        
        // Thêm badge số lượng
        $previewContainer.append(`<div style="width:100%;margin-bottom:5px;"><span class="badge bg-primary">${fileCount} ảnh đã chọn</span></div>`);
        
        for (let i = 0; i < fileCount; i++) {
            const file = input.files[i];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const $imgWrapper = $(`
                    <div style="position: relative; width: 60px; height: 60px;">
                        <img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
                        <span style="position: absolute; top: -5px; right: -5px; background: #333; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 9px;">${i + 1}</span>
                    </div>
                `);
                $previewContainer.append($imgWrapper);
            };
            
            reader.readAsDataURL(file);
        }
    }
}

