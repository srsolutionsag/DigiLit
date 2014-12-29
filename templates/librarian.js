$('#library_select').change(function (e) {
	var $lib_id = $('#library_select').val();
	sr_link_input = '{URL}&lib_id=' + $lib_id;
	var replacer = new RegExp('amp;', 'g');
	sr_link_input = sr_link_input.replace(replacer, '');

	$.ajax({
		type: 'GET',
		url: sr_link_input,
		data: JSON.stringify($lib_id),
		//data: '',
		contentType: 'text/html;',
		dataType: 'text',
		cache: false,
		success: function (data) {
			//console.log(data);
			$('#librarian_select').prop('disabled', false);
			$('#librarian_select').html(data);

		},
		error: function (xhr, ajaxOptions, thrownError) {
			console.log('error...', xhr);
			//error logging
		},
		complete: function () {
			//afer ajax call is completed
		}
	});
});
