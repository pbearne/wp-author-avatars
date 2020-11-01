/**
 * BLOCK: author-avatars
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './style.scss';
import './editor.scss';

const {__} = wp.i18n; // Import __() from wp.i18n
const {
	registerBlockType
} = wp.blocks; // Import registerBlockType() from wp.blocks
const {
	RadioControl,
	Panel,
	PanelBody,
	PanelRow,
	SelectControl,
	Spinner,
	TextControl,
	RangeControl,
	ColorPalette,
	PanelColorSettings,
	CheckboxControl,
	TextareaControl
} = wp.components;
const {
	InspectorControls,
	InspectorAdvancedControls,
	BlockControls,
	AlignmentToolbar
} = wp.blockEditor;
const {withSelect, setState} = wp.data;
const {serverSideRender: ServerSideRender} = wp;
const {Fragment} = wp.element;

let user_options = [];
let display_options = [];
let user_roles = [];
let user_links = [];
let sort_list = [];
let blogs_list = [];
let DonateButton = '';

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
registerBlockType('author-avatars/show-avatar', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __('Avatar Lists', 'author-avatars'), // Block title.
	icon: 'businessman', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__('avatar', 'author-avatars'),
		__('Author Avatars', 'author-avatars'),
		__('profile pictures', 'author-avatars'),
	],
    example: {
        attributes: {
            'preview' : true,
        },
    },
	attributes: {
		size: {
			type: 'init',
			default: 50,
		},
		email: {
			type: 'string',
		},
		alignment: {
			type: 'string',
			default: 'left',
		},
		Options: {
			type: 'array',
		},
		link: {
			type: 'string',
		},
		display: {
			type: 'object',
		},
		role: {
			type: 'object',
		},
		blogs: {
			type: 'object',
		},
		sort_avatars_by: {
			type: 'string',
			default: 'display_name',
		},
		sort_order: {
			type: 'string',
		},
		bio_length: {
			type: 'init',
			default: 50,
		},
		user_id: {
			type: 'init',
			default: 0,
		},
		limit: {
			type: 'number',
		},
		page_size: {
			type: 'number',
		},
		min_post_count: {
			type: 'number',
		},
		hidden_users: {
			type: 'string',
		},
		whitelist_users: {
			type: 'string',
		},
		background_color: {
			type: 'string',
			default: '#fff', // Default value for newly added block
		},
		font_color: {
			type: 'string',
			default: '#000', // Default value for newly added block
		},
		border_radius: {
			type: 'number',
			default: '0', // Default value for newly added block
		},
		// To storage the complete style of the div that will be 'merged' with the selected colours
		block_style: {
            type: 'string',
			selector: 'div', // From tag a
			source: 'attribute', // binds an attribute of the tag
			attribute: 'style', // binds style of a: the dynamic colours
		},

	},
	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 */
	//   edit: function (props, attributes, className) {
	// Creates a <p class='wp-block-cgb-block-author-avatars'></p>.
	edit:
		withSelect(select => {
			return {
				data: wp.apiFetch({path: '/author_avatar/blocks/v1/data'}).then(data => {
						// console.log(data);
						user_options = data.users;
						display_options = data.display_options;
						user_roles = data.roles;
						user_links = data.links;
						sort_list = data.sort_avatars_by;
						blogs_list = data.blogs;
						DonateButton = data.donate;

						// return data;
					}
				)
			};
		})
		((props) => {
			const {
				className, user, data, isSelected, attributes, setAttributes
            } = props;
			var background_color = props.attributes.background_color;
			var font_color = props.attributes.font_color;
			var user_id = props.attributes.user_id;
			var email = props.attributes.email;
			var link = props.attributes.link;
			var sort_avatars_by = props.attributes.sort_avatars_by;
			var sort_order = props.attributes.sort_order;
			var border_radius = props.attributes.border_radius;
			var size = props.attributes.size;
			var bio_length = props.attributes.bio_length;
			var page_size = props.attributes.page_size;
			var min_post_count = props.attributes.min_post_count;
			var whitelist_users = props.attributes.whitelist_users;
            var hidden_users = props.attributes.hidden_users;
            var preview = props.attributes.preview;

			var limit = props.attributes.limit;
			const {alignment} = attributes;

			// Style object for the button
			// I created a style in JSX syntax to keep it here for
			// the dynamic changes
			var block_style = props.attributes.block_style // To bind the style of the button
			block_style = {
				backgroundColor: background_color,
				color: font_color,
				padding: '14px 25px',
				fontSize: '16px',
			};


			// /wp-json/wp/v2/users

			//
			// onChange event functions
			//
			function onChangeBgColor(content) {
				props.setAttributes({background_color: content})
			}

			function onChangeFontColor(content) {
				props.setAttributes({font_color: content})
			}

			function onChangelink(content) {
				props.setAttributes({link: content})
			}

			function onChangeUser(content) {
				props.setAttributes({user_id: content})
			}

			function onChangeEmail(content) {
				props.setAttributes({email: content})
			}

			function onChangeSize(content) {
				props.setAttributes({size: content})
			}

			function onChangeLimit(content) {
				props.setAttributes({limit: content})
			}

			function onChangeMinPosts(content) {
				props.setAttributes({min_post_count: content})
			}

			function onChangebio_length(content) {
				props.setAttributes({bio_length: content})
			}

			function onChangeSortOrder(content) {
				props.setAttributes({sort_order: content})
			}

			function onChangeSortBy(content) {
				props.setAttributes({sort_avatars_by: content})
			}

			function onChangePageSize(content) {
				props.setAttributes({page_size: content})
			}

			function onChangeHiddenUsers(content) {
				props.setAttributes({hidden_users: content})
			}

			function onChangeWhitelistUsers(content) {
				props.setAttributes({whitelist_users: content})
			}

			function onChangeAlignment(updatedAlignment) {
				props.setAttributes({alignment: updatedAlignment});
			}

			function onChangeBorderRadius(content) {
				props.setAttributes({border_radius: content});
			}


			const display = ('display' in attributes) ? attributes.display : new Object
			const DisplayCheckBoxes = wp.compose.withState({
				checked_obj: Object.assign(new Object, display)
			})(({checked_obj, setState}) => (
				<ul>
					{
						display_options.map((v) => (
							<li key={v.value}><CheckboxControl
								className="check_items"
								label={v.label}
								checked={checked_obj[v.value]}
								onChange={(check) => {
									check ? checked_obj[v.value] = true : delete checked_obj[v.value]
									setAttributes({display: checked_obj})
									setState({checked_obj})
								}}
							/></li>
						))
					}
				</ul>
			))

			const role = ('role' in attributes) ? attributes.role : new Object
			const RolesCheckBoxes = wp.compose.withState({
				checked_obj: Object.assign(new Object, role)
			})(({checked_obj, setState}) => (
				<ul>
					{
						user_roles.map((v) => (
							<li key={v.value}><CheckboxControl
								className="check_items"
								label={v.label}
								checked={checked_obj[v.value]}
								onChange={(check) => {
									check ? checked_obj[v.value] = true : delete checked_obj[v.value]
									setAttributes({role: checked_obj})
									setState({checked_obj})
								}}
							/></li>
						))
					}
				</ul>
			))

			const blogs = ('blogs' in attributes) ? attributes.blogs : new Object
			const BlogsCheckBoxes = wp.compose.withState({
				checked_obj: Object.assign(new Object, role)
			})(({checked_obj, setState}) => (
				<ul>
					{
						blogs_list.map((v) => (
							<li key={v.value}><CheckboxControl
								className="check_items"
								label={v.label}
								checked={checked_obj[v.value]}
								onChange={(check) => {
									check ? checked_obj[v.value] = true : delete checked_obj[v.value]
									setAttributes({blogs: checked_obj})
									setState({checked_obj})
								}}
							/></li>
						))
					}
				</ul>
			))

			//
			// let statusXX = users=>status();
			// console.log( statusXX );
			// if( statusXX ){
			// 	users.forEach((user) => {
			// 		options.push({value:user.value, label:user.label});
			// 	});
			// }
            if ( preview ) {
                return(
                    <Fragment>
                        <img className="author-avatars-preview" src={authorAvatars.wppic_preview} />
                    </Fragment>
                );
            }

			// if we have no tax set for the page then just show a messege to save a call to server side
			// if (0 === users.length) {
			// 	return <p>{__('Select the resort in the sidebar, 'mvc' ) }</p>;
			// 	}
			// the server side block with the tax object getting passed
			return [

				<InspectorControls key={'000'}>
					<div className="author-avatar-components-panel">


					<label className="blocks-base-control__label">{__('Background color', 'author-avatar')}</label>
					<ColorPalette  // Element Tag for Gutenberg standard colour selector
						label={__('Background color', 'author-avatar')}
						onChange={onChangeBgColor} // onChange event callback
					/>
					<label className="blocks-base-control__label">{__('Font color', 'author-avatar')}</label>
					<ColorPalette  // Element Tag for Gutenberg standard colour selector
						label={__('Font color', 'author-avatar')}
						title={__('Font color', 'author-avatar')}
						onChange={onChangeFontColor} // onChange event callback
					/>


					<SelectControl
						label={__('User or Email addrerss/user_id or Roles', 'author-avatar')}
						name='user_id'
                        value={user_id}
						options={user_options}
						onChange={onChangeUser}
					/>
					{-1 == user_id && (

						<TextControl
							label='Custom email / id'
							type={'text'}
							value={email}
							onChange={onChangeEmail}
						/>

					)}
					{0 == user_id && (
						<Fragment>
							<label
								className="blocks-base-control__label">{__('Which Roles to display:', 'author-avatar')}</label>

							<RolesCheckBoxes/>
						</Fragment>
					)}
					<label
						className="blocks-base-control__label">{__('Info to show with avatar:', 'author-avatar')}</label>
					<DisplayCheckBoxes/>


					<SelectControl
						label={__('Link avatars to', 'author-avatar')}
						value={link}
						options={user_links}
						onChange={onChangelink}
					/>

					<SelectControl
						label={__('Sort by', 'author-avatar')}
						value={sort_avatars_by}
						options={sort_list}
						onChange={onChangeSortBy}
					/>

					<SelectControl
						label={__('Sort order', 'author-avatar')}
						value={sort_order}
						options={[
							{label: 'Ascending', value: 'asc'},
							{label: 'Descending', value: 'desc'},
						]}
						onChange={onChangeSortOrder}
					/>

					<RangeControl
						label="Size"
						value={size}
						onChange={onChangeSize}
						min={10}
						max={500}
						initialPosition={50}
						beforeIcon={'businessman'}
					/>

					<RangeControl
						label="Corner size"
						value={border_radius}
						onChange={onChangeBorderRadius}
						min={0}
						max={50}
						initialPosition={0}
						beforeIcon={'buddicons-buddypress-logo'}
					/>
					<Fragment>
						<a className={'donate'} href={'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=MZTZ5S8MGF75C&lc=CA&item_name=Author%20Avatars%20Plugin%20Support&item_number=authoravatars&currency_code=CAD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted'}
						   target={'_donante'}>
							<img alt={'Donate to support Plugin'}
								 src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif"/>
						</a>
					</Fragment>
					<div>
						<label
							className="blocks-base-control__label">{__('More options in Adavanced:', 'author-avatar')}</label>
					</div>
					</div>
				</InspectorControls>,

				<InspectorAdvancedControls key={'111'}>
					{true === display.show_biography && (
						<RangeControl
							label="bio_length"
							value={bio_length}
							onChange={onChangebio_length}
							min={10}
							max={200}
							initialPosition={50}
						/>
					)}
					{0 == user_id && (
						<Fragment>
							<TextControl
								label={__('Max. avatars shown:', 'author-avatar')}
								type={'number'}
								value={limit}
								name={'limit'}
								onChange={onChangeLimit}
							/>

							<TextControl
								label={__('Max. avatars per page:', 'author-avatar')}
								type={'number'}
								value={page_size}
								name={'limit'}
								onChange={onChangePageSize}
							/>

							<TextControl
								label={__('Required number of posts:', 'author-avatar')}
								type={'number'}
								value={min_post_count}
								name={'limit'}
								onChange={onChangeMinPosts}
							/>

							<TextareaControl
								label={__('Hidden users', 'author-avatar')}
								help={__('(Comma separate list of user login ids. Hidden user are removed before the white list)', 'author-avatar')}
								value={hidden_users}
								onChange={onChangeHiddenUsers}
							/>

							<TextareaControl
								label={__('White List of users:', 'author-avatar')}
								help={__('(0nly show these users, Comma separate list of user login ids)', 'author-avatar')}
								value={whitelist_users}
								onChange={onChangeWhitelistUsers}
							/>

							<BlogsCheckBoxes/>
						</Fragment>
					)}

				</InspectorAdvancedControls>,


				<div className={className} style={block_style} key={'222'}>
					{
						!!focus && (
							<BlockControls>
								<AlignmentToolbar
									value={alignment}
									onChange={onChangeAlignment}
								/>
							</BlockControls>
						)
					}

					<ServerSideRender block="author-avatars/show-avatar" attributes={attributes}/>
				</div>
			];

		}),


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

