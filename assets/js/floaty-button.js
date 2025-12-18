( function () {
	if ( ! window.VZFLTY_SETTINGS ) {
		return;
	}

	const settings = window.VZFLTY_SETTINGS;
	const containerID = 'vzflty-button-container';
	const i18n = settings.i18n || {};
	const whatsappLabel = i18n.whatsappLabel || 'WhatsApp';
	const defaultButtonLabel = i18n.defaultButtonLabel || 'Book now';
	const modalCloseLabel = i18n.modalCloseLabel || 'Close';
	const modalCloseText = i18n.modalCloseText || 'Close';
	let container = document.getElementById( containerID );

	if ( ! container ) {
		container = document.createElement( 'div' );
		container.id = containerID;
		document.body.appendChild( container );
	}

	const positionClass = settings.position ? `vzflty-position-${ settings.position }` : 'vzflty-position-bottom_right';
	const buttonMode = settings.mode === 'whatsapp' ? 'whatsapp' : ( settings.buttonTemplate === 'whatsapp' ? 'whatsapp' : 'custom' );
	const isWhatsApp = buttonMode === 'whatsapp';
	const buttonLabel = settings.buttonLabel || ( isWhatsApp ? whatsappLabel : defaultButtonLabel );

	if ( isWhatsApp ) {
		container.innerHTML = `
			<a href="#" class="vzflty-whatsapp-btn ${ positionClass }" aria-label="${ buttonLabel }">
				<span class="vzflty-whatsapp-icon">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" preserveAspectRatio="xMidYMid meet" aria-hidden="true" focusable="false"><!--!Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-28.4l-6.7-4.6-69.8 18.3 18.6-68.1-4.4-6.9c-19.7-31.3-30.2-68-30.2-106.1 0-101.9 82.9-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/></svg>
				</span>
			</a>
		`;
	} else {
		container.innerHTML = `
			<button class="vzflty-button ${ positionClass }" type="button">
				${ buttonLabel }
			</button>
			<div class="vzflty-modal-backdrop" hidden></div>
			<div class="vzflty-modal" hidden>
				<button class="vzflty-modal-close" type="button" aria-label="${ modalCloseLabel }" title="${ modalCloseLabel }">${ modalCloseText }</button>
				<iframe class="vzflty-modal-iframe" src="" frameborder="0"></iframe>
			</div>
		`;
	}

	const button = container.querySelector( '.vzflty-button, .vzflty-whatsapp-btn' );
	const backdrop = container.querySelector( '.vzflty-modal-backdrop' );
	const modal = container.querySelector( '.vzflty-modal' );
	const iframe = container.querySelector( '.vzflty-modal-iframe' );
	const closeBtn = container.querySelector( '.vzflty-modal-close' );

	function pushToDataLayer() {
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push( {
			event: settings.eventName || 'vzflty_click',
			floatyActionType: isWhatsApp ? 'whatsapp' : ( settings.actionType || 'link' ),
			floatyLabel: buttonLabel,
		} );
	}

	function openLink() {
		if ( ! settings.linkUrl ) {
			return;
		}
		const target = settings.linkTarget || '_blank';
		window.open( settings.linkUrl, target );
	}

	function openWhatsApp() {
		if ( ! settings.whatsappPhone ) {
			return;
		}
		const phone = settings.whatsappPhone.replace( /[^0-9]/g, '' );
		const message = encodeURIComponent( settings.whatsappMessage || '' );
		const url = `https://wa.me/${ phone }?text=${ message }`;
		window.open( url, '_blank' );
	}

	function openIframeModal() {
		if ( ! settings.iframeUrl ) {
			return;
		}
		if ( iframe ) {
			iframe.src = settings.iframeUrl;
		}
		if ( backdrop ) {
			backdrop.hidden = false;
		}
		if ( modal ) {
			modal.hidden = false;
		}
		document.body.style.overflow = 'hidden';
	}

	function closeModal() {
		if ( backdrop ) {
			backdrop.hidden = true;
		}
		if ( modal ) {
			modal.hidden = true;
		}
		if ( iframe ) {
			iframe.src = '';
		}
		document.body.style.overflow = '';
	}

	if ( button ) {
		button.addEventListener( 'click', function ( event ) {
			if ( isWhatsApp ) {
				event.preventDefault();
				pushToDataLayer();
				openWhatsApp();
			} else {
				pushToDataLayer();

				if ( settings.actionType === 'iframe_modal' ) {
					openIframeModal();
				} else {
					openLink();
				}
			}
		} );
	}

	if ( backdrop ) {
		backdrop.addEventListener( 'click', closeModal );
	}
	if ( closeBtn ) {
		closeBtn.addEventListener( 'click', closeModal );
	}

	window.VZFLTY_showButton = function () {
		if ( button ) {
			button.style.display = '';
		}
	};
	window.VZFLTY_hideButton = function () {
		if ( button ) {
			button.style.display = 'none';
		}
	};
} )();
