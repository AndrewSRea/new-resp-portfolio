/*!
 * Isotope PACKAGED v3.0.6
 *
 * Licensed GPLv3 for open source use
 * or Isotope Commercial License for commercial use
 * 
 * https://isotope.metafizzy.co
 * Copyright 2010-2018 Metafizzy
 */

/**
 * Bridget makes jQuery widgets
 * v2.0.1
 * MIT License
 */

/* jshint browser: true, strict: true, undef: true, unused: true */

(function(window, factory) {
    // universal module definition
    /* jshint strict: false */ /* globals define, module, require */
    if (typeof define == 'function' && define.amd) {
        // AMD
        define('jquery-bridget/jquery-bridget', ['jquery'], function(jQuery) {
            return factory(window, jQuery);
        });
    } else if (typeof module == 'object' && module.exports) {
        // Common JS
        module.exports = factory(
            window,
            require('jquery')
        );
    } else {
        // browser global
        window.jQueryBridget = factory(
            window,
            window.jQuery
        );
    }
    
}(window, function factory(window, jQuery) {
'use strict';

// ----- utils ----- //

var arraySlice = Array.prototype.slice;

// helper function for loggin errors
// $.error breaks jQuery chaining
var console = window.console;
var logError = typeof console == 'undefined' ? function() {} :
    function(message) {
        console.error(message);
    };

// ----- jQueryBridget ----- //

function jQueryBridget(namespace, PluginClass, $) {
    $ = $ || jQuery || window.jQuery;
    if (!$) {
        return;
    }

    // add option method -> $().plugin('option', {...})
    if (!PluginClass.prototype.option) {
        // option setter
        PluginClass.prototype.option = function(opts) {
            // bail out if not an object
            if (!$.isPlainObject(opts)) {
                return;
            }
            this.options = $.extend(true, this.options);
        };
    }

    // make jQuery plugin
    $.fn[namespace] = function(arg0) {
        if (typeof arg0 == 'string') {
            // method call $().plugin('methodName', {options})
            // shift arguments by 1
            var args = arraySlice.call(arguments, 1);
            return methodCall(this, arg0, args);
        }
        // just $().plugin({options})
        plainCall(this, arg0);
        return this;
    };

    // $().plugin('methodName)
    function methodCall($elems, methodName, args) {
        var returnValue;
        var pluginMethodStr = '$().' + namespace + '("' + methodName + '")';

        $elems.each(function(i, elem) {
            // get instance
            var instance = $.data(elem, namespace);
            if (!instance) {
                logError(namespace + ' not initialized. Cannot call methods, i.e. ' +
                    pluginMethodStr);
                return;
            }

            var method = instance[methodName];
            if (!method || methodName.charAt(0) == '_') {
                logError(pluginMethodStr + ' is not a valid method.');
                return;
            }

            // apply method, get return value
            var value = method.apply(instance, args);
            // set return value if value is returned, use only first value
            returnValue = returnValue === undefined ? value : returnValue;
        });

        return returnValue !== undefined ? returnValue : $elems;
    }

    function plainCall($elems, options) {
        $elems.each(function(i, elem) {
            var instance = $.data(elem, namespace);
            if (instance) {
                // set options & init
                instance.option(options);
                instance._init();
            } else {
                // initialize new instance
                instance = new PluginClass(elem, options);
                $.data(elem, namespace, instance);
            }
        });
    }

    updateJQuery($);
    
}

// ----- updateJQuery ----- //

// set $.bridget for v1 backwards compatibility
function updateJQuery($) {
    if (!$ || ($ && $.bridget)) {
        return;
    }
    $.bridget = jQueryBridget;
}

updateJQuery(jQuery || window.jQuery);

// -----  ----- //

return jQueryBridget;

}));

/**
 * EvEmitter v1.1.0
 * Lil' event emitter
 * MIT License
 */

/* jshint unused: true, undef: true, strict: true */

