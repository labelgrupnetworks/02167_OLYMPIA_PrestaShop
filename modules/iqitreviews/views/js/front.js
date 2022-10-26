/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./front/front.js":
/*!************************!*\
  !*** ./front/front.js ***!
  \************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _lib_bootstrap_rating_input_bootstrap_rating_input_min__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./lib/bootstrap-rating-input/bootstrap-rating-input.min */ "./front/lib/bootstrap-rating-input/bootstrap-rating-input.min.js");
/* harmony import */ var _lib_bootstrap_rating_input_bootstrap_rating_input_min__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_lib_bootstrap_rating_input_bootstrap_rating_input_min__WEBPACK_IMPORTED_MODULE_0__);


iqitreviews.script = function () {
  var $productReviewForm = $('#iqitreviews-productreview-form');
  return {
    'init': function init() {
      if (iqitTheme.pp_tabs == 'tabh' || iqitTheme.pp_tabs == 'tabha') {
        $('#iqitreviews-rating-product').on('click', function () {
          var element = document.getElementById("product-infos-tabs");
          $('.nav-tabs a[data-iqitextra="iqit-reviews-tab"]').tab('show');

          if (typeof element != 'undefined' && element != null) {
            element.scrollIntoView();
          }
        });
      } else {
        $('#iqitreviews-rating-product').on('click', function () {
          document.getElementById("iqit-reviews-tab").scrollIntoView();
        });
      }

      $productReviewForm.submit(function (e) {
        e.preventDefault();
        var $productReviewFormAlert = $('#iqitreviews-productreview-form-alert'),
            $productReviewFormSuccessAlert = $('#iqitreviews-productreview-form-success-alert');
        $.post($(this).attr('action'), $(this).serialize(), null, 'json').then(function (resp) {
          if (!resp.success) {
            var htmlResp = '<strong>' + resp.data.message + '</strong>';
            htmlResp = htmlResp + '<ul>';
            $.each(resp.data.errors, function (index, value) {
              htmlResp = htmlResp + '<li>' + value + '</li>';
            });
            htmlResp = htmlResp + '</ul>';
            $productReviewFormAlert.html(htmlResp);
            $productReviewFormAlert.removeClass('hidden-xs-up');
          } else {
            var _htmlResp = '<strong>' + resp.data.message + '</strong>';

            $productReviewFormSuccessAlert.html(_htmlResp);
            $productReviewFormSuccessAlert.removeClass('hidden-xs-up');
            $('#iqit-reviews-modal').modal('hide');
          }
        }).fail(function (resp) {
          $productReviewFormAlert.html(resp);
          $productReviewFormAlert.removeClass('invisible');
        });
        e.preventDefault();
      });
    }
  };
}();

$(document).ready(function () {
  iqitreviews.script.init();
});

/***/ }),

/***/ "./front/lib/bootstrap-rating-input/bootstrap-rating-input.min.js":
/*!************************************************************************!*\
  !*** ./front/lib/bootstrap-rating-input/bootstrap-rating-input.min.js ***!
  \************************************************************************/
