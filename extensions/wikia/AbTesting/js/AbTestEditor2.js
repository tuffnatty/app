$( function() {
	$('td.timeago').timeago();
	$('#AbTestEditor').on('click','tr.exp', $.proxy(function(ev) {
		var id = $(ev.target).closest('tr').data()['id'];
		$('tr.exp').not('tr[data-id="'+id+'"]').addClass('collapsed');
		$('tr[data-id="'+id+'"]').removeClass('collapsed');
	},this));

})