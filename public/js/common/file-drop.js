function createFileDrop(dropZoneId, fileInputId, fileListId) {
    const dropZone = document.getElementById(dropZoneId);
    const fileInput = document.getElementById(fileInputId);
    const fileList = document.getElementById(fileListId);
    if (!dropZone || !fileInput || !fileList) return;

    function updateList(files) {
        const items = Array.from(files || []);
        fileList.innerHTML = '';
        if (items.length === 0) return;
        const ul = document.createElement('ul');
        ul.className = 'list-unstyled mb-0';
        items.forEach((f, i) => {
            const li = document.createElement('li');
            li.className = 'd-flex justify-content-between align-items-center py-1';
            const name = document.createElement('span');
            name.textContent = f.name + (f.size ? ` (${Math.round(f.size/1024)} KB)` : '');
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn btn-sm btn-link text-danger p-0';
            remove.textContent = 'Remove';
            remove.addEventListener('click', () => {
                try {
                    const dt = new DataTransfer();
                    Array.from(fileInput.files).forEach((file, idx) => { if (idx !== i) dt.items.add(file); });
                    fileInput.files = dt.files;
                    updateList(fileInput.files);
                } catch (err) {
                    fileInput.value = '';
                    updateList([]);
                }
            });
            li.appendChild(name);
            li.appendChild(remove);
            ul.appendChild(li);
        });
        fileList.appendChild(ul);
    }

    function setFilesFromList(newFiles) {
        try {
            const dt = new DataTransfer();
            newFiles.forEach(f => dt.items.add(f));
            fileInput.files = dt.files;
        } catch (err) {
            // ignore
        }
        updateList(fileInput.files);
    }

    function handleFiles(files) {
        if (!files || files.length === 0) return;
        const existing = Array.from(fileInput.files || []);
        const incoming = Array.from(files);
        const merged = existing.concat(incoming);
        setFilesFromList(merged);
    }

    ['dragenter', 'dragover'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#0248D4';
            dropZone.style.backgroundColor = '#f8fbff';
        });
    });
    ['dragleave', 'dragend', 'drop'].forEach(evt => {
        dropZone.addEventListener(evt, (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '';
            dropZone.style.backgroundColor = '';
        });
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        const dt = e.dataTransfer;
        if (dt && dt.files && dt.files.length) {
            handleFiles(dt.files);
        }
    });

    dropZone.addEventListener('click', () => fileInput.click());
    dropZone.addEventListener('keydown', (e) => { if (e.key === 'Enter' || e.key === ' ') fileInput.click(); });

    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    updateList(fileInput.files);
    return { updateList };
}

export function initFileDrop() {
    createFileDrop('cipgDropZone', 'cipgFileInput', 'cipgFileList');
}

export { createFileDrop };