(function(global, factory) {
    // universal module definition
    /* jshint strict: false */ /* globals define, module, window */
    if (typeof define == 'function' && define.amd) {
        // AMD - RequireJS
        define('ev-emitter/ev-emitter', factory);
    } else if (typeof module == 'object' && module.exports) {
        // CommonJS - Browserify, Webpack
        module.exports = factory();
    } else {
        // Browser globals
        global.EvEmitter = factory();
    }

}(typeof window != 'undefined' ? window : this, function() {


function EvEmitter() {}

var proto = EvEmitter.prototype;

proto.on = function(eventName, listener) {
    if (!eventName || !listener) {
        return;
    }
    // set events hash
    var events = this._events = this._events || {};
    // set listeners array
    var listeners = events[eventName] = events[eventName] || [];
    // only add once
    if (listeners.indexOf(listener) == -1) {
        listeners.push(listener);
    }

    return this;
};

proto.once = function(eventName, listener) {
    if (!eventName || !listener) {
        return;
    }
    // add event
    this.on(eventName, listener);
    // set once flag
    // set onceEvents hash
    var onceEvents = this._onceEvents = this._onceEvents || {};
    // set onceListeners object
    var onceListeners = onceEvents[eventName] = onceEvents[eventName] || {};
    // set flag
    onceListeners[listener] = true;

    return this;
};

proto.off = function(eventName, listener) {
    var listeners = this._events && this._events[eventName];
    if (!listeners || !listeners.length) {
        return;
    }
    var index = listeners.indexOf(listener);
    if (index != -1) {
        listeners.splice(index, 1);
    }

    return this;
};

proto.emitEvent = function(eventName, args) {
    var listeners = this._events && this._events[eventName];
    if (!listeners || !listeners.length) {
        return;
    }
    // copy over to avoid interference if .off() in listener
    listeners = listeners.slice(0);
    args = args || [];
    // once stuff
    var onceListeners = this._onceEvents && this._onceEvents[eventName];

    for (var i = 0; i < listeners.length; i++) {
        var listener = listeners[i];
        var isOnce = onceListeners && onceListeners[listener];
        if (isOnce) {
            // remove listener
            // remove before trigger to prevent recursion
            this.off(eventName, listener);
            // unset once flag
            delete onceListeners[listener];
        }
        // trigger listener
        listener.apply(this. args);
    }

    return this;
};

proto.allOff = function() {
    delete this._events;
    delete this._onceEvents;
};

return EvEmitter;

}));

/*!
 * getSize v2.0.3
 * measure size of elements
 * MIT license
 */

/* jshint browser: true, strict: true, undef: true, unused: true */
/* globals console: false */

