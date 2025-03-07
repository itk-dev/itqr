import './styles/app.css';

document.addEventListener('DOMContentLoaded', () => {
    const qrCodeContainer = document.getElementById('qrCodeContainer');
    const tabsContainer = document.getElementById('qrCodeTabs'); // Navigation tabs
    const tabContentContainer = document.getElementById('qrCodeTabContent'); // Tab content
    const form = document.querySelector('.form-wrapper form');
    const selectedQrCodes = document.getElementById('selectedQrCodes');

    // Ensure all containers and elements exist
    if (!qrCodeContainer || !tabsContainer || !tabContentContainer || !form) {
        console.error('Required elements not found!');
        return;
    }

    const generateQrPath = qrCodeContainer.dataset.generateQrPath;

    if (!generateQrPath) {
        console.error('QR Code generate path is missing! Add the "data-generate-qr-path" attribute.');
        return;
    }

    async function updateQRCode() {
        // Clear tab and content containers
        tabsContainer.innerHTML = '';
        tabContentContainer.innerHTML = '';

        // Prepare form data for POST request
        const formData = new FormData(form);
        formData.append('selectedQrCodes', selectedQrCodes.value);

        try {
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
                                ${title}
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
        }
    }

    // Timeout before updating qr code after typing
    let typingTimer;

    form.addEventListener('input', () => {
        clearTimeout(typingTimer);

        typingTimer = setTimeout(() => {
            updateQRCode();
        }, 500);
    });

    updateQRCode();
});
