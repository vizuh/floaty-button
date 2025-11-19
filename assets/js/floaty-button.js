(function () {
    if (!window.FLOATY_BUTTON_SETTINGS) return;
    const settings = window.FLOATY_BUTTON_SETTINGS;

    const containerID = 'floaty-button-container';
    let container = document.getElementById(containerID);

    if (!container) {
        container = document.createElement('div');
        container.id = containerID;
        document.body.appendChild(container);
    }

    const positionClass = settings.position ? `floaty-position-${settings.position}` : 'floaty-position-bottom_right';

    container.innerHTML = `
        <button class="floaty-button ${positionClass}">
            ${settings.buttonLabel || 'Book now'}
        </button>
        <div class="floaty-modal-backdrop" hidden></div>
        <div class="floaty-modal" hidden>
            <button class="floaty-modal-close" aria-label="Close">&times;</button>
            <iframe class="floaty-modal-iframe" src="" frameborder="0"></iframe>
        </div>
    `;

    const button = container.querySelector('.floaty-button');
    const backdrop = container.querySelector('.floaty-modal-backdrop');
    const modal = container.querySelector('.floaty-modal');
    const iframe = container.querySelector('.floaty-modal-iframe');
    const closeBtn = container.querySelector('.floaty-modal-close');

    function pushToDataLayer() {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: settings.eventName || 'floaty_click',
            floatyActionType: settings.actionType || 'link',
            floatyLabel: settings.buttonLabel || ''
        });
    }

    function openLink() {
        if (!settings.linkUrl) return;
        const target = settings.linkTarget || '_blank';
        window.open(settings.linkUrl, target);
    }

    function openIframeModal() {
        if (!settings.iframeUrl) return;
        if (iframe) iframe.src = settings.iframeUrl;
        if (backdrop) backdrop.hidden = false;
        if (modal) modal.hidden = false;
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
    }

    function closeModal() {
        if (backdrop) backdrop.hidden = true;
        if (modal) modal.hidden = true;
        if (iframe) iframe.src = ''; // Stop video/audio if any
        document.body.style.overflow = ''; // Restore background scrolling
    }

    if (button) {
        button.addEventListener('click', function () {
            // tracking
            pushToDataLayer();

            // action
            if (settings.actionType === 'iframe_modal') {
                openIframeModal();
            } else {
                openLink();
            }
        });
    }

    if (backdrop) backdrop.addEventListener('click', closeModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);

    // Expose optional global helpers
    window.FLOATY_showButton = function () {
        if (button) button.style.display = '';
    };
    window.FLOATY_hideButton = function () {
        if (button) button.style.display = 'none';
    };
})();
