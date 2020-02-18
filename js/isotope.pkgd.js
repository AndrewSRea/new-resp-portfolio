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