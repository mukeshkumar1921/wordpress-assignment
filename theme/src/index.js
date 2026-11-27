import { createRoot, useState } from '@wordpress/element';

const blank = () => ( { title: '', description: '', image: 0, thumbnail: '' } );
let nextId = 0;
const withId = ( row ) => ( { ...row, id: ++nextId, expanded: true } );

function ProgrammeEditor( { initial } ) {
	const [ year, setYear ] = useState( initial.year );
	const [ duration, setDuration ] = useState( initial.duration );
	const [ rows, setRows ] = useState( () => initial.sections.map( withId ) );
	const update = ( id, values ) => setRows( ( current ) =>
		current.map( ( row ) => row.id === id ? { ...row, ...values } : row )
	);
	const move = ( index, offset ) => setRows( ( current ) => {
		const reordered = [ ...current ];
		const [ row ] = reordered.splice( index, 1 );
		reordered.splice( index + offset, 0, row );
		return reordered;
	} );
	const selectImage = ( id ) => {
		const frame = window.wp.media( {
			title: 'Choose a section image',
			button: { text: 'Use image' },
			library: { type: 'image' },
			multiple: false,
		} );
		frame.on( 'select', () => {
			const attachment = frame.state().get( 'selection' ).first().toJSON();
			update( id, { image: attachment.id, thumbnail: attachment.sizes?.thumbnail?.url || attachment.url } );
		} );
		frame.open();
	};

	return (
		<div className="programme-editor">
			<input type="hidden" name="aberdeen_programme_fields" value={ JSON.stringify( {
				year, duration, sections: rows.map( ( { title, description, image } ) => ( { title, description, image } ) ),
			} ) } />
			<div className="editor-fields">
				<label>Academic year <input value={ year } placeholder="2025–26" maxLength="80" onChange={ ( event ) => setYear( event.target.value ) } /></label>
				<label>Duration <input value={ duration } placeholder="3 Years" maxLength="80" onChange={ ( event ) => setDuration( event.target.value ) } /></label>
			</div>
			<h3>Content sections</h3>
			{ ! rows.length && <p>Add sections for topics such as entry requirements and careers.</p> }
			{ rows.map( ( row, index ) => (
				<section key={ row.id } className="editor-section">
					<div className="editor-toolbar">
						<button type="button" aria-expanded={ row.expanded } aria-controls={ 'programme-section-' + row.id } onClick={ () => update( row.id, { expanded: ! row.expanded } ) }>
							{ row.title || 'Section ' + ( index + 1 ) }
						</button>
						<button type="button" disabled={ index === 0 } aria-label="Move section up" onClick={ () => move( index, -1 ) }>↑</button>
						<button type="button" disabled={ index === rows.length - 1 } aria-label="Move section down" onClick={ () => move( index, 1 ) }>↓</button>
						<button type="button" onClick={ () => setRows( ( current ) => current.filter( ( item ) => item.id !== row.id ) ) }>Remove</button>
					</div>
					<div hidden={ ! row.expanded } id={ 'programme-section-' + row.id } className="editor-body">
						<label>Title <input value={ row.title } maxLength="120" onChange={ ( event ) => update( row.id, { title: event.target.value } ) } /></label>
						<label>Description <textarea rows="4" value={ row.description } maxLength="2500" onChange={ ( event ) => update( row.id, { description: event.target.value } ) } /></label>
						{ row.thumbnail && <img src={ row.thumbnail } alt="" /> }
						<button type="button" onClick={ () => selectImage( row.id ) }>{ row.image ? 'Replace image' : 'Choose image' }</button>
						{ row.image > 0 && <button type="button" onClick={ () => update( row.id, { image: 0, thumbnail: '' } ) }>Remove image</button> }
					</div>
				</section>
			) ) }
			<button className="editor-add" type="button" disabled={ rows.length >= 40 } onClick={ () => setRows( ( current ) => [ ...current, withId( blank() ) ] ) }>Add section</button>
		</div>
	);
}

const node = document.getElementById( 'aberdeen-programme-editor' );
if ( node ) {
	createRoot( node ).render( <ProgrammeEditor initial={ JSON.parse( node.dataset.initial ) } /> );
}
