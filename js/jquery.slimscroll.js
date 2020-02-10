/*! Copyright (c) 2011 Piotr Rochala (http://rocha.la)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 * 
 * Version: 1.3.8
 * 
 */

(function($) {

    $.fn.extend({
        slimScroll: function(options) {
            var defaults = {

                // width in pixels of the visible scroll area
                width : 'auto',

                // height in pixels of the visible scroll area
                height : '250px',

                // width in pixels of the scrollbar and rail
                size : '7px',

                // scrollbar color, accepts any hex/color value
                color : '#000',

                // scrollbar position - left/right
                position : 'right',

                // distance in pixels between the side edge and the scrollbar
                distance : '1px',

                // default scroll position on load - top / bottom / $('selector')
                start : 'top',

                // sets scrollbar opacity
                opacity : .4,

                // enables always-on mode for the scrollbar
                alwaysVisible : false,

                // check if we should hide the scrollbar when user is hovering over
                disableFadeOut : false,

                // sets visibility of the rail
                railVisible : false,

                // sets rail color
                railColor : '#333',

                // sets rail opacity
                railOpacity : .2,

                // whether we should use jQuery UI Draggable to enable bar dragging
                railDraggable : true,

                // default CSS class of the slimscroll rail
                railClass : 'slimScrollRail',

                // default CSS class of the slimscroll bar
                barClass : 'slimScrollBar',

                // default CSS class of the slimscroll wrapper
                wrapperClass : 'slimScrollDiv',

                // check if mousewheel should scroll the window if top/bottom is reached
                allowPageScroll : false,

                // scroll amount applied to each mouse wheel step
                wheelStep : 20,

                // scroll amount applied when user is using gestures
                touchScrollStep : 200,

                // sets border radius 
                borderRadius : '7px',

                // sets border radius of the rail
                railBorderRadius : '7px'
            };

            var o = $.extend(defaults, options);

            // do it for every element that matches selector
            this.each(function() {
                var isOverPanel, isOverBar, isDragg, queueHide, TouchDif,
                    barHeight, percentScroll, lastScroll,
                    divS = '<div></div>',
                    minBarHeight = 30,
                    releaseScroll = false;

                // used in event handlers and for better minification
                var me = $(this);

                // ensure we are not binding it again
                if (me.parent().hasClass(o.wrapperClass)) {

                    //start from lasst bar position
                    var offset = me.scrollTop();

                    // find bar and rail
                    bar = me.siblings('.' + o.barClass);
                    rail = me.siblings('.' + o.railClass);

                    getBarHeight();

                    // check if we should scroll existing instance
                    if ($.isPlainObject(options)) {

                        // Pass height: auto to an existing slimscroll object to force a resize after contents have changed
                        if ( 'height' in options && options.height == 'auto' ) {
                            me.parent().css('height', 'auto');
                            me.css('height', 'auto');
                            var height = me.parent().parent().height();
                            me.parent().css('height', height);
                            me.css('height', height);
                        } else if ('height' in options) {
                            var h = options.height;
                            me.parent().css('height', h);
                            me.css('height', h);
                        }

                        if ('scrollTo' in options) {
                            // jump to a static point
                            offset = parseInt(o.scrollTo);
                        } else if ('scrollBy' in options) {
                            // jump by value pixels
                            offset += parseInt(o.scrollBy);
                        } else if ('destroy' in options) {
                            // remove slimscroll elements
                            bar.remove();
                            rail.remove();
                            me.unwrap();
                            return;
                        }

                        // scroll content by the given offset
                        scrollContent(offset, false, true);
                    }

                    return;
                }
                else if ($.isPlainObject(options))
                {
                    if ('destroy' in options) {
                        return;
                    }
                }

                // optionally set height to the parent's height
                o.height = (o.height == 'auto') ? me.parent().height() : o.height;

                // wrap content
                var wrapper = $(divS)
                    .addClass(o.wrapperClass)
                    .css({
                        position: 'relative',
                        overflow: 'hidden',
                        width: o.width,
                        height: o.height
                    });

                // update style for the div
                me.css({
                    overflow: 'hidden',
                    width: o.width,
                    height: o.height
                });

                // create scrollbar rail
                var rail = $(divS)
                    .addClass(o.railClass)
                    .css({
                        width: o.size,
                        height: '100%',
                        position: 'absolute',
                        top: 0,
                        display: (o.alwaysVisible && o.railVisible) ? 'block' : 'none',
                        'border-radius': o.railBorderRadius,
                        background: o.railColor,
                        opacity: o.railOpacity,
                        zIndex: 90
                    });

                // create scrollbar  
            })
        }
    })
})