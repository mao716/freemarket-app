document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('image');
    const preview = document.getElementById('uploader-preview');
    if (!input || !preview) return;

    input.addEventListener('change', (e) => {
        const file = e.target.files?.[0];
        preview.innerHTML = '';

        if (!file || !file.type.startsWith('image/')) return;

        const url = URL.createObjectURL(file);
        const img = document.createElement('img');
        img.src = url;
        img.onload = () => URL.revokeObjectURL(url);
        preview.appendChild(img);
    });
});
