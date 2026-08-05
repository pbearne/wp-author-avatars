/**
 * BLOCK: author-avatars
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './style.scss';
import './editor.scss';
import metadata from './block.json';

import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	RadioControl,
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
	Spinner,
	TextControl,
	RangeControl,
	ColorPicker,
	CheckboxControl,
	TextareaControl,
} from '@wordpress/components';
import {
	InspectorControls,
	InspectorAdvancedControls,
	BlockControls,
	AlignmentToolbar,
	PanelColorSettings,
	useBlockProps,
} from '@wordpress/block-editor';
import { Fragment, useState, useEffect } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import _ServerSideRender from '@wordpress/server-side-render';

const PanelColorSettingsEditor = PanelColorSettings || wp.editor?.PanelColorSettings || (() => null);
const ServerSideRender = _ServerSideRender || wp.components?.ServerSideRender || (() => null);
const UnitControl = wp.components?.__experimentalUnitControl || wp.components?.UnitControl || (() => null);
const BoxControl = wp.components?.__experimentalBoxControl || wp.components?.BoxControl || (() => null);
const BorderControl = wp.components?.__experimentalBorderControl || wp.components?.BorderControl || (() => null);
const BorderRadiusControl = wp.components?.__experimentalBorderRadiusControl || wp.components?.BorderRadiusControl || (() => null);

const MultiCheckboxControl = ( { label, options, selected, onChange } ) => (
	<Fragment>
		{ label && <label className="blocks-base-control__label">{ label }</label> }
		<ul>
			{ options?.map( ( v ) => (
				<li key={ v.value }>
					<CheckboxControl
						className="check_items"
						label={ v.label }
						checked={ !! selected[ v.value ] }
						onChange={ ( check ) => {
							const newSelected = { ...selected };
							if ( check ) {
								newSelected[ v.value ] = true;
							} else {
								delete newSelected[ v.value ];
							}
							onChange( newSelected );
						} }
					/>
				</li>
			) ) }
		</ul>
	</Fragment>
);

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
/**
 * Edit component for the block.
 */
