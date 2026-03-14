document.addEventListener("DOMContentLoaded", function () {
    const messages = document.querySelectorAll(".trade-message");

    messages.forEach(function (message) {
        const editButton = message.querySelector("[data-edit-toggle]");
        const cancelButton = message.querySelector("[data-edit-cancel]");
        const viewArea = message.querySelector("[data-message-view]");
        const editForm = message.querySelector("[data-message-edit-form]");
        const editImageButton = message.querySelector(
            "[data-edit-image-button]",
        );
        const editFileInput = message.querySelector("[data-edit-file]");
        const editPreviewArea = message.querySelector("[data-edit-preview]");
        const currentImageWrap = message.querySelector(
            "[data-current-image-wrap]",
        );

        if (!editButton || !viewArea || !editForm) {
            return;
        }

        editButton.addEventListener("click", function () {
            viewArea.hidden = true;
            editForm.hidden = false;
        });

        if (cancelButton) {
            cancelButton.addEventListener("click", function () {
                editForm.hidden = true;
                viewArea.hidden = false;

                if (editFileInput) {
                    editFileInput.value = "";
                }

                if (editPreviewArea) {
                    editPreviewArea.innerHTML = "";
                }

                if (currentImageWrap) {
                    currentImageWrap.hidden = false;
                }
            });
        }

        if (editImageButton && editFileInput) {
            editImageButton.addEventListener("click", function () {
                editFileInput.click();
            });
        }

        if (editFileInput && editPreviewArea) {
            editFileInput.addEventListener("change", function () {
                editPreviewArea.innerHTML = "";

                const file = this.files[0];

                if (!file) {
                    if (currentImageWrap) {
                        currentImageWrap.hidden = false;
                    }
                    return;
                }

                if (currentImageWrap) {
                    currentImageWrap.hidden = true;
                }

                const reader = new FileReader();

                reader.onload = function (e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.classList.add("trade-message__edit-preview-image");

                    editPreviewArea.appendChild(img);
                };

                reader.readAsDataURL(file);
            });
        }
    });
});
