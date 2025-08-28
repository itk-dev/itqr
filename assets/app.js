import './styles/app.css';

const uploadBasePath = 'uploads/qr_codes/';

document.addEventListener('DOMContentLoaded', () => {
    // Enforce only one URL per QR code.
    handleQrUrlCollection();

    const qrCodeContainer = document.getElementById('qrCodeContainer');
    const tabsContainer = document.getElementById('qrCodeTabs'); // Navigation tabs
    const tabContentContainer = document.getElementById('qrCodeTabContent'); // Tab content
    const form = document.querySelector('.form-wrapper form');
    const formName = form ? form.getAttribute('name') : '';
    const selectedQrCodes = document.getElementById('selectedQrCodes');

    // Ensure all containers and elements exist
    if (!qrCodeContainer || !tabsContainer || !tabContentContainer || !form) {
        console.log('Required elements not found');
        return;
    }

    const generateQrPath = qrCodeContainer.dataset.generateQrPath;

    if (!generateQrPath) {
        console.error('QR Code generate path is missing! Add the "data-generate-qr-path" attribute.');
        return;
    }

    let isDesignUpdating = false;
    let updatePromise = null;
    const designSelect = document.querySelector('#batch_download_design');
    if (designSelect) {
        designSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];

            if (!selectedOption.value) {
                // Set default values if no design selected
                const defaultFields = {
                    'size': 400,
                    'margin': 0,
                    'backgroundColor': '#ffffff',
                    'foregroundColor': '#000000',
                    'labelText': '',
                    'labelSize': 12,
                    'labelTextColor': '#000000',
                    'labelMarginTop': 0,
                    'labelMarginBottom': 0,
                    'errorCorrectionLevel': 'medium',
                    'logo': '',
                    'logoPath': '',
                };

                Object.entries(defaultFields).forEach(([field, value]) => {
                    const input = document.querySelector(`#batch_download_${field}`);
                    if (input) {
                        input.value = value;
                    }
                });

                return;
            }

            isDesignUpdating = true;
            // Get the design data using the API Platform endpoint
            fetch(`/admin/qr_visual_configs/${selectedOption.value}`)
                .then(response => response.json())
                .then(design => {

                    // Update form fields with design values
                    const fields = {
                        'size': design.size,
                        'margin': design.margin,
                        'backgroundColor': design.backgroundColor,
                        'foregroundColor': design.foregroundColor,
                        'labelText': design.labelText || '',
                        'labelSize': design.labelSize,
                        'labelTextColor': design.labelTextColor,
                        'labelMarginTop': design.labelMarginTop,
                        'labelMarginBottom': design.labelMarginBottom,
                        'errorCorrectionLevel': design.errorCorrectionLevel,
                        'logoPath': design.logo,
                    };

                    // Update each form field
                    Object.entries(fields).forEach(([field, value]) => {
                        const input = document.querySelector(`#batch_download_${field}`);
                        if (input) {
                            input.value = value;
                        }
                    });

                    isDesignUpdating = false;
                })
                .catch(error => {
                    console.error('Error fetching design details:', error);
                    isDesignUpdating = false;
                });
        });
    }

    async function updateQRCode() {
        if (updatePromise) {
            return updatePromise;
        }

        // Clear tab and content containers
        tabsContainer.innerHTML = '';
        tabContentContainer.innerHTML = '';

        // Prepare form data for POST request
        const formData = new FormData(form);
        formData.append('selectedQrCodes', selectedQrCodes.value);
        formData.append('formName', formName);

        updatePromise = (async () => {
            try {
                formData.forEach((value, key) => {
                    console.log(key + ': ' + value);
                });
                // Fetch the QR codes from the endpoint
                const response = await fetch(generateQrPath, {
                    method: 'POST',
                    body: formData,
                });

                if (response.ok) {
                    const data = await response.json();
                    const qrCodes = data.qrCodes; // Array of qr titles and generated base64 images

                    if (typeof qrCodes === 'object') {
                        // Loop through all images and create tabs dynamically
                        Object.entries(qrCodes).forEach(([title, imageSrc]) => {
                            // Sanitize the title to create valid IDs
                            const sanitizedTitle = title.replace(/[^a-zA-Z0-9-_]/g, '_');

                            // Create a unique tab ID
                            const tabId = `qrCodeTab-${sanitizedTitle}`;
                            const tabPaneId = `qrCodeContent-${sanitizedTitle}`;

                            // Create tab navigation item
                            const tabItem = document.createElement('li');
                            tabItem.className = 'nav-item';
                            tabItem.role = 'presentation';
                            tabItem.innerHTML = `
                                <button class="nav-link ${tabsContainer.children.length === 0 ? 'active' : ''}" 
                                        id="${tabId}" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#${tabPaneId}" 
                                        type="button" 
                                        role="tab" 
                                        aria-controls="${tabPaneId}" 
                                        aria-selected="${tabsContainer.children.length === 0}">
                                    ${title === 'examplePreview' ? '' : title}
                                </button>
                            `;
                            tabsContainer.appendChild(tabItem);

                            // Create tab content (image)
                            const tabContent = document.createElement('div');
                            tabContent.className = `tab-pane fade ${tabsContainer.children.length === 1 ? 'show active' : ''} qr-code-tab-pane`;
                            tabContent.id = tabPaneId;
                            tabContent.role = 'tabpanel';
                            tabContent.innerHTML = `
                                <img src="${imageSrc}" alt="QR Code titled ${title}" class="qr-code-image">
                            `;
                            tabContentContainer.appendChild(tabContent);
                        });
                    } else {
                        console.error('Invalid data format. Expected an array of QR code images.');
                    }
                } else {
                    console.error('Failed to fetch QR codes. Status:', response.status);
                }
            } catch (error) {
                console.error('Error while fetching QR codes:', error);
            } finally {
                updatePromise = null;
            }
        })();

        return updatePromise;
    }

    let typingTimer;

    form.addEventListener('input', () => {
        clearTimeout(typingTimer);

        typingTimer = setTimeout(() => {
            updateQRCode();
        }, 500);
    });
    updateQRCode();
});

function handleBatchDisableConfirm() {
    document.querySelectorAll('.disable-confirm').forEach(actionBtn => {
        actionBtn.addEventListener('click', function() {
            let modal = document.getElementById('modal-batch-action');
            document.querySelector('.modal-backdrop').classList.add('invisible');
            modal.classList.add('invisible');
            modal.querySelector('#modal-batch-action-button').click();
        });
    });
}

function handleQrUrlCollection() {
    const qrUrlCollectionParent = document.querySelector('.qr-urls-collection');
    const qrUrlCollectionAddButton = document.querySelector('.qr-urls-collection .field-collection-add-button');
    const urlCollectionCount = qrUrlCollectionParent ? qrUrlCollectionParent.getAttribute('data-num-items') : null;

    if (qrUrlCollectionAddButton) {
        setTimeout(() => {
            qrUrlCollectionAddButton.click();
        }, 1)

        if (parseInt(urlCollectionCount) === 1) {
            qrUrlCollectionAddButton.classList.add('d-none');
        }

        qrUrlCollectionAddButton.addEventListener('click', () => {
            setTimeout(() => {
                const urlCollectionCount = qrUrlCollectionParent.getAttribute('data-num-items');

                if (parseInt(urlCollectionCount) === 1) {
                    qrUrlCollectionAddButton.classList.add('d-none');
                }
            }, 1)
        });
    }
}

document.addEventListener('readystatechange', function(event) {
    if ('complete' === document.readyState) {
        handleBatchDisableConfirm();
    }
});
