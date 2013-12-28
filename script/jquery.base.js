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
			animate: "fast",
			max: 255,
			min: 0,
			slideStop: function( event, ui ) {
			  console.log(ui.value);
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

			},
			value: dimmervalue
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
	
	//alert('loaded');
});