const Edit = ( props ) => {
	const {
		isSelected, attributes, setAttributes
	} = props;

	const blockProps = useBlockProps();

	const [ blockData, setBlockData ] = useState( {
		user_options: [],
		display_options: [],
		user_roles: [],
		user_links: [],
		sort_list: [],
		blogs_list: [],
		DonateButton: '',
		loading: true
	} );

	useEffect( () => {
		apiFetch( { path: '/author_avatar/blocks/v1/data' } ).then( data => {
			setBlockData( {
				user_options: data?.users || [],
				display_options: data?.display_options || [],
				user_roles: data?.roles || [],
				user_links: data?.links || [],
				sort_list: data?.sort_avatars_by || [],
				blogs_list: data?.blogs || [],
				DonateButton: data?.donate || '',
				loading: false
			} );
		} ).catch( () => {
			setBlockData( prev => ( { ...prev, loading: false } ) );
		} );
	}, [] );

	const {
		user_options, display_options, user_roles, user_links, sort_list, blogs_list, DonateButton, loading
	} = blockData;

	const {
		background_color, font_color, link_color, link_hover_color,
		user_id, email, link, sort_avatars_by, sort_order,
		size, bio_length, page_size, min_post_count, whitelist_users,
		hidden_users, preview, limit, alignment
	} = attributes;

	// Normalize attributes that might be in legacy formats (e.g. from old blocks)
	const normalizeToMap = ( val ) => {
		if ( Array.isArray( val ) ) {
			return val.reduce( ( acc, v ) => ( { ...acc, [ v ]: true } ), {} );
		}
		if ( typeof val === 'boolean' || ! val ) {
			return {};
		}
		return val;
	};

	const display = normalizeToMap( attributes.display );
	const role = normalizeToMap( attributes.role );
	const blogs = normalizeToMap( attributes.blogs );

	if ( preview ) {
		return (
			<div { ...blockProps }>
				<img className="author-avatars-preview" src={ window.authorAvatars?.wppic_preview } alt="Preview" />
			</div>
		);
	}

	return (
		<Fragment>
			<InspectorControls key="inspector">
				<div className="author-avatar-components-panel">
					{ loading && <Spinner /> }
					<SelectControl
						label={ __( 'User or Email addrerss/user_id or Roles', 'author-avatar' ) }
						name="user_id"
						value={ user_id }
						options={ user_options }
						onChange={ ( val ) => setAttributes( { user_id: val } ) }
					/>
					{ -1 == user_id && (
						<TextControl
							label="Custom email / id"
							type="text"
							value={ email }
							onChange={ ( val ) => setAttributes( { email: val } ) }
						/>
					) }
					{ 0 == user_id && (
						<MultiCheckboxControl
							label={ __( 'Which Roles to display:', 'author-avatar' ) }
							options={ user_roles }
							selected={ role }
							onChange={ ( val ) => setAttributes( { role: val } ) }
						/>
					) }
					<MultiCheckboxControl
						label={ __( 'Info to show with avatar:', 'author-avatar' ) }
						options={ display_options }
						selected={ display }
						onChange={ ( val ) => setAttributes( { display: val } ) }
					/>

					<SelectControl
						label={ __( 'Sort by', 'author-avatar' ) }
						value={ sort_avatars_by }
						options={ sort_list }
						onChange={ ( val ) => setAttributes( { sort_avatars_by: val } ) }
					/>

					<SelectControl
						label={ __( 'Sort order', 'author-avatar' ) }
						value={ sort_order }
						options={ [
							{ label: 'Ascending', value: 'asc' },
							{ label: 'Descending', value: 'desc' },
						] }
						onChange={ ( val ) => setAttributes( { sort_order: val } ) }
					/>

					<PanelBody title={ __( 'Avatar Card Styles', 'author-avatars' ) } initialOpen={ true }>
						<SelectControl
							label={ __( 'Link avatars to', 'author-avatar' ) }
							value={ link }
							options={ user_links }
							onChange={ ( val ) => setAttributes( { link: val } ) }
						/>
						<PanelColorSettingsEditor
							title={ __( 'Card Colors', 'author-avatars' ) }
							initialOpen={ false }
							colorSettings={ [
								{
									value: background_color,
									onChange: ( val ) => setAttributes( { background_color: val } ),
									label: __( 'Background Color', 'author-avatars' ),
								},
								{
									value: font_color,
									onChange: ( val ) => setAttributes( { font_color: val } ),
									label: __( 'Font Color', 'author-avatars' ),
								},
								{
									value: link_color,
									onChange: ( val ) => setAttributes( { link_color: val } ),
									label: __( 'Link Color', 'author-avatars' ),
								},
								{
									value: link_hover_color,
									onChange: ( val ) => setAttributes( { link_hover_color: val } ),
									label: __( 'Link Hover Color', 'author-avatars' ),
								},
							] }
						/>
						<div className="author-avatars-border-control-wrapper">
							<BorderControl
								label={ __( 'Border', 'author-avatars' ) }
								value={ attributes.card_border }
								onChange={ ( value ) => setAttributes( { card_border: value } ) }
							/>
						</div>
						<BorderRadiusControl
							label={ __( 'Border Radius', 'author-avatars' ) }
							values={ attributes.card_border_radius }
							onChange={ ( value ) => setAttributes( { card_border_radius: value } ) }
						/>
						<BoxControl
							__next40pxDefaultSize={ true }
							label={ __( 'Padding', 'author-avatars' ) }
							values={ attributes.avatar_padding }
							onChange={ ( value ) => setAttributes( { avatar_padding: value } ) }
						/>
						<BoxControl
							__next40pxDefaultSize={ true }
							label={ __( 'Margin', 'author-avatars' ) }
							values={ attributes.avatar_margin }
							onChange={ ( value ) => setAttributes( { avatar_margin: value } ) }
						/>

						<PanelRow className="author-avatars-inline-unit-controls">
							<UnitControl
								__next40pxDefaultSize={ true }
								label={ __( 'Min Width', 'author-avatars' ) }
								value={ attributes.card_min_width }
								onChange={ ( value ) => setAttributes( { card_min_width: value } ) }
							/>
							<UnitControl
								__next40pxDefaultSize={ true }
								label={ __( 'Max Width', 'author-avatars' ) }
								value={ attributes.card_max_width }
								onChange={ ( value ) => setAttributes( { card_max_width: value } ) }
							/>
						</PanelRow>
						<PanelRow className="author-avatars-inline-unit-controls">
							<UnitControl
								__next40pxDefaultSize={ true }
								label={ __( 'Min Height', 'author-avatars' ) }
								value={ attributes.card_min_height }
								onChange={ ( value ) => setAttributes( { card_min_height: value } ) }
							/>
							<UnitControl
								__next40pxDefaultSize={ true }
								label={ __( 'Max Height', 'author-avatars' ) }
								value={ attributes.card_max_height }
								onChange={ ( value ) => setAttributes( { card_max_height: value } ) }
							/>
						</PanelRow>
					</PanelBody>

					<Fragment>
						<div dangerouslySetInnerHTML={ { __html: DonateButton } } />
					</Fragment>
					<div>
						<label className="blocks-base-control__label">
							{ __( 'More options in Adavanced:', 'author-avatar' ) }
						</label>
					</div>
				</div>
			</InspectorControls>

			<InspectorAdvancedControls key="advanced">
				{ true === display?.show_biography && (
					<RangeControl
						label="bio_length"
						value={ bio_length }
						onChange={ ( val ) => setAttributes( { bio_length: val } ) }
						min={ 10 }
						max={ 200 }
						initialPosition={ 50 }
					/>
				) }
				{ 0 == user_id && (
					<Fragment>
						<TextControl
							label={ __( 'Max. avatars shown:', 'author-avatar' ) }
							type="number"
							value={ limit }
							onChange={ ( val ) => setAttributes( { limit: parseInt( val ) } ) }
						/>

						<TextControl
							label={ __( 'Max. avatars per page:', 'author-avatar' ) }
							type="number"
							value={ page_size }
							onChange={ ( val ) => setAttributes( { page_size: parseInt( val ) } ) }
						/>

						<TextControl
							label={ __( 'Required number of posts:', 'author-avatar' ) }
							type="number"
							value={ min_post_count }
							onChange={ ( val ) => setAttributes( { min_post_count: parseInt( val ) } ) }
						/>

						<TextareaControl
							label={ __( 'Hidden users', 'author-avatar' ) }
							help={ __( '(Comma separate list of user login ids. Hidden user are removed before the white list)', 'author-avatar' ) }
							value={ hidden_users }
							onChange={ ( val ) => setAttributes( { hidden_users: val } ) }
						/>

						<TextareaControl
							label={ __( 'White List of users:', 'author-avatar' ) }
							help={ __( '(0nly show these users, Comma separate list of user login ids)', 'author-avatar' ) }
							value={ whitelist_users }
							onChange={ ( val ) => setAttributes( { whitelist_users: val } ) }
						/>

						<MultiCheckboxControl
							label={ __( 'Blogs to display from:', 'author-avatar' ) }
							options={ blogs_list }
							selected={ blogs }
							onChange={ ( val ) => setAttributes( { blogs: val } ) }
						/>
					</Fragment>
				) }
			</InspectorAdvancedControls>

			<div { ...blockProps }>
				{ !! isSelected && (
					<BlockControls>
						<AlignmentToolbar
							value={ alignment }
							onChange={ ( val ) => setAttributes( { alignment: val } ) }
						/>
					</BlockControls>
				) }
				{ loading ? <Spinner /> : <ServerSideRender block="author-avatars/show-avatar" attributes={ attributes } /> }
			</div>
		</Fragment>
	);
};

registerBlockType( metadata.name, {
	edit: Edit,


	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	save:

		function (props) {
			// Rendering in PHP
			return null;
		},
});