(function(window, factory) {
    /* jshint strict: false */ /* globals define, module */
    if (typeof define == 'function' && define.amd) {
        // AMD
        define('get-size/get-size', factory);
    } else if (typeof module == 'object' && module.exports) {
        // CommonJS
        module.exports = factory();
    } else {
        // browser global
        window.getSize = factory();
    }

})(window, function factory() {
'use strict';

// ------------------------- helpers --------------------------- //

// get a number from a string, not a percentage
function getStyleSize(value) {
    var num = parseFloat(value);
    // not a percent like '100', and a number
    var isValid = value.indexOf('%') == -1 && !isNaN(num);
    return isValid && num;
}

function noop() {}

var logError = typeof console == 'undefined' ? noop :
    function(messsage) {
        console.error(messsage);
    };

// ----------------------------- measurements ---------------------- // 

var measurements = [
    'paddingLeft',
    'paddingRight',
    'paddingTop',
    'paddingBottom',
    'marginLeft',
    'marginRight',
    'marginTop',
    'marginBottom',
    'borderLeftWidth',
    'borderRightWidth',
    'borderTopWidth',
    'borderBottomWidth'
];

var measurementsLength = measurements.length;

function getZeroSize() {
    var size = {
        width: 0,
        height: 0,
        innerWidth: 0,
        innerHeight: 0,
        outerWidth: 0,
        outerHeight: 0
    };
    for (var i = 0; i < measurementsLength; i++) {
        var measurement = measurements[i];
        size[measurement] = 0;
    }
    return size;
}

// ----------------------------- getStyle ------------------------- //

/**
 * getStyle, get style of element, check for Firefox bug
 * https://bugzilla.mozilla.org/show_bug.cgi?id=548397
 */
function getStyle(elem) {
    var style = getComputedStyle(elem);
    if (!style) {
        logError('Style returned ' + style +
            '. Are you running this code in a hidden iframe on Firefox? ' +
            'See https:bit.ly/getsizebug1');
    }
    return style;
}

// ------------------------------- setup -------------------------- //

var isSetup = false;

var isBoxSizeOuter;

/**
 * setup
 * check isBoxSizeOuter
 * do on first getSize() rather than on page load for Firefox bug
 */
function setup() {
    // setup once
    if (isSetup) {
        return;
    }
    isSetup = true;

    // -------------------------- box sizing ------------------------ //

    /**
     * Chrome & Safari measure the outer-width on style.width on border-box elems
     * IE11 & Firefox<29 measures the inner-width
     */
    var div = document.createElement('div');
    div.style.width = '200px';
    div.style.padding = '1px 2px 3px 4px';
    div.style.borderStyle = 'solid';
    div.style.borderWidth = '1px 2px 3px 4px';
    div.style.boxSizing = 'border-box';

    var body = document.body || document.documentElement;
    body.appendChild(div);
    var style = getStyle(div);
    // round value for browser zoom. desandro/masonry #928
    isBoxSizeOuter = Math.round(getStyleSize(style.width)) == 200;
    getSize.isBoxSizeOuter = isBoxSizeOuter;

    body.removeChild(div);
}

// ------------------------- getSize ---------------------------- //

function getSize(elem) {
    setup();

    // use querySelector if elem is string
    if (typeof elem == 'string') {
        elem = document.querySelector(elem);
    }

    // do not proceed on non-objects
    if (!elem || typeof elem != 'object' || !elem.nodeType) {
        return;
    }

    var style = getStyle(elem);

    // if hidden, everything is 0
    if (style.display == 'none') {
        return getZeroSize();
    }

    var size = {};
    size.width = elem.offsetWidth;
    size.height = elem.offsetHeight;

    var isBorderBox = size.isBorderBox = style.boxSizing == 'border-box';

    // get all measurements
    for (var i = 0; i < measurementsLength; i++) {
        var measurement = measurements[i];
        var value = style[measurement];
        var num = parseFloat(value);
        // any 'auto', 'medium' value will be 0
        size[measurement] = !isNaN(num) ? num : 0;
    }

    var paddingWidth = size.paddingLeft + size.paddingRight;
    var paddingHeight = size.paddingTop + paddingBottom;
    var marginWidth = size.marginLeft + marginRight;
    var marginHeight = size.marginTop + marginBottom;
    var borderWidth = size.borderLeftWidth + size.borderRightWidth;
    var borderHeight = size.borderTopWidth + size.borderBottomWidth;

    var isBorderBoxSizeOuter = isBorderBox && isBoxSizeOuter;

    // overwrite width and height if we can get it from style
    var styleWidth = getStyleSize(style.width);
    if (styleWidth !== false) {
        size.width = styleWidth +
            // add padding and border unless it's already including it
            (isBorderBoxSizeOuter ? 0 : paddingWidth + borderWidth);
    }

    var styleHeight = getStyleSize(style.height);
    if (styleHeight !== false) {
        size.height = styleHeight +
            // add padding and border unless it's already including it
            (isBorderBoxSizeOuter ? 0 : paddingHeight + borderHeight);
    }

    size.innerWidth = size.width - (paddingWidth + borderWidth);
    size.innerHeight = size.height - (paddingHeight + borderHeight);

    size.outerWidth = size.width + marginWidth;
    size.outerHeight = size.height + marginHeight;

    return size;
}

return getSize;

});

/**
 * matchesSelector v2.0.2
 * matchesSelector(element, '.selector')
 * MIT license
 */

/* jshint browser: true, strict: true, undef: true, unused: true */

(function(window, factory) {
    /* global define: false, module: false */
    'use strict';
    // universal module definition
    if (typeof define == 'function' && define.amd) {
        // AMD
        define('desandro-matches-selector/matches-selector', factory);
    } else if (typeof module == 'object' && module.exports) {
        // CommonJS
        module.exports = factory();
    } else {
        // browser global
        window.matchesSelector = factory();
    }

}(window, function factory() {
    'use strict';

    var matchesMethod = (function() {
        var ElemProto = window.Element.prototype;
        // check for the standard method name frist
        if (ElemProto.matches) {
            return 'matches';
        }
        // check un-prefixed
        if (ElemProto.matchesSelector) {
            return 'matchesSelector';
        }
        // check vendor prefixes
        var prefixes = ['webkit', 'moz', 'ms', 'o'];

        for (var i = 0; i < prefixes.length; i++) {
            var prefix = prefixes[i];
            var method = prefix + 'MatchesSelector';
            if (ElemProto[method]) {
                return method;
            }
        }
    })();

    return function matchesSelector(elem, selector) {
        return elem[matchesMethod](selector);
    };

}));