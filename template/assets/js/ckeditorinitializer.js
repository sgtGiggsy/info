let editorInstance;

ClassicEditor
    .create(document.querySelector('#' + selectorname), {
        toolbar: [
            'bold', 'italic', 'heading', 'link', 'bulletedList', 'numberedList',  'blockQuote', 'undo', 'redo'
        ]
    })
    .then(editor => {
        editorInstance = editor;
    })
    .catch(error => {
        console.error(error);
    });