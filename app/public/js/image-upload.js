function initImageUpload() {
    const wrapper = document.getElementById('image-upload-section');
    if (!wrapper) return;

    const input     = wrapper.querySelector('input[type="file"]');
    const container = document.getElementById('image-preview-container');
    const preview   = document.getElementById('image-preview');
    if (!input || !container || !preview) return;

    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) {
            container.hidden = true;
            return;
        }
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            container.hidden = false;
        };
        reader.readAsDataURL(file);
    });
}

document.addEventListener('DOMContentLoaded', initImageUpload);
