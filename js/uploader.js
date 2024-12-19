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

        // Add form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const withDriverRadio = document.getElementById('withDriver');
            
            if (withDriverRadio.checked) {
                const nameInput = document.querySelector('#driverInfo input[placeholder="Name"]');
                const phoneInput = document.querySelector('#driverInfo input[placeholder="Mobile number"]');
                const licenseInput = document.querySelector('#driverInfo input[placeholder="Driver\'s License Number"]');
                const fileInput = document.getElementById('file-input');
                
                if (!nameInput.value.trim()) {
                    e.preventDefault();
                    alert('Please enter driver\'s name');
                    return;
                }
                
                if (!phoneInput.value.trim()) {
                    e.preventDefault();
                    alert('Please enter driver\'s phone number');
                    return;
                }
                
                if (!licenseInput.value.trim()) {
                    e.preventDefault();
                    alert('Please enter driver\'s license number');
                    return;
                }
                
                if (selectedFiles.length < 1) {
                    e.preventDefault();
                    alert('Please upload at least one ID document');
                    return;
                }
            }
        });
