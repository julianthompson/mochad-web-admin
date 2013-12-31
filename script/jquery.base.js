$(document).ready(function() {


	$('.make-switch').on('switch-change', function (e, data) {
		var $el = $(data.el)
		var value = data.value;
		var target = $(this).closest('div.node').data('id');
		console.log(e, $el, value);

		$.ajax({
			url: './command.php',
			data: {
				t: target,
			  	c: (value)?'on':'off'
			},
			success: function(data, textStatus, jqXHR) {
				console.log(data);
		  		$('#responsedata').html('<pre>'+JSON.stringify(data)+'</pre>');
		  		//alert('here');
				process_status(data.status);
			},
			dataType: "json"
		});
		
	});
	
	//$('div.onoffswitch').bootstrapSwitch();


	$( "div.device" ).each(function(index,value) {
		
		var target = $(this).data('id');
		$dimmer = $(this).find('div.dimmer');
		
		if ($dimmer) {
			var dimmervalue = $dimmer.data('value');
		}
		
		$dimmer.slider({ 
			max: 100,
			min: 0
		}).on('slideStop', function( event) {
			console.log($(this)[0].slider('getvalue'));
			$device = $(ui.handle).closest('div.node');		  
			$device.find('.brightness').html(ui.value + '%');
			$.ajax({
				url: './command.php',
				  data: {
				  	t: target,
				  	c: 'xdim',
				  	l: ui.value
				  },
				  success: function(data, textStatus, jqXHR) {
				    console.log(data);
		  			$('#responsedata').html('<pre>'+JSON.stringify(data)+'</pre>');
		  			//alert('here');
				  },
				  dataType: "json"
			});

		});
		
		$(this).find('a.button').each(function(index,value) {
			$(this).click(function(e) {
		    e.preventDefault();
		    var command = $(this).data('command');
		    console.log(command+':'+target);
		    $.ajax({
				  url: './command.php',
				  data: {
				  	t: target,
				  	c: command
				  },
				  success: function(data, textStatus, jqXHR) {
				    console.log(data);
		  			$('#responsedata').html('<pre>'+JSON.stringify(data)+'</pre>');
		  			//alert('here');
				  },
				  dataType: "json"
				});
			});
		});
		
	});

	function process_status(status) {
		$.each(status, function(housecode) {
			$.each(this, function(index) {
				var id = housecode+index;
				id = id.toLowerCase();
				console.log(id);
				console.log(this);
				if (this=='0') {
					$('.node[data-id='+id+']').removeClass('status-on');
				} else {
					$('.node[data-id='+id+']').addClass('status-on');
				}
			});
		});
	}
	
	//alert('loaded');
});


