/*!
 * modernizr v3.0.0-alpha.3
 * Build http://v3.modernizr.com/download/#-backgroundsize-bgsizecover-checked-contenteditable-contextmenu-cookies-cssanimations-csscalc-cssfilters-cssgradients-csstransforms-csstransforms3d-cssvhunit-cssvwunit-datauri-draganddrop-fileinput-filereader-flexbox-fontface-generatedcontent-hashchange-history-input-inputtypes-localstorage-notification-nthchild-opacity-sessionstorage-svg-textshadow-shiv-dontmin
 *
 * Copyright (c)
 *  Faruk Ates
 *  Paul Irish
 *  Alex Sexton
 *  Ryan Seddon
 *  Alexander Farkas
 *  Patrick Kettner
 *  Stu Cox
 *  Richard Herrera

 * MIT License
 */

/*
 * Modernizr tests which native CSS3 and HTML5 features are available in the
 * current UA and makes the results available to you in two ways: as properties on
 * a global `Modernizr` object, and as classes on the `<html>` element. This
 * information allows you to progressively enhance your pages with a granular level
 * of control over the experience.
 */

;(function(window, document, undefined){
	var classes = [];


	// Take the html5 variable out of the
	// html5shiv scope so we can return it.
	var html5;
	/**
	 * @preserve HTML5 Shiv 3.7.2 | @afarkas @jdalton @jon_neal @rem | MIT/GPL2 Licensed
	 */
	;(function(window, document) {
		/*jshint evil:true */
		/** version */
		var version = '3.7.2';

		/** Preset options */
		var options = window.html5 || {};

		/** Used to skip problem elements */
		var reSkip = /^<|^(?:button|map|select|textarea|object|iframe|option|optgroup)$/i;

		/** Not all elements can be cloned in IE **/
		var saveClones = /^(?:a|b|code|div|fieldset|h1|h2|h3|h4|h5|h6|i|label|li|ol|p|q|span|strong|style|table|tbody|td|th|tr|ul)$/i;

		/** Detect whether the browser supports default html5 styles */
		var supportsHtml5Styles;

		/** Name of the expando, to work with multiple documents or to re-shiv one document */
		var expando = '_html5shiv';

		/** The id for the the documents expando */
		var expanID = 0;

		/** Cached data for each document */
		var expandoData = {};

		/** Detect whether the browser supports unknown elements */
		var supportsUnknownElements;

		(function() {
			try {
				var a = document.createElement('a');
				a.innerHTML = '<xyz></xyz>';
				//if the hidden property is implemented we can assume, that the browser supports basic HTML5 Styles
				supportsHtml5Styles = ('hidden' in a);

				supportsUnknownElements = a.childNodes.length == 1 || (function() {
						// assign a false positive if unable to shiv
						(document.createElement)('a');
						var frag = document.createDocumentFragment();
						return (
							typeof frag.cloneNode == 'undefined' ||
							typeof frag.createDocumentFragment == 'undefined' ||
							typeof frag.createElement == 'undefined'
						);
					}());
			} catch(e) {
				// assign a false positive if detection fails => unable to shiv
				supportsHtml5Styles = true;
				supportsUnknownElements = true;
			}

		}());

		/*--------------------------------------------------------------------------*/

		/**
		 * Creates a style sheet with the given CSS text and adds it to the document.
		 * @private
		 * @param {Document} ownerDocument The document.
		 * @param {String} cssText The CSS text.
		 * @returns {StyleSheet} The style element.
		 */
		function addStyleSheet(ownerDocument, cssText) {
			var p = ownerDocument.createElement('p'),
				parent = ownerDocument.getElementsByTagName('head')[0] || ownerDocument.documentElement;

			p.innerHTML = 'x<style>' + cssText + '</style>';
			return parent.insertBefore(p.lastChild, parent.firstChild);
		}

		/**
		 * Returns the value of `html5.elements` as an array.
		 * @private
		 * @returns {Array} An array of shived element node names.
		 */
		function getElements() {
			var elements = html5.elements;
			return typeof elements == 'string' ? elements.split(' ') : elements;
		}

		/**
		 * Extends the built-in list of html5 elements
		 * @memberOf html5
		 * @param {String|Array} newElements whitespace separated list or array of new element names to shiv
		 * @param {Document} ownerDocument The context document.
		 */
		function addElements(newElements, ownerDocument) {
			var elements = html5.elements;
			if(typeof elements != 'string'){
				elements = elements.join(' ');
			}
			if(typeof newElements != 'string'){
				newElements = newElements.join(' ');
			}
			html5.elements = elements +' '+ newElements;
			shivDocument(ownerDocument);
		}

		/**
		 * Returns the data associated to the given document
		 * @private
		 * @param {Document} ownerDocument The document.
		 * @returns {Object} An object of data.
		 */
		function getExpandoData(ownerDocument) {
			var data = expandoData[ownerDocument[expando]];
			if (!data) {
				data = {};
				expanID++;
				ownerDocument[expando] = expanID;
				expandoData[expanID] = data;
			}
			return data;
		}

		/**
		 * returns a shived element for the given nodeName and document
		 * @memberOf html5
		 * @param {String} nodeName name of the element
		 * @param {Document} ownerDocument The context document.
		 * @returns {Object} The shived element.
		 */
		function createElement(nodeName, ownerDocument, data){
			if (!ownerDocument) {
				ownerDocument = document;
			}
			if(supportsUnknownElements){
				return ownerDocument.createElement(nodeName);
			}
			if (!data) {
				data = getExpandoData(ownerDocument);
			}
			var node;

			if (data.cache[nodeName]) {
				node = data.cache[nodeName].cloneNode();
			} else if (saveClones.test(nodeName)) {
				node = (data.cache[nodeName] = data.createElem(nodeName)).cloneNode();
			} else {
				node = data.createElem(nodeName);
			}

			// Avoid adding some elements to fragments in IE < 9 because
			// * Attributes like `name` or `type` cannot be set/changed once an element
			//   is inserted into a document/fragment
			// * Link elements with `src` attributes that are inaccessible, as with
			//   a 403 response, will cause the tab/window to crash
			// * Script elements appended to fragments will execute when their `src`
			//   or `text` property is set
			return node.canHaveChildren && !reSkip.test(nodeName) && !node.tagUrn ? data.frag.appendChild(node) : node;
		}

		/**
		 * returns a shived DocumentFragment for the given document
		 * @memberOf html5
		 * @param {Document} ownerDocument The context document.
		 * @returns {Object} The shived DocumentFragment.
		 */
		function createDocumentFragment(ownerDocument, data){
			if (!ownerDocument) {
				ownerDocument = document;
			}
			if(supportsUnknownElements){
				return ownerDocument.createDocumentFragment();
			}
			data = data || getExpandoData(ownerDocument);
			var clone = data.frag.cloneNode(),
				i = 0,
				elems = getElements(),
				l = elems.length;
			for(;i<l;i++){
				clone.createElement(elems[i]);
			}
			return clone;
		}

		/**
		 * Shivs the `createElement` and `createDocumentFragment` methods of the document.
		 * @private
		 * @param {Document|DocumentFragment} ownerDocument The document.
		 * @param {Object} data of the document.
		 */
		function shivMethods(ownerDocument, data) {
			if (!data.cache) {
				data.cache = {};
				data.createElem = ownerDocument.createElement;
				data.createFrag = ownerDocument.createDocumentFragment;
				data.frag = data.createFrag();
			}


			ownerDocument.createElement = function(nodeName) {
				//abort shiv
				if (!html5.shivMethods) {
					return data.createElem(nodeName);
				}
				return createElement(nodeName, ownerDocument, data);
			};

			ownerDocument.createDocumentFragment = Function('h,f', 'return function(){' +
				'var n=f.cloneNode(),c=n.createElement;' +
				'h.shivMethods&&(' +
					// unroll the `createElement` calls
				getElements().join().replace(/[\w\-:]+/g, function(nodeName) {
					data.createElem(nodeName);
					data.frag.createElement(nodeName);
					return 'c("' + nodeName + '")';
				}) +
				');return n}'
			)(html5, data.frag);
		}

		/*--------------------------------------------------------------------------*/

		/**
		 * Shivs the given document.
		 * @memberOf html5
		 * @param {Document} ownerDocument The document to shiv.
		 * @returns {Document} The shived document.
		 */
		function shivDocument(ownerDocument) {
			if (!ownerDocument) {
				ownerDocument = document;
			}
			var data = getExpandoData(ownerDocument);

			if (html5.shivCSS && !supportsHtml5Styles && !data.hasCSS) {
				data.hasCSS = !!addStyleSheet(ownerDocument,
					// corrects block display not defined in IE6/7/8/9
					'article,aside,dialog,figcaption,figure,footer,header,hgroup,main,nav,section{display:block}' +
						// adds styling not present in IE6/7/8/9
					'mark{background:#FF0;color:#000}' +
						// hides non-rendered elements
					'template{display:none}'
				);
			}
			if (!supportsUnknownElements) {
				shivMethods(ownerDocument, data);
			}
			return ownerDocument;
		}

		/*--------------------------------------------------------------------------*/

		/**
		 * The `html5` object is exposed so that more elements can be shived and
		 * existing shiving can be detected on iframes.
		 * @type Object
		 * @example
		 *
		 * // options can be changed before the script is included
		 * html5 = { 'elements': 'mark section', 'shivCSS': false, 'shivMethods': false };
		 */
		var html5 = {

			/**
			 * An array or space separated string of node names of the elements to shiv.
			 * @memberOf html5
			 * @type Array|String
			 */
			'elements': options.elements || 'abbr article aside audio bdi canvas data datalist details dialog figcaption figure footer header hgroup main mark meter nav output picture progress section summary template time video',

			/**
			 * current version of html5shiv
			 */
			'version': version,

			/**
			 * A flag to indicate that the HTML5 style sheet should be inserted.
			 * @memberOf html5
			 * @type Boolean
			 */
			'shivCSS': (options.shivCSS !== false),

			/**
			 * Is equal to true if a browser supports creating unknown/HTML5 elements
			 * @memberOf html5
			 * @type boolean
			 */
			'supportsUnknownElements': supportsUnknownElements,

			/**
			 * A flag to indicate that the document's `createElement` and `createDocumentFragment`
			 * methods should be overwritten.
			 * @memberOf html5
			 * @type Boolean
			 */
			'shivMethods': (options.shivMethods !== false),

			/**
			 * A string to describe the type of `html5` object ("default" or "default print").
			 * @memberOf html5
			 * @type String
			 */
			'type': 'default',

			// shivs the document according to the specified `html5` object options
			'shivDocument': shivDocument,

			//creates a shived element
			createElement: createElement,

			//creates a shived documentFragment
			createDocumentFragment: createDocumentFragment,

			//extends list of elements
			addElements: addElements
		};

		/*--------------------------------------------------------------------------*/

		// expose html5
		window.html5 = html5;

		// shiv the document
		shivDocument(document);

	}(this, document));


	var tests = [];


	var ModernizrProto = {
		// The current version, dummy
		_version: '3.0.0-alpha.3',

		// Any settings that don't work as separate modules
		// can go in here as configuration.
		_config: {
			'classPrefix' : '',
			'enableClasses' : true,
			'enableJSClass' : true,
			'usePrefixes' : true
		},

		// Queue of tests
		_q: [],

		// Stub these for people who are listening
		on: function( test, cb ) {
			// I don't really think people should do this, but we can
			// safe guard it a bit.
			// -- NOTE:: this gets WAY overridden in src/addTest for
			// actual async tests. This is in case people listen to
			// synchronous tests. I would leave it out, but the code
			// to *disallow* sync tests in the real version of this
			// function is actually larger than this.
			var self = this;
			setTimeout(function() {
				cb(self[test]);
			}, 0);
		},

		addTest: function( name, fn, options ) {
			tests.push({name : name, fn : fn, options : options });
		},

		addAsyncTest: function (fn) {
			tests.push({name : null, fn : fn});
		}
	};



	// Fake some of Object.create
	// so we can force non test results
	// to be non "own" properties.
	var Modernizr = function(){};
	Modernizr.prototype = ModernizrProto;

	// Leak modernizr globally when you `require` it
	// rather than force it here.
	// Overwrite name so constructor name is nicer :D
	Modernizr = new Modernizr();


	/*!
	 {
	 "name": "Cookies",
	 "property": "cookies",
	 "tags": ["storage"],
	 "authors": ["tauren"]
	 }
	 !*/
	/* DOC
	 Detects whether cookie support is enabled.
	 */

	// https://github.com/Modernizr/Modernizr/issues/191

	Modernizr.addTest('cookies', function () {
		// navigator.cookieEnabled cannot detect custom or nuanced cookie blocking
		// configurations. For example, when blocking cookies via the Advanced
		// Privacy Settings in IE9, it always returns true. And there have been
		// issues in the past with site-specific exceptions.
		// Don't rely on it.

		// try..catch because some in situations `document.cookie` is exposed but throws a
		// SecurityError if you try to access it; e.g. documents created from data URIs
		// or in sandboxed iframes (depending on flags/context)
		try {
			// Create cookie
			document.cookie = 'cookietest=1';
			var ret = document.cookie.indexOf('cookietest=') != -1;
			// Delete cookie
			document.cookie = 'cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT';
			return ret;
		}
		catch (e) {
			return false;
		}
	});

	/*!
	 {
	 "name": "File API",
	 "property": "filereader",
	 "caniuse": "fileapi",
	 "notes": [{
	 "name": "W3C Working Draft",
	 "href": "http://www.w3.org/TR/FileAPI/"
	 }],
	 "tags": ["file"],
	 "builderAliases": ["file_api"],
	 "knownBugs": ["Will fail in Safari 5 due to its lack of support for the standards defined FileReader object"]
	 }
	 !*/
	/* DOC
	 `filereader` tests for the File API specification

	 Tests for objects specific to the File API W3C specification without
	 being redundant (don't bother testing for Blob since it is assumed
	 to be the File object's prototype.)
	 */

	Modernizr.addTest('filereader', !!(window.File && window.FileList && window.FileReader));

	/*!
	 {
	 "name": "History API",
	 "property": "history",
	 "caniuse": "history",
	 "tags": ["history"],
	 "authors": ["Hay Kranen", "Alexander Farkas"],
	 "notes": [{
	 "name": "W3C Spec",
	 "href": "http://www.w3.org/TR/html51/browsers.html#the-history-interface"
	 }, {
	 "name": "MDN documentation",
	 "href": "https://developer.mozilla.org/en-US/docs/Web/API/window.history"
	 }],
	 "polyfills": ["historyjs", "html5historyapi"]
	 }
	 !*/
	/* DOC
	 Detects support for the History API for manipulating the browser session history.
	 */

	Modernizr.addTest('history', function() {
		// Issue #733
		// The stock browser on Android 2.2 & 2.3, and 4.0.x returns positive on history support
		// Unfortunately support is really buggy and there is no clean way to detect
		// these bugs, so we fall back to a user agent sniff :(
		var ua = navigator.userAgent;

		// We only want Android 2 and 4.0, stock browser, and not Chrome which identifies
		// itself as 'Mobile Safari' as well, nor Windows Phone (issue #1471).
		if ((ua.indexOf('Android 2.') !== -1 ||
			(ua.indexOf('Android 4.0') !== -1)) &&
			ua.indexOf('Mobile Safari') !== -1 &&
			ua.indexOf('Chrome') === -1 &&
			ua.indexOf('Windows Phone') === -1) {
			return false;
		}

		// Return the regular check
		return (window.history && 'pushState' in window.history);
	});

	/*!
	 {
	 "name": "Local Storage",
	 "property": "localstorage",
	 "caniuse": "namevalue-storage",
	 "tags": ["storage"],
	 "knownBugs": [],
	 "notes": [],
	 "warnings": [],
	 "polyfills": [
	 "joshuabell-polyfill",
	 "cupcake",
	 "storagepolyfill",
	 "amplifyjs",
	 "yui-cacheoffline"
	 ]
	 }
	 !*/

	// In FF4, if disabled, window.localStorage should === null.

	// Normally, we could not test that directly and need to do a
	//   `('localStorage' in window) && ` test first because otherwise Firefox will
	//   throw bugzil.la/365772 if cookies are disabled

	// Also in iOS5 Private Browsing mode, attempting to use localStorage.setItem
	// will throw the exception:
	//   QUOTA_EXCEEDED_ERRROR DOM Exception 22.
	// Peculiarly, getItem and removeItem calls do not throw.

	// Because we are forced to try/catch this, we'll go aggressive.

	// Just FWIW: IE8 Compat mode supports these features completely:
	//   www.quirksmode.org/dom/html5.html
	// But IE8 doesn't support either with local files

	Modernizr.addTest('localstorage', function() {
		var mod = 'modernizr';
		try {
			localStorage.setItem(mod, mod);
			localStorage.removeItem(mod);
			return true;
		} catch(e) {
			return false;
		}
	});

	/*!
	 {
	 "name": "Notification",
	 "property": "notification",
	 "caniuse": "notifications",
	 "authors": ["Theodoor van Donge", "Hendrik Beskow"],
	 "notes": [{
	 "name": "HTML5 Rocks tutorial",
	 "href": "http://www.html5rocks.com/en/tutorials/notifications/quick/"
	 },{
	 "name": "W3C spec",
	 "href": "www.w3.org/TR/notifications/"
	 }],
	 "polyfills": ["desktop-notify", "html5-notifications"]
	 }
	 !*/
	/* DOC
	 Detects support for the Notifications API
	 */

	Modernizr.addTest('notification', 'Notification' in window && 'permission' in window.Notification && 'requestPermission' in window.Notification);

	/*!
	 {
	 "name": "SVG",
	 "property": "svg",
	 "caniuse": "svg",
	 "tags": ["svg"],
	 "authors": ["Erik Dahlstrom"],
	 "polyfills": [
	 "svgweb",
	 "raphael",
	 "amplesdk",
	 "canvg",
	 "svg-boilerplate",
	 "sie",
	 "dojogfx",
	 "fabricjs"
	 ]
	 }
	 !*/
	/* DOC
	 Detects support for SVG in `<embed>` or `<object>` elements.
	 */

	Modernizr.addTest('svg', !!document.createElementNS && !!document.createElementNS('http://www.w3.org/2000/svg', 'svg').createSVGRect);

	/*!
	 {
	 "name": "Session Storage",
	 "property": "sessionstorage",
	 "tags": ["storage"],
	 "polyfills": ["joshuabell-polyfill", "cupcake", "sessionstorage"]
	 }
	 !*/

	// Because we are forced to try/catch this, we'll go aggressive.

	// Just FWIW: IE8 Compat mode supports these features completely:
	//   www.quirksmode.org/dom/html5.html
	// But IE8 doesn't support either with local files
	Modernizr.addTest('sessionstorage', function() {
		var mod = 'modernizr';
		try {
			sessionStorage.setItem(mod, mod);
			sessionStorage.removeItem(mod);
			return true;
		} catch(e) {
			return false;
		}
	});


	var docElement = document.documentElement;


	// Pass in an and array of class names, e.g.:
	//  ['no-webp', 'borderradius', ...]
	function setClasses( classes ) {
		var className = docElement.className;
		var classPrefix = Modernizr._config.classPrefix || '';

		// Change `no-js` to `js` (we do this independently of the `enableClasses`
		// option)
		// Handle classPrefix on this too
		if(Modernizr._config.enableJSClass) {
			var reJS = new RegExp('(^|\\s)'+classPrefix+'no-js(\\s|$)');
			className = className.replace(reJS, '$1'+classPrefix+'js$2');
		}

		if(Modernizr._config.enableClasses) {
			// Add the new classes
			className += ' ' + classPrefix + classes.join(' ' + classPrefix);
			docElement.className = className;
		}

	}

	;
	/*!
	 {
	 "name": "Context menus",
	 "property": "contextmenu",
	 "caniuse": "menu",
	 "notes": [{
	 "name": "W3C spec",
	 "href": "http://www.w3.org/TR/html5/interactive-elements.html#context-menus"
	 },{
	 "name": "thewebrocks.com Demo",
	 "href": "http://thewebrocks.com/demos/context-menu/"
	 }],
	 "polyfills": ["jquery-contextmenu"]
	 }
	 !*/
	/* DOC
	 Detects support for custom context menus.
	 */

	Modernizr.addTest(
		'contextmenu',
		('contextMenu' in docElement && 'HTMLMenuItemElement' in window)
	);


	/**
	 * is returns a boolean for if typeof obj is exactly type.
	 */
	function is( obj, type ) {
		return typeof obj === type;
	}
	;

	// Run through all tests and detect their support in the current UA.
	function testRunner() {
		var featureNames;
		var feature;
		var aliasIdx;
		var result;
		var nameIdx;
		var featureName;
		var featureNameSplit;

		for ( var featureIdx in tests ) {
			featureNames = [];
			feature = tests[featureIdx];
			// run the test, throw the return value into the Modernizr,
			//   then based on that boolean, define an appropriate className
			//   and push it into an array of classes we'll join later.
			//
			//   If there is no name, it's an 'async' test that is run,
			//   but not directly added to the object. That should
			//   be done with a post-run addTest call.
			if ( feature.name ) {
				featureNames.push(feature.name.toLowerCase());

				if (feature.options && feature.options.aliases && feature.options.aliases.length) {
					// Add all the aliases into the names list
					for (aliasIdx = 0; aliasIdx < feature.options.aliases.length; aliasIdx++) {
						featureNames.push(feature.options.aliases[aliasIdx].toLowerCase());
					}
				}
			}

			// Run the test, or use the raw value if it's not a function
			result = is(feature.fn, 'function') ? feature.fn() : feature.fn;


			// Set each of the names on the Modernizr object
			for (nameIdx = 0; nameIdx < featureNames.length; nameIdx++) {
				featureName = featureNames[nameIdx];
				// Support dot properties as sub tests. We don't do checking to make sure
				// that the implied parent tests have been added. You must call them in
				// order (either in the test, or make the parent test a dependency).
				//
				// Cap it to TWO to make the logic simple and because who needs that kind of subtesting
				// hashtag famous last words
				featureNameSplit = featureName.split('.');

				if (featureNameSplit.length === 1) {
					Modernizr[featureNameSplit[0]] = result;
				} else {
					// cast to a Boolean, if not one already
					/* jshint -W053 */
					if (Modernizr[featureNameSplit[0]] && !(Modernizr[featureNameSplit[0]] instanceof Boolean)) {
						Modernizr[featureNameSplit[0]] = new Boolean(Modernizr[featureNameSplit[0]]);
					}

					Modernizr[featureNameSplit[0]][featureNameSplit[1]] = result;
				}

				classes.push((result ? '' : 'no-') + featureNameSplit.join('-'));
			}
		}
	}

	;

	var createElement = function() {
		if (typeof document.createElement !== 'function') {
			// This is the case in IE7, where the type of createElement is "object".
			// For this reason, we cannot call apply() as Object is not a Function.
			return document.createElement(arguments[0]);
		} else {
			return document.createElement.apply(document, arguments);
		}
	};

	/*!
	 {
	 "name": "Content Editable",
	 "property": "contenteditable",
	 "caniuse": "contenteditable",
	 "notes": [{
	 "name": "WHATWG spec",
	 "href": "http://www.whatwg.org/specs/web-apps/current-work/multipage/editing.html#contenteditable"
	 }]
	 }
	 !*/
	/* DOC
	 Detects support for the `contenteditable` attribute of elements, allowing their DOM text contents to be edited directly by the user.
	 */

	Modernizr.addTest('contenteditable', function() {
		// early bail out
		if (!('contentEditable' in docElement)) return;

		// some mobile browsers (android < 3.0, iOS < 5) claim to support
		// contentEditable, but but don't really. This test checks to see
		// confirms whether or not it actually supports it.

		var div = createElement('div');
		div.contentEditable = true;
		return div.contentEditable === 'true';
	});

	/*!
	 {
	 "name": "Drag & Drop",
	 "property": "draganddrop",
	 "caniuse": "dragndrop",
	 "knownBugs": ["Mobile browsers like Android, iOS < 6, and Firefox OS technically support the APIs, but don't expose it to the end user, resulting in a false positive."],
	 "notes": [{
	 "name": "W3C spec",
	 "href": "http://www.w3.org/TR/2010/WD-html5-20101019/dnd.html"
	 }],
	 "polyfills": ["dropfile", "moxie", "fileapi"]
	 }
	 !*/
	/* DOC
	 Detects support for native drag & drop of elements.
	 */

	Modernizr.addTest('draganddrop', function() {
		var div = createElement('div');
		return ('draggable' in div) || ('ondragstart' in div && 'ondrop' in div);
	});

	/*!
	 {
	 "name": "input[file] Attribute",
	 "property": "fileinput",
	 "caniuse" : "forms",
	 "tags": ["file", "forms", "input"],
	 "builderAliases": ["forms_fileinput"]
	 }
	 !*/
	/* DOC
	 Detects whether input type="file" is available on the platform

	 E.g. iOS < 6 and some android version don't support this
	 */

	Modernizr.addTest('fileinput', function() {
		if(navigator.userAgent.match(/(Android (1.0|1.1|1.5|1.6|2.0|2.1))|(Windows Phone (OS 7|8.0))|(XBLWP)|(ZuneWP)|(w(eb)?OSBrowser)|(webOS)|(Kindle\/(1.0|2.0|2.5|3.0))/)) {
			return false;
		}
		var elem = createElement('input');
		elem.type = 'file';
		return !elem.disabled;
	});

	/*!
	 {
	 "name": "CSS Supports",
	 "property": "supports",
	 "caniuse": "css-featurequeries",
	 "tags": ["css"],
	 "builderAliases": ["css_supports"],
	 "notes": [{
	 "name": "W3 Spec",
	 "href": "http://dev.w3.org/csswg/css3-conditional/#at-supports"
	 },{
	 "name": "Related Github Issue",
	 "href": "github.com/Modernizr/Modernizr/issues/648"
	 },{
	 "name": "W3 Info",
	 "href": "http://dev.w3.org/csswg/css3-conditional/#the-csssupportsrule-interface"
	 }]
	 }
	 !*/

	var newSyntax = 'CSS' in window && 'supports' in window.CSS;
	var oldSyntax = 'supportsCSS' in window;
	Modernizr.addTest('supports', newSyntax || oldSyntax);


	// List of property values to set for css tests. See ticket #21
	var prefixes = (ModernizrProto._config.usePrefixes ? ' -webkit- -moz- -o- -ms- '.split(' ') : []);

	// expose these for the plugin API. Look in the source for how to join() them against your input
	ModernizrProto._prefixes = prefixes;


	/*!
	 {
	 "name": "CSS Calc",
	 "property": "csscalc",
	 "caniuse": "calc",
	 "tags": ["css"],
	 "builderAliases": ["css_calc"],
	 "authors": ["@calvein"]
	 }
	 !*/
	/* DOC
	 Method of allowing calculated values for length units. For example:

	 ```css
	 #elem {
	 width: calc(100% - 3em);
	 }
	 ```
	 */

	Modernizr.addTest('csscalc', function() {
		var prop = 'width:';
		var value = 'calc(10px);';
		var el = createElement('div');

		el.style.cssText = prop + prefixes.join(value + prop);

		return !!el.style.length;
	});

	/*!
	 {
	 "name": "CSS Filters",
	 "property": "cssfilters",
	 "caniuse": "css-filters",
	 "polyfills": ["polyfilter"],
	 "tags": ["css"],
	 "builderAliases": ["css_filters"],
	 "notes": [{
	 "name": "MDN article on CSS filters",
	 "href": "https://developer.mozilla.org/en-US/docs/Web/CSS/filter"
	 }]
	 }
	 !*/

	// https://github.com/Modernizr/Modernizr/issues/615
	// documentMode is needed for false positives in oldIE, please see issue above
	Modernizr.addTest('cssfilters', function() {
		var el = createElement('div');
		el.style.cssText = prefixes.join('filter:blur(2px); ');
		if (Modernizr.supports) {
			var supports = 'CSS' in window ?
				window.CSS.supports('filter', 'url()') :
				window.supportsCSS('filter', 'url()');

			// older firefox only supports `url` filters;
			return supports;
		} else {
			return !!el.style.length && ((document.documentMode === undefined || document.documentMode > 9));
		}
	});


	/*!
	 {
	 "name": "CSS Gradients",
	 "caniuse": "css-gradients",
	 "property": "cssgradients",
	 "tags": ["css"],
	 "knownBugs": ["False-positives on webOS (https://github.com/Modernizr/Modernizr/issues/202)"],
	 "notes": [{
	 "name": "Webkit Gradient Syntax",
	 "href": "http://webkit.org/blog/175/introducing-css-gradients/"
	 },{
	 "name": "Mozilla Linear Gradient Syntax",
	 "href": "http://developer.mozilla.org/en/CSS/-moz-linear-gradient"
	 },{
	 "name": "Mozilla Radial Gradient Syntax",
	 "href": "http://developer.mozilla.org/en/CSS/-moz-radial-gradient"
	 },{
	 "name": "W3C Gradient Spec",
	 "href": "dev.w3.org/csswg/css3-images/#gradients-"
	 }]
	 }
	 !*/


	Modernizr.addTest('cssgradients', function() {

		var str1 = 'background-image:';
		var str2 = 'gradient(linear,left top,right bottom,from(#9f9),to(white));';
		var str3 = 'linear-gradient(left top,#9f9, white);';

		// standard syntax             // trailing 'background-image:'
		var css = str1 + prefixes.join(str3 + str1).slice(0, -str1.length);
		if (Modernizr._config.usePrefixes) {
			// legacy webkit syntax (FIXME: remove when syntax not in use anymore)
			css += str1 + '-webkit-' + str2;
		}

		var elem = createElement('div');
		var style = elem.style;
		style.cssText = css;

		// IE6 returns undefined so cast to string
		return ('' + style.backgroundImage).indexOf('gradient') > -1;
	});

	/*!
	 {
	 "name": "CSS Opacity",
	 "caniuse": "css-opacity",
	 "property": "opacity",
	 "tags": ["css"]
	 }
	 !*/

	// Browsers that actually have CSS Opacity implemented have done so
	// according to spec, which means their return values are within the
	// range of [0.0,1.0] - including the leading zero.

	Modernizr.addTest('opacity', function() {
		var elem = createElement('div');
		var style = elem.style;
		style.cssText = prefixes.join('opacity:.55;');

		// The non-literal . in this regex is intentional:
		// German Chrome returns this value as 0,55
		// github.com/Modernizr/Modernizr/issues/#issue/59/comment/516632
		return (/^0.55$/).test(style.opacity);
	});


	var inputElem = createElement('input');


	var inputtypes = 'search tel url email datetime date month week time datetime-local number range color'.split(' ');


	var inputs = {};


	var smile = ':)';

	/*!
	 {
	 "name": "Form input types",
	 "property": "inputtypes",
	 "caniuse": "forms",
	 "tags": ["forms"],
	 "authors": ["Mike Taylor"],
	 "polyfills": [
	 "jquerytools",
	 "webshims",
	 "h5f",
	 "webforms2",
	 "nwxforms",
	 "fdslider",
	 "html5slider",
	 "galleryhtml5forms",
	 "jscolor",
	 "html5formshim",
	 "selectedoptionsjs",
	 "formvalidationjs"
	 ]
	 }
	 !*/
	/* DOC
	 Detects support for HTML5 form input types and exposes Boolean subproperties with the results:

	 ```javascript
	 Modernizr.inputtypes.color
	 Modernizr.inputtypes.date
	 Modernizr.inputtypes.datetime
	 Modernizr.inputtypes['datetime-local']
	 Modernizr.inputtypes.email
	 Modernizr.inputtypes.month
	 Modernizr.inputtypes.number
	 Modernizr.inputtypes.range
	 Modernizr.inputtypes.search
	 Modernizr.inputtypes.tel
	 Modernizr.inputtypes.time
	 Modernizr.inputtypes.url
	 Modernizr.inputtypes.week
	 ```
	 */

	// Run through HTML5's new input types to see if the UA understands any.
	//   This is put behind the tests runloop because it doesn't return a
	//   true/false like all the other tests; instead, it returns an object
	//   containing each input type with its corresponding true/false value

	// Big thanks to @miketaylr for the html5 forms expertise. miketaylr.com/
	Modernizr['inputtypes'] = (function(props) {
		var bool;
		var inputElemType;
		var defaultView;
		var len = props.length;

		for ( var i = 0; i < len; i++ ) {

			inputElem.setAttribute('type', inputElemType = props[i]);
			bool = inputElem.type !== 'text';

			// We first check to see if the type we give it sticks..
			// If the type does, we feed it a textual value, which shouldn't be valid.
			// If the value doesn't stick, we know there's input sanitization which infers a custom UI
			if ( bool ) {

				inputElem.value         = smile;
				inputElem.style.cssText = 'position:absolute;visibility:hidden;';

				if ( /^range$/.test(inputElemType) && inputElem.style.WebkitAppearance !== undefined ) {

					docElement.appendChild(inputElem);
					defaultView = document.defaultView;

					// Safari 2-4 allows the smiley as a value, despite making a slider
					bool =  defaultView.getComputedStyle &&
						defaultView.getComputedStyle(inputElem, null).WebkitAppearance !== 'textfield' &&
							// Mobile android web browser has false positive, so must
							// check the height to see if the widget is actually there.
						(inputElem.offsetHeight !== 0);

					docElement.removeChild(inputElem);

				} else if ( /^(search|tel)$/.test(inputElemType) ){
					// Spec doesn't define any special parsing or detectable UI
					//   behaviors so we pass these through as true

					// Interestingly, opera fails the earlier test, so it doesn't
					//  even make it here.

				} else if ( /^(url|email|number)$/.test(inputElemType) ) {
					// Real url and email support comes with prebaked validation.
					bool = inputElem.checkValidity && inputElem.checkValidity() === false;

				} else {
					// If the upgraded input compontent rejects the :) text, we got a winner
					bool = inputElem.value != smile;
				}
			}

			inputs[ props[i] ] = !!bool;
		}
		return inputs;
	})(inputtypes);


	var attrs = {};


	var inputattrs = 'autocomplete autofocus list placeholder max min multiple pattern required step'.split(' ');

	/*!
	 {
	 "name": "Input attributes",
	 "property": "input",
	 "tags": ["forms"],
	 "authors": ["Mike Taylor"],
	 "notes": [{
	 "name": "WHATWG spec",
	 "href": "http://www.whatwg.org/specs/web-apps/current-work/multipage/the-input-element.html#input-type-attr-summary"
	 }],
	 "knownBugs": ["Some blackberry devices report false positive for input.multiple"]
	 }
	 !*/
	/* DOC
	 Detects support for HTML5 `<input>` element attributes and exposes Boolean subproperties with the results:

	 ```javascript
	 Modernizr.input.autocomplete
	 Modernizr.input.autofocus
	 Modernizr.input.list
	 Modernizr.input.max
	 Modernizr.input.min
	 Modernizr.input.multiple
	 Modernizr.input.pattern
	 Modernizr.input.placeholder
	 Modernizr.input.required
	 Modernizr.input.step
	 ```
	 */

	// Run through HTML5's new input attributes to see if the UA understands any.
	// Mike Taylr has created a comprehensive resource for testing these attributes
	//   when applied to all input types:
	//   miketaylr.com/code/input-type-attr.html

	// Only input placeholder is tested while textarea's placeholder is not.
	// Currently Safari 4 and Opera 11 have support only for the input placeholder
	// Both tests are available in feature-detects/forms-placeholder.js
	Modernizr['input'] = (function( props ) {
		for ( var i = 0, len = props.length; i < len; i++ ) {
			attrs[ props[i] ] = !!(props[i] in inputElem);
		}
		if (attrs.list){
			// safari false positive's on datalist: webk.it/74252
			// see also github.com/Modernizr/Modernizr/issues/146
			attrs.list = !!(createElement('datalist') && window.HTMLDataListElement);
		}
		return attrs;
	})(inputattrs);


	// hasOwnProperty shim by kangax needed for Safari 2.0 support
	var hasOwnProp;

	(function() {
		var _hasOwnProperty = ({}).hasOwnProperty;
		/* istanbul ignore else */
		/* we have no way of testing IE 5.5 or safari 2,
		 * so just assume the else gets hit */
		if ( !is(_hasOwnProperty, 'undefined') && !is(_hasOwnProperty.call, 'undefined') ) {
			hasOwnProp = function (object, property) {
				return _hasOwnProperty.call(object, property);
			};
		}
		else {
			hasOwnProp = function (object, property) { /* yes, this can give false positives/negatives, but most of the time we don't care about those */
				return ((property in object) && is(object.constructor.prototype[property], 'undefined'));
			};
		}
	})();



	// As far as I can think of, we shouldn't need or
	// allow 'on' for non-async tests, and you can't do
	// async tests without this 'addTest' module.

	// Listeners for async or post-run tests
	ModernizrProto._l = {};

	// 'addTest' implies a test after the core runloop,
	// So we'll add in the events
	ModernizrProto.on = function( test, cb ) {
		// Create the list of listeners if it doesn't exist
		if (!this._l[test]) {
			this._l[test] = [];
		}

		// Push this test on to the listener list
		this._l[test].push(cb);

		// If it's already been resolved, trigger it on next tick
		if (Modernizr.hasOwnProperty(test)) {
			// Next Tick
			setTimeout(function() {
				Modernizr._trigger(test, Modernizr[test]);
			}, 0);
		}
	};

	ModernizrProto._trigger = function( test, res ) {
		if (!this._l[test]) {
			return;
		}

		var cbs = this._l[test];

		// Force async
		setTimeout(function() {
			var i, cb;
			for (i = 0; i < cbs.length; i++) {
				cb = cbs[i];
				cb(res);
			}
		},0);

		// Don't trigger these again
		delete this._l[test];
	};

	/**
	 * addTest allows the user to define their own feature tests
	 * the result will be added onto the Modernizr object,
	 * as well as an appropriate className set on the html element
	 *
	 * @param feature - String naming the feature
	 * @param test - Function returning true if feature is supported, false if not
	 */
	function addTest( feature, test ) {
		if ( typeof feature == 'object' ) {
			for ( var key in feature ) {
				if ( hasOwnProp( feature, key ) ) {
					addTest( key, feature[ key ] );
				}
			}
		} else {

			feature = feature.toLowerCase();
			var featureNameSplit = feature.split('.');
			var last = Modernizr[featureNameSplit[0]];

			// Again, we don't check for parent test existence. Get that right, though.
			if (featureNameSplit.length == 2) {
				last = last[featureNameSplit[1]];
			}

			if ( typeof last != 'undefined' ) {
				// we're going to quit if you're trying to overwrite an existing test
				// if we were to allow it, we'd do this:
				//   var re = new RegExp("\\b(no-)?" + feature + "\\b");
				//   docElement.className = docElement.className.replace( re, '' );
				// but, no rly, stuff 'em.
				return Modernizr;
			}

			test = typeof test == 'function' ? test() : test;

			// Set the value (this is the magic, right here).
			if (featureNameSplit.length == 1) {
				Modernizr[featureNameSplit[0]] = test;
			} else {
				// cast to a Boolean, if not one already
				/* jshint -W053 */
				if (Modernizr[featureNameSplit[0]] && !(Modernizr[featureNameSplit[0]] instanceof Boolean)) {
					Modernizr[featureNameSplit[0]] = new Boolean(Modernizr[featureNameSplit[0]]);
				}

				Modernizr[featureNameSplit[0]][featureNameSplit[1]] = test;
			}

			// Set a single class (either `feature` or `no-feature`)
			/* jshint -W041 */
			setClasses([(!!test && test != false ? '' : 'no-') + featureNameSplit.join('-')]);
			/* jshint +W041 */

			// Trigger the event
			Modernizr._trigger(feature, test);
		}

		return Modernizr; // allow chaining.
	}

	// After all the tests are run, add self
	// to the Modernizr prototype
	Modernizr._q.push(function() {
		ModernizrProto.addTest = addTest;
	});


	/*!
	 {
	 "name": "Data URI",
	 "property": "datauri",
	 "caniuse": "datauri",
	 "tags": ["url"],
	 "builderAliases": ["url_data_uri"],
	 "async": true,
	 "notes": [{
	 "name": "Wikipedia article",
	 "href": "http://en.wikipedia.org/wiki/Data_URI_scheme"
	 }],
	 "warnings": ["Support in Internet Explorer 8 is limited to images and linked resources like CSS files, not HTML files"]
	 }
	 !*/
	/* DOC
	 Detects support for data URIs. Provides a subproperty to report support for data URIs over 32kb in size:

	 ```javascript
	 Modernizr.datauri           // true
	 Modernizr.datauri.over32kb  // false in IE8
	 ```
	 */

	// https://github.com/Modernizr/Modernizr/issues/14
	Modernizr.addAsyncTest(function() {
		/* jshint -W053 */

		// IE7 throw a mixed content warning on HTTPS for this test, so we'll
		// just blacklist it (we know it doesn't support data URIs anyway)
		// https://github.com/Modernizr/Modernizr/issues/362
		if(navigator.userAgent.indexOf('MSIE 7.') !== -1) {
			// Keep the test async
			setTimeout(function () {
				addTest('datauri', false);
			}, 10);
		}

		var datauri = new Image();

		datauri.onerror = function() {
			addTest('datauri', false);
		};
		datauri.onload = function() {
			if(datauri.width == 1 && datauri.height == 1) {
				testOver32kb();
			}
			else {
				addTest('datauri', false);
			}
		};

		datauri.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

		// Once we have datauri, let's check to see if we can use data URIs over
		// 32kb (IE8 can't). https://github.com/Modernizr/Modernizr/issues/321
		function testOver32kb(){

			var datauriBig = new Image();

			datauriBig.onerror = function() {
				addTest('datauri', true);
				Modernizr.datauri = new Boolean(true);
				Modernizr.datauri.over32kb = false;
			};
			datauriBig.onload = function() {
				addTest('datauri', true);
				Modernizr.datauri = new Boolean(true);
				Modernizr.datauri.over32kb = (datauriBig.width == 1 && datauriBig.height == 1);
			};

			var base64str = 'R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
			while (base64str.length < 33000) {
				base64str = '\r\n' + base64str;
			}
			datauriBig.src= 'data:image/gif;base64,' + base64str;
		}

	});


	// isEventSupported determines if the given element supports the given event
	// kangax.github.com/iseventsupported/
	// github.com/Modernizr/Modernizr/pull/636
	//
	// Known incorrects:
	//   Modernizr.hasEvent("webkitTransitionEnd", elem) // false negative
	//   Modernizr.hasEvent("textInput") // in Webkit. github.com/Modernizr/Modernizr/issues/333
	var isEventSupported = (function (undefined) {

		// Detect whether event support can be detected via `in`. Test on a DOM element
		// using the "blur" event b/c it should always exist. bit.ly/event-detection
		var needsFallback = !('onblur' in document.documentElement);

		/**
		 * @param  {string|*}           eventName  is the name of an event to test for (e.g. "resize")
		 * @param  {(Object|string|*)=} element    is the element|document|window|tagName to test on
		 * @return {boolean}
		 */
		function isEventSupportedInner( eventName, element ) {

			var isSupported;
			if ( !eventName ) { return false; }
			if ( !element || typeof element === 'string' ) {
				element = createElement(element || 'div');
			}

			// Testing via the `in` operator is sufficient for modern browsers and IE.
			// When using `setAttribute`, IE skips "unload", WebKit skips "unload" and
			// "resize", whereas `in` "catches" those.
			eventName = 'on' + eventName;
			isSupported = eventName in element;

			// Fallback technique for old Firefox - bit.ly/event-detection
			if ( !isSupported && needsFallback ) {
				if ( !element.setAttribute ) {
					// Switch to generic element if it lacks `setAttribute`.
					// It could be the `document`, `window`, or something else.
					element = createElement('div');
				}

				element.setAttribute(eventName, '');
				isSupported = typeof element[eventName] === 'function';

				if ( element[eventName] !== undefined ) {
					// If property was created, "remove it" by setting value to `undefined`.
					element[eventName] = undefined;
				}
				element.removeAttribute(eventName);
			}

			return isSupported;
		}
		return isEventSupportedInner;
	})();



	// Modernizr.hasEvent() detects support for a given event, with an optional element to test on
	// Modernizr.hasEvent('gesturestart', elem)
	var hasEvent = ModernizrProto.hasEvent = isEventSupported;

	/*!
	 {
	 "name": "Hashchange event",
	 "property": "hashchange",
	 "caniuse": "hashchange",
	 "tags": ["history"],
	 "notes": [{
	 "name": "MDN documentation",
	 "href": "https://developer.mozilla.org/en-US/docs/Web/API/window.onhashchange"
	 }],
	 "polyfills": [
	 "jquery-hashchange",
	 "moo-historymanager",
	 "jquery-ajaxy",
	 "hasher",
	 "shistory"
	 ]
	 }
	 !*/
	/* DOC
	 Detects support for the `hashchange` event, fired when the current location fragment changes.
	 */

	Modernizr.addTest('hashchange', function() {
		if (hasEvent('hashchange', window) === false) {
			return false;
		}

		// documentMode logic from YUI to filter out IE8 Compat Mode
		//   which false positives.
		return (document.documentMode === undefined || document.documentMode > 7);
	});


	function getBody() {
		// After page load injecting a fake body doesn't work so check if body exists
		var body = document.body;

		if(!body) {
			// Can't use the real body create a fake one.
			body = createElement('body');
			body.fake = true;
		}

		return body;
	}

	;

	// Inject element with style element and some CSS rules
	function injectElementWithStyles( rule, callback, nodes, testnames ) {
		var mod = 'modernizr';
		var style;
		var ret;
		var node;
		var docOverflow;
		var div = createElement('div');
		var body = getBody();

		if ( parseInt(nodes, 10) ) {
			// In order not to give false positives we create a node for each test
			// This also allows the method to scale for unspecified uses
			while ( nodes-- ) {
				node = createElement('div');
				node.id = testnames ? testnames[nodes] : mod + (nodes + 1);
				div.appendChild(node);
			}
		}

		// <style> elements in IE6-9 are considered 'NoScope' elements and therefore will be removed
		// when injected with innerHTML. To get around this you need to prepend the 'NoScope' element
		// with a 'scoped' element, in our case the soft-hyphen entity as it won't mess with our measurements.
		// msdn.microsoft.com/en-us/library/ms533897%28VS.85%29.aspx
		// Documents served as xml will throw if using &shy; so use xml friendly encoded version. See issue #277
		style = ['&#173;','<style id="s', mod, '">', rule, '</style>'].join('');
		div.id = mod;
		// IE6 will false positive on some tests due to the style element inside the test div somehow interfering offsetHeight, so insert it into body or fakebody.
		// Opera will act all quirky when injecting elements in documentElement when page is served as xml, needs fakebody too. #270
		(!body.fake ? div : body).innerHTML += style;
		body.appendChild(div);
		if ( body.fake ) {
			//avoid crashing IE8, if background image is used
			body.style.background = '';
			//Safari 5.13/5.1.4 OSX stops loading if ::-webkit-scrollbar is used and scrollbars are visible
			body.style.overflow = 'hidden';
			docOverflow = docElement.style.overflow;
			docElement.style.overflow = 'hidden';
			docElement.appendChild(body);
		}

		ret = callback(div, rule);
		// If this is done after page load we don't want to remove the body so check if body exists
		if ( body.fake ) {
			body.parentNode.removeChild(body);
			docElement.style.overflow = docOverflow;
			// Trigger layout so kinetic scrolling isn't disabled in iOS6+
			docElement.offsetHeight;
		} else {
			div.parentNode.removeChild(div);
		}

		return !!ret;

	}

	;

	var testStyles = ModernizrProto.testStyles = injectElementWithStyles;

	/*!
	 {
	 "name": "@font-face",
	 "property": "fontface",
	 "authors": ["Diego Perini", "Mat Marquis"],
	 "tags": ["css"],
	 "knownBugs": [
	 "False Positive: WebOS http://github.com/Modernizr/Modernizr/issues/342",
	 "False Postive: WP7 http://github.com/Modernizr/Modernizr/issues/538"
	 ],
	 "notes": [{
	 "name": "@font-face detection routine by Diego Perini",
	 "href": "http://javascript.nwbox.com/CSSSupport/"
	 },{
	 "name": "Filament Group @font-face compatibility research",
	 "href": "https://docs.google.com/presentation/d/1n4NyG4uPRjAA8zn_pSQ_Ket0RhcWC6QlZ6LMjKeECo0/edit#slide=id.p"
	 },{
	 "name": "Filament Grunticon/@font-face device testing results",
	 "href": "https://docs.google.com/spreadsheet/ccc?key=0Ag5_yGvxpINRdHFYeUJPNnZMWUZKR2ItMEpRTXZPdUE#gid=0"
	 },{
	 "name": "CSS fonts on Android",
	 "href": "http://stackoverflow.com/questions/3200069/css-fonts-on-android"
	 },{
	 "name": "@font-face and Android",
	 "href": "http://archivist.incutio.com/viewlist/css-discuss/115960"
	 }]
	 }
	 !*/

	var blacklist = (function() {
		var ua = navigator.userAgent;
		var wkvers = ua.match( /applewebkit\/([0-9]+)/gi ) && parseFloat( RegExp.$1 );
		var webos = ua.match( /w(eb)?osbrowser/gi );
		var wppre8 = ua.match( /windows phone/gi ) && ua.match( /iemobile\/([0-9])+/gi ) && parseFloat( RegExp.$1 ) >= 9;
		var oldandroid = wkvers < 533 && ua.match( /android/gi );
		return webos || oldandroid || wppre8;
	}());
	if( blacklist ) {
		Modernizr.addTest('fontface', false);
	} else {
		testStyles('@font-face {font-family:"font";src:url("https://")}', function( node, rule ) {
			var style = document.getElementById('smodernizr');
			var sheet = style.sheet || style.styleSheet;
			var cssText = sheet ? (sheet.cssRules && sheet.cssRules[0] ? sheet.cssRules[0].cssText : sheet.cssText || '') : '';
			var bool = /src/i.test(cssText) && cssText.indexOf(rule.split(' ')[0]) === 0;
			Modernizr.addTest('fontface', bool);
		});
	}
	;
	/*!
	 {
	 "name": "CSS :checked pseudo-selector",
	 "caniuse": "css-sel3",
	 "property": "checked",
	 "tags": ["css"],
	 "notes": [{
	 "name": "Related Github Issue",
	 "href": "https://github.com/Modernizr/Modernizr/pull/879"
	 }]
	 }
	 !*/

	Modernizr.addTest('checked', function(){
		return testStyles('#modernizr {position:absolute} #modernizr input {margin-left:10px} #modernizr :checked {margin-left:20px;display:block}', function( elem ){
			var cb = createElement('input');
			cb.setAttribute('type', 'checkbox');
			cb.setAttribute('checked', 'checked');
			elem.appendChild(cb);
			return cb.offsetLeft === 20;
		});
	});

	/*!
	 {
	 "name": "CSS :nth-child pseudo-selector",
	 "caniuse": "css-sel3",
	 "property": "nthchild",
	 "tags": ["css"],
	 "notes": [
	 {
	 "name": "Related Github Issue",
	 "href": "https://github.com/Modernizr/Modernizr/pull/685"
	 },
	 {
	 "name": "Sitepoint :nth-child documentation",
	 "href": "http://reference.sitepoint.com/css/pseudoclass-nthchild"
	 }
	 ],
	 "authors": ["@emilchristensen"],
	 "warnings": ["Known false negative in Safari 3.1 and Safari 3.2.2"]
	 }
	 !*/
	/* DOC
	 Detects support for the ':nth-child()' CSS pseudo-selector.
	 */

	// 5 `<div>` elements with `1px` width are created.
	// Then every other element has its `width` set to `2px`.
	// A Javascript loop then tests if the `<div>`s have the expected width
	// using the modulus operator.
	testStyles('#modernizr div {width:1px} #modernizr div:nth-child(2n) {width:2px;}', function( elem ) {
		Modernizr.addTest('nthchild', function() {
			var elems = elem.getElementsByTagName('div'),
				test = true;

			for (var i = 0; i < 5; i++) {
				test = test && elems[i].offsetWidth === i % 2 + 1;
			}

			return test;
		});
	}, 5);

	/*!
	 {
	 "name": "CSS Generated Content",
	 "property": "generatedcontent",
	 "tags": ["css"],
	 "warnings": ["Android won't return correct height for anything below 7px #738"],
	 "notes": [{
	 "name": "W3C CSS Selectors Level 3 spec",
	 "href": "http://www.w3.org/TR/css3-selectors/#gen-content"
	 },{
	 "name": "MDN article on :before",
	 "href": "https://developer.mozilla.org/en-US/docs/Web/CSS/::before"
	 },{
	 "name": "MDN article on :after",
	 "href": "https://developer.mozilla.org/en-US/docs/Web/CSS/::before"
	 }]
	 }
	 !*/

	testStyles('#modernizr{font:0/0 a}#modernizr:after{content:":)";visibility:hidden;font:7px/1 a}', function( node ) {
		Modernizr.addTest('generatedcontent', node.offsetHeight >= 7);
	});

	/*!
	 {
	 "name": "CSS vh unit",
	 "property": "cssvhunit",
	 "caniuse": "viewport-units",
	 "tags": ["css"],
	 "builderAliases": ["css_vhunit"],
	 "notes": [{
	 "name": "Related Modernizr Issue",
	 "href": "https://github.com/Modernizr/Modernizr/issues/572"
	 },{
	 "name": "Similar JSFiddle",
	 "href": "http://jsfiddle.net/FWeinb/etnYC/"
	 }]
	 }
	 !*/

	testStyles('#modernizr { height: 50vh; }', function( elem ) {
		var height = parseInt(window.innerHeight/2,10);
		var compStyle = parseInt((window.getComputedStyle ?
			getComputedStyle(elem, null) :
			elem.currentStyle)['height'],10);
		Modernizr.addTest('cssvhunit', compStyle == height);
	});

	/*!
	 {
	 "name": "CSS vw unit",
	 "property": "cssvwunit",
	 "caniuse": "viewport-units",
	 "tags": ["css"],
	 "builderAliases": ["css_vwunit"],
	 "notes": [{
	 "name": "Related Modernizr Issue",
	 "href": "https://github.com/Modernizr/Modernizr/issues/572"
	 },{
	 "name": "JSFiddle Example",
	 "href": "http://jsfiddle.net/FWeinb/etnYC/"
	 }]
	 }
	 !*/

	testStyles('#modernizr { width: 50vw; }', function( elem ) {
		var width = parseInt(window.innerWidth / 2, 10);
		var compStyle = parseInt((window.getComputedStyle ?
			getComputedStyle(elem, null) :
			elem.currentStyle).width, 10);

		Modernizr.addTest('cssvwunit', compStyle == width);
	});


	/**
	 * contains returns a boolean for if substr is found within str.
	 */
	function contains( str, substr ) {
		return !!~('' + str).indexOf(substr);
	}

	;

	// Helper function for converting kebab-case to camelCase,
	// e.g. box-sizing -> boxSizing
	function cssToDOM( name ) {
		return name.replace(/([a-z])-([a-z])/g, function(str, m1, m2) {
			return m1 + m2.toUpperCase();
		}).replace(/^-/, '');
	}
	;

	// Following spec is to expose vendor-specific style properties as:
	//   elem.style.WebkitBorderRadius
	// and the following would be incorrect:
	//   elem.style.webkitBorderRadius

	// Webkit ghosts their properties in lowercase but Opera & Moz do not.
	// Microsoft uses a lowercase `ms` instead of the correct `Ms` in IE8+
	//   erik.eae.net/archives/2008/03/10/21.48.10/

	// More here: github.com/Modernizr/Modernizr/issues/issue/21
	var omPrefixes = 'Moz O ms Webkit';


	var cssomPrefixes = (ModernizrProto._config.usePrefixes ? omPrefixes.split(' ') : []);
	ModernizrProto._cssomPrefixes = cssomPrefixes;


	var domPrefixes = (ModernizrProto._config.usePrefixes ? omPrefixes.toLowerCase().split(' ') : []);
	ModernizrProto._domPrefixes = domPrefixes;


	// Change the function's scope.
	function fnBind(fn, that) {
		return function() {
			return fn.apply(that, arguments);
		};
	}

	;

	/**
	 * testDOMProps is a generic DOM property test; if a browser supports
	 *   a certain property, it won't return undefined for it.
	 */
	function testDOMProps( props, obj, elem ) {
		var item;

		for ( var i in props ) {
			if ( props[i] in obj ) {

				// return the property name as a string
				if (elem === false) return props[i];

				item = obj[props[i]];

				// let's bind a function
				if (is(item, 'function')) {
					// bind to obj unless overriden
					return fnBind(item, elem || obj);
				}

				// return the unbound function or obj or value
				return item;
			}
		}
		return false;
	}

	;

	/**
	 * Create our "modernizr" element that we do most feature tests on.
	 */
	var modElem = {
		elem : createElement('modernizr')
	};

	// Clean up this element
	Modernizr._q.push(function() {
		delete modElem.elem;
	});



	var mStyle = {
		style : modElem.elem.style
	};

	// kill ref for gc, must happen before
	// mod.elem is removed, so we unshift on to
	// the front of the queue.
	Modernizr._q.unshift(function() {
		delete mStyle.style;
	});



	// Helper function for converting camelCase to kebab-case,
	// e.g. boxSizing -> box-sizing
	function domToCSS( name ) {
		return name.replace(/([A-Z])/g, function(str, m1) {
			return '-' + m1.toLowerCase();
		}).replace(/^ms-/, '-ms-');
	}
	;

	// Function to allow us to use native feature detection functionality if available.
	// Accepts a list of property names and a single value
	// Returns `undefined` if native detection not available
	function nativeTestProps ( props, value ) {
		var i = props.length;
		// Start with the JS API: http://www.w3.org/TR/css3-conditional/#the-css-interface
		if ('CSS' in window && 'supports' in window.CSS) {
			// Try every prefixed variant of the property
			while (i--) {
				if (window.CSS.supports(domToCSS(props[i]), value)) {
					return true;
				}
			}
			return false;
		}
		// Otherwise fall back to at-rule (for Opera 12.x)
		else if ('CSSSupportsRule' in window) {
			// Build a condition string for every prefixed variant
			var conditionText = [];
			while (i--) {
				conditionText.push('(' + domToCSS(props[i]) + ':' + value + ')');
			}
			conditionText = conditionText.join(' or ');
			return injectElementWithStyles('@supports (' + conditionText + ') { #modernizr { position: absolute; } }', function( node ) {
				return getComputedStyle(node, null).position == 'absolute';
			});
		}
		return undefined;
	}
	;

	// testProps is a generic CSS / DOM property test.

	// In testing support for a given CSS property, it's legit to test:
	//    `elem.style[styleName] !== undefined`
	// If the property is supported it will return an empty string,
	// if unsupported it will return undefined.

	// We'll take advantage of this quick test and skip setting a style
	// on our modernizr element, but instead just testing undefined vs
	// empty string.

	// Property names can be provided in either camelCase or kebab-case.

	function testProps( props, prefixed, value, skipValueTest ) {
		skipValueTest = is(skipValueTest, 'undefined') ? false : skipValueTest;

		// Try native detect first
		if (!is(value, 'undefined')) {
			var result = nativeTestProps(props, value);
			if(!is(result, 'undefined')) {
				return result;
			}
		}

		// Otherwise do it properly
		var afterInit, i, propsLength, prop, before;

		// If we don't have a style element, that means
		// we're running async or after the core tests,
		// so we'll need to create our own elements to use
		if ( !mStyle.style ) {
			afterInit = true;
			mStyle.modElem = createElement('modernizr');
			mStyle.style = mStyle.modElem.style;
		}

		// Delete the objects if we
		// we created them.
		function cleanElems() {
			if (afterInit) {
				delete mStyle.style;
				delete mStyle.modElem;
			}
		}

		propsLength = props.length;
		for ( i = 0; i < propsLength; i++ ) {
			prop = props[i];
			before = mStyle.style[prop];

			if (contains(prop, '-')) {
				prop = cssToDOM(prop);
			}

			if ( mStyle.style[prop] !== undefined ) {

				// If value to test has been passed in, do a set-and-check test.
				// 0 (integer) is a valid property value, so check that `value` isn't
				// undefined, rather than just checking it's truthy.
				if (!skipValueTest && !is(value, 'undefined')) {

					// Needs a try catch block because of old IE. This is slow, but will
					// be avoided in most cases because `skipValueTest` will be used.
					try {
						mStyle.style[prop] = value;
					} catch (e) {}

					// If the property value has changed, we assume the value used is
					// supported. If `value` is empty string, it'll fail here (because
					// it hasn't changed), which matches how browsers have implemented
					// CSS.supports()
					if (mStyle.style[prop] != before) {
						cleanElems();
						return prefixed == 'pfx' ? prop : true;
					}
				}
				// Otherwise just return true, or the property name if this is a
				// `prefixed()` call
				else {
					cleanElems();
					return prefixed == 'pfx' ? prop : true;
				}
			}
		}
		cleanElems();
		return false;
	}

	;

	// Modernizr.testProp() investigates whether a given style property is recognized
	// Property names can be provided in either camelCase or kebab-case.
	// Modernizr.testProp('pointerEvents')
	// Also accepts optional 2nd arg, of a value to use for native feature detection, e.g.:
	// Modernizr.testProp('pointerEvents', 'none')
	var testProp = ModernizrProto.testProp = function( prop, value, useValue ) {
		return testProps([prop], undefined, value, useValue);
	};

	/*!
	 {
	 "name": "CSS textshadow",
	 "property": "textshadow",
	 "caniuse": "css-textshadow",
	 "tags": ["css"],
	 "knownBugs": ["FF3.0 will false positive on this test"]
	 }
	 !*/

	Modernizr.addTest('textshadow', testProp('textShadow', '1px 1px'));


	/**
	 * testPropsAll tests a list of DOM properties we want to check against.
	 *     We specify literally ALL possible (known and/or likely) properties on
	 *     the element including the non-vendor prefixed one, for forward-
	 *     compatibility.
	 */
	function testPropsAll( prop, prefixed, elem, value, skipValueTest ) {

		var ucProp = prop.charAt(0).toUpperCase() + prop.slice(1),
			props = (prop + ' ' + cssomPrefixes.join(ucProp + ' ') + ucProp).split(' ');

		// did they call .prefixed('boxSizing') or are we just testing a prop?
		if(is(prefixed, 'string') || is(prefixed, 'undefined')) {
			return testProps(props, prefixed, value, skipValueTest);

			// otherwise, they called .prefixed('requestAnimationFrame', window[, elem])
		} else {
			props = (prop + ' ' + (domPrefixes).join(ucProp + ' ') + ucProp).split(' ');
			return testDOMProps(props, prefixed, elem);
		}
	}

	// Modernizr.testAllProps() investigates whether a given style property,
	//     or any of its vendor-prefixed variants, is recognized
	// Note that the property names must be provided in the camelCase variant.
	// Modernizr.testAllProps('boxSizing')
	ModernizrProto.testAllProps = testPropsAll;



	/**
	 * testAllProps determines whether a given CSS property, in some prefixed
	 * form, is supported by the browser. It can optionally be given a value; in
	 * which case testAllProps will only return true if the browser supports that
	 * value for the named property; this latter case will use native detection
	 * (via window.CSS.supports) if available. A boolean can be passed as a 3rd
	 * parameter to skip the value check when native detection isn't available,
	 * to improve performance when simply testing for support of a property.
	 *
	 * @param prop - String naming the property to test (either camelCase or
	 *               kebab-case)
	 * @param value - [optional] String of the value to test
	 * @param skipValueTest - [optional] Whether to skip testing that the value
	 *                        is supported when using non-native detection
	 *                        (default: false)
	 */
	function testAllProps (prop, value, skipValueTest) {
		return testPropsAll(prop, undefined, undefined, value, skipValueTest);
	}
	ModernizrProto.testAllProps = testAllProps;

	/*!
	 {
	 "name": "Background Size",
	 "property": "backgroundsize",
	 "tags": ["css"],
	 "knownBugs": ["This will false positive in Opera Mini - http://github.com/Modernizr/Modernizr/issues/396"],
	 "notes": [{
	 "name": "Related Issue",
	 "href": "http://github.com/Modernizr/Modernizr/issues/396"
	 }]
	 }
	 !*/

	Modernizr.addTest('backgroundsize', testAllProps('backgroundSize', '100%', true));

	/*!
	 {
	 "name": "Background Size Cover",
	 "property": "bgsizecover",
	 "tags": ["css"],
	 "builderAliases": ["css_backgroundsizecover"],
	 "notes": [{
	 "name" : "MDN Docs",
	 "href": "http://developer.mozilla.org/en/CSS/background-size"
	 }]
	 }
	 !*/

	// Must test value, as this specifically tests the `cover` value
	Modernizr.addTest('bgsizecover', testAllProps('backgroundSize', 'cover'));

	/*!
	 {
	 "name": "CSS Animations",
	 "property": "cssanimations",
	 "caniuse": "css-animation",
	 "polyfills": ["transformie", "csssandpaper"],
	 "tags": ["css"],
	 "warnings": ["Android < 4 will pass this test, but can only animate a single property at a time"],
	 "notes": [{
	 "name" : "Article: 'Dispelling the Android CSS animation myths'",
	 "href": "http://goo.gl/OGw5Gm"
	 }]
	 }
	 !*/
	/* DOC
	 Detects whether or not elements can be animated using CSS
	 */

	Modernizr.addTest('cssanimations', testAllProps('animationName', 'a', true));

	/*!
	 {
	 "name": "CSS Transforms",
	 "property": "csstransforms",
	 "caniuse": "transforms2d",
	 "tags": ["css"]
	 }
	 !*/

	Modernizr.addTest('csstransforms', function() {
		// Android < 3.0 is buggy, so we sniff and blacklist
		// http://git.io/hHzL7w
		return navigator.userAgent.indexOf('Android 2.') === -1 &&
			testAllProps('transform', 'scale(1)', true);
	});

	/*!
	 {
	 "name": "CSS Transforms 3D",
	 "property": "csstransforms3d",
	 "caniuse": "transforms3d",
	 "tags": ["css"],
	 "warnings": [
	 "Chrome may occassionally fail this test on some systems; more info: https://code.google.com/p/chromium/issues/detail?id=129004"
	 ]
	 }
	 !*/

	Modernizr.addTest('csstransforms3d', function() {
		var ret = !!testAllProps('perspective', '1px', true);
		var usePrefix = Modernizr._config.usePrefixes;

		// Webkit's 3D transforms are passed off to the browser's own graphics renderer.
		//   It works fine in Safari on Leopard and Snow Leopard, but not in Chrome in
		//   some conditions. As a result, Webkit typically recognizes the syntax but
		//   will sometimes throw a false positive, thus we must do a more thorough check:
		if ( ret && (!usePrefix || 'webkitPerspective' in docElement.style )) {
			var mq;
			// Use CSS Conditional Rules if available
			if (Modernizr.supports) {
				mq = '@supports (perspective: 1px)';
			} else {
				// Otherwise, Webkit allows this media query to succeed only if the feature is enabled.
				// `@media (transform-3d),(-webkit-transform-3d){ ... }`
				mq = '@media (transform-3d)';
				if (usePrefix ) mq += ',(-webkit-transform-3d)';
			}
			// If loaded inside the body tag and the test element inherits any padding, margin or borders it will fail #740
			mq += '{#modernizr{left:9px;position:absolute;height:5px;margin:0;padding:0;border:0}}';

			testStyles(mq, function( elem ) {
				ret = elem.offsetLeft === 9 && elem.offsetHeight === 5;
			});
		}

		return ret;
	});

	/*!
	 {
	 "name": "Flexbox",
	 "property": "flexbox",
	 "caniuse": "flexbox",
	 "tags": ["css"],
	 "notes": [{
	 "name": "The _new_ flexbox",
	 "href": "http://dev.w3.org/csswg/css3-flexbox"
	 }],
	 "warnings": [
	 "A `true` result for this detect does not imply that the `flex-wrap` property is supported; see the `flexwrap` detect."
	 ]
	 }
	 !*/
	/* DOC
	 Detects support for the Flexible Box Layout model, a.k.a. Flexbox, which allows easy manipulation of layout order and sizing within a container.
	 */

	Modernizr.addTest('flexbox', testAllProps('flexBasis', '1px', true));


	// Run each test
	testRunner();

	// Remove the "no-js" class if it exists
	setClasses(classes);

	delete ModernizrProto.addTest;
	delete ModernizrProto.addAsyncTest;

	// Run the things that are supposed to run after the tests
	for (var i = 0; i < Modernizr._q.length; i++) {
		Modernizr._q[i]();
	}

	// Leak Modernizr namespace
	window.Modernizr = Modernizr;


	;

})(window, document);