/* Selector */

function Selector(value, type) {
	this.val = value;
	this.type = type;
	if (this.type != "" && this.val.indexOf(this.type) == 0)
		this.val = this.val.substr(1);
}

Selector.Types = {
					'Class':	'.',
					'ID':		'#',
					'Tag':		''
					};

/**
 * Returns formatted value for use as jQuery selector
 * @return string formatted as a jQuery selector
 */
Selector.prototype.get = function() {
	return this.type + this.val;
}

function escSelector(id_value) {
	return id_value.toString().replace(/(\[|\])/gi, '\\$1');
}