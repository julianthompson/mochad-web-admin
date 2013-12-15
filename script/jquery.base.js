$(document).ready(function() {

	$("input[type=checkbox]").switchButton({
	  on_label: 'ON',
	  off_label: 'OFF',
	  width: 40,
	  height: 20,
	  button_width: 20
	}).change(function() {
		console.log(this);
	});

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
			stop: function( event, ui ) {
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


