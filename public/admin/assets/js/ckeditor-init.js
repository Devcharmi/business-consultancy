// let editorInstances = {};
// function initAllCKEditors(prefixes = [], defaultContents = {}) {
//     prefixes.forEach((prefix) => {
//         // Select all <textarea> elements whose IDs start with the prefix
//         const textareas = document.querySelectorAll(
//             `textarea[id^="${prefix}"]`
//         );

//         textareas.forEach((textarea) => {
//             const id = textarea.id;

//             if (!textarea.hasAttribute("data-ckeditor-initialized")) {
//                 ClassicEditor.create(textarea)
//                     .then((editor) => {
//                         editorInstances[id] = editor;
//                         textarea.setAttribute(
//                             "data-ckeditor-initialized",
//                             "true"
//                         );

//                         const trimmedValue = textarea.value.trim();
//                         const hasContent = trimmedValue.length > 0;

//                         // Only apply default content if textarea is empty and there's a matching default
//                         if (!hasContent && defaultContents.hasOwnProperty(id)) {
//                             editor.setData(defaultContents[id]);
//                         } else {
//                             // Disable CKEditor if there's no exact match
//                             // editor.enableReadOnlyMode(id);
//                         }
//                     })
//                     .catch((error) =>
//                         console.error(`CKEditor error on #${id}:`, error)
//                     );
//             }
//         });
//     });
// }

let editorInstances = {};
function initAllCKEditors(prefixes = [], defaultContents = {}, heights = {}) {
    prefixes.forEach((prefix) => {
        // Select all <textarea> elements whose IDs start with the prefix
        const textareas = document.querySelectorAll(
            `textarea[id^="${prefix}"]`
        );

        textareas.forEach((textarea) => {
            const id = textarea.id;

            if (!textarea.hasAttribute("data-ckeditor-initialized")) {
                ClassicEditor.create(textarea)
                    .then((editor) => {
                        editorInstances[id] = editor;
                        textarea.setAttribute(
                            "data-ckeditor-initialized",
                            "true"
                        );

                        const trimmedValue = textarea.value.trim();
                        const hasContent = trimmedValue.length > 0;

                        // Set editor-specific height
                        const editorHeight = heights[id] || "200px"; // default fallback
                        const editableElement = editor.ui.view.editable.element;
                        editableElement.style.height = editorHeight;
                        editableElement.style.minHeight = editorHeight;

                        // Only apply default content if textarea is empty and there's a matching default
                        if (!hasContent && defaultContents.hasOwnProperty(id)) {
                            editor.setData(defaultContents[id]);
                        } else {
                            // Disable CKEditor if there's no exact match
                            // editor.enableReadOnlyMode(id);
                        }
                    })
                    .catch((error) =>
                        console.error(`CKEditor error on #${id}:`, error)
                    );
            }
        });
    });
}

function updateTextareasFromEditors() {
    for (const id in editorInstances) {
        const editor = editorInstances[id];
        const textarea = document.getElementById(id);
        if (textarea) {
            textarea.value = editor.getData();
        }
    }
}

function destroyEditorById(editorId) {
    const textarea = document.getElementById(editorId);
    if (editorInstances[editorId]) {
        editorInstances[editorId]
            .destroy()
            .then(() => {
                delete editorInstances[editorId]; // Remove from object
                $("#" + editorId).addClass("d-none"); // Hide textarea
                if (textarea)
                    textarea.removeAttribute("data-ckeditor-initialized"); // allow re-init
            })
            .catch((err) => console.error("CKEditor destroy error:", err));
    } else {
        $("#" + editorId).addClass("d-none"); // Fallback
        if (textarea) textarea.removeAttribute("data-ckeditor-initialized");
    }
}
