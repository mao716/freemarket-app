document.addEventListener("DOMContentLoaded", function () {
    const messages = document.querySelectorAll(".trade-message");

    messages.forEach(function (message) {
        const editButton = message.querySelector("[data-edit-toggle]");
        const cancelButton = message.querySelector("[data-edit-cancel]");
        const viewArea = message.querySelector("[data-message-view]");
        const editForm = message.querySelector("[data-message-edit-form]");

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
            });
        }
    });
});
