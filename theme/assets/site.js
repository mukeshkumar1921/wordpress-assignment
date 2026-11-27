document.addEventListener( 'click', ( event ) => {
	const target = event.target.closest?.( '[data-track]' );
	if ( ! target || ! window.aberdeen?.track ) {
		return;
	}
	let data;
	try {
		data = JSON.parse( target.dataset.track );
	} catch {
		return;
	}
	const body = JSON.stringify( { ...data, page_url: location.pathname } );
	const queued = navigator.sendBeacon?.( window.aberdeen.track, new Blob( [ body ], { type: 'application/json' } ) );
	if ( ! queued ) {
		fetch( window.aberdeen.track, {
			method: 'POST', body, keepalive: true, headers: { 'Content-Type': 'application/json' },
		} ).catch( () => {} );
	}
}, { capture: true } );

const dialog = document.querySelector( '.enquiry' );
if ( dialog ) {
	const form = dialog.querySelector( 'form' );
	const success = dialog.querySelector( '[data-enquiry-success]' );
	const error = dialog.querySelector( '[role="alert"]' );
	const submit = form.querySelector( '[type="submit"]' );
	const level = form.elements.level;
	const programme = form.elements.programme;
	let opener;

	function filterProgrammes() {
		for ( const option of programme.options ) {
			option.hidden = Boolean( option.value ) && ! option.dataset.levels.split( ' ' ).includes( level.value );
			option.disabled = option.hidden;
		}
		if ( programme.selectedOptions[0]?.disabled ) {
			programme.value = '';
		}
	}

	level.addEventListener( 'change', filterProgrammes );
	document.addEventListener( 'click', ( event ) => {
		const trigger = event.target.closest?.( '[data-open-enquiry]' );
		if ( ! trigger || dialog.open ) {
			return;
		}
		event.preventDefault();
		opener = trigger;
		error.hidden = true;
		success.hidden = true;
		form.hidden = false;
		const option = [...programme.options].find( ( item ) => item.value === dialog.dataset.programme );
		if ( option ) {
			level.value = option.dataset.levels.split( ' ' )[0];
		}
		filterProgrammes();
		if ( option && ! option.disabled ) {
			programme.value = option.value;
		}
		dialog.showModal();
	} );
	dialog.querySelector( '[data-close-enquiry]' ).addEventListener( 'click', () => dialog.close() );
	dialog.addEventListener( 'close', () => opener?.focus() );
	form.addEventListener( 'submit', async ( event ) => {
		event.preventDefault();
		error.hidden = true;
		submit.disabled = true;
		try {
			const response = await fetch( window.aberdeen.lead, {
				method: 'POST', headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify( Object.fromEntries( new FormData( form ) ) ),
			} );
			if ( ! response.ok ) {
				const problem = await response.json();
				throw new Error( problem.message || 'Could not submit your registration.' );
			}
			form.reset();
			form.hidden = true;
			success.hidden = false;
			success.focus();
		} catch ( problem ) {
			error.textContent = problem.message || 'Could not submit your registration.';
			error.hidden = false;
		} finally {
			submit.disabled = false;
		}
	} );
}

const carousel = document.querySelector( '[data-carousel]' );
if ( carousel ) {
	const slides = [...carousel.querySelectorAll( '[data-slide]' )];
	const dots = [...carousel.querySelectorAll( '[data-slide-index]' )];
	let index = 0;
	carousel.addEventListener( 'click', ( event ) => {
		const control = event.target.closest( '[data-slide-step], [data-slide-index]' );
		if ( ! control ) {
			return;
		}
		index = control.hasAttribute( 'data-slide-index' )
			? Number( control.dataset.slideIndex )
			: ( index + Number( control.dataset.slideStep ) + slides.length ) % slides.length;
		slides.forEach( ( slide, item ) => { slide.hidden = item !== index; } );
		dots.forEach( ( dot, item ) => dot.setAttribute( 'aria-pressed', item === index ) );
	} );
}
