		$(document).ready(function () {
			// Ajax call function when it starting
			$("#loadingImg").ajaxStart(function(){
			   $(this).show();
			});
			$("#loadingImg").ajaxStop(function(){
			   $(this).hide();
			});
			// Ajax
			$('#submit').on('click',function(){
				var val = $('input[name="url"]').val();
				var regEx = /^https:\/\/(www\.)?instagram\.com\//gi;
				
				if(!val){
					alert('Please enter the content.');
					$('input[name="url"]').focus();
				}
				else if(!regEx.exec(val)){
					alert('Please enter correct url.');
				}
				else{
					$('input[name="url"]').val('');
					$.ajax({
						url: 'ajax.php',
						type: 'POST',
						data: {
							url: val,
						},
						error: function() {
							console.log("Ajax Error");
						},
						success: function(response){
							var obj = JSON.parse(response);
							if(obj[0].Error){
								alert(obj[0].msg);
							}
							else{
								$('#content').empty();
								var forEachIt = obj.forEach(function(item, index, array){
									var i = index+1;
								  if(item.isVideo==1){ // Video
									  $('#content').append('<a class="download" href="'+item.video_url+'&dl=1"><img src="'+item.image_url+'" /><div>Video ('+i+')</div></a>');
									  $('#content').append('<a class="download" href="'+item.image_url+'&dl=1"><img src="'+item.image_url+'" /><div>Video thumbnail ('+i+')</div></a>');
								  }
								  else if(item.isProfile==1){ // Profile
									  $('#content').append('<a class="download" href="'+item.image_url+'&dl=1"><img src="'+item.image_url+'" /><div>Profile picture</div></a>');
								  }
								  else{ // Photo
									  $('#content').append('<a class="download" href="'+item.image_url+'&dl=1"><img src="'+item.image_url+'" /><div>Post photo ('+i+')</div></a>');
								  }
								});
								$('#content').append('<h4>Note: Click images to download.</h1>');
							}
						}
					});
				}
				
			});
		});