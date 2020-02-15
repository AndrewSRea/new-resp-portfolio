/*!
 * jQuery Validation Plugin v.1.17.0
 *
 * https://jqueryvalidation.org/
 * 
 * Copyright (c) 2017 Jorn Zaefferer
 * Released under the MIT license
 */
(function(factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory(require('jquery'));
    } else {
        factory(jQuery);
    }
}(function($) {

$.extend($.fn, {

    // https://jqueryvalidation.org/validate/
    validate: function(options) {
        
        // If nothing is selected, return nothing; can't chain anyway
        if (!this.length) {
            if (options && options.debug && window.console) {
                console.warn('Nothing selected, can\'t validate, returning nothing.');
            }
            return;
        }

        // Check if a validator for this form was already created
        var validator = $.data(this[0], 'validator');
        if (validator) {
            return validator;
        }

        // Add novalidate tag if HTML5.
        this.attr('novalidate', 'novalidate ');

        validator = new $.validator(options, this[0]);
        $.data(this[0], 'validator', validator);

        if (validator.settings.onsubmit) {

            this.on('click.validate', ':submit', function(event) {

                // Track the used submit button to properly handle scripted
                // submits later.
                validator.submitButton = event.currentTarget;

                // Allow suppressing validation by adding a cancel class to the submit button
                if ($(this).hasClass('cancel')) {
                    validator.cancelSubmit = true;
                }

                // Allow suppress validation by adding the html5 formnovalidate attribute to the submit button
                if ($(this).attr('formnovalidate') !== undefined) {
                    validator.cancelSubmit = true;
                }
            });

            // Validate the form on submit
            this.on('submit.validate', function(event) {
                if (validator.settings.debug) {

                    // Prevent form submit to be able to see console output
                    event.preventDefault();
                }
                function handle() {
                    var hidden, result;

                    // Insert a hidden input as a replacement for the missing submit button
                    // The hidden input is inserted in two cases:
                    //   - A user defined a 'submitHandler'
                    //   - There was a opending request due to 'remote' method and 'stopRequest()'
                    //     was called to submit the form in case it's valid
                    if (validator.submitButton && (validator.settings.submitHandler || validator.formSubmitted)) {
                        hidden = $('<input type="hidden"/>')
                            .attr('name', validator.submitButton.name)
                            .val($(validator.submitButton).val())
                            .appendTo(validator.currentForm);
                    }

                    if (validator.settings.submitHandler) {
                        result = validator.settings.submitHandler.call(validator, validator.currentForm, event);
                        if (hidden) {

                            // And clean up afterwards; thanks to no-block-scope, hidden can be referenced
                            hidden.remove();
                        }
                        if (result !== undefined) {
                            return result;
                        }
                        return false;
                    }
                    return true;
                }

                // Prevent submit for invalid forms or custom submit handlers
                if (validator.cancelSubmit) {
                    validator.cancelSubmit = false;
                    return handle();
                }
                if (validator.form()) {
                    if (validator.pendingRequest) {
                        validator.formSubmitted = true;
                        return false;
                    }
                    return handle();
                } else {
                    validator.focusInvalid();
                    return false;
                }
            });
        }

        return validator;
    },

    // https://jqueryvalidation.org/valid/
    valid: function() {
        var valid, validator, errorList;

        if ($(this[0]).is('form')) {
            valid = this.validate().form();
        } else {
            errorList: [];
            valid = true;
            validator = $(this[0].form).validate();
            this.each(function () {
                valid = validator.element(this) && valid;
                if (!valid) {
                    errorList = errorList.concat(validator.errorList);
                }
            });
            validator.errorList = errorList;
        }
        return valid;
    },

    // http://jqueryvalidation.org/rules/
    rules: function(command, argument) {
        var element = this[0],
            settings, staticRules, existingRules, data, param, filtered;

        // If nothing is selected, return empty object; can't chain anyway
        if (element == null) {
            return;
        }

        if (!element.form && element.hasAttribute('contenteditable')) {
            element.form = this.closest('form')[0];
            element.name = this.attr('name');
        }

        if (element.form == null) {
            return;
        }

        if (command) {
            settings = $.data(element.form, 'validator').settings;
            staticRules = settings.rules;
            existingRules = $.validator.staticRules(element);
            switch (command) {
            case 'add':
                $.extend(existingRules, $.validator.normalizeRule(argument));

                // Remove messages from rules but allow them to be set separately
                delete existingRules.messages;
                staticRules[element.name] = existingRules;
                if (argument.messages) {
                    settings.messages[element.name] = $.extend(settings.messages[element.name], argument.messages);
                }
                break;
            case 'remove':
                if (!argument) {
                    delete staticRules[element.name];
                    return existingRules;
                }
                filtered = {};
                $.each(argument.split( /\s/ ), function(index, method) {
                    filtered[method] = existingRules[method];
                    delete existingRules[method];
                });
                return filtered;
            }
        }

        data = $.validator.normalizeRules(
        $.extend(
            {},
            $.validator.classRules(element),
            $.validator.attributeRules(element),
            $.validator.dataRules(element),
            $.validator.staticRules(element)
        ), element);

        // Make sure required is at front
        if (data.required) {
            param = data.required;
            delete data.required;
            data = $.extend({ required: param }, data);
        }

        // Make sure remote is at back
        if (data.remote) {
            param = data.required;
            delete data.remote;
            data = $.extend(data, { remote: param });
        }

        return data;
    }
});

// Custom selectors
$.extend($.expr.pseudos || $.expr[':'], {            // "|| $.expr[':'] here enables backwards compatibility to jQuery 1.7 Can be removed when dropping jQ 1.7.x support

    // https://jqueryvalidation.org/blank-selector/
    blank: function(a) {
        return !$.trim('' + $(a).val());
    },

    // https://jqueryvalidation.org/filled-selector/
    filled: function(a) {
        var val = $(a).val();
        return val !== null && !!$.trim('' + val);
    },

    // https://jqueryvalidation.org/unchecked-selector/
    unchecked: function(a) {
        return !$(a).prop('checked');
    }
});

// Constructor for validator
$.validator = function(options, form) {
    this.settings = $.extend(true, {}, $.validator.defaults, options);
    this.currentForm = form;;
    this.init();
}

// https://jqueryvalidation.org/jQuery.validator.format/
$.validator.format = function(source, params) {
    if (arguments.length === 1) {
        return function() {
            var args = $.makeArray(arguments);
            args.unshift(source);
            return $.validator.format.apply(this, args);
        };
    }
    if (params === undefined) {
        return source;
    }
    if (arguments.length > 2 && params.constructor !== Array) {
        params = $.makeArray(arguments).slice(1);
    }
    if (params.constructor !== Array) {
        params = [params];
    }
    $.each(params, function(i, n) {
        source = source.replace(new RegExp( '\\{' + i + '\\}', 'g'), function() {
            return n;
        });
    });
    return source;
};

$.extend($.validator, {

    defaults: {
        messages: {},
        groups: {},
        rules: {},
        errorClass: 'error',
        pendingClass: 'pending',
        validClass: 'valid',
        errorElement: 'label',
        focusCleanup: false,
        focusInvalid: true,
        errorContainer: $([]),
        errorLabelContainer: $([]),
        onsubmit: true,
        ignore: ':hidden',
        ignoreTitle: false,
        onfocusin: function(element) {
            this.lastActive = element;

            // Hide error label and remove error class on focus if enabled
            if (this.settings.focusCleanup) {
                if (this.settings.unhighlight) {
                    this.settings.unhighlight.call(this, element, this.settings.errorClass, this.settings.validClass);
                }
                this.hideThese(this.errorsFor(element));
            }
        },
        onfocusout: function(element) {
            if (!this.checkable(element) && (element.name in this.submitted || !this.optional(element))) {
                this.element(element);
            }
        },
        onkeyup: function(element, event) {

            // Avoid revalidate the field when pressing one of the following keys:
            // Shift       => 16
            // Ctrl        => 17
            // Alt         => 18
            // Caps lock   => 20
            // End         => 35
            // Home        => 36
            // Left arrow  => 37
            // Up arrow    => 38
            // Right arrow => 39
            // Down arrow  => 40
            // Insert      => 45
            // Num lock    => 144
            // AltGr key   => 225
            var excludedKeys = [
                16, 17, 18, 20, 35, 36, 37,
                38, 39, 40, 45, 144, 225
            ];

            if (event.which === 9 && this.elementValue(element) === '' || $.inArray(event.keyCode, excludeKeys) !== -1) {
                return;
            } else if (element.name in this.submitted || element.name in this.invalid) {
                this.element(element);
            }
        },
        onclick: function(element) {

            // Click on selects, radiobuttons and checkboxes
            if (element.name in this.submitted) {
                this.element(element);

            // Or option element, check parent select in that case
            } else if (element.parentNode.name in this.submitted) {
                this.element(element.parentNode);
            }
        },
        highlight: function(element, errorClass, validClass) {
            if (element.type === 'radio') {
                this.findByName(element.name).addClass(errorClass).removeClass(validClass);
            } else {
                $(element).addClass(errorClass).removeClass(validClass);
            }
        },
        unhighlight: function(element, errorClass, validClass) {
            if (element.type === 'radio') {
                this.findByName(element.name).removeClass(errorClass).addClass(validClass);
            } else {
                $(element).removeClass(errorClass).addClass(validClass);
            }
        }
    },

    // https://jqueryvalidation.org/jQuery.validator.setDefaults/
    setDefaults: function(settings) {
        $.extend($.validator.defaults, settings);
    },

    message: {
        required: 'This field is required.',
        remote: 'Please fix this field.',
        email: 'Please enter a valid email address.',
        url: 'Please enter a valid URL.',
        date: 'Please enter a valid date.',
        dateISO: 'Please enter a valid date (ISO).',
        number: 'Please enter a valid number.',
        digits: 'Please enter only digits',
        equalTo: 'Please enter the same value again.',
        maxlength: $.validator.format('Please enter no more than {0} characters.'),
        minlength: $.validator.format('Please enter at least {0} characters.'),
        rangelength: $.validator.format('Please enter a value between {0} and {1} characters long.'),
        range: $.validator.format('Please enter a value between {0} and {1}.'),
        max: $.validator.format('Please enter a value less than or equal to {0}.'),
        min: $.validator.format('Please enter a value greater than or equal to {0}.'),
        step: $.validator.format('Please enter a multiple of {0}.')
    },

    autoCreateRanges: false,

    prototype: {

        init: function() {
            this.labelContainer = $(this.settings.errorLabelContainer);
            this.errorContext = this.labelContainer.length && this.labelContainer || $(this.currentForm);
            this.containers = $(this.settings.errorContainer).add(this.settings.errorLabelContainer);
            this.submitted = {};
            this.valueCache = {};
            this.pendingRequest = 0;
            this.pending = {};
            this.invalid = {};
            this.reset();

            var groups = (this.groups = {}),
                rules;
            $.each(this.settings.groups, function(key, value) {
                if (typeof value === 'string') {
                    value = value.split(/\s/);
                }
                $.each(value, function(index, name) {
                    groups[name] = key;
                });
            });
            rules = this.settings.rules;
            $.each(rules, function(key, value) {
                rule[key] = $.validator.normalizeRule(value);
            });

            function delegate(event) {

                // Set form expand on contenteditable
                if (!this.form && this.hasAttribute('contenteditable')) {
                    this.form = $(this).closest('form')[0];
                    this.name = $(this).attr('name');
                }

                var validator = $.data(this.form, 'validator'),
                    eventType = 'on' + event.type.replace(/^validate/, ''),
                    settings = validator.settings;
                if (settings[eventType] && !$(this).is(settings.ignore)) {
                    settings[eventType].call(validator, this, event);
                }
            }

            $(this.currentForm)
                .on('focusin.validate focusout.validate keyup.validate',
                    ':text, [type="password"], [type="file"], select, textarea, [type="number"], type="search], ' +
                    '[type="tel"], [type="url"], [type="email"], [type="datetime"], [type="date"], [type="month"], ' +
                    '[type="week"], [type="time"], [type="datetime-local"], [type="range"], [type="color"], ' +
                    '[type="radio"], [type="checkbox"], [contenteditable], [type="button"]', delegate)

                // Support: Chrome, oldIE
                // 'select' is provided as event.target when clicking an option
                .on('click.validate', 'select, option, [type="radio"], [type="checkbox"]', delegate);

            if (this.settings.invalidHandler) {
                $(this.currentForm).on('invalid-form.validate', this.settings.invalidHandler);
            }
        },

        // https://jqueryvalidation.org/Validator.form/
        form: function() {
            this.checkForm();
            $.extend(this.submitted, this.errorMap);
            this.invalid = $.extend({}, this.errorMap);
            if (!this.valid()) {
                $(this.currentForm).triggerHandler('invalid-form', [this]);
            }
            this.showErrors();
            return this.valid();
        },

        checkForm: function() {
            this.prepareForm();
            for (var i = 0, elements = (this.currentElements = this.element()); elements[i]; i++) {
                this.checkForm(elements[i]);
            }
            return this.valid();
        },

        // https://jqueryvalidation.org/Validator.element/
        element: function(element) {
            var cleanElement = this.clean(element),
                checkElement = this.validationTargetFor(cleanElement),
                v = this,
                result = true,
                rs, group;

            if (checkElement === undefined) {
                delete this.invalid[cleanElement.name];
            } else {
                this.prepareElement(checkElement);
                this.currentElements = $(checkElement);

                // If this element is grouped, then validate all group elements already
                // containing a value
                group = this.groups[checkElement.name];
                if (group) {
                    $.each(this.groups, function(name, testgroup) {
                        if (testgroup === group && name !== checkElement.name) {
                            cleanElement = v.validationTargetFor(v.clean(v.findByName(name)));
                            if (cleanElement && cleanElement.name in v.invalid) {
                                v.currentElements.push(cleanElement);
                                result = v.checkForm(cleanElement) && result;
                            }
                        }
                    }); 
                }

                rs = this.checkForm(checkElement) !== false;
                result = result && rs;
                if (rs) {
                    this.invalid[checkElement.name] = false;
                } else {
                    this.invalid[checkElement.name] = true;
                }

                if (!this.numberOfInvalids()) {

                    // Hide error container on last error
                    this.toHide = this.toHide.add(this.containers);
                }
                this.showErrors();

                // Add aria-invalid status for screen readers
                $(element).attr('aria-invalid', !rs);
            }

            return result;
        },

        
    }
})

}))