/***/ (() => {

function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }

!function (a) {
  "use strict";

  function b(a) {
    return "[data-value" + (a ? "=" + a : "") + "]";
  }

  function c(a, b, c) {
    var d = c.activeIcon,
        e = c.inactiveIcon;
    a.removeClass(b ? e : d).addClass(b ? d : e);
  }

  function d(b, c) {
    var d = a.extend({}, i, b.data(), c);
    return d.inline = "" === d.inline || d.inline, d.readonly = "" === d.readonly || d.readonly, d.clearable === !1 ? d.clearableLabel = "" : d.clearableLabel = d.clearable, d.clearable = "" === d.clearable || d.clearable, d;
  }

  function e(b, c) {
    if (c.inline) var d = a('<span class="rating-input"></span>');else var d = a('<div class="rating-input"></div>');
    d.addClass(b.attr("class")), d.removeClass("rating");

    for (var e = c.min; e <= c.max; e++) {
      d.append('<i class="' + c.iconLib + '" data-value="' + e + '"></i>');
    }

    return c.clearable && !c.readonly && d.append("&nbsp;").append('<a class="' + f + '"><i class="' + c.iconLib + " " + c.clearableIcon + '"/>' + c.clearableLabel + "</a>"), d;
  }

  var f = "rating-clear",
      g = "." + f,
      h = "hidden",
      i = {
    min: 1,
    max: 5,
    "empty-value": 0,
    iconLib: "glyphicon",
    activeIcon: "glyphicon-star",
    inactiveIcon: "glyphicon-star-empty",
    clearable: !1,
    clearableIcon: "glyphicon-remove",
    inline: !1,
    readonly: !1
  },
      j = function j(a, b) {
    var c = this.$input = a;
    this.options = d(c, b);
    var f = this.$el = e(c, this.options);
    c.addClass(h).before(f), c.attr("type", "hidden"), this.highlight(c.val());
  };

  j.VERSION = "0.4.0", j.DEFAULTS = i, j.prototype = {
    clear: function clear() {
      this.setValue(this.options["empty-value"]);
    },
    setValue: function setValue(a) {
      this.highlight(a), this.updateInput(a);
    },
    highlight: function highlight(a, d) {
      var e = this.options,
          f = this.$el;

      if (a >= this.options.min && a <= this.options.max) {
        var i = f.find(b(a));
        c(i.prevAll("i").andSelf(), !0, e), c(i.nextAll("i"), !1, e);
      } else c(f.find(b()), !1, e);

      d || (a && a != this.options["empty-value"] ? f.find(g).removeClass(h) : f.find(g).addClass(h));
    },
    updateInput: function updateInput(a) {
      var b = this.$input;
      b.val() != a && b.val(a).change();
    }
  };

  var k = a.fn.rating = function (c) {
    return this.filter("input[type=number]").each(function () {
      var d = a(this),
          e = "object" == _typeof(c) && c || {},
          f = new j(d, e);
      f.options.readonly || f.$el.on("mouseenter", b(), function () {
        f.highlight(a(this).data("value"), !0);
      }).on("mouseleave", b(), function () {
        f.highlight(d.val(), !0);
      }).on("click", b(), function () {
        f.setValue(a(this).data("value"));
      }).on("click", g, function () {
        f.clear();
      });
    });
  };

  k.Constructor = j, a(function () {
    a("input.rating[type=number]").each(function () {
      a(this).rating();
    });
  });
}(jQuery);

/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js??ruleSet[1].rules[1].use[1]!./node_modules/css-loader/dist/cjs.js!./node_modules/sass-loader/dist/cjs.js!./front/front.scss":
/*!*****************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js??ruleSet[1].rules[1].use[1]!./node_modules/css-loader/dist/cjs.js!./node_modules/sass-loader/dist/cjs.js!./front/front.scss ***!
  \*****************************************************************************************************************************************************************************************/
/***/ (() => {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./front/front.scss":
/*!**************************!*\
  !*** ./front/front.scss ***!
  \**************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! !../node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js */ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js");
/* harmony import */ var _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ruleSet_1_rules_1_use_1_node_modules_css_loader_dist_cjs_js_node_modules_sass_loader_dist_cjs_js_front_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! !!../node_modules/mini-css-extract-plugin/dist/loader.js??ruleSet[1].rules[1].use[1]!../node_modules/css-loader/dist/cjs.js!../node_modules/sass-loader/dist/cjs.js!./front.scss */ "./node_modules/mini-css-extract-plugin/dist/loader.js??ruleSet[1].rules[1].use[1]!./node_modules/css-loader/dist/cjs.js!./node_modules/sass-loader/dist/cjs.js!./front/front.scss");
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_ruleSet_1_rules_1_use_1_node_modules_css_loader_dist_cjs_js_node_modules_sass_loader_dist_cjs_js_front_scss__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_node_modules_mini_css_extract_plugin_dist_loader_js_ruleSet_1_rules_1_use_1_node_modules_css_loader_dist_cjs_js_node_modules_sass_loader_dist_cjs_js_front_scss__WEBPACK_IMPORTED_MODULE_1__);

            

var options = {};

options.insert = "head";
options.singleton = false;

var update = _node_modules_style_loader_dist_runtime_injectStylesIntoStyleTag_js__WEBPACK_IMPORTED_MODULE_0___default()((_node_modules_mini_css_extract_plugin_dist_loader_js_ruleSet_1_rules_1_use_1_node_modules_css_loader_dist_cjs_js_node_modules_sass_loader_dist_cjs_js_front_scss__WEBPACK_IMPORTED_MODULE_1___default()), options);



/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ((_node_modules_mini_css_extract_plugin_dist_loader_js_ruleSet_1_rules_1_use_1_node_modules_css_loader_dist_cjs_js_node_modules_sass_loader_dist_cjs_js_front_scss__WEBPACK_IMPORTED_MODULE_1___default().locals) || {});

/***/ }),

