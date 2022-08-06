(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _DOM = _interopRequireDefault(require("../Utils/DOM"));

var _Utility = _interopRequireDefault(require("../Utils/Utility"));

var _Helpers = _interopRequireDefault(require("../Utils/Helpers"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classPrivateFieldSet(receiver, privateMap, value) { var descriptor = _classExtractFieldDescriptor(receiver, privateMap, "set"); _classApplyDescriptorSet(receiver, descriptor, value); return value; }

function _classApplyDescriptorSet(receiver, descriptor, value) { if (descriptor.set) { descriptor.set.call(receiver, value); } else { if (!descriptor.writable) { throw new TypeError("attempted to set read only private field"); } descriptor.value = value; } }

function _classPrivateFieldGet(receiver, privateMap) { var descriptor = _classExtractFieldDescriptor(receiver, privateMap, "get"); return _classApplyDescriptorGet(receiver, descriptor); }

function _classExtractFieldDescriptor(receiver, privateMap, action) { if (!privateMap.has(receiver)) { throw new TypeError("attempted to " + action + " private field on non-instance"); } return privateMap.get(receiver); }

function _classApplyDescriptorGet(receiver, descriptor) { if (descriptor.get) { return descriptor.get.call(receiver); } return descriptor.value; }

var _lastScrolBarPosition = /*#__PURE__*/new WeakMap();

var _noSticky = /*#__PURE__*/new WeakMap();

var Header = function Header() {
  var _this = this;

  _classCallCheck(this, Header);

  _lastScrolBarPosition.set(this, {
    writable: true,
    value: 0
  });

  _defineProperty(this, "sticky", function () {
    var _DOM$siteHeader;

    if (_classPrivateFieldGet(_this, _noSticky).call(_this)) {
      return;
    }

    if (!(_DOM["default"].headerWrapper || _DOM["default"].siteHeader || _DOM["default"].header)) {
      return;
    }

    var currentPosition = _Utility["default"].elemOffset(_DOM["default"].headerWrapper).top - Header.getOffset();
    var slideStickyCurrentPosition = currentPosition; // If slide effect

    if (_Helpers["default"].slideStickyEffect() && !((_DOM$siteHeader = _DOM["default"].siteHeader) !== null && _DOM$siteHeader !== void 0 && _DOM$siteHeader.classList.contains("vertical-header"))) {
      currentPosition = currentPosition + _DOM["default"].headerWrapper.offsetHeight;
    } // When scrolling


    if (_Utility["default"].scrollBarTopPosition() !== 0 && _Utility["default"].scrollBarTopPosition() >= currentPosition) {
      _DOM["default"].headerWrapper.classList.add("is-sticky");

      _DOM["default"].header.style.top = Header.getOffset() + "px";
      _DOM["default"].header.style.width = _DOM["default"].headerWrapper.offsetWidth + "px"; // If slide effect

      if (_Helpers["default"].slideStickyEffect() && !_DOM["default"].siteHeader.classList.contains("vertical-header")) {
        _DOM["default"].siteHeader.classList.add("show");
      }
    } else {
      // If is not slide effect
      if (!_Helpers["default"].slideStickyEffect()) {
        // Remove sticky wrap class
        _DOM["default"].headerWrapper.classList.remove("is-sticky");

        _DOM["default"].header.style.top = "";
        _DOM["default"].header.style.width = "";
      }
    } // If slide effect


    if (_Helpers["default"].slideStickyEffect() && !_DOM["default"].siteHeader.classList.contains("vertical-header")) {
      // Remove sticky class when window top
      if (_Utility["default"].scrollBarTopPosition() <= slideStickyCurrentPosition) {
        // Remove sticky wrap class
        _DOM["default"].headerWrapper.classList.remove("is-sticky");

        _DOM["default"].header.style.top = "";
        _DOM["default"].header.style.width = ""; // Remove slide effect class

        _DOM["default"].siteHeader.classList.remove("show");
      }
    }
  });

  _defineProperty(this, "updateSticky", function () {
    var _DOM$siteHeader2, _DOM$headerWrapper;

    // Return if is vertical header style
    if (window.innerWidth > 960 && (_DOM$siteHeader2 = _DOM["default"].siteHeader) !== null && _DOM$siteHeader2 !== void 0 && _DOM$siteHeader2.classList.contains("vertical-header")) {
      return;
    }

    if (!((_DOM$headerWrapper = _DOM["default"].headerWrapper) !== null && _DOM$headerWrapper !== void 0 && _DOM$headerWrapper.classList.contains("is-sticky")) && !!_DOM["default"].header) {
      if (_DOM["default"].headerWrapper) {
        _DOM["default"].headerWrapper.style.height = _DOM["default"].header.offsetHeight + "px";
      }
    }

    if (_Utility["default"].scrollBarTopPosition() !== 0) {
      if (!!_DOM["default"].header && !!_DOM["default"].headerWrapper) {
        _DOM["default"].header.style.top = Header.getOffset() + "px";
        _DOM["default"].header.style.width = _DOM["default"].headerWrapper.offsetWidth + "px";
      }
    }
  });

  _defineProperty(this, "addVerticalHeaderSticky", function () {
    var _DOM$verticalHeader;

    // Return if is not vertical header style and transparent
    if (!((_DOM$verticalHeader = _DOM["default"].verticalHeader) !== null && _DOM$verticalHeader !== void 0 && _DOM$verticalHeader.classList.contains("is-transparent"))) {
      return;
    } // Return if no header wrapper


    if (!_DOM["default"].headerWrapper) {
      return;
    }

    var currentPosition = _Utility["default"].elemOffset(_DOM["default"].headerWrapper).top; // When scrolling


    if (_Utility["default"].scrollBarTopPosition() !== 0 && _Utility["default"].scrollBarTopPosition() >= currentPosition) {
      _DOM["default"].headerWrapper.classList.add("is-sticky");
    } else {
      _DOM["default"].headerWrapper.classList.remove("is-sticky");
    }
  });

  _defineProperty(this, "stickyEffects", function () {
    var _DOM$siteHeader3;

    // Return if is vertical header style
    if ((_DOM$siteHeader3 = _DOM["default"].siteHeader) !== null && _DOM$siteHeader3 !== void 0 && _DOM$siteHeader3.classList.contains("vertical-header")) {
      return;
    } // Return if no header wrapper


    if (!_DOM["default"].headerWrapper) {
      return;
    } // If show up effect


    if (_Helpers["default"].upStickyEffect()) {
      var currentPosition = _Utility["default"].elemOffset(_DOM["default"].headerWrapper).top + _DOM["default"].headerWrapper.offsetHeight;

      var scrollBarTopPosition = document.documentElement.scrollTop;

      if (scrollBarTopPosition >= _classPrivateFieldGet(_this, _lastScrolBarPosition) && scrollBarTopPosition >= currentPosition) {
        _DOM["default"].siteHeader.classList.remove("header-down");

        _DOM["default"].siteHeader.classList.add("header-up");
      } else {
        _DOM["default"].siteHeader.classList.remove("header-up");

        _DOM["default"].siteHeader.classList.add("header-down");
      }

      _classPrivateFieldSet(_this, _lastScrolBarPosition, scrollBarTopPosition);
    }
  });

  _defineProperty(this, "createStickyWrapper", function () {
    var _DOM$siteHeader4;

    // Create header sticky wrapper element
    _DOM["default"].headerWrapper = document.createElement("div");

    _DOM["default"].headerWrapper.setAttribute("id", "site-header-sticky-wrapper");

    _DOM["default"].headerWrapper.setAttribute("class", "oceanwp-sticky-header-holder"); // Wrap header sticky wrapper around header


    if (!!_DOM["default"].header) {
      var _DOM$headerWrapper2;

      (_DOM$headerWrapper2 = _DOM["default"].headerWrapper) === null || _DOM$headerWrapper2 === void 0 ? void 0 : _DOM$headerWrapper2.oceanWrapAll(_DOM["default"].header);
    } // Set header sticky wrapper height


    if (!((_DOM$siteHeader4 = _DOM["default"].siteHeader) !== null && _DOM$siteHeader4 !== void 0 && _DOM$siteHeader4.classList.contains("vertical-header"))) {
      if (!!_DOM["default"].headerWrapper && !!_DOM["default"].header) {
        _DOM["default"].headerWrapper.style.height = _DOM["default"].header.offsetHeight + "px";
      }
    }
  });

  _noSticky.set(this, {
    writable: true,
    value: function value() {
      var _DOM$siteHeader5, _DOM$siteHeader6;

      if ((_DOM$siteHeader5 = _DOM["default"].siteHeader) !== null && _DOM$siteHeader5 !== void 0 && _DOM$siteHeader5.classList.contains("vertical-header")) {
        if (window.innerWidth <= 960) {
          return !_DOM["default"].headerWrapper || _Helpers["default"].isMobileStickyDisabled();
        }
      }

      return !_DOM["default"].headerWrapper || _Helpers["default"].isMobileStickyDisabled() || !((_DOM$siteHeader6 = _DOM["default"].siteHeader) !== null && _DOM$siteHeader6 !== void 0 && _DOM$siteHeader6.classList.contains("fixed-scroll"));
    }
  });
};

exports["default"] = Header;

_defineProperty(Header, "getOffset", function () {
  var offset = 0; // Add WP Adminbar offset

  if (_Utility["default"].isWPAdminbarVisible()) {
    if (!!_DOM["default"].WPAdminbar) {
      offset = offset + _DOM["default"].WPAdminbar.offsetHeight;
    }
  } // Offset topbar sticky


  if (_Helpers["default"].isTopbarStickyEnabled()) {
    if (!!_DOM["default"].topbar) {
      offset = offset + _DOM["default"].topbar.offsetHeight;
    }
  }

  return offset;
});

},{"../Utils/DOM":4,"../Utils/Helpers":6,"../Utils/Utility":7}],2:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _DOM = _interopRequireDefault(require("../Utils/DOM"));

var _Helpers = _interopRequireDefault(require("../Utils/Helpers"));

var _Utility = _interopRequireDefault(require("../Utils/Utility"));

var _Header = _interopRequireDefault(require("./Header"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classPrivateFieldGet(receiver, privateMap) { var descriptor = _classExtractFieldDescriptor(receiver, privateMap, "get"); return _classApplyDescriptorGet(receiver, descriptor); }

function _classApplyDescriptorGet(receiver, descriptor) { if (descriptor.get) { return descriptor.get.call(receiver); } return descriptor.value; }

function _classPrivateFieldSet(receiver, privateMap, value) { var descriptor = _classExtractFieldDescriptor(receiver, privateMap, "set"); _classApplyDescriptorSet(receiver, descriptor, value); return value; }

function _classExtractFieldDescriptor(receiver, privateMap, action) { if (!privateMap.has(receiver)) { throw new TypeError("attempted to " + action + " private field on non-instance"); } return privateMap.get(receiver); }

function _classApplyDescriptorSet(receiver, descriptor, value) { if (descriptor.set) { descriptor.set.call(receiver, value); } else { if (!descriptor.writable) { throw new TypeError("attempted to set read only private field"); } descriptor.value = value; } }

var _logo = /*#__PURE__*/new WeakMap();

var _customLogo = /*#__PURE__*/new WeakMap();

var _returnOnSomeHeaderStyles = /*#__PURE__*/new WeakMap();

var Logo = function Logo() {
  var _this = this;

  _classCallCheck(this, Logo);

  _logo.set(this, {
    writable: true,
    value: void 0
  });

  _customLogo.set(this, {
    writable: true,
    value: void 0
  });

  _defineProperty(this, "setMaxHeight", function () {
    var _DOM$siteHeader, _DOM$logoWrapper;

    // If header style is center
    if ((_DOM$siteHeader = _DOM["default"].siteHeader) !== null && _DOM$siteHeader !== void 0 && _DOM$siteHeader.classList.contains("center-header")) {
      _classPrivateFieldSet(_this, _logo, _DOM["default"].middleLogo);

      _classPrivateFieldSet(_this, _customLogo, _DOM["default"].customMiddleLogo);
    } // Return if not shrink style and on some header styles


    if (_classPrivateFieldGet(_this, _returnOnSomeHeaderStyles).call(_this)) {
      return;
    } // If mobile logo exists


    if ((_DOM$logoWrapper = _DOM["default"].logoWrapper) !== null && _DOM$logoWrapper !== void 0 && _DOM$logoWrapper.classList.contains("has-responsive-logo") && _Utility["default"].elemVisible(_DOM["default"].mobileLogo)) {
      _classPrivateFieldSet(_this, _customLogo, _DOM["default"].mobileLogo);
    } // Get logo position


    var initialLogoHeight;

    if (_classPrivateFieldGet(_this, _customLogo)) {
      initialLogoHeight = _classPrivateFieldGet(_this, _customLogo).offsetHeight;
    }

    var currentPosition = _Utility["default"].elemOffset(_DOM["default"].headerWrapper).top - _Header["default"].getOffset();

    window.addEventListener("scroll", function () {
      // When scrolling
      if (_Utility["default"].scrollBarTopPosition() !== 0 && _Utility["default"].scrollBarTopPosition() >= currentPosition) {
        Array.from(_classPrivateFieldGet(_this, _logo)).forEach(function (elem) {
          return elem.style.maxHeight = _Helpers["default"].getShrinkLogoHeight() + "px";
        });
      } else if (!!initialLogoHeight) {
        Array.from(_classPrivateFieldGet(_this, _logo)).forEach(function (elem) {
          return elem.style.maxHeight = initialLogoHeight + "px";
        });
      }
    });
  });

  _returnOnSomeHeaderStyles.set(this, {
    writable: true,
    value: function value() {
      var _DOM$siteHeader2, _DOM$siteHeader3, _DOM$siteHeader4, _DOM$siteHeader5;

      return !_Helpers["default"].shrinkStickyStyle() || !_classPrivateFieldGet(_this, _logo) || !_DOM["default"].headerWrapper || _Helpers["default"].isMobileStickyDisabled() || _Helpers["default"].manualSticky() || !((_DOM$siteHeader2 = _DOM["default"].siteHeader) !== null && _DOM$siteHeader2 !== void 0 && _DOM$siteHeader2.classList.contains("fixed-scroll")) || ((_DOM$siteHeader3 = _DOM["default"].siteHeader) === null || _DOM$siteHeader3 === void 0 ? void 0 : _DOM$siteHeader3.classList.contains("top-header")) || ((_DOM$siteHeader4 = _DOM["default"].siteHeader) === null || _DOM$siteHeader4 === void 0 ? void 0 : _DOM$siteHeader4.classList.contains("vertical-header")) || ((_DOM$siteHeader5 = _DOM["default"].siteHeader) === null || _DOM$siteHeader5 === void 0 ? void 0 : _DOM$siteHeader5.classList.contains("medium-header")) && _DOM["default"].bottomHeader.classList.contains("fixed-scroll");
    }
  });

  _classPrivateFieldSet(this, _logo, _DOM["default"].logo);

  _classPrivateFieldSet(this, _customLogo, _DOM["default"].customLogo);
};

exports["default"] = Logo;

},{"../Utils/DOM":4,"../Utils/Helpers":6,"../Utils/Utility":7,"./Header":1}],3:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _DOM = _interopRequireDefault(require("../Utils/DOM"));

var _Utility = _interopRequireDefault(require("../Utils/Utility"));

var _Helpers = _interopRequireDefault(require("../Utils/Helpers"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classPrivateFieldGet(receiver, privateMap) { var descriptor = _classExtractFieldDescriptor(receiver, privateMap, "get"); return _classApplyDescriptorGet(receiver, descriptor); }

function _classExtractFieldDescriptor(receiver, privateMap, action) { if (!privateMap.has(receiver)) { throw new TypeError("attempted to " + action + " private field on non-instance"); } return privateMap.get(receiver); }

function _classApplyDescriptorGet(receiver, descriptor) { if (descriptor.get) { return descriptor.get.call(receiver); } return descriptor.value; }

var _noSticky = /*#__PURE__*/new WeakMap();

var Topbar = function Topbar() {
  var _this = this;

  _classCallCheck(this, Topbar);

  _defineProperty(this, "sticky", function () {
    if (_classPrivateFieldGet(_this, _noSticky).call(_this)) {
      return;
    }

    var currentPosition = 0;

    if (!!_DOM["default"].topbarWrapper) {
      currentPosition = _Utility["default"].elemOffset(_DOM["default"].topbarWrapper).top - _this.getOffset();
    } // When scrolling


    if (_Utility["default"].scrollBarTopPosition() !== 0 && _Utility["default"].scrollBarTopPosition() >= currentPosition) {
      var _DOM$topbarWrapper, _DOM$topbarWrapper2;

      (_DOM$topbarWrapper = _DOM["default"].topbarWrapper) === null || _DOM$topbarWrapper === void 0 ? void 0 : _DOM$topbarWrapper.classList.add("is-sticky");
      _DOM["default"].topbar.style.top = _this.getOffset() + "px";
      _DOM["default"].topbar.style.width = ((_DOM$topbarWrapper2 = _DOM["default"].topbarWrapper) === null || _DOM$topbarWrapper2 === void 0 ? void 0 : _DOM$topbarWrapper2.offsetWidth) + "px";
    } else {
      var _DOM$topbarWrapper3;

      (_DOM$topbarWrapper3 = _DOM["default"].topbarWrapper) === null || _DOM$topbarWrapper3 === void 0 ? void 0 : _DOM$topbarWrapper3.classList.remove("is-sticky");
      _DOM["default"].topbar.style.top = "";
      _DOM["default"].topbar.style.width = "";
    }
  });

  _defineProperty(this, "updateSticky", function () {
    if (!_DOM["default"].topbar || !_DOM["default"].topbarWrapper || !_Helpers["default"].isTopbarStickyEnabled()) {
      return;
    }

    if (!_DOM["default"].topbarWrapper.classList.contains("is-sticky")) {
      _DOM["default"].topbarWrapper.style.height = _DOM["default"].topbar.offsetHeight + "px";
    }

    if (_Utility["default"].scrollBarTopPosition() !== 0) {
      var _DOM$topbarWrapper4;

      _DOM["default"].topbar.style.top = _this.getOffset() + "px";
      _DOM["default"].topbar.style.width = ((_DOM$topbarWrapper4 = _DOM["default"].topbarWrapper) === null || _DOM$topbarWrapper4 === void 0 ? void 0 : _DOM$topbarWrapper4.offsetWidth) + "px";
    }
  });

  _defineProperty(this, "createStickyWrapper", function () {
    if (!_Helpers["default"].isTopbarStickyEnabled()) {
      return;
    } // Create topbar sticky wrapper element


    _DOM["default"].topbarWrapper = document.createElement("div");

    _DOM["default"].topbarWrapper.setAttribute("id", "top-bar-sticky-wrapper");

    _DOM["default"].topbarWrapper.setAttribute("class", "oceanwp-sticky-top-bar-holder"); // Wrap topbar sticky wrapper around topbar


    if (!!_DOM["default"].topbar) {
      var _DOM$topbarWrapper5;

      (_DOM$topbarWrapper5 = _DOM["default"].topbarWrapper) === null || _DOM$topbarWrapper5 === void 0 ? void 0 : _DOM$topbarWrapper5.oceanWrapAll(_DOM["default"].topbar); // Set topbar sticky wrapper height

      _DOM["default"].topbarWrapper.style.height = _DOM["default"].topbar.offsetHeight + "px";
    }
  });

  _defineProperty(this, "getOffset", function () {
    var offset = 0; // Add WP Adminbar offset

    if (_Utility["default"].isWPAdminbarVisible()) {
      var _DOM$WPAdminbar;

      offset = offset + ((_DOM$WPAdminbar = _DOM["default"].WPAdminbar) === null || _DOM$WPAdminbar === void 0 ? void 0 : _DOM$WPAdminbar.offsetHeight);
    }

    return offset;
  });

  _noSticky.set(this, {
    writable: true,
    value: function value() {
      return !_Helpers["default"].isTopbarStickyEnabled() || !_DOM["default"].topbar || _Helpers["default"].isMobileStickyDisabled();
    }
  });
};

exports["default"] = Topbar;

},{"../Utils/DOM":4,"../Utils/Helpers":6,"../Utils/Utility":7}],4:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _Helpers = _interopRequireDefault(require("./Helpers"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

var DOM = {
  WPAdminbar: document.querySelector("#wpadminbar"),
  topbar: document.querySelector("#top-bar-wrap"),
  siteHeader: document.querySelector("#site-header"),
  verticalHeader: document.querySelector("#site-header.vertical-header"),
  bottomHeader: document.querySelector(".bottom-header-wrap"),
  logoWrapper: document.querySelector("#site-logo"),
  logo: document.querySelectorAll("#site-logo img"),
  customLogo: document.querySelector("#site-logo .custom-logo"),
  middleLogo: document.querySelectorAll(".middle-site-logo img"),
  customMiddleLogo: document.querySelector(".middle-site-logo .custom-logo"),
  mobileLogo: document.querySelector("#site-logo .responsive-logo")
};

DOM.getHeader = function () {
  var _DOM$siteHeader, _DOM$siteHeader2, _DOM$bottomHeader;

  var headerClass; // If manual sticky

  if (_Helpers["default"].manualSticky()) {
    headerClass = ".owp-sticky";
  } else {
    headerClass = "#site-header";
  } // If top header style


  if ((_DOM$siteHeader = DOM.siteHeader) !== null && _DOM$siteHeader !== void 0 && _DOM$siteHeader.classList.contains("top-header")) {
    headerClass = "#site-header .header-top";
  } // Medium header style


  if ((_DOM$siteHeader2 = DOM.siteHeader) !== null && _DOM$siteHeader2 !== void 0 && _DOM$siteHeader2.classList.contains("medium-header") && (_DOM$bottomHeader = DOM.bottomHeader) !== null && _DOM$bottomHeader !== void 0 && _DOM$bottomHeader.classList.contains("fixed-scroll")) {
    headerClass = ".bottom-header-wrap";
  }

  return document.querySelector(headerClass);
};

DOM.header = DOM.getHeader();
var _default = DOM;
exports["default"] = _default;

},{"./Helpers":6}],5:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _default = function () {
  // Wrap an HTMLElement around each element in an HTMLElement array.
  HTMLElement.prototype.oceanWrap = function (elms) {
    // Convert `elms` to an array, if necessary.
    if (!elms.length) elms = [elms]; // Loops backwards to prevent having to clone the wrapper on the
    // first element (see `child` below).

    for (var i = elms.length - 1; i >= 0; i--) {
      var child = i > 0 ? this.cloneNode(true) : this;
      var el = elms[i]; // Cache the current parent and sibling.

      var parent = el.parentNode;
      var sibling = el.nextSibling; // Wrap the element (is automatically removed from its current
      // parent).

      child.appendChild(el); // If the element had a sibling, insert the wrapper before
      // the sibling to maintain the HTML structure; otherwise, just
      // append it to the parent.

      if (sibling) {
        parent.insertBefore(child, sibling);
      } else {
        parent.appendChild(child);
      }
    }
  }; // Wrap an HTMLElement around another HTMLElement or an array of them.


  HTMLElement.prototype.oceanWrapAll = function (elms) {
    var el = !!elms && elms.length ? elms[0] : elms; // Cache the current parent and sibling of the first element.

    var parent = el.parentNode;
    var sibling = el.nextSibling; // Wrap the first element (is automatically removed from its
    // current parent).

    this.appendChild(el); // Wrap all other elements (if applicable). Each element is
    // automatically removed from its current parent and from the elms
    // array.

    while (elms.length) {
      this.appendChild(elms[0]);
    } // If the first element had a sibling, insert the wrapper before the
    // sibling to maintain the HTML structure; otherwise, just append it
    // to the parent.


    if (sibling) {
      parent.insertBefore(this, sibling);
    } else {
      parent.appendChild(this);
    }
  };
}();

exports["default"] = _default;

},{}],6:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var Helpers = function Helpers() {
  _classCallCheck(this, Helpers);
};

