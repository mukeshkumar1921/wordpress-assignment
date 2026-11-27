import { getContext, getElement, store, withSyncEvent } from '@wordpress/interactivity';

let pending;
function select( context, choice, listing ) {
	context.current = choice;
	context.title = context.titles[ choice ];
	context.hasResults = Boolean( context.counts[ choice ] );
	document.title = context.title + ' – ' + context.siteName;
	listing.querySelectorAll( '[data-filter]' ).forEach( ( link ) => {
		if ( link.dataset.filter === choice ) {
			link.setAttribute( 'aria-current', 'page' );
		} else {
			link.removeAttribute( 'aria-current' );
		}
	} );
}

store( 'aberdeen/programmes', {
	actions: {
		filter: withSyncEvent( ( event ) => {
			const link = getElement().ref;
			if ( event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey ) {
				return;
			}
			const context = getContext();
			const choice = link.dataset.filter;
			event.preventDefault();
			if ( choice === context.current && ! context.loading ) {
				return;
			}
			clearTimeout( pending );
			context.loading = true;
			pending = setTimeout( () => {
				if ( location.href !== link.href ) {
					history.pushState( { programmeType: choice }, '', link.href );
				}
				select( context, choice, link.closest( 'main' ) );
				context.loading = false;
			}, 100 );
		} ),
	},
	callbacks: {
		watchHistory() {
			const context = getContext();
			const listing = getElement().ref;
			window.addEventListener( 'popstate', () => {
				const choice = Object.keys( context.urls ).find( ( key ) =>
					new URL( context.urls[ key ], location.href ).href === location.href
				);
				if ( choice ) {
					clearTimeout( pending );
					context.loading = false;
					select( context, choice, listing );
				}
			} );
		},
	},
} );
