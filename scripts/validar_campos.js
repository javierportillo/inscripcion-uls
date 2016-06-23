/*jslint browser: true, devel: true */
/*global $ */

/**
 * Valida campos imput según valores mínimos, máximos y RegExp
 */

var validar = function ($input_selector, min_len, max_len, regexp_obj) {
    'use strict';
    if ($input_selector.length === 0) {
        // El objeto no existe
        return {valid: false, status: 'bad-selector'};
    }
    if ($input_selector[0].tagName !== 'INPUT') {
        // El objeto no es un input.
        return {valid: false, status: 'not-an-input'};
    }
    if ($input_selector.val().length === 0) {
        return {valid: false, status: 'empty'};
    }
    if ($input_selector.val().length < min_len || $input_selector.val().length > max_len) {
        // El valor es muy corto o muy largo.
        return {valid: false, status: 'out-of-bounds'};
    }
    if (regexp_obj !== undefined && regexp_obj.test($input_selector.val()) === false) {
        return {valid: false, status: 'no-match'};
    }
    return {valid: true, status: 'valid'};
};
