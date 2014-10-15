// Main javascript
var $ = jQuery.noConflict();

var form_validate_class = '.validate-form';
var policy_link = 'a.policy-link';


$(document).ready(function(){
	
	//Fix placeholder for IE8 and IE9
	$('input, textarea').placeholder();
	//Sets a validation in forms
	$(form_validate_class).submit(validateForm);
	$(form_validate_class).find("input[type='text'], input[type='password']").click(function(){ $(this).removeClass('input-error'); });
	
	$(policy_link).fancybox({type : 'iframe'});
	
	//Activates the messages display
	getMainMessages();
	
});



/**
 * Validates a form wich elements have an special class
 */

function validateForm(){
	
	var errors = 0;
	resetFormErrors($(this));
	var email_validation = /^([\da-z_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
	
	$.each($(this).find("input[type='text'], input[type='password']"), function(index, value){
		
		if(($(this).attr('type') == 'text' || $(this).attr('type') == 'password') && $(this).hasClass('validate')){
			if($(this).val() == ''){
				setErrorInForm($(this));
				errors++;
			}
			else if($(this).hasClass('email-field')){
				if( !email_validation.test($(this).val()) ) {
					setErrorInForm($(this));
					errors++;
				}
			}
			
		}
		
	});
	
	
	if(errors > 0){
		scrollToAnchor('contact');
		return false;
	}
	else if($(this).find("input[name='policy']").length > 0 && !$(this).find("input[name='policy']").is(':checked')){
		alert('Debes aceptar los términos y condiciones y el aviso de privacidad');
		return false;
	}
	else{
		return true;	
	}
	
}


function setErrorInForm(field){
	field.addClass('input-error');
}

function resetFormErrors(form){
	form.find("input[type='text'], input[type='password']").removeClass('input-error');
}



/**
 * Displays messages
 */

function getMainMessages(){
	
	var main_content = '.main-wrapper';
	var main_message = '#main-messages';
	
	if($(main_message).length > 0){
	
		var message_width = $(main_content).width() * 0.6;
		$(main_message).children('ul').width(message_width);
		
		var message_left_position = parseInt($(main_message).children('ul').width() / 2);
		$(main_message).children('ul').css('left', '-' + message_left_position + 'px');
		
		$(main_message).show();
		var close_icon_top_position = ($(main_message + ' ul').children('li').height() / 2) - $(main_message + ' ul li').children('.close-icon').height();
		$(main_message + ' ul').children('li').append('<a href="javascript: void();" class="close-icon"></a>');
		$(main_message + ' ul li').children('.close-icon').css('top', close_icon_top_position + 'px');
		
		$(main_message + ' ul li').children('.close-icon').click(function(){
			
			$(this).parent('li').fadeOut(500, function(){ 
			
				$(this).remove(); 
				
				if($(main_message + ' ul').children('li').length <= 0){
					$(main_message).remove();
				}
			
			});
			
		});
	
		$.each($(main_message + ' ul').children('li'), function(index){
			$(this).delay(index * 500).fadeIn(500);
		});
		
	}
	
}



function scrollToAnchor(aid){
    var aTag = $("a[name='"+ aid +"']");
    $('html,body').animate({scrollTop: aTag.offset().top},'slow');
}