exports["default"] = Helpers;

_defineProperty(Helpers, "isTopbarStickyEnabled", function () {
  return oceanwpLocalize.hasStickyTopBar == true;
});

_defineProperty(Helpers, "isMobileStickyDisabled", function () {
  return window.innerWidth <= 960 && oceanwpLocalize.hasStickyMobile != true;
});

_defineProperty(Helpers, "slideStickyEffect", function () {
  return oceanwpLocalize.stickyEffect == "slide";
});

_defineProperty(Helpers, "upStickyEffect", function () {
  return oceanwpLocalize.stickyEffect == "up";
});

_defineProperty(Helpers, "manualSticky", function () {
  return oceanwpLocalize.stickyChoose == "manual";
});

_defineProperty(Helpers, "shrinkStickyStyle", function () {
  return oceanwpLocalize.stickyStyle == "shrink";
});

_defineProperty(Helpers, "getShrinkLogoHeight", function () {
  var shrinkLogoHeight = parseInt(oceanwpLocalize.shrinkLogoHeight);
  return shrinkLogoHeight ? shrinkLogoHeight : 30;
});

},{}],7:[function(require,module,exports){
"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

var _DOM = _interopRequireDefault(require("./DOM"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

var Utility = function Utility() {
  _classCallCheck(this, Utility);
};

exports["default"] = Utility;

_defineProperty(Utility, "scrollBarTopPosition", function () {
  return window.pageYOffset;
});

_defineProperty(Utility, "elemExists", function (elem) {
  return elem && elem !== null;
});

_defineProperty(Utility, "elemVisible", function (elem) {
  return !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length);
});

_defineProperty(Utility, "elemOffset", function (elem) {
  if (!elem.getClientRects().length) {
    return {
      top: 0,
      left: 0
    };
  } // Get document-relative position by adding viewport scroll to viewport-relative gBCR


  var rect = elem.getBoundingClientRect();
  var win = elem.ownerDocument.defaultView;
  return {
    top: rect.top + win.pageYOffset,
    left: rect.left + win.pageXOffset
  };
});

_defineProperty(Utility, "isWPAdminbarVisible", function () {
  return Utility.elemExists(_DOM["default"].WPAdminbar) && window.innerWidth > 600;
});

},{"./DOM":4}],8:[function(require,module,exports){
"use strict";

require("./Utils/DOMMethods");

var _Utility = _interopRequireDefault(require("./Utils/Utility"));

var _Topbar = _interopRequireDefault(require("./Components/Topbar"));

var _Header = _interopRequireDefault(require("./Components/Header"));

var _Logo = _interopRequireDefault(require("./Components/Logo"));

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { "default": obj }; }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

function _classPrivateFieldGet(receiver, privateMap) { var descriptor = _classExtractFieldDescriptor(receiver, privateMap, "get"); return _classApplyDescriptorGet(receiver, descriptor); }

function _classApplyDescriptorGet(receiver, descriptor) { if (descriptor.get) { return descriptor.get.call(receiver); } return descriptor.value; }

function _classPrivateFieldSet(receiver, privateMap, value) { var descriptor = _classExtractFieldDescriptor(receiver, privateMap, "set"); _classApplyDescriptorSet(receiver, descriptor, value); return value; }

function _classExtractFieldDescriptor(receiver, privateMap, action) { if (!privateMap.has(receiver)) { throw new TypeError("attempted to " + action + " private field on non-instance"); } return privateMap.get(receiver); }

function _classApplyDescriptorSet(receiver, descriptor, value) { if (descriptor.set) { descriptor.set.call(receiver, value); } else { if (!descriptor.writable) { throw new TypeError("attempted to set read only private field"); } descriptor.value = value; } }

var _scrollBarlatestTopPosition = /*#__PURE__*/new WeakMap();

var _setupEventListeners = /*#__PURE__*/new WeakMap();

var _onWindowLoad = /*#__PURE__*/new WeakMap();

var _onWindowScroll = /*#__PURE__*/new WeakMap();

var _onWindowResize = /*#__PURE__*/new WeakMap();

var OW_StickyHeader = function OW_StickyHeader() {
  var _this = this;

  _classCallCheck(this, OW_StickyHeader);

  _scrollBarlatestTopPosition.set(this, {
    writable: true,
    value: void 0
  });

  _defineProperty(this, "start", function () {
    _classPrivateFieldSet(_this, _scrollBarlatestTopPosition, _Utility["default"].scrollBarTopPosition());

    _classPrivateFieldGet(_this, _setupEventListeners).call(_this);
  });

  _setupEventListeners.set(this, {
    writable: true,
    value: function value() {
      window.addEventListener("load", _classPrivateFieldGet(_this, _onWindowLoad));
      window.addEventListener("scroll", _classPrivateFieldGet(_this, _onWindowScroll));
      window.addEventListener("resize", _classPrivateFieldGet(_this, _onWindowResize));
      window.addEventListener("orientationchange", _classPrivateFieldGet(_this, _onWindowResize));
    }
  });

  _onWindowLoad.set(this, {
    writable: true,
    value: function value() {
      _this.topbar.createStickyWrapper();

      _this.header.createStickyWrapper();

      _this.header.addVerticalHeaderSticky();

      _this.logo.setMaxHeight();
    }
  });

  _onWindowScroll.set(this, {
    writable: true,
    value: function value() {
      if (_Utility["default"].scrollBarTopPosition() != _classPrivateFieldGet(_this, _scrollBarlatestTopPosition)) {
        _this.topbar.sticky();

        _this.header.sticky();

        _this.header.stickyEffects();

        _this.header.addVerticalHeaderSticky();

        _classPrivateFieldSet(_this, _scrollBarlatestTopPosition, _Utility["default"].scrollBarTopPosition());
      }
    }
  });

  _onWindowResize.set(this, {
    writable: true,
    value: function value() {
      _this.topbar.updateSticky();

      _this.header.updateSticky();
    }
  });

  this.topbar = new _Topbar["default"]();
  this.header = new _Header["default"]();
  this.logo = new _Logo["default"]();
};

"use strict";

var stickyHeader = new OW_StickyHeader();
stickyHeader.start();

},{"./Components/Header":1,"./Components/Logo":2,"./Components/Topbar":3,"./Utils/DOMMethods":5,"./Utils/Utility":7}]},{},[8])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvc3JjL2pzL0NvbXBvbmVudHMvSGVhZGVyLmpzIiwiYXNzZXRzL3NyYy9qcy9Db21wb25lbnRzL0xvZ28uanMiLCJhc3NldHMvc3JjL2pzL0NvbXBvbmVudHMvVG9wYmFyLmpzIiwiYXNzZXRzL3NyYy9qcy9VdGlscy9ET00uanMiLCJhc3NldHMvc3JjL2pzL1V0aWxzL0RPTU1ldGhvZHMuanMiLCJhc3NldHMvc3JjL2pzL1V0aWxzL0hlbHBlcnMuanMiLCJhc3NldHMvc3JjL2pzL1V0aWxzL1V0aWxpdHkuanMiLCJhc3NldHMvc3JjL2pzL3N0aWNreS1oZWFkZXIuanMiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7Ozs7Ozs7O0FDQUE7O0FBQ0E7O0FBQ0E7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7SUFFcUIsTTs7Ozs7OztXQUNPOzs7a0NBRWYsWUFBTTtBQUFBOztBQUNYLDhCQUFJLEtBQUosa0JBQUksS0FBSixHQUFzQjtBQUNsQjtBQUNIOztBQUVELFFBQUksRUFBRSxnQkFBSSxhQUFKLElBQXFCLGdCQUFJLFVBQXpCLElBQXVDLGdCQUFJLE1BQTdDLENBQUosRUFBMEQ7QUFDdEQ7QUFDSDs7QUFFRCxRQUFJLGVBQWUsR0FBRyxvQkFBUSxVQUFSLENBQW1CLGdCQUFJLGFBQXZCLEVBQXNDLEdBQXRDLEdBQTRDLE1BQU0sQ0FBQyxTQUFQLEVBQWxFO0FBQ0EsUUFBSSwwQkFBMEIsR0FBRyxlQUFqQyxDQVZXLENBWVg7O0FBQ0EsUUFBSSxvQkFBUSxpQkFBUixNQUErQixxQkFBQyxnQkFBSSxVQUFMLDRDQUFDLGdCQUFnQixTQUFoQixDQUEwQixRQUExQixDQUFtQyxpQkFBbkMsQ0FBRCxDQUFuQyxFQUEyRjtBQUN2RixNQUFBLGVBQWUsR0FBRyxlQUFlLEdBQUcsZ0JBQUksYUFBSixDQUFrQixZQUF0RDtBQUNILEtBZlUsQ0FpQlg7OztBQUNBLFFBQUksb0JBQVEs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Wjs7QUFDQSxNQUFBLEtBQUksQ0FBQyxNQUFMLENBQVksWUFBWjtBQUNIO0FBeENhOztBQUNWLE9BQUssTUFBTCxHQUFjLElBQUksa0JBQUosRUFBZDtBQUNBLE9BQUssTUFBTCxHQUFjLElBQUksa0JBQUosRUFBZDtBQUNBLE9BQUssSUFBTCxHQUFZLElBQUksZ0JBQUosRUFBWjtBQUNILEM7O0FBdUNKLFlBQUQ7O0FBRUEsSUFBTSxZQUFZLEdBQUcsSUFBSSxlQUFKLEVBQXJCO0FBQ0EsWUFBWSxDQUFDLEtBQWIiLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbigpe2Z1bmN0aW9uIHIoZSxuLHQpe2Z1bmN0aW9uIG8oaSxmKXtpZighbltpXSl7aWYoIWVbaV0pe3ZhciBjPVwiZnVuY3Rpb25cIj09dHlwZW9mIHJlcXVpcmUmJnJlcXVpcmU7aWYoIWYmJmMpcmV0dXJuIGMoaSwhMCk7aWYodSlyZXR1cm4gdShpLCEwKTt2YXIgYT1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK2krXCInXCIpO3Rocm93IGEuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixhfXZhciBwPW5baV09e2V4cG9ydHM6e319O2VbaV1bMF0uY2FsbChwLmV4cG9ydHMsZnVuY3Rpb24ocil7dmFyIG49ZVtpXVsxXVtyXTtyZXR1cm4gbyhufHxyKX0scCxwLmV4cG9ydHMscixlLG4sdCl9cmV0dXJuIG5baV0uZXhwb3J0c31mb3IodmFyIHU9XCJmdW5jdGlvblwiPT10eXBlb2YgcmVxdWlyZSYmcmVxdWlyZSxpPTA7aTx0Lmxlbmd0aDtpKyspbyh0W2ldKTtyZXR1cm4gb31yZXR1cm4gcn0pKCkiLCJpbXBvcnQgRE9NIGZyb20gXCIuLi9VdGlscy9ET01cIjtcbmltcG9ydCBVdGlsaXR5IGZyb20gXCIuLi9VdGlscy9VdGlsaXR5XCI7XG5pbXBvcnQgSGVscGVycyBmcm9tIFwiLi4vVXRpbHMvSGVscGVyc1wiO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBIZWFkZXIge1xuICAgICNsYXN0U2Nyb2xCYXJQb3NpdGlvbiA9IDA7XG5cbiAgICBzdGlja3kgPSAoKSA9PiB7XG4gICAgICAgIGlmICh0aGlzLiNub1N0aWNreSgpKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoIShET00uaGVhZGVyV3JhcHBlciB8fCBET00uc2l0ZUhlYWRlciB8fCBET00uaGVhZGVyKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgbGV0IGN1cnJlbnRQb3NpdGlvbiA9IFV0aWxpdHkuZWxlbU9mZnNldChET00uaGVhZGVyV3JhcHBlcikudG9wIC0gSGVhZGVyLmdldE9mZnNldCgpO1xuICAgICAgICBsZXQgc2xpZGVTdGlja3lDdXJyZW50UG9zaXRpb24gPSBjdXJyZW50UG9zaXRpb247XG5cbiAgICAgICAgLy8gSWYgc2xpZGUgZWZmZWN0XG4gICAgICAgIGlmIChIZWxwZXJzLnNsaWRlU3RpY2t5RWZmZWN0KCkgJiYgIURPTS5zaXRlSGVhZGVyPy5jbGFzc0xpc3QuY29udGFpbnMoXCJ2ZXJ0aWNhbC1oZWFkZXJcIikpIHtcbiAgICAgICAgICAgIGN1cnJlbnRQb3NpdGlvbiA9IGN1cnJlbnRQb3NpdGlvbiArIERPTS5oZWFkZXJXcmFwcGVyLm9mZnNldEhlaWdodDtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIFdoZW4gc2Nyb2xsaW5nXG4gICAgICAgIGlmIChVdGlsaXR5LnNjcm9sbEJhclRvcFBvc2l0aW9uKCkgIT09IDAgJiYgVXRpbGl0eS5zY3JvbGxCYXJUb3BQb3NpdGlvbigpID49IGN1cnJlbnRQb3NpdGlvbikge1xuICAgICAgICAgICAgRE9NLmhlYWRlcldyYXBwZXIuY2xhc3NMaXN0LmFkZChcImlzLXN0aWNreVwiKTtcblxuICAgICAgICAgICAgRE9NLmhlYWRlci5zdHlsZS50b3AgPSBIZWFkZXIuZ2V0T2Zmc2V0KCkgKyBcInB4XCI7XG4gICAgICAgICAgICBET00uaGVhZGVyLnN0eWxlLndpZHRoID0gRE9NLmhlYWRlcldyYXBwZXIub2Zmc2V0V2lkdGggKyBcInB4XCI7XG5cbiAgICAgICAgICAgIC8vIElmIHNsaWRlIGVmZmVjdFxuICAgICAgICAgICAgaWYgKEhlbHBlcnMuc2xpZGVTdGlja3lFZmZlY3QoKSAmJiAhRE9NLnNpdGVIZWFkZXIuY2xhc3NMaXN0LmNvbnRhaW5zKFwidmVydGljYWwtaGVhZGVyXCIpKSB7XG4gICAgICAgICAgICAgICAgRE9NLnNpdGVIZWFkZXIuY2xhc3NMaXN0LmFkZChcInNob3dcIik7XG4gICAgICAgICAgICB9XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAvLyBJZiBpcyBub3Qgc2xpZGUgZWZmZWN0XG4gICAgICAgICAgICBpZiAoIUhlbHBlcnMuc2xpZGVTdGlja3lFZmZlY3QoKSkge1xuICAgICAgICAgICAgICAgIC8vIFJlbW92ZSBzdGlja3kgd3JhcCBjbGFzc1xuICAgICAgICAgICAgICAgIERPTS5oZWFkZXJXcmFwcGVyLmNsYXNzTGlzdC5yZW1vdmUoXCJpcy1zdGlja3lcIik7XG5cbiAgICAgICAgICAgICAgICBET00uaGVhZGVyLnN0eWxlLnRvcCA9IFwiXCI7XG4gICAgICAgICAgICAgICAgRE9NLmhlYWRlci5zdHlsZS53aWR0aCA9IFwiXCI7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICAvLyBJZiBzbGlkZSBlZmZlY3RcbiAgICAgICAgaWYgKEhlbHBlcnMuc2xpZGVTdGlja3lFZmZlY3QoKSAmJiAhRE9NLnNpdGVIZWFkZXIuY2xhc3NMaXN0LmNvbnRhaW5zKFwidmVydGljYWwtaGVhZGVyXCIpKSB7XG4gICAgICAgICAgICAvLyBSZW1vdmUgc3RpY2t5IGNsYXNzIHdoZW4gd2luZG93IHRvcFxuICAgICAgICAgICAgaWYgKFV0aWxpdHkuc2Nyb2xsQmFyVG9wUG9zaXRpb24oKSA8PSBzbGlkZVN0aWNreUN1cnJlbnRQb3NpdGlvbikge1xuICAgICAgICAgICAgICAgIC8vIFJlbW92ZSBzdGlja3kgd3JhcCBjbGFzc1xuICAgICAgICAgICAgICAgIERPTS5oZWFkZXJXcmFwcGVyLmNsYXNzTGlzdC5yZW1vdmUoXCJpcy1zdGlja3lcIik7XG5cbiAgICAgICAgICAgICAgICBET00uaGVhZGVyLnN0eWxlLnRvcCA9IFwiXCI7XG4gICAgICAgICAgICAgICAgRE9NLmhlYWRlci5zdHlsZS53aWR0aCA9IFwiXCI7XG5cbiAgICAgICAgICAgICAgICAvLyBSZW1vdmUgc2xpZGUgZWZmZWN0IGNsYXNzXG4gICAgICAgICAgICAgICAgRE9NLnNpdGVIZWFkZXIuY2xhc3NMaXN0LnJlbW92ZShcInNob3dcIik7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgdXBkYXRlU3RpY2t5ID0gKCkgPT4ge1xuICAgICAgICAvLyBSZXR1cm4gaWYgaXMgdmVydGljYWwgaGVhZGVyIHN0eWxlXG4gICAgICAgIGlmICh3aW5kb3cuaW5uZXJXaWR0aCA+IDk2MCAmJiBET00uc2l0ZUhlYWRlcj8uY2xhc3NMaXN0LmNvbnRhaW5zKFwidmVydGljYWwtaGVhZGVyXCIpKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoIURPTS5oZWFkZXJXcmFwcGVyPy5jbGFzc0xpc3QuY29udGFpbnMoXCJpcy1zdGlja3lcIikgJiYgISFET00uaGVhZGVyKSB7XG4gICAgICAgICAgICBpZiAoIERPTS5oZWFkZXJXcmFwcGVyICkge1xuICAgICAgICAgICAgICAgIERPTS5oZWFkZXJXcmFwcGVyLnN0eWxlLmhlaWdodCA9IERPTS5oZWFkZXIub2Zmc2V0SGVpZ2h0ICsgXCJweFwiO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG5cbiAgICAgICAgaWYgKFV0aWxpdHkuc2Nyb2xsQmFyVG9wUG9zaXRpb24oKSAhPT0gMCkge1xuICAgICAgICAgICAgaWYgKCEhRE9NLmhlYWRlciAmJiAhIURPTS5oZWFkZXJXcmFwcGVyKSB7XG4gICAgICAgICAgICAgICAgRE9NLmhlYWRlci5zdHlsZS50b3AgPSBIZWFkZXIuZ2V0T2Zmc2V0KCkgKyBcInB4XCI7XG4gICAgICAgICAgICAgICAgRE9NLmhlYWRlci5zdHlsZS53aWR0aCA9IERPTS5oZWFkZXJXcmFwcGVyLm9mZnNldFdpZHRoICsgXCJweFwiO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfTtcblxuICAgIGFkZFZlcnRpY2FsSGVhZGVyU3RpY2t5ID0gKCkgPT4ge1xuICAgICAgICAvLyBSZXR1cm4gaWYgaXMgbm90IHZlcnRpY2FsIGhlYWRlciBzdHlsZSBhbmQgdHJhbnNwYXJlbnRcbiAgICAgICAgaWYgKCFET00udmVydGljYWxIZWFkZXI/LmNsYXNzTGlzdC5jb250YWlucyhcImlzLXRyYW5zcGFyZW50XCIpKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICAvLyBSZXR1cm4gaWYgbm8gaGVhZGVyIHdyYXBwZXJcbiAgICAgICAgaWYgKCFET00uaGVhZGVyV3JhcHBlcikge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgbGV0IGN1cnJlbnRQb3NpdGlvbiA9IFV0aWxpdHkuZWxlbU9mZnNldChET00uaGVhZGVyV3JhcHBlcikudG9wO1xuXG4gICAgICAgIC8vIFdoZW4gc2Nyb2xsaW5nXG4gICAgICAgIGlmIChVdGlsaXR5LnNjcm9sbEJhclRvcFBvc2l0aW9uKCkgIT09IDAgJiYgVXRpbGl0eS5zY3JvbGxCYXJUb3BQb3NpdGlvbigpID49IGN1cnJlbnRQb3NpdGlvbikge1xuICAgICAgICAgICAgRE9NLmhlYWRlcldyYXBwZXIuY2xhc3NMaXN0LmFkZChcImlzLXN0aWNreVwiKTtcbiAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgIERPTS5oZWFkZXJXcmFwcGVyLmNsYXNzTGlzdC5yZW1vdmUoXCJpcy1zdGlja3lcIik7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgc3RpY2t5RWZmZWN0cyA9ICgpID0+IHtcbiAgICAgICAgLy8gUmV0dXJuIGlmIGlzIHZlcnRpY2FsIGhlYWRlciBzdHlsZVxuICAgICAgICBpZiAoRE9NLnNpdGVIZWFkZXI/LmNsYXNzTGlzdC5jb250YWlucyhcInZlcnRpY2FsLWhlYWRlclwiKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gUmV0dXJuIGlmIG5vIGhlYWRlciB3cmFwcGVyXG4gICAgICAgIGlmICghRE9NLmhlYWRlcldyYXBwZXIpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIElmIHNob3cgdXAgZWZmZWN0XG4gICAgICAgIGlmIChIZWxwZXJzLnVwU3RpY2t5RWZmZWN0KCkpIHtcbiAgICAgICAgICAgIGNvbnN0IGN1cnJlbnRQb3NpdGlvbiA9IFV0aWxpdHkuZWxlbU9mZnNldChET00uaGVhZGVyV3JhcHBlcikudG9wICsgRE9NLmhlYWRlcldyYXBwZXIub2Zmc2V0SGVpZ2h0O1xuICAgICAgICAgICAgY29uc3Qgc2Nyb2xsQmFyVG9wUG9zaXRpb24gPSBkb2N1bWVudC5kb2N1bWVudEVsZW1lbnQuc2Nyb2xsVG9wO1xuXG4gICAgICAgICAgICBpZiAoc2Nyb2xsQmFyVG9wUG9zaXRpb24gPj0gdGhpcy4jbGFzdFNjcm9sQmFyUG9zaXRpb24gJiYgc2Nyb2xsQmFyVG9wUG9zaXRpb24gPj0gY3VycmVudFBvc2l0aW9uKSB7XG4gICAgICAgICAgICAgICAgRE9NLnNpdGVIZWFkZXIuY2xhc3NMaXN0LnJlbW92ZShcImhlYWRlci1kb3duXCIpO1xuICAgICAgICAgICAgICAgIERPTS5zaXRlSGVhZGVyLmNsYXNzTGlzdC5hZGQoXCJoZWFkZXItdXBcIik7XG4gICAgICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgICAgIERPTS5zaXRlSGVhZGVyLmNsYXNzTGlzdC5yZW1vdmUoXCJoZWFkZXItdXBcIik7XG4gICAgICAgICAgICAgICAgRE9NLnNpdGVIZWFkZXIuY2xhc3NMaXN0LmFkZChcImhlYWRlci1kb3duXCIpO1xuICAgICAgICAgICAgfVxuXG4gICAgICAgICAgICB0aGlzLiNsYXN0U2Nyb2xCYXJQb3NpdGlvbiA9IHNjcm9sbEJhclRvcFBvc2l0aW9uO1xuICAgICAgICB9XG4gICAgfTtcblxuICAgIGNyZWF0ZVN0aWNreVdyYXBwZXIgPSAoKSA9PiB7XG4gICAgICAgIC8vIENyZWF0ZSBoZWFkZXIgc3RpY2t5IHdyYXBwZXIgZWxlbWVudFxuICAgICAgICBET00uaGVhZGVyV3JhcHBlciA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoXCJkaXZcIik7XG4gICAgICAgIERPTS5oZWFkZXJXcmFwcGVyLnNldEF0dHJpYnV0ZShcImlkXCIsIFwic2l0ZS1oZWFkZXItc3RpY2t5LXdyYXBwZXJcIik7XG4gICAgICAgIERPTS5oZWFkZXJXcmFwcGVyLnNldEF0dHJpYnV0ZShcImNsYXNzXCIsIFwib2NlYW53cC1zdGlja3ktaGVhZGVyLWhvbGRlclwiKTtcblxuICAgICAgICAvLyBXcmFwIGhlYWRlciBzdGlja3kgd3JhcHBlciBhcm91bmQgaGVhZGVyXG4gICAgICAgIGlmICghIURPTS5oZWFkZXIpIHtcbiAgICAgICAgICAgIERPTS5oZWFkZXJXcmFwcGVyPy5vY2VhbldyYXBBbGwoRE9NLmhlYWRlcik7XG4gICAgICAgIH1cblxuICAgICAgICAvLyBTZXQgaGVhZGVyIHN0aWNreSB3cmFwcGVyIGhlaWdodFxuICAgICAgICBpZiAoIURPTS5zaXRlSGVhZGVyPy5jbGFzc0xpc3QuY29udGFpbnMoXCJ2ZXJ0aWNhbC1oZWFkZXJcIikpIHtcbiAgICAgICAgICAgIGlmICghIURPTS5oZWFkZXJXcmFwcGVyICYmICEhRE9NLmhlYWRlcikge1xuICAgICAgICAgICAgICAgIERPTS5oZWFkZXJXcmFwcGVyLnN0eWxlLmhlaWdodCA9IERPTS5oZWFkZXIub2Zmc2V0SGVpZ2h0ICsgXCJweFwiO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfTtcblxuICAgIHN0YXRpYyBnZXRPZmZzZXQgPSAoKSA9PiB7XG4gICAgICAgIGxldCBvZmZzZXQgPSAwO1xuXG4gICAgICAgIC8vIEFkZCBXUCBBZG1pbmJhciBvZmZzZXRcbiAgICAgICAgaWYgKFV0aWxpdHkuaXNXUEFkbWluYmFyVmlzaWJsZSgpKSB7XG4gICAgICAgICAgICBpZiAoISFET00uV1BBZG1pbmJhcikge1xuICAgICAgICAgICAgICAgIG9mZnNldCA9IG9mZnNldCArIERPTS5XUEFkbWluYmFyLm9mZnNldEhlaWdodDtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIC8vIE9mZnNldCB0b3BiYXIgc3RpY2t5XG4gICAgICAgIGlmIChIZWxwZXJzLmlzVG9wYmFyU3RpY2t5RW5hYmxlZCgpKSB7XG4gICAgICAgICAgICBpZiAoISFET00udG9wYmFyKSB7XG4gICAgICAgICAgICAgICAgb2Zmc2V0ID0gb2Zmc2V0ICsgRE9NLnRvcGJhci5vZmZzZXRIZWlnaHQ7XG4gICAgICAgICAgICB9XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gb2Zmc2V0O1xuICAgIH07XG5cbiAgICAjbm9TdGlja3kgPSAoKSA9PiB7XG4gICAgICAgIGlmIChET00uc2l0ZUhlYWRlcj8uY2xhc3NMaXN0LmNvbnRhaW5zKFwidmVydGljYWwtaGVhZGVyXCIpKSB7XG4gICAgICAgICAgICBpZiAod2luZG93LmlubmVyV2lkdGggPD0gOTYwKSB7XG4gICAgICAgICAgICAgICAgcmV0dXJuICFET00uaGVhZGVyV3JhcHBlciB8fCBIZWxwZXJzLmlzTW9iaWxlU3RpY2t5RGlzYWJsZWQoKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfVxuXG4gICAgICAgIHJldHVybiAhRE9NLmhlYWRlcldyYXBwZXIgfHwgSGVscGVycy5pc01vYmlsZVN0aWNreURpc2FibGVkKCkgfHwgIURPTS5zaXRlSGVhZGVyPy5jbGFzc0xpc3QuY29udGFpbnMoXCJmaXhlZC1zY3JvbGxcIik7XG4gICAgfTtcbn1cbiIsImltcG9ydCBET00gZnJvbSBcIi4uL1V0aWxzL0RPTVwiO1xuaW1wb3J0IEhlbHBlcnMgZnJvbSBcIi4uL1V0aWxzL0hlbHBlcnNcIjtcbmltcG9ydCBVdGlsaXR5IGZyb20gXCIuLi9VdGlscy9VdGlsaXR5XCI7XG5pbXBvcnQgSGVhZGVyIGZyb20gXCIuL0hlYWRlclwiO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBMb2dvIHtcbiAgICAjbG9nbztcbiAgICAjY3VzdG9tTG9nbztcblxuICAgIGNvbnN0cnVjdG9yKCkge1xuICAgICAgICB0aGlzLiNsb2dvID0gRE9NLmxvZ287XG4gICAgICAgIHRoaXMuI2N1c3RvbUxvZ28gPSBET00uY3VzdG9tTG9nbztcbiAgICB9XG5cbiAgICBzZXRNYXhIZWlnaHQgPSAoKSA9PiB7XG4gICAgICAgIC8vIElmIGhlYWRlciBzdHlsZSBpcyBjZW50ZXJcbiAgICAgICAgaWYgKERPTS5zaXRlSGVhZGVyPy5jbGFzc0xpc3QuY29udGFpbnMoXCJjZW50ZXItaGVhZGVyXCIpKSB7XG4gICAgICAgICAgICB0aGlzLiNsb2dvID0gRE9NLm1pZGRsZUxvZ287XG4gICAgICAgICAgICB0aGlzLiNjdXN0b21Mb2dvID0gRE9NLmN1c3RvbU1pZGRsZUxvZ287XG4gICAgICAgIH1cblxuICAgICAgICAvLyBSZXR1cm4gaWYgbm90IHNocmluayBzdHlsZSBhbmQgb24gc29tZSBoZWFkZXIgc3R5bGVzXG4gICAgICAgIGlmICh0aGlzLiNyZXR1cm5PblNvbWVIZWFkZXJTdHlsZXMoKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gSWYgbW9iaWxlIGxvZ28gZXhpc3RzXG4gICAgICAgIGlmIChET00ubG9nb1dyYXBwZXI/LmNsYXNzTGlzdC5jb250YWlucyhcImhhcy1yZXNwb25zaXZlLWxvZ29cIikgJiYgVXRpbGl0eS5lbGVtVmlzaWJsZShET00ubW9iaWxlTG9nbykpIHtcbiAgICAgICAgICAgIHRoaXMuI2N1c3RvbUxvZ28gPSBET00ubW9iaWxlTG9nbztcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIEdldCBsb2dvIHBvc2l0aW9uXG4gICAgICAgIGxldCBpbml0aWFsTG9nb0hlaWdodDtcbiAgICAgICAgaWYgKHRoaXMuI2N1c3RvbUxvZ28pIHtcbiAgICAgICAgICAgIGluaXRpYWxMb2dvSGVpZ2h0ID0gdGhpcy4jY3VzdG9tTG9nby5vZmZzZXRIZWlnaHQ7XG4gICAgICAgIH1cblxuICAgICAgICBsZXQgY3VycmVudFBvc2l0aW9uID0gVXRpbGl0eS5lbGVtT2Zmc2V0KERPTS5oZWFkZXJXcmFwcGVyKS50b3AgLSBIZWFkZXIuZ2V0T2Zmc2V0KCk7XG5cbiAgICAgICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoXCJzY3JvbGxcIiwgKCkgPT4ge1xuICAgICAgICAgICAgLy8gV2hlbiBzY3JvbGxpbmdcbiAgICAgICAgICAgIGlmIChVdGlsaXR5LnNjcm9sbEJhclRvcFBvc2l0aW9uKCkgIT09IDAgJiYgVXRpbGl0eS5zY3JvbGxCYXJUb3BQb3NpdGlvbigpID49IGN1cnJlbnRQb3NpdGlvbikge1xuICAgICAgICAgICAgICAgIEFycmF5LmZyb20odGhpcy4jbG9nbykuZm9yRWFjaCgoZWxlbSkgPT4gKGVsZW0uc3R5bGUubWF4SGVpZ2h0ID0gSGVscGVycy5nZXRTaHJpbmtMb2dvSGVpZ2h0KCkgKyBcInB4XCIpKTtcbiAgICAgICAgICAgIH0gZWxzZSBpZiAoISFpbml0aWFsTG9nb0hlaWdodCkge1xuICAgICAgICAgICAgICAgIEFycmF5LmZyb20odGhpcy4jbG9nbykuZm9yRWFjaCgoZWxlbSkgPT4gKGVsZW0uc3R5bGUubWF4SGVpZ2h0ID0gaW5pdGlhbExvZ29IZWlnaHQgKyBcInB4XCIpKTtcbiAgICAgICAgICAgIH1cbiAgICAgICAgfSk7XG4gICAgfTtcblxuICAgICNyZXR1cm5PblNvbWVIZWFkZXJTdHlsZXMgPSAoKSA9PiB7XG4gICAgICAgIHJldHVybiAoXG4gICAgICAgICAgICAhSGVscGVycy5zaHJpbmtTdGlja3lTdHlsZSgpIHx8XG4gICAgICAgICAgICAhdGhpcy4jbG9nbyB8fFxuICAgICAgICAgICAgIURPTS5oZWFkZXJXcmFwcGVyIHx8XG4gICAgICAgICAgICBIZWxwZXJzLmlzTW9iaWxlU3RpY2t5RGlzYWJsZWQoKSB8fFxuICAgICAgICAgICAgSGVscGVycy5tYW51YWxTdGlja3koKSB8fFxuICAgICAgICAgICAgIURPTS5zaXRlSGVhZGVyPy5jbGFzc0xpc3QuY29udGFpbnMoXCJmaXhlZC1zY3JvbGxcIikgfHxcbiAgICAgICAgICAgIERPTS5zaXRlSGVhZGVyPy5jbGFzc0xpc3QuY29udGFpbnMoXCJ0b3AtaGVhZGVyXCIpIHx8XG4gICAgICAgICAgICBET00uc2l0ZUhlYWRlcj8uY2xhc3NMaXN0LmNvbnRhaW5zKFwidmVydGljYWwtaGVhZGVyXCIpIHx8XG4gICAgICAgICAgICAoRE9NLnNpdGVIZWFkZXI/LmNsYXNzTGlzdC5jb250YWlucyhcIm1lZGl1bS1oZWFkZXJcIikgJiYgRE9NLmJvdHRvbUhlYWRlci5jbGFzc0xpc3QuY29udGFpbnMoXCJmaXhlZC1zY3JvbGxcIikpXG4gICAgICAgICk7XG4gICAgfTtcbn1cbiIsImltcG9ydCBET00gZnJvbSBcIi4uL1V0aWxzL0RPTVwiO1xuaW1wb3J0IFV0aWxpdHkgZnJvbSBcIi4uL1V0aWxzL1V0aWxpdHlcIjtcbmltcG9ydCBIZWxwZXJzIGZyb20gXCIuLi9VdGlscy9IZWxwZXJzXCI7XG5cbmV4cG9ydCBkZWZhdWx0IGNsYXNzIFRvcGJhciB7XG4gICAgc3RpY2t5ID0gKCkgPT4ge1xuICAgICAgICBpZiAodGhpcy4jbm9TdGlja3koKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgbGV0IGN1cnJlbnRQb3NpdGlvbiA9IDA7XG5cbiAgICAgICAgaWYgKCEhRE9NLnRvcGJhcldyYXBwZXIpIHtcbiAgICAgICAgICAgIGN1cnJlbnRQb3NpdGlvbiA9IFV0aWxpdHkuZWxlbU9mZnNldChET00udG9wYmFyV3JhcHBlcikudG9wIC0gdGhpcy5nZXRPZmZzZXQoKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIFdoZW4gc2Nyb2xsaW5nXG4gICAgICAgIGlmIChVdGlsaXR5LnNjcm9sbEJhclRvcFBvc2l0aW9uKCkgIT09IDAgJiYgVXRpbGl0eS5zY3JvbGxCYXJUb3BQb3NpdGlvbigpID49IGN1cnJlbnRQb3NpdGlvbikge1xuICAgICAgICAgICAgRE9NLnRvcGJhcldyYXBwZXI/LmNsYXNzTGlzdC5hZGQoXCJpcy1zdGlja3lcIik7XG5cbiAgICAgICAgICAgIERPTS50b3BiYXIuc3R5bGUudG9wID0gdGhpcy5nZXRPZmZzZXQoKSArIFwicHhcIjtcbiAgICAgICAgICAgIERPTS50b3BiYXIuc3R5bGUud2lkdGggPSBET00udG9wYmFyV3JhcHBlcj8ub2Zmc2V0V2lkdGggKyBcInB4XCI7XG4gICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICBET00udG9wYmFyV3JhcHBlcj8uY2xhc3NMaXN0LnJlbW92ZShcImlzLXN0aWNreVwiKTtcblxuICAgICAgICAgICAgRE9NLnRvcGJhci5zdHlsZS50b3AgPSBcIlwiO1xuICAgICAgICAgICAgRE9NLnRvcGJhci5zdHlsZS53aWR0aCA9IFwiXCI7XG4gICAgICAgIH1cbiAgICB9O1xuXG4gICAgdXBkYXRlU3RpY2t5ID0gKCkgPT4ge1xuICAgICAgICBpZiAoIURPTS50b3BiYXIgfHwgIURPTS50b3BiYXJXcmFwcGVyIHx8ICFIZWxwZXJzLmlzVG9wYmFyU3RpY2t5RW5hYmxlZCgpKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoIURPTS50b3BiYXJXcmFwcGVyLmNsYXNzTGlzdC5jb250YWlucyhcImlzLXN0aWNreVwiKSkge1xuICAgICAgICAgICAgRE9NLnRvcGJhcldyYXBwZXIuc3R5bGUuaGVpZ2h0ID0gRE9NLnRvcGJhci5vZmZzZXRIZWlnaHQgKyBcInB4XCI7XG4gICAgICAgIH1cblxuICAgICAgICBpZiAoVXRpbGl0eS5zY3JvbGxCYXJUb3BQb3NpdGlvbigpICE9PSAwKSB7XG4gICAgICAgICAgICBET00udG9wYmFyLnN0eWxlLnRvcCA9IHRoaXMuZ2V0T2Zmc2V0KCkgKyBcInB4XCI7XG4gICAgICAgICAgICBET00udG9wYmFyLnN0eWxlLndpZHRoID0gRE9NLnRvcGJhcldyYXBwZXI/Lm9mZnNldFdpZHRoICsgXCJweFwiO1xuICAgICAgICB9XG4gICAgfTtcblxuICAgIGNyZWF0ZVN0aWNreVdyYXBwZXIgPSAoKSA9PiB7XG4gICAgICAgIGlmICghSGVscGVycy5pc1RvcGJhclN0aWNreUVuYWJsZWQoKSkge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gQ3JlYXRlIHRvcGJhciBzdGlja3kgd3JhcHBlciBlbGVtZW50XG4gICAgICAgIERPTS50b3BiYXJXcmFwcGVyID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudChcImRpdlwiKTtcbiAgICAgICAgRE9NLnRvcGJhcldyYXBwZXIuc2V0QXR0cmlidXRlKFwiaWRcIiwgXCJ0b3AtYmFyLXN0aWNreS13cmFwcGVyXCIpO1xuICAgICAgICBET00udG9wYmFyV3JhcHBlci5zZXRBdHRyaWJ1dGUoXCJjbGFzc1wiLCBcIm9jZWFud3Atc3RpY2t5LXRvcC1iYXItaG9sZGVyXCIpO1xuXG4gICAgICAgIC8vIFdyYXAgdG9wYmFyIHN0aWNreSB3cmFwcGVyIGFyb3VuZCB0b3BiYXJcbiAgICAgICAgaWYgKCEhRE9NLnRvcGJhcikge1xuICAgICAgICAgICAgRE9NLnRvcGJhcldyYXBwZXI/Lm9jZWFuV3JhcEFsbChET00udG9wYmFyKTtcblxuICAgICAgICAgICAgLy8gU2V0IHRvcGJhciBzdGlja3kgd3JhcHBlciBoZWlnaHRcbiAgICAgICAgICAgIERPTS50b3BiYXJXcmFwcGVyLnN0eWxlLmhlaWdodCA9IERPTS50b3BiYXIub2Zmc2V0SGVpZ2h0ICsgXCJweFwiO1xuICAgICAgICB9XG4gICAgfTtcblxuICAgIGdldE9mZnNldCA9ICgpID0+IHtcbiAgICAgICAgbGV0IG9mZnNldCA9IDA7XG5cbiAgICAgICAgLy8gQWRkIFdQIEFkbWluYmFyIG9mZnNldFxuICAgICAgICBpZiAoVXRpbGl0eS5pc1dQQWRtaW5iYXJWaXNpYmxlKCkpIHtcbiAgICAgICAgICAgIG9mZnNldCA9IG9mZnNldCArIERPTS5XUEFkbWluYmFyPy5vZmZzZXRIZWlnaHQ7XG4gICAgICAgIH1cblxuICAgICAgICByZXR1cm4gb2Zmc2V0O1xuICAgIH07XG5cbiAgICAjbm9TdGlja3kgPSAoKSA9PiAhSGVscGVycy5pc1RvcGJhclN0aWNreUVuYWJsZWQoKSB8fCAhRE9NLnRvcGJhciB8fCBIZWxwZXJzLmlzTW9iaWxlU3RpY2t5RGlzYWJsZWQoKTtcbn1cbiIsImltcG9ydCBIZWxwZXJzIGZyb20gXCIuL0hlbHBlcnNcIjtcblxuY29uc3QgRE9NID0ge1xuICAgIFdQQWRtaW5iYXI6IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoXCIjd3BhZG1pbmJhclwiKSxcbiAgICB0b3BiYXI6IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoXCIjdG9wLWJhci13cmFwXCIpLFxuICAgIHNpdGVIZWFkZXI6IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoXCIjc2l0ZS1oZWFkZXJcIiksXG4gICAgdmVydGljYWxIZWFkZXI6IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoXCIjc2l0ZS1oZWFkZXIudmVydGljYWwtaGVhZGVyXCIpLFxuICAgIGJvdHRvbUhlYWRlcjogZG9jdW1lbnQucXVlcnlTZWxlY3RvcihcIi5ib3R0b20taGVhZGVyLXdyYXBcIiksXG4gICAgbG9nb1dyYXBwZXI6IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoXCIjc2l0ZS1sb2dvXCIpLFxuICAgIGxvZ286IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoXCIjc2l0ZS1sb2dvIGltZ1wiKSxcbiAgICBjdXN0b21Mb2dvOiBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKFwiI3NpdGUtbG9nbyAuY3VzdG9tLWxvZ29cIiksXG4gICAgbWlkZGxlTG9nbzogZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbChcIi5taWRkbGUtc2l0ZS1sb2dvIGltZ1wiKSxcbiAgICBjdXN0b21NaWRkbGVMb2dvOiBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKFwiLm1pZGRsZS1zaXRlLWxvZ28gLmN1c3RvbS1sb2dvXCIpLFxuICAgIG1vYmlsZUxvZ286IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3IoXCIjc2l0ZS1sb2dvIC5yZXNwb25zaXZlLWxvZ29cIiksXG59O1xuXG5ET00uZ2V0SGVhZGVyID0gKCkgPT4ge1xuICAgIGxldCBoZWFkZXJDbGFzcztcblxuICAgIC8vIElmIG1hbnVhbCBzdGlja3lcbiAgICBpZiAoSGVscGVycy5tYW51YWxTdGlja3koKSkge1xuICAgICAgICBoZWFkZXJDbGFzcyA9IFwiLm93cC1zdGlja3lcIjtcbiAgICB9IGVsc2Uge1xuICAgICAgICBoZWFkZXJDbGFzcyA9IFwiI3NpdGUtaGVhZGVyXCI7XG4gICAgfVxuXG4gICAgLy8gSWYgdG9wIGhlYWRlciBzdHlsZVxuICAgIGlmIChET00uc2l0ZUhlYWRlcj8uY2xhc3NMaXN0LmNvbnRhaW5zKFwidG9wLWhlYWRlclwiKSkge1xuICAgICAgICBoZWFkZXJDbGFzcyA9IFwiI3NpdGUtaGVhZGVyIC5oZWFkZXItdG9wXCI7XG4gICAgfVxuXG4gICAgLy8gTWVkaXVtIGhlYWRlciBzdHlsZVxuICAgIGlmIChET00uc2l0ZUhlYWRlcj8uY2xhc3NMaXN0LmNvbnRhaW5zKFwibWVkaXVtLWhlYWRlclwiKSAmJiBET00uYm90dG9tSGVhZGVyPy5jbGFzc0xpc3QuY29udGFpbnMoXCJmaXhlZC1zY3JvbGxcIikpIHtcbiAgICAgICAgaGVhZGVyQ2xhc3MgPSBcIi5ib3R0b20taGVhZGVyLXdyYXBcIjtcbiAgICB9XG5cbiAgICByZXR1cm4gZG9jdW1lbnQucXVlcnlTZWxlY3RvcihoZWFkZXJDbGFzcyk7XG59O1xuXG5ET00uaGVhZGVyID0gRE9NLmdldEhlYWRlcigpO1xuXG5leHBvcnQgZGVmYXVsdCBET007XG4iLCJleHBvcnQgZGVmYXVsdCAoKCkgPT4ge1xuICAgIC8vIFdyYXAgYW4gSFRNTEVsZW1lbnQgYXJvdW5kIGVhY2ggZWxlbWVudCBpbiBhbiBIVE1MRWxlbWVudCBhcnJheS5cbiAgICBIVE1MRWxlbWVudC5wcm90b3R5cGUub2NlYW5XcmFwID0gZnVuY3Rpb24gKGVsbXMpIHtcbiAgICAgICAgLy8gQ29udmVydCBgZWxtc2AgdG8gYW4gYXJyYXksIGlmIG5lY2Vzc2FyeS5cbiAgICAgICAgaWYgKCFlbG1zLmxlbmd0aCkgZWxtcyA9IFtlbG1zXTtcblxuICAgICAgICAvLyBMb29wcyBiYWNrd2FyZHMgdG8gcHJldmVudCBoYXZpbmcgdG8gY2xvbmUgdGhlIHdyYXBwZXIgb24gdGhlXG4gICAgICAgIC8vIGZpcnN0IGVsZW1lbnQgKHNlZSBgY2hpbGRgIGJlbG93KS5cbiAgICAgICAgZm9yIChsZXQgaSA9IGVsbXMubGVuZ3RoIC0gMTsgaSA+PSAwOyBpLS0pIHtcbiAgICAgICAgICAgIGNvbnN0IGNoaWxkID0gaSA+IDAgPyB0aGlzLmNsb25lTm9kZSh0cnVlKSA6IHRoaXM7XG4gICAgICAgICAgICBjb25zdCBlbCA9IGVsbXNbaV07XG5cbiAgICAgICAgICAgIC8vIENhY2hlIHRoZSBjdXJyZW50IHBhcmVudCBhbmQgc2libGluZy5cbiAgICAgICAgICAgIGNvbnN0IHBhcmVudCA9IGVsLnBhcmVudE5vZGU7XG4gICAgICAgICAgICBjb25zdCBzaWJsaW5nID0gZWwubmV4dFNpYmxpbmc7XG5cbiAgICAgICAgICAgIC8vIFdyYXAgdGhlIGVsZW1lbnQgKGlzIGF1dG9tYXRpY2FsbHkgcmVtb3ZlZCBmcm9tIGl0cyBjdXJyZW50XG4gICAgICAgICAgICAvLyBwYXJlbnQpLlxuICAgICAgICAgICAgY2hpbGQuYXBwZW5kQ2hpbGQoZWwpO1xuXG4gICAgICAgICAgICAvLyBJZiB0aGUgZWxlbWVudCBoYWQgYSBzaWJsaW5nLCBpbnNlcnQgdGhlIHdyYXBwZXIgYmVmb3JlXG4gICAgICAgICAgICAvLyB0aGUgc2libGluZyB0byBtYWludGFpbiB0aGUgSFRNTCBzdHJ1Y3R1cmU7IG90aGVyd2lzZSwganVzdFxuICAgICAgICAgICAgLy8gYXBwZW5kIGl0IHRvIHRoZSBwYXJlbnQuXG4gICAgICAgICAgICBpZiAoc2libGluZykge1xuICAgICAgICAgICAgICAgIHBhcmVudC5pbnNlcnRCZWZvcmUoY2hpbGQsIHNpYmxpbmcpO1xuICAgICAgICAgICAgfSBlbHNlIHtcbiAgICAgICAgICAgICAgICBwYXJlbnQuYXBwZW5kQ2hpbGQoY2hpbGQpO1xuICAgICAgICAgICAgfVxuICAgICAgICB9XG4gICAgfTtcblxuICAgIC8vIFdyYXAgYW4gSFRNTEVsZW1lbnQgYXJvdW5kIGFub3RoZXIgSFRNTEVsZW1lbnQgb3IgYW4gYXJyYXkgb2YgdGhlbS5cbiAgICBIVE1MRWxlbWVudC5wcm90b3R5cGUub2NlYW5XcmFwQWxsID0gZnVuY3Rpb24gKGVsbXMpIHtcbiAgICAgICAgY29uc3QgZWwgPSAhIWVsbXMgJiYgZWxtcy5sZW5ndGggPyBlbG1zWzBdIDogZWxtcztcblxuICAgICAgICAvLyBDYWNoZSB0aGUgY3VycmVudCBwYXJlbnQgYW5kIHNpYmxpbmcgb2YgdGhlIGZpcnN0IGVsZW1lbnQuXG4gICAgICAgIGNvbnN0IHBhcmVudCA9IGVsLnBhcmVudE5vZGU7XG4gICAgICAgIGNvbnN0IHNpYmxpbmcgPSBlbC5uZXh0U2libGluZztcblxuICAgICAgICAvLyBXcmFwIHRoZSBmaXJzdCBlbGVtZW50IChpcyBhdXRvbWF0aWNhbGx5IHJlbW92ZWQgZnJvbSBpdHNcbiAgICAgICAgLy8gY3VycmVudCBwYXJlbnQpLlxuICAgICAgICB0aGlzLmFwcGVuZENoaWxkKGVsKTtcblxuICAgICAgICAvLyBXcmFwIGFsbCBvdGhlciBlbGVtZW50cyAoaWYgYXBwbGljYWJsZSkuIEVhY2ggZWxlbWVudCBpc1xuICAgICAgICAvLyBhdXRvbWF0aWNhbGx5IHJlbW92ZWQgZnJvbSBpdHMgY3VycmVudCBwYXJlbnQgYW5kIGZyb20gdGhlIGVsbXNcbiAgICAgICAgLy8gYXJyYXkuXG4gICAgICAgIHdoaWxlIChlbG1zLmxlbmd0aCkge1xuICAgICAgICAgICAgdGhpcy5hcHBlbmRDaGlsZChlbG1zWzBdKTtcbiAgICAgICAgfVxuXG4gICAgICAgIC8vIElmIHRoZSBmaXJzdCBlbGVtZW50IGhhZCBhIHNpYmxpbmcsIGluc2VydCB0aGUgd3JhcHBlciBiZWZvcmUgdGhlXG4gICAgICAgIC8vIHNpYmxpbmcgdG8gbWFpbnRhaW4gdGhlIEhUTUwgc3RydWN0dXJlOyBvdGhlcndpc2UsIGp1c3QgYXBwZW5kIGl0XG4gICAgICAgIC8vIHRvIHRoZSBwYXJlbnQuXG4gICAgICAgIGlmIChzaWJsaW5nKSB7XG4gICAgICAgICAgICBwYXJlbnQuaW5zZXJ0QmVmb3JlKHRoaXMsIHNpYmxpbmcpO1xuICAgICAgICB9IGVsc2Uge1xuICAgICAgICAgICAgcGFyZW50LmFwcGVuZENoaWxkKHRoaXMpO1xuICAgICAgICB9XG4gICAgfTtcbn0pKCk7XG4iLCJleHBvcnQgZGVmYXVsdCBjbGFzcyBIZWxwZXJzIHtcbiAgICBzdGF0aWMgaXNUb3BiYXJTdGlja3lFbmFibGVkID0gKCkgPT4gb2NlYW53cExvY2FsaXplLmhhc1N0aWNreVRvcEJhciA9PSB0cnVlO1xuXG4gICAgc3RhdGljIGlzTW9iaWxlU3RpY2t5RGlzYWJsZWQgPSAoKSA9PiB3aW5kb3cuaW5uZXJXaWR0aCA8PSA5NjAgJiYgb2NlYW53cExvY2FsaXplLmhhc1N0aWNreU1vYmlsZSAhPSB0cnVlO1xuXG4gICAgc3RhdGljIHNsaWRlU3RpY2t5RWZmZWN0ID0gKCkgPT4gb2NlYW53cExvY2FsaXplLnN0aWNreUVmZmVjdCA9PSBcInNsaWRlXCI7XG5cbiAgICBzdGF0aWMgdXBTdGlja3lFZmZlY3QgPSAoKSA9PiBvY2VhbndwTG9jYWxpemUuc3RpY2t5RWZmZWN0ID09IFwidXBcIjtcblxuICAgIHN0YXRpYyBtYW51YWxTdGlja3kgPSAoKSA9PiBvY2VhbndwTG9jYWxpemUuc3RpY2t5Q2hvb3NlID09IFwibWFudWFsXCI7XG5cbiAgICBzdGF0aWMgc2hyaW5rU3RpY2t5U3R5bGUgPSAoKSA9PiBvY2VhbndwTG9jYWxpemUuc3RpY2t5U3R5bGUgPT0gXCJzaHJpbmtcIjtcblxuICAgIHN0YXRpYyBnZXRTaHJpbmtMb2dvSGVpZ2h0ID0gKCkgPT4ge1xuICAgICAgICBjb25zdCBzaHJpbmtMb2dvSGVpZ2h0ID0gcGFyc2VJbnQob2NlYW53cExvY2FsaXplLnNocmlua0xvZ29IZWlnaHQpO1xuXG4gICAgICAgIHJldHVybiBzaHJpbmtMb2dvSGVpZ2h0ID8gc2hyaW5rTG9nb0hlaWdodCA6IDMwO1xuICAgIH07XG59XG4iLCJpbXBvcnQgRE9NIGZyb20gXCIuL0RPTVwiO1xuXG5leHBvcnQgZGVmYXVsdCBjbGFzcyBVdGlsaXR5IHtcbiAgICBzdGF0aWMgc2Nyb2xsQmFyVG9wUG9zaXRpb24gPSAoKSA9PiB3aW5kb3cucGFnZVlPZmZzZXQ7XG5cbiAgICBzdGF0aWMgZWxlbUV4aXN0cyA9IChlbGVtKSA9PiB7XG4gICAgICAgIHJldHVybiBlbGVtICYmIGVsZW0gIT09IG51bGw7XG4gICAgfTtcblxuICAgIHN0YXRpYyBlbGVtVmlzaWJsZSA9IChlbGVtKSA9PiAhIShlbGVtLm9mZnNldFdpZHRoIHx8IGVsZW0ub2Zmc2V0SGVpZ2h0IHx8IGVsZW0uZ2V0Q2xpZW50UmVjdHMoKS5sZW5ndGgpO1xuXG4gICAgc3RhdGljIGVsZW1PZmZzZXQgPSAoZWxlbSkgPT4ge1xuICAgICAgICBpZiAoIWVsZW0uZ2V0Q2xpZW50UmVjdHMoKS5sZW5ndGgpIHtcbiAgICAgICAgICAgIHJldHVybiB7IHRvcDogMCwgbGVmdDogMCB9O1xuICAgICAgICB9XG5cbiAgICAgICAgLy8gR2V0IGRvY3VtZW50LXJlbGF0aXZlIHBvc2l0aW9uIGJ5IGFkZGluZyB2aWV3cG9ydCBzY3JvbGwgdG8gdmlld3BvcnQtcmVsYXRpdmUgZ0JDUlxuICAgICAgICBjb25zdCByZWN0ID0gZWxlbS5nZXRCb3VuZGluZ0NsaWVudFJlY3QoKTtcbiAgICAgICAgY29uc3Qgd2luID0gZWxlbS5vd25lckRvY3VtZW50LmRlZmF1bHRWaWV3O1xuICAgICAgICByZXR1cm4ge1xuICAgICAgICAgICAgdG9wOiByZWN0LnRvcCArIHdpbi5wYWdlWU9mZnNldCxcbiAgICAgICAgICAgIGxlZnQ6IHJlY3QubGVmdCArIHdpbi5wYWdlWE9mZnNldCxcbiAgICAgICAgfTtcbiAgICB9O1xuXG4gICAgc3RhdGljIGlzV1BBZG1pbmJhclZpc2libGUgPSAoKSA9PiB0aGlzLmVsZW1FeGlzdHMoRE9NLldQQWRtaW5iYXIpICYmIHdpbmRvdy5pbm5lcldpZHRoID4gNjAwO1xufVxuIiwiaW1wb3J0IFwiLi9VdGlscy9ET01NZXRob2RzXCI7XG5pbXBvcnQgVXRpbGl0eSBmcm9tIFwiLi9VdGlscy9VdGlsaXR5XCI7XG5pbXBvcnQgVG9wYmFyIGZyb20gXCIuL0NvbXBvbmVudHMvVG9wYmFyXCI7XG5pbXBvcnQgSGVhZGVyIGZyb20gXCIuL0NvbXBvbmVudHMvSGVhZGVyXCI7XG5pbXBvcnQgTG9nbyBmcm9tIFwiLi9Db21wb25lbnRzL0xvZ29cIjtcblxuY2xhc3MgT1dfU3RpY2t5SGVhZGVyIHtcbiAgICAjc2Nyb2xsQmFybGF0ZXN0VG9wUG9zaXRpb247XG5cbiAgICBjb25zdHJ1Y3RvcigpIHtcbiAgICAgICAgdGhpcy50b3BiYXIgPSBuZXcgVG9wYmFyKCk7XG4gICAgICAgIHRoaXMuaGVhZGVyID0gbmV3IEhlYWRlcigpO1xuICAgICAgICB0aGlzLmxvZ28gPSBuZXcgTG9nbygpO1xuICAgIH1cblxuICAgIHN0YXJ0ID0gKCkgPT4ge1xuICAgICAgICB0aGlzLiNzY3JvbGxCYXJsYXRlc3RUb3BQb3NpdGlvbiA9IFV0aWxpdHkuc2Nyb2xsQmFyVG9wUG9zaXRpb24oKTtcblxuICAgICAgICB0aGlzLiNzZXR1cEV2ZW50TGlzdGVuZXJzKCk7XG4gICAgfTtcblxuICAgICNzZXR1cEV2ZW50TGlzdGVuZXJzID0gKCkgPT4ge1xuICAgICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcihcImxvYWRcIiwgdGhpcy4jb25XaW5kb3dMb2FkKTtcbiAgICAgICAgd2luZG93LmFkZEV2ZW50TGlzdGVuZXIoXCJzY3JvbGxcIiwgdGhpcy4jb25XaW5kb3dTY3JvbGwpO1xuICAgICAgICB3aW5kb3cuYWRkRXZlbnRMaXN0ZW5lcihcInJlc2l6ZVwiLCB0aGlzLiNvbldpbmRvd1Jlc2l6ZSk7XG4gICAgICAgIHdpbmRvdy5hZGRFdmVudExpc3RlbmVyKFwib3JpZW50YXRpb25jaGFuZ2VcIiwgdGhpcy4jb25XaW5kb3dSZXNpemUpO1xuICAgIH07XG5cbiAgICAjb25XaW5kb3dMb2FkID0gKCkgPT4ge1xuICAgICAgICB0aGlzLnRvcGJhci5jcmVhdGVTdGlja3lXcmFwcGVyKCk7XG4gICAgICAgIHRoaXMuaGVhZGVyLmNyZWF0ZVN0aWNreVdyYXBwZXIoKTtcbiAgICAgICAgdGhpcy5oZWFkZXIuYWRkVmVydGljYWxIZWFkZXJTdGlja3koKTtcbiAgICAgICAgdGhpcy5sb2dvLnNldE1heEhlaWdodCgpO1xuICAgIH07XG5cbiAgICAjb25XaW5kb3dTY3JvbGwgPSAoKSA9PiB7XG4gICAgICAgIGlmIChVdGlsaXR5LnNjcm9sbEJhclRvcFBvc2l0aW9uKCkgIT0gdGhpcy4jc2Nyb2xsQmFybGF0ZXN0VG9wUG9zaXRpb24pIHtcbiAgICAgICAgICAgIHRoaXMudG9wYmFyLnN0aWNreSgpO1xuICAgICAgICAgICAgdGhpcy5oZWFkZXIuc3RpY2t5KCk7XG4gICAgICAgICAgICB0aGlzLmhlYWRlci5zdGlja3lFZmZlY3RzKCk7XG4gICAgICAgICAgICB0aGlzLmhlYWRlci5hZGRWZXJ0aWNhbEhlYWRlclN0aWNreSgpO1xuXG4gICAgICAgICAgICB0aGlzLiNzY3JvbGxCYXJsYXRlc3RUb3BQb3NpdGlvbiA9IFV0aWxpdHkuc2Nyb2xsQmFyVG9wUG9zaXRpb24oKTtcbiAgICAgICAgfVxuICAgIH07XG5cbiAgICAjb25XaW5kb3dSZXNpemUgPSAoKSA9PiB7XG4gICAgICAgIHRoaXMudG9wYmFyLnVwZGF0ZVN0aWNreSgpO1xuICAgICAgICB0aGlzLmhlYWRlci51cGRhdGVTdGlja3koKTtcbiAgICB9O1xufVxuXG4oXCJ1c2Ugc3RyaWN0XCIpO1xuXG5jb25zdCBzdGlja3lIZWFkZXIgPSBuZXcgT1dfU3RpY2t5SGVhZGVyKCk7XG5zdGlja3lIZWFkZXIuc3RhcnQoKTtcbiJdfQ==