/***/ "./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js":
/*!****************************************************************************!*\
  !*** ./node_modules/style-loader/dist/runtime/injectStylesIntoStyleTag.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var isOldIE = function isOldIE() {
  var memo;
  return function memorize() {
    if (typeof memo === 'undefined') {
      // Test for IE <= 9 as proposed by Browserhacks
      // @see http://browserhacks.com/#hack-e71d8692f65334173fee715c222cb805
      // Tests for existence of standard globals is to allow style-loader
      // to operate correctly into non-standard environments
      // @see https://github.com/webpack-contrib/style-loader/issues/177
      memo = Boolean(window && document && document.all && !window.atob);
    }

    return memo;
  };
}();

var getTarget = function getTarget() {
  var memo = {};
  return function memorize(target) {
    if (typeof memo[target] === 'undefined') {
      var styleTarget = document.querySelector(target); // Special case to return head of iframe instead of iframe itself

      if (window.HTMLIFrameElement && styleTarget instanceof window.HTMLIFrameElement) {
        try {
          // This will throw an exception if access to iframe is blocked
          // due to cross-origin restrictions
          styleTarget = styleTarget.contentDocument.head;
        } catch (e) {
          // istanbul ignore next
          styleTarget = null;
        }
      }

      memo[target] = styleTarget;
    }

    return memo[target];
  };
}();

var stylesInDom = [];

function getIndexByIdentifier(identifier) {
  var result = -1;

  for (var i = 0; i < stylesInDom.length; i++) {
    if (stylesInDom[i].identifier === identifier) {
      result = i;
      break;
    }
  }

  return result;
}

function modulesToDom(list, options) {
  var idCountMap = {};
  var identifiers = [];

  for (var i = 0; i < list.length; i++) {
    var item = list[i];
    var id = options.base ? item[0] + options.base : item[0];
    var count = idCountMap[id] || 0;
    var identifier = "".concat(id, " ").concat(count);
    idCountMap[id] = count + 1;
    var index = getIndexByIdentifier(identifier);
    var obj = {
      css: item[1],
      media: item[2],
      sourceMap: item[3]
    };

    if (index !== -1) {
      stylesInDom[index].references++;
      stylesInDom[index].updater(obj);
    } else {
      stylesInDom.push({
        identifier: identifier,
        updater: addStyle(obj, options),
        references: 1
      });
    }

    identifiers.push(identifier);
  }

  return identifiers;
}

function insertStyleElement(options) {
  var style = document.createElement('style');
  var attributes = options.attributes || {};

  if (typeof attributes.nonce === 'undefined') {
    var nonce =  true ? __webpack_require__.nc : 0;

    if (nonce) {
      attributes.nonce = nonce;
    }
  }

  Object.keys(attributes).forEach(function (key) {
    style.setAttribute(key, attributes[key]);
  });

  if (typeof options.insert === 'function') {
    options.insert(style);
  } else {
    var target = getTarget(options.insert || 'head');

    if (!target) {
      throw new Error("Couldn't find a style target. This probably means that the value for the 'insert' parameter is invalid.");
    }

    target.appendChild(style);
  }

  return style;
}

function removeStyleElement(style) {
  // istanbul ignore if
  if (style.parentNode === null) {
    return false;
  }

  style.parentNode.removeChild(style);
}
/* istanbul ignore next  */


