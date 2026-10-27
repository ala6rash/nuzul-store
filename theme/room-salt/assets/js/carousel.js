/* Room & Salt product slider: native horizontal scroll with snap, arrows and a page counter. Works in RTL. */
( function () {
	document.querySelectorAll( '[data-rs-carousel]' ).forEach( function ( root ) {
		var track = root.querySelector( '[data-track]' );
		var count = root.querySelector( '[data-count]' );
		var btns = root.querySelectorAll( '[data-dir]' );
		var rtl = getComputedStyle( track ).direction === 'rtl';

		function pages() {
			return Math.max( 1, Math.ceil( ( track.scrollWidth - 4 ) / track.clientWidth ) );
		}
		function page() {
			return Math.min( pages(), Math.round( Math.abs( track.scrollLeft ) / track.clientWidth ) + 1 );
		}
		function update() {
			var n = pages(), p = page();
			count.textContent = p + '/' + n;
			btns[ 0 ].disabled = p <= 1;
			btns[ 1 ].disabled = p >= n;
			root.classList.toggle( 'is-single', n === 1 );
		}
		btns.forEach( function ( b ) {
			b.addEventListener( 'click', function () {
				var step = track.clientWidth * Number( b.dataset.dir );
				track.scrollBy( { left: rtl ? -step : step, behavior: 'smooth' } );
			} );
		} );
		track.addEventListener( 'scroll', function () {
			window.requestAnimationFrame( update );
		}, { passive: true } );
		window.addEventListener( 'resize', update );
		update();
	} );
}() );
