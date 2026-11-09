const dropzone = document.getElementById('dropzone');
const input = document.getElementById(uploadcontainer);
const preview = document.getElementById('preview');
let selectedFiles = [];

// kattintás
dropzone.addEventListener('click', () => input.click());

// drag highlight
dropzone.addEventListener('dragover', e => {
    e.preventDefault();
    dropzone.classList.add('dragover');
});

dropzone.addEventListener('dragleave', () => {
    dropzone.classList.remove('dragover');
});

// drop
dropzone.addEventListener('drop', e => {
    e.preventDefault();
    dropzone.classList.remove('dragover');

    const files = e.dataTransfer.files;
    input.files = files;
    handleFiles(files);
});

// normál kiválasztás
input.addEventListener('change', () => {
    handleFiles(input.files);
});

function updateInput() {
    const dt = new DataTransfer();
    selectedFiles.forEach(file => dt.items.add(file));
    input.files = dt.files;
}

// fájlok kezelése
function handleFiles(files) {
    [...files].forEach(file => {
        selectedFiles.push(file);
    });

    renderPreview();
    updateInput();
}

function renderPreview() {
    preview.innerHTML = '';

    selectedFiles.forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'preview-item';

        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            div.appendChild(img);
        }

        const name = document.createElement('div');
        name.textContent = file.name;
        div.appendChild(name);

        // törlés gomb
        const btn = document.createElement('button');
        btn.textContent = '✕';
        btn.type = 'button';

        btn.onclick = () => {
            selectedFiles.splice(index, 1);
            renderPreview();
            updateInput();
        };

        div.appendChild(btn);

        preview.appendChild(div);
    });
}