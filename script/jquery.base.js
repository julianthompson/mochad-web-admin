$(document).ready(function() {


	$('.make-switch').on('switch-change', function (e, data) {
		var $el = $(data.el)
		var value = data.value;
		var target = $(this).closest('div.node').data('id');
		//console.log(e, $el, value);

		$.ajax({
			url: './command.php',
			data: {
				t: target,
			  c: (value)?'on':'off'
			},
			dataType: "json",
			success: function(data, textStatus, jqXHR) {
				console.log(data);
		  	$('#responsedata').html('<pre>'+JSON.stringify(data)+'</pre>');
				process_status(data.status);
			}
		});
		
	});


	$( "div.device" ).each(function(index,value) {
		
		var target = $(this).data('id');
		$dimmer = $(this).find('input.dimmer');
		var $brightness = $(this).find('.brightnesslevel');

		try {
			$dimmer.slider({ 
				max: 100,
				min: 0,
				step: 5
			}).on('slideStop', function(event) {
				var $this = $(this);
				var sliderdata = $this.data('slider');
				var slidervalue = sliderdata.getValue();
				console.log(slidervalue);
				$brightness.text(event.value + '%');
				$.ajax({
					url: './command.php',
				  data: {
				  	t: target,
				  	c: 'xdim',
				  	l: slidervalue
				  },
				  success: function(data, textStatus, jqXHR) {
						console.log(data);
		  			$('#responsedata').html('<pre>'+JSON.stringify(data)+'</pre>');
						process_status(data.status);
				  },
				  dataType: "json"
				});
			}).on('slide', function(event) {
					$brightness.text(event.value + '%');
					console.log('move');
			});
			
		} catch(e) {
		
		};

		$(this).find('a.button').each(function(index,value) {
			$(this).click(function(e) {
		    e.preventDefault();
		    var command = $(this).data('command');
		    console.log(command+':'+target);
		    switch(command) {
		    	case 'bright':
		    	  
		    		var $node = $('.node[data-id='+target+']');
		    		var $slider = $node.find('.slider')
		    		var sliderdata = $slider.data('slider');
		    		//var slidervalue = $slider.getValue();
		    		console.log(sliderdata);//.data('slider').getValue());
		    	break;
		    	
		    	case 'dim':
		    		
		    	break;
		    }
		    $.ajax({
				  url: './command.php',
				  data: {
				  	t: target,
				  	c: command
				  },
				  success: function(data, textStatus, jqXHR) {
				    console.log(data);
		  			$('#responsedata').html('<pre>'+JSON.stringify(data)+'</pre>');
						process_status(data.status);
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
				//console.log(id);
				//console.log(this);
				if (this.status==0) {
					//$('.node[data-id='+id+']').find('.slider').slider('disable');
					$('.node[data-id='+id+']').removeClass('status-on');
				} else {
					//$('.node[data-id='+id+']').find('.slider').slider('enable');
					$('.node[data-id='+id+']').addClass('status-on');
				}
			});
		});
	}

});


