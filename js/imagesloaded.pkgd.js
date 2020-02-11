/*!
 * imagesLoaded PACKAGED v4.1.0
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

/**
 * EvEmitter v1.o.1
 * Lil' event emitter
 * MIT License
*/

/* shint unused: true, undef: true, strict: true */

( function( global, factory ) {
    //universal module definition
    /* jshint strict: false */ /* globals define, module */
    if ( typeof define == 'function' && define.amd ) {
        // AMD - RequireJS
        define( 'ev-emitter/ev-emitter', factory );
    } else if ( typeof module == 'object' && module.exports ) {
        // CommonJS - Browserify, Webpack
        module.exports = factory();
    } else {
        // Browser globals
        global.EvEmitter = factory();
    }
}( this, function() {


function EvEmitter() {} 

var proto = EvEmitter.prototype;

proto.on = function( eventName, listener ) {
    if ( !eventName || !listener ) {
        return;
    }
    // set events hash
    var events = this._events = this._events || {};
    // set listeners array
    var listeners = events[ eventName ] = events[ eventName ] || [];
    // only add once
    if ( listeners.indexOf( listener ) == -1 ) {
        listeners.push( listener );
    }

    return this;
};

proto.once = function( eventName, listener ) {
    if ( !eventName || !listener ) {
        return;
    }
    // add event
    this.on( eventName, listener );
    // set once flag
    // set onceEvents hash
    var onceEvents = this._onceEvents = this._onceEvents || {};
    // set onceListeners array
    var onceListeners = onceEvents[ eventName ] = onceEvents[ eventName ] || [];
    // set flag
    onceListeners[ listeners ] = true;

    return this;
};

proto.off = function( eventName, listener ) {
    var listeners = this._events && this._events[ eventName ];
    if ( !listeners || !listeners.length ) {
        return;
    }
    var index = listeners.indexOf( listener );
    if ( index != -1 ) {
        listeners.splice( index, 1 );
    }

    return this;
};

proto.emitEvent = function( eventName, args ) {
    var listeners = this._events && this._events[ eventName ];
    if ( !listeners || !listeners.length ) {
        return;
    }
    var i = 0;
    var listener = listeners[i];
    args = args || [];
    // once stuff
    var onceListeners = this._onceEvents && this.onceEvents[ eventName ];

    while ( listener ) {
        var isOnce = onceListeners && onceListeners[ listener ];
        if ( isOnce ) {
            // remove listener
            // remove before trigger to prevent recursion
            this.off( eventName, listener );
            // unset once flag
            delete onceListeners[ listener ];
        }
        // trigger listener
        listener.apply( this, args );
        // get next listener
        i += isOnce ? 0 : 1;
        listener = listeners[i];
    }

    return this;
};

return EvEmitter;

}));

/*!
 * imagesLoaded v4.1.0
 * JavaScript is all like "You images done yet or what?"
 * MIT License
 */

( function( window, factory ) { 'use strict';
    // universal module definition
    /*global define: false, module: false, require: false */
    
    if ( typeof define == 'function' && define.amd ) {
        // AMD
        define( [
            'ev-emitter/ev-emitter'
        ], function( EvEmitter ) {
            return factory( window, EvEmitter );
        });
    } else if ( typeof module == 'object' && module.exports ) {
        // Common JS
        module.exports = factory(
            window,
            require('ev-emitter')
        );
    } else {
        // browser global
        window.imagesLoaded = factory(
            window,
            window.EvEmitter
        );
    }

})( window,

// --------------------------  factory  -------------------------- //

function factory( window, EvEmitter ) {


var $ = window.jQuery;
var console = window.console;

// --------------------------  helpers  -------------------------- //

// extend objects
function extend(a, b ) {
    for ( var prop in b ) {
        a[ prop ] = b[ prop ];
    }
    return a;
}

// turn element or nodeList into an array
function makeArray( obj ) {
    var ary = [];
    if ( Array.isArray( obj ) ) {
        // use object if already an array
        ary = obj;
    } else if ( typeof obj.length == 'number' ) {
        // convert nodeList to array
        for ( var i=0; i < obj.length; i++ ) {
            ary.push( obj[i] );
        }
    } else {
        // array of single index
        ary.push( obj );
    }
    return ary;
}

// ------------------------  imagesLoaded ------------------------ //

/**
 * @param {Array, Element, NodeList, String} elem
 * @param {Object or Function} options - if function, use as callback
 * @param {Function} onAlways - callback function
 */

function ImagesLoaded( elem, options, onAlways ) {
    // coerce Images Loaded() without new, to be new ImagesLoaded()
    if ( !( this instanceof ImagesLoaded ) ) {
        return new ImagesLoaded( elem, options, onAlways );
    }
    // use elem as seletor string
    if ( typeof elem == 'string' ) {
        elem = document.querySelectorAll( elem );
    }

    this.elements = makeArray( elem );
    this.options = extend( {}, this.options );

    if ( typeof options == 'function' ) {
        onAlways = options;
    } else {
        extend( this.options, options );
    }

    if ( onAlways ) {
        this.on( 'always', onAlways );
    }

    this.getImages();

    if ( $ ) {
        // add jQuery Deferred object
        this.jqDeferred = new $.Deferred();
    }

    // HACK check async to allow time to bind listeners
    setTimeout( function() {
        this.check();
    }.bind( this ));
}

ImagesLoaded.prototype = Object.create( EvEmitter.prototype );

ImagesLoaded.prototype.options = {};

ImagesLoaded.prototype.getImages = function() {
    this.images = [];

    // filter & find items if we have an item selector
    this.elements.forEach( this.addElementImages, this );
};

/**
 * @param {Node} element
 */

ImagesLoaded.prototype.addElementImages = function( elem ) {
    // filter siblings
    if ( elem.nodeName == 'IMG' ) {
        this.addImage( elem );
    }
    // get background image on element
    if ( this.options.background === true ) {
        this.addElementBackgroundImages( elem );
    }

    // find children
    // no non-element nodes, #143
    var nodeType = elem.nodeType;
    if ( !nodeType || !elementNodeTypes[ nodeType ] ) {
        return;
    }
    var childImgs = elem.querySelectorAll('img');
    // concat childElems to filterFound array
    for ( var i=0; i < childImgs.length; i++ ) {
        var img = childImgs[i];
        this.addImage( img );
    }

    // get child background images
    if ( typeof this.options.background == 'string' ) {
        var children = elem.querySelectorAll( this.options.background );
        for ( i=0; i < children.length; i++ ) {
            var child = children[i];
            this.addElementBackgroundImages( child );
        }
    }
};



}

    )