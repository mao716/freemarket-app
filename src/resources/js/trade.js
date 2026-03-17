document.addEventListener("DOMContentLoaded", function () {
    const messages = document.querySelectorAll(".trade-message");

    messages.forEach(function (message) {
        const editButton = message.querySelector("[data-edit-toggle]");
        const cancelButton = message.querySelector("[data-edit-cancel]");
        const actions = message.querySelector("[data-message-actions]");
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
            document
                .querySelectorAll("[data-message-edit-form]")
                .forEach(function (form) {
                    form.hidden = true;
                });

            document
                .querySelectorAll("[data-message-view]")
                .forEach(function (view) {
                    view.hidden = false;
                });

            document
                .querySelectorAll("[data-message-actions]")
                .forEach(function (actionArea) {
                    actionArea.hidden = false;
                });

            viewArea.hidden = true;
            editForm.hidden = false;

            if (actions) {
                actions.hidden = true;
            }
        });

        if (cancelButton) {
            cancelButton.addEventListener("click", function () {
                editForm.hidden = true;
                viewArea.hidden = false;

                if (actions) {
                    actions.hidden = false;
                }

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

    const reviewOpenButton = document.querySelector("[data-review-open]");
    const reviewModal = document.querySelector("[data-review-modal]");
    const reviewCloseButtons = document.querySelectorAll("[data-review-close]");
    const reviewStarInputs = document.querySelectorAll(
        ".trade-review-stars__input",
    );

    if (reviewOpenButton && reviewModal) {
        reviewOpenButton.addEventListener("click", function () {
            reviewModal.hidden = false;
        });
    }

    reviewCloseButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            reviewModal.hidden = true;
        });
    });

    reviewStarInputs.forEach(function (input) {
        input.addEventListener("change", function () {
            reviewStarInputs.forEach(function (currentInput) {
                const label = currentInput.closest(
                    ".trade-review-stars__label",
                );

                if (!label) {
                    return;
                }

                if (Number(currentInput.value) <= Number(input.value)) {
                    label.classList.add("is-active");
                    return;
                }

                label.classList.remove("is-active");
            });
        });
    });

    const draftTextarea = document.getElementById("trade-message-body");
    const draftWrapper = document.querySelector(".trade-form__row");

    if (!draftTextarea || !draftWrapper) {
        return;
    }

    const tradeId = draftWrapper.dataset.tradeId;
    const isMessagePosted = draftWrapper.dataset.messagePosted === "1";

    if (!tradeId) {
        return;
    }

    const storageKey = `trade_message_draft_${tradeId}`;

    if (isMessagePosted) {
        sessionStorage.removeItem(storageKey);
        draftTextarea.value = "";
        return;
    }

    const savedValue = sessionStorage.getItem(storageKey);

    if (savedValue !== null && draftTextarea.value.trim() === "") {
        draftTextarea.value = savedValue;
    }

    draftTextarea.addEventListener("input", function () {
        sessionStorage.setItem(storageKey, draftTextarea.value);
    });
});
