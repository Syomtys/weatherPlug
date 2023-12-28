jQuery(function($) {
  function get_data_db() {
	$.ajax({
		url: $('.form_act').attr('data-url'),
		data: {
		  action: 'weatherPlug'
		},
		type: 'GET',
		dataType: 'json',
		success: function(response) {
		  $('.list_users_weather .item').remove();	
		  $('.user_weather span').remove();
		  $('.content_weather .form_act button').remove();
		  if (response.status == 1) {
			response.val.forEach((element) => {
			  $('.list_users_weather').append('<div class="item">	<span class="city">'+element.city+'</span> <span class="datetime">'+element.recorded_at+'</span> <spanclass="temperature">'+element.temperature+'</span>	<div>');
			});
			$('.user_weather').append('<span class="city">'+response.user.city+'</span> <span class="temperature">'+response.user.temperature+'</span>');
			$('.content_weather .form_act').append('<button>OFF</button>');
		  } else if (response.status == 0) {
			$('.content_weather .form_act').append('<button>ON</button>');
			$('.user_weather').append('<span class="city">виджет погоды выключен</span>');
		  }
		}
	  });  
  }
  get_data_db();
  
  $(document).on('click', '#form_recall button', function(e) {
	e.preventDefault();
	var action = $(this).text();
	console.log(action);
	$.ajax({
	  url: $('.form_act').attr('data-url'),
	  data: {
		action: 'weatherPlug_form',
		form: action
	  },
	  type: 'POST',
	  success: function(response) {
		console.log(response);
		get_data_db();
	  }
	});
  });
});