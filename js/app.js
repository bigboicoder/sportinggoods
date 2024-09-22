// ============================================================================
// General Functions
// ============================================================================
// Photo preview and drag-and-drop
document.addEventListener('DOMContentLoaded', function () {
    const dropZone = document.getElementById('dropZone');
    const previewImg = document.getElementById('previewImg');
    const fileName = document.getElementById('fileName');
    const profilePicInput = document.getElementById('profilePicInput');

    // Handle drag events
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault(); // Allow dropping
        dropZone.style.borderColor = '#007bff'; // Highlight border
    });

    dropZone.addEventListener('dragleave', function () {
        dropZone.style.borderColor = '#ced4da'; // Reset border color when leaving
    });

    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.style.borderColor = '#ced4da'; // Reset border color after drop

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0]; // Only declare 'file' once
            if (file.type.startsWith('image/')) {
                // Preview the dropped image
                previewImg.src = URL.createObjectURL(file);
                fileName.textContent = file.name;

                // Set the file to the input[type="file"]
                profilePicInput.files = e.dataTransfer.files;
            } else {
                // If the dropped file is not an image, reset to the default image
                previewImg.src = '/images/default-user-profile.png'; // Reset to default
                fileName.textContent = 'default-profile.jpg';
            }
        }
    });

});
// ============================================================================
// Page Load (jQuery)
// ============================================================================

$(() => {

    // Autofocus
    $('form :input:not(button):first').focus();
    $('.err:first').prev().focus();
    $('.err:first').prev().find(':input:first').focus();

    // Confirmation message
    $('[data-confirm]').on('click', e => {
        const text = e.target.dataset.confirm || 'Are you sure?';
        if (!confirm(text)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    });

    // Initiate GET request
    $('[data-get]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.get;
        location = url || location;
    });

    // Initiate POST request
    $('[data-post]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.post;
        const f = $('<form>').appendTo(document.body)[0];
        f.method = 'POST';
        f.action = url || location;
        f.submit();
    });

    // Reset form
    $('[type=reset]').on('click', e => {
        e.preventDefault();
        location = location;
    });

    // Auto uppercase
    $('[data-upper]').on('input', e => {
        const a = e.target.selectionStart;
        const b = e.target.selectionEnd;
        e.target.value = e.target.value.toUpperCase();
        e.target.setSelectionRange(a, b);
    });

    // Photo preview
    $('label.upload input[type=file]').on('change', e => {
        const f = e.target.files[0];
        const img = $(e.target).siblings('img')[0];
        const text = $(e.target).siblings('p')[0];

        if (!img) return;

        img.dataset.src ??= img.src;

        if (f?.type.startsWith('image/')) {
            img.src = URL.createObjectURL(f);
            text.textContent = f.name;
        }
        else {
            img.src = img.dataset.src;
            e.target.value = '';
        }
    });

});