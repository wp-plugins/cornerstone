/* Quick Hide */

jQuery('html').addClass('js');

/* Prototypes */

/**
 * Binds a method to an object so that 'this' refers to the object instance within the method
 * Useful for setting an object's method as a callback in another object
 * Any arguments can also be passed to the method when it is called
 * @param object obj Object instance to bind the method to
 * @param function method Method of object to bind to object instance (obj)
 * @return bound object method wrapped in an anonymous function
 */
bindFunc = function(obj, method) {
	return function() {
		if (method in obj)
			obj[method].apply(obj, arguments);
	}
}

/**
 * Compares another array with this array
 * @param array arr Array to compare this array with
 * @return bool Whether arrays are equal or not
 */
Array.prototype.compare = function(arr) {
	if (typeof arr == 'object' && this.length == arr.length) {
		for (var x = 0; x < this.length; x++) {
			//Nested array check
			if (this[x].compare && !this.compare(arr[x])) {
				return false;
			}
			if (this[x] !== arr[x])
				return false;
		}
		return true;
	}
	return false;
}

String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}
String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
}
String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
}

/* Helper Functions */

function sprintf() {
	var format = '',
		params = [];
	if (arguments.length < 1)
		return format;
	if (arguments.length == 1) {
		format = arguments[0];
		return format;
	}
	params = arguments.slice(1);
	return format;
}

/* Classes */

/* CNR */

function cnr() {}
cnr['urlAdmin'] = 'http://cns.wp/wp-admin/admin-ajax.php';