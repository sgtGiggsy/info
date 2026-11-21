let editorInstance;
let db = selectornames.length;
for (let i = 0; i < db; i++) {
    ClassicEditor
        .create(document.querySelector('#' + selectornames[i]), {
            toolbar: [
                'bold', 'italic', 'heading', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'
            ]
        })
        .then(editor => {
            editorInstance = editor;
        })
        .catch(error => {
            console.error(error);
        });
}