var replaceText = function replaceText() {
  var textStore = [];
  return function replace(index, replacement) {
    textStore[index] = replacement;
    return textStore.filter(Boolean).join('\n');
  };
}();

function applyToSingletonTag(style, index, remove, obj) {
  var css = remove ? '' : obj.media ? "@media ".concat(obj.media, " {").concat(obj.css, "}") : obj.css; // For old IE

  /* istanbul ignore if  */

  if (style.styleSheet) {
    style.styleSheet.cssText = replaceText(index, css);
  } else {
    var cssNode = document.createTextNode(css);
    var childNodes = style.childNodes;

    if (childNodes[index]) {
      style.removeChild(childNodes[index]);
    }

    if (childNodes.length) {
      style.insertBefore(cssNode, childNodes[index]);
    } else {
      style.appendChild(cssNode);
    }
  }
}

function applyToTag(style, options, obj) {
  var css = obj.css;
  var media = obj.media;
  var sourceMap = obj.sourceMap;

  if (media) {
    style.setAttribute('media', media);
  } else {
    style.removeAttribute('media');
  }

  if (sourceMap && typeof btoa !== 'undefined') {
    css += "\n/*# sourceMappingURL=data:application/json;base64,".concat(btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))), " */");
  } // For old IE

  /* istanbul ignore if  */


  if (style.styleSheet) {
    style.styleSheet.cssText = css;
  } else {
    while (style.firstChild) {
      style.removeChild(style.firstChild);
    }

    style.appendChild(document.createTextNode(css));
  }
}

var singleton = null;
var singletonCounter = 0;

function addStyle(obj, options) {
  var style;
  var update;
  var remove;

  if (options.singleton) {
    var styleIndex = singletonCounter++;
    style = singleton || (singleton = insertStyleElement(options));
    update = applyToSingletonTag.bind(null, style, styleIndex, false);
    remove = applyToSingletonTag.bind(null, style, styleIndex, true);
  } else {
    style = insertStyleElement(options);
    update = applyToTag.bind(null, style, options);

    remove = function remove() {
      removeStyleElement(style);
    };
  }

  update(obj);
  return function updateStyle(newObj) {
    if (newObj) {
      if (newObj.css === obj.css && newObj.media === obj.media && newObj.sourceMap === obj.sourceMap) {
        return;
      }

      update(obj = newObj);
    } else {
      remove();
    }
  };
}

module.exports = function (list, options) {
  options = options || {}; // Force single-tag solution on IE6-9, which has a hard limit on the # of <style>
  // tags it will allow on a page

  if (!options.singleton && typeof options.singleton !== 'boolean') {
    options.singleton = isOldIE();
  }

  list = list || [];
  var lastIdentifiers = modulesToDom(list, options);
  return function update(newList) {
    newList = newList || [];

    if (Object.prototype.toString.call(newList) !== '[object Array]') {
      return;
    }

    for (var i = 0; i < lastIdentifiers.length; i++) {
      var identifier = lastIdentifiers[i];
      var index = getIndexByIdentifier(identifier);
      stylesInDom[index].references--;
    }

    var newLastIdentifiers = modulesToDom(newList, options);

    for (var _i = 0; _i < lastIdentifiers.length; _i++) {
      var _identifier = lastIdentifiers[_i];

      var _index = getIndexByIdentifier(_identifier);

      if (stylesInDom[_index].references === 0) {
        stylesInDom[_index].updater();

        stylesInDom.splice(_index, 1);
      }
    }

    lastIdentifiers = newLastIdentifiers;
  };
};

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
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!************************!*\
  !*** ./front/index.js ***!
  \************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _front_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./front.js */ "./front/front.js");
/* harmony import */ var _front_scss__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./front.scss */ "./front/front.scss");


})();

/******/ })()
;
//# sourceMappingURL=front.js.map