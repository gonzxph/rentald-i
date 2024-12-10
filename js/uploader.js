const fileInput = document.getElementById('file-input');
        const previewContainer = document.querySelector('.preview-container');
        const fileCount = document.querySelector('.file-count');
        let selectedFiles = [];

        fileInput.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            
            files.forEach(file => {
                if (!selectedFiles.some(f => f.name === file.name)) {
                    selectedFiles.push(file);
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'preview-item';
                        
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="${file.name}">
                            <p>${file.name}</p>
                            <button class="remove-btn" data-name="${file.name}">×</button>
                        `;
                        
                        previewContainer.appendChild(previewItem);
                        updateFileCount();
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
        });

        previewContainer.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-btn')) {
                const fileName = e.target.dataset.name;
                selectedFiles = selectedFiles.filter(file => file.name !== fileName);
                e.target.closest('.preview-item').remove();
                updateFileCount();
            }
        });

        function updateFileCount() {
            fileCount.textContent = `${selectedFiles.length} Files Selected`;
        }
