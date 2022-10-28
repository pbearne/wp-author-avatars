/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./src/show-avatar/block.js":
/*!**********************************!*\
  !*** ./src/show-avatar/block.js ***!
  \**********************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _style_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./style.scss */ "./src/show-avatar/style.scss");
/* harmony import */ var _editor_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./editor.scss */ "./src/show-avatar/editor.scss");

/**
 * BLOCK: author-avatars
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.


const {
  __
} = wp.i18n; // Import __() from wp.i18n
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
  ColorPicker,
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
const {
  withSelect,
  setState
} = wp.data;
const {
  serverSideRender: ServerSideRender
} = wp;
const {
  Fragment
} = wp.element;
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
  title: __('Avatar Lists', 'author-avatars'),
  // Block title.
  icon: 'businessman',
  // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
  category: 'common',
  // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
  keywords: [__('avatar', 'author-avatars'), __('Author Avatars', 'author-avatars'), __('profile pictures', 'author-avatars')],
  example: {
    attributes: {
      'preview': true
    }
  },
  attributes: {
    size: {
      type: 'init',
      default: 50
    },
    email: {
      type: 'string'
    },
    alignment: {
      type: 'string',
      default: 'left'
    },
    Options: {
      type: 'array'
    },
    link: {
      type: 'string'
    },
    display: {
      type: 'object'
    },
    role: {
      type: 'object'
    },
    blogs: {
      type: 'object'
    },
    sort_avatars_by: {
      type: 'string',
      default: 'display_name'
    },
    sort_order: {
      type: 'string'
    },
    bio_length: {
      type: 'init',
      default: 50
    },
    user_id: {
      type: 'init',
      default: 0
    },
    limit: {
      type: 'number'
    },
    page_size: {
      type: 'number'
    },
    min_post_count: {
      type: 'number'
    },
    hidden_users: {
      type: 'string'
    },
    whitelist_users: {
      type: 'string'
    },
    background_color: {
      type: 'string',
      default: '#fff' // Default value for newly added block
    },

    font_color: {
      type: 'string',
      default: '#000' // Default value for newly added block
    },

    border_size: {
      type: 'number',
      default: '0' // Default value for newly added block
    },

    border_color: {
      type: 'string',
      default: '#000' // Default value for newly added block
    },

    border_radius: {
      type: 'number',
      default: '0' // Default value for newly added block
    },

    // To storage the complete style of the div that will be 'merged' with the selected colours
    block_style: {
      type: 'string',
      selector: 'div',
      // From tag a
      source: 'attribute',
      // binds an attribute of the tag
      attribute: 'style' // binds style of a: the dynamic colours
    }
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
  edit: withSelect(select => {
    return {
      data: wp.apiFetch({
        path: '/author_avatar/blocks/v1/data'
      }).then(data => {
        // console.log(data);
        user_options = data.users;
        display_options = data.display_options;
        user_roles = data.roles;
        user_links = data.links;
        sort_list = data.sort_avatars_by;
        blogs_list = data.blogs;
        DonateButton = data.donate;

        // return data;
      })
    };
  })(props => {
    const {
      className,
      user,
      data,
      isSelected,
      attributes,
      setAttributes
    } = props;
    var background_color = props.attributes.background_color;
    var font_color = props.attributes.font_color;
    var border_size = props.attributes.border_size;
    var border_color = props.attributes.border_color;
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
    const {
      alignment
    } = attributes;

    // Style object for the button
    // I created a style in JSX syntax to keep it here for
    // the dynamic changes
    var block_style = props.attributes.block_style; // To bind the style of the button
    block_style = {
      backgroundColor: background_color,
      color: font_color,
      borderColor: border_color,
      borderWidth: border_size + 'px',
      padding: '14px 25px',
      fontSize: '16px'
    };

    // /wp-json/wp/v2/users

    //
    // onChange event functions
    //
    function onChangeBgColor(content) {
      props.setAttributes({
        background_color: content
      });
    }
    function onChangeFontColor(content) {
      props.setAttributes({
        font_color: content
      });
    }
    function onChangeBorderColor(content) {
      props.setAttributes({
        border_color: content
      });
    }
    function onChangeBorderSize(content) {
      props.setAttributes({
        border_size: content
      });
    }
    function onChangelink(content) {
      props.setAttributes({
        link: content
      });
    }
    function onChangeUser(content) {
      props.setAttributes({
        user_id: content
      });
    }
    function onChangeEmail(content) {
      props.setAttributes({
        email: content
      });
    }
    function onChangeSize(content) {
      props.setAttributes({
        size: content
      });
    }
    function onChangeLimit(content) {
      props.setAttributes({
        limit: content
      });
    }
    function onChangeMinPosts(content) {
      props.setAttributes({
        min_post_count: content
      });
    }
    function onChangebio_length(content) {
      props.setAttributes({
        bio_length: content
      });
    }
    function onChangeSortOrder(content) {
      props.setAttributes({
        sort_order: content
      });
    }
    function onChangeSortBy(content) {
      props.setAttributes({
        sort_avatars_by: content
      });
    }
    function onChangePageSize(content) {
      props.setAttributes({
        page_size: content
      });
    }
    function onChangeHiddenUsers(content) {
      props.setAttributes({
        hidden_users: content
      });
    }
    function onChangeWhitelistUsers(content) {
      props.setAttributes({
        whitelist_users: content
      });
    }
    function onChangeAlignment(updatedAlignment) {
      props.setAttributes({
        alignment: updatedAlignment
      });
    }
    function onChangeBorderRadius(content) {
      props.setAttributes({
        border_radius: content
      });
    }
    const display = 'display' in attributes ? attributes.display : new Object();
    const DisplayCheckBoxes = wp.compose.withState({
      checked_obj: Object.assign(new Object(), display)
    })(_ref => {
      var _display_options;
      let {
        checked_obj,
        setState
      } = _ref;
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", null, (_display_options = display_options) === null || _display_options === void 0 ? void 0 : _display_options.map(v => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
        key: v.value
      }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
        className: "check_items",
        label: v.label,
        checked: checked_obj[v.value],
        onChange: check => {
          check ? checked_obj[v.value] = true : delete checked_obj[v.value];
          setAttributes({
            display: checked_obj
          });
          setState({
            checked_obj
          });
        }
      }))));
    });
    const role = 'role' in attributes ? attributes.role : new Object();
    const RolesCheckBoxes = wp.compose.withState({
      checked_obj: Object.assign(new Object(), role)
    })(_ref2 => {
      var _user_roles;
      let {
        checked_obj,
        setState
      } = _ref2;
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", null, (_user_roles = user_roles) === null || _user_roles === void 0 ? void 0 : _user_roles.map(v => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
        key: v.value
      }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
        className: "check_items",
        label: v.label,
        checked: checked_obj[v.value],
        onChange: check => {
          check ? checked_obj[v.value] = true : delete checked_obj[v.value];
          setAttributes({
            role: checked_obj
          });
          setState({
            checked_obj
          });
        }
      }))));
    });
    const blogs = 'blogs' in attributes ? attributes.blogs : new Object();
    const BlogsCheckBoxes = wp.compose.withState({
      checked_obj: Object.assign(new Object(), role)
    })(_ref3 => {
      var _blogs_list;
      let {
        checked_obj,
        setState
      } = _ref3;
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("ul", null, (_blogs_list = blogs_list) === null || _blogs_list === void 0 ? void 0 : _blogs_list.map(v => (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("li", {
        key: v.value
      }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(CheckboxControl, {
        className: "check_items",
        label: v.label,
        checked: checked_obj[v.value],
        onChange: check => {
          check ? checked_obj[v.value] = true : delete checked_obj[v.value];
          setAttributes({
            blogs: checked_obj
          });
          setState({
            checked_obj
          });
        }
      }))));
    });

    //
    // let statusXX = users=>status();
    // console.log( statusXX );
    // if( statusXX ){
    // 	users.forEach((user) => {
    // 		options.push({value:user.value, label:user.label});
    // 	});
    // }
    if (preview) {
      return (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
        className: "author-avatars-preview",
        src: authorAvatars.wppic_preview
      }));
    }

    // if we have no tax set for the page then just show a messege to save a call to server side
    // if (0 === users.length) {
    // 	return <p>{__('Select the resort in the sidebar, 'mvc' ) }</p>;
    // 	}
    // the server side block with the tax object getting passed
    return [(0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(InspectorControls, {
      key: '000'
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: "author-avatar-components-panel"
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(SelectControl, {
      label: __('User or Email addrerss/user_id or Roles', 'author-avatar'),
      name: "user_id",
      value: user_id,
      options: user_options,
      onChange: onChangeUser
    }), -1 == user_id && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: "Custom email / id",
      type: 'text',
      value: email,
      onChange: onChangeEmail
    }), 0 == user_id && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
      className: "blocks-base-control__label"
    }, __('Which Roles to display:', 'author-avatar')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(RolesCheckBoxes, null)), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
      className: "blocks-base-control__label"
    }, __('Info to show with avatar:', 'author-avatar')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(DisplayCheckBoxes, null), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(SelectControl, {
      label: __('Link avatars to', 'author-avatar'),
      value: link,
      options: user_links,
      onChange: onChangelink
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(SelectControl, {
      label: __('Sort by', 'author-avatar'),
      value: sort_avatars_by,
      options: sort_list,
      onChange: onChangeSortBy
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(SelectControl, {
      label: __('Sort order', 'author-avatar'),
      value: sort_order,
      options: [{
        label: 'Ascending',
        value: 'asc'
      }, {
        label: 'Descending',
        value: 'desc'
      }],
      onChange: onChangeSortOrder
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(RangeControl, {
      label: "Avatar Size",
      value: size,
      onChange: onChangeSize,
      min: 10,
      max: 500,
      initialPosition: 50,
      beforeIcon: 'businessman'
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(RangeControl, {
      label: "Avatar Corner size",
      value: border_radius,
      onChange: onChangeBorderRadius,
      min: 0,
      max: 50,
      initialPosition: 0,
      beforeIcon: 'buddicons-buddypress-logo'
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
      className: "blocks-base-control__label"
    }, __('Background color', 'author-avatar')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ColorPicker // Element Tag for Gutenberg standard colour selector
    , {
      color: background_color,
      enableAlpha: true,
      label: __('Background color', 'author-avatar'),
      defaultValue: "#000",
      onChange: onChangeBgColor // onChange event callback
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
      className: "blocks-base-control__label"
    }, __('Font color', 'author-avatar')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ColorPicker // Element Tag for Gutenberg standard colour selector
    , {
      color: font_color,
      label: __('Font color', 'author-avatar'),
      title: __('Font color', 'author-avatar'),
      defaultValue: "#fff",
      onChange: onChangeFontColor // onChange event callback
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(RangeControl, {
      label: "Border size",
      value: border_size,
      onChange: onChangeBorderSize,
      min: 0,
      max: 50,
      initialPosition: 0,
      beforeIcon: 'buddicons-buddypress-logo'
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
      className: "blocks-base-control__label"
    }, __('Border color', 'author-avatar')), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ColorPicker // Element Tag for Gutenberg standard colour selector
    , {
      color: border_color,
      label: __('Font color', 'author-avatar'),
      title: __('Font color', 'author-avatar'),
      defaultValue: "#fff",
      onChange: onChangeBorderColor // onChange event callback
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
      className: 'donate',
      href: 'https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=MZTZ5S8MGF75C&lc=CA&item_name=Author%20Avatars%20Plugin%20Support&item_number=authoravatars&currency_code=CAD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted',
      target: '_donante'
    }, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("img", {
      alt: 'Donate to support Plugin',
      src: "https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif"
    }))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
      className: "blocks-base-control__label"
    }, __('More options in Adavanced:', 'author-avatar'))))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(InspectorAdvancedControls, {
      key: '111'
    }, true === display.show_biography && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(RangeControl, {
      label: "bio_length",
      value: bio_length,
      onChange: onChangebio_length,
      min: 10,
      max: 200,
      initialPosition: 50
    }), 0 == user_id && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(Fragment, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Max. avatars shown:', 'author-avatar'),
      type: 'number',
      value: limit,
      name: 'limit',
      onChange: onChangeLimit
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Max. avatars per page:', 'author-avatar'),
      type: 'number',
      value: page_size,
      name: 'limit',
      onChange: onChangePageSize
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextControl, {
      label: __('Required number of posts:', 'author-avatar'),
      type: 'number',
      value: min_post_count,
      name: 'limit',
      onChange: onChangeMinPosts
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextareaControl, {
      label: __('Hidden users', 'author-avatar'),
      help: __('(Comma separate list of user login ids. Hidden user are removed before the white list)', 'author-avatar'),
      value: hidden_users,
      onChange: onChangeHiddenUsers
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(TextareaControl, {
      label: __('White List of users:', 'author-avatar'),
      help: __('(0nly show these users, Comma separate list of user login ids)', 'author-avatar'),
      value: whitelist_users,
      onChange: onChangeWhitelistUsers
    }), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(BlogsCheckBoxes, null))), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
      className: className,
      style: block_style,
      key: '222'
    }, !!focus && (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(BlockControls, null, (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(AlignmentToolbar, {
      value: alignment,
      onChange: onChangeAlignment
    })), (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createElement)(ServerSideRender, {
      block: "author-avatars/show-avatar",
      attributes: attributes
    }))];
  }),
  /**
   * The save function defines the way in which the different attributes should be combined
   * into the final markup, which is then serialized by Gutenberg into post_content.
   *
   * The "save" property must be specified and must be a valid function.
   *
   * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
   */
  save: function (props) {
    // Rendering in PHP
    return null;
  }
});

/***/ }),

/***/ "./src/show-avatar/editor.scss":
/*!*************************************!*\
  !*** ./src/show-avatar/editor.scss ***!
  \*************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./src/show-avatar/style.scss":
/*!************************************!*\
  !*** ./src/show-avatar/style.scss ***!
  \************************************/
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ (function(module) {

module.exports = window["wp"]["element"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	!function() {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = function(result, chunkIds, fn, priority) {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var chunkIds = deferred[i][0];
/******/ 				var fn = deferred[i][1];
/******/ 				var priority = deferred[i][2];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every(function(key) { return __webpack_require__.O[key](chunkIds[j]); })) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	!function() {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"show-avatar/block": 0,
/******/ 			"show-avatar/style-block": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = function(chunkId) { return installedChunks[chunkId] === 0; };
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = function(parentChunkLoadingFunction, data) {
/******/ 			var chunkIds = data[0];
/******/ 			var moreModules = data[1];
/******/ 			var runtime = data[2];
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some(function(id) { return installedChunks[id] !== 0; })) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunkavator_list_block"] = self["webpackChunkavator_list_block"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	}();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["show-avatar/style-block"], function() { return __webpack_require__("./src/show-avatar/block.js"); })
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;
//# sourceMappingURL=block.js.map