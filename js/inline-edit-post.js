if ( typeof(cnr) == 'undefined' || typeof(cnr) != 'object' )
	cnr = {};
if ( typeof(postData) == 'undefined' )
	postData = {};
(function($) {
	//Move init method to guarantee execution order
	inlineEditPost.initSaved = inlineEditPost.init;
	inlineEditPost.init = function() {};
	//Extend inlineEditPost object
	cnr.inlineEditPost = $.extend({}, inlineEditPost);
	
	cnr.inlineEditPost.field_parent = 'cnr_post_parent';
	cnr.inlineEditPost.init = function() {
		//Execute default init method
		if ( inlineEditPost.initSaved )
			inlineEditPost.initSaved();
		//Unbind quick edit click events
		$('a.editinline').die('click');
		//Bind new quick edit click handler
		$('a.editinline').live('click', function() { cnr.inlineEditPost.editHandler(this); return false; });
		var qeRow = $('#inline-edit');
		$('a.save', qeRow).click(function() { return cnr.inlineEditPost.save(this); });
		$('td', qeRow).keydown(function(e) { if (e.which == 13) { return cnr.inlineEditPost.save(this); } });
		//Restore original init method for future use
		if ( inlineEditPost.initSaved )
			inlineEditPost.init = inlineEditPost.initSaved;
	};
	
	cnr.inlineEditPost.save = function(id) {
		var t = this, post_id, post_parent;
		//Update post data
		if (typeof(id) == 'object')
			id = t.getId(id);
		if (id) {
			//Setup postData object for post
			post_id = 'post_' + id;
			if (typeof(postData) == 'undefined')
				postData = {};
			if (!(post_id in postData))
				postData[post_id] = {};
				
			//Get post parent
			post_parent = $('#edit-' + id + ' #' + t.field_parent + ' option:selected');
			if (post_parent.length) {
				//Set post parent in postData
				postData[post_id]['post_parent'] = post_parent.val();
			}
		}
		return true;
	};
	
	cnr.inlineEditPost.editHandler = function(id) {
		this.preEdit(id);
		inlineEditPost.edit(id);
		this.postEdit(id);
	}
	
	cnr.inlineEditPost.preEdit = function(id) {
		var t = this, post_id, section_select, parent_id;
		if (typeof(id) == 'object')
			id = t.getId(id);
		//Get master section selection
		section_select = $('#inline-edit #' + t.field_parent);
		//Get Parent ID
		if (section_select.length && typeof(postData) != 'undefined' && (post_id = 'post_' + id) && ((post_id) in postData) && ('post_parent' in postData[post_id])) {
			parent_id = postData[post_id].post_parent;
			//Set selected
			$('option[value=' + parent_id + ']', section_select).get(0).defaultSelected = true;
		}
	};
	
	cnr.inlineEditPost.postEdit = function(id) {
		$('#inline-edit #' + this.field_parent + ' option').removeAttr('selected');
	};
	
	$(document).ready(function() {cnr.inlineEditPost.init();});
})(jQuery);