function parseDate(str) {
	var mdy = str.split('-');
	var mon = mdy[1]-1;
	return new Date(mdy[0],mon , mdy[2]);
}
		
function daydiff(first, second) {
	return (second-first)/(1000*60*60*24)
}
function trim(str) {
		return str.replace(/^\s+|\s+$/g,"");
}

function roundNumber(rnum, rlength) { 
  var newnumber = Math.round(rnum*Math.pow(10,rlength))/Math.pow(10,rlength);
  return parseFloat(newnumber);
}

function get_states(country_code,siteurl) {
	
	 $('#span_state').html('<img src="'+siteurl+'/images/ajax-loading-light-green.gif" />');
	  $.ajax({
		 url: siteurl+"/tour_ajax_functions.php?operation=get_states_dropdown&field_name=agent_state&country_code=" + country_code,
		 success: function(data) {
			if(data=='') {
				document.getElementById('span_state').innerHTML = 'none';
				document.getElementById('span_state_other').style.display = 'inline';
			} else {
				document.getElementById('span_state').innerHTML = data;
				if(document.getElementById('span_state_other')){
					document.getElementById('span_state_other').style.display = 'none';
				}
				document.getElementById('span_state').style.display = 'inline';
			}			 
		}
	});
}

function show_states_with_field(countryid,fieldname,siteurl){
	$('#disp_states').html('<img src="'+siteurl+'/images/loading.gif" />');
	if(countryid=="CA" || countryid=="US"){
			$.ajax({
			 url: siteurl+"/tour_ajax_functions.php?operation=show_states_with_field&fieldname="+fieldname+"&countryid="+countryid,
			 success: function(data) {
				 document.getElementById('disp_states').innerHTML = data;
			}
		  });
	}else{
			$.ajax({
			 url: siteurl+"/tour_ajax_functions.php?operation=show_states_with_textfield&fieldname="+fieldname,
			 success: function(data) {
				 document.getElementById('disp_states').innerHTML = data;
			}
		  });

	}
}

function show_dmc_from_country(countryid,fieldname,siteurl){
	$('#disp_dmc').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_dmc_from_country&fieldname="+fieldname+"&countryid="+countryid,
	 success: function(data) {
		 
		 document.getElementById('disp_dmc').innerHTML = data;
	}
  });

	
}



function show_country_tags_dropdown(tagid,fieldname,siteurl){
	$('#disp_country').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_country_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_country').innerHTML = data;
	}
  });	
}

function show_region_tags_dropdown(tagid,fieldname,siteurl){
	$('#disp_region').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_region_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_region').innerHTML = data;
	}
  });	
}



function show_departure_location_tags_dropdown(tagid,fieldname,siteurl){
	$('#disp_region').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_departure_location_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_region').innerHTML = data;
	}
  });	
}


function show_routes_tags_dropdown(tagid,fieldname,siteurl){
	$('#disp_routes').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_routes_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_routes').innerHTML = data;
	}
  });	
}


function show_hotels_from_type(type,fieldname,siteurl){
	$('#disp_dmc').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_hotels_from_type&fieldname="+fieldname+"&type="+type,
	 success: function(data) {
		 
		 document.getElementById('disp_dmc').innerHTML = data;
	}
  });

	
}



function show_tags_dropdown(tagid,fieldname,dispid,siteurl,show_parent){
	 show_parent = (typeof show_parent == 'undefined') ?
     '' : show_parent;
	
	$('#disp_dmc').html('<img src="'+siteurl+'/images/loading.gif" />');
	if(show_parent!=''){
		var tag_url=siteurl+"/tour_ajax_functions.php?operation=show_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid+"&show_parent="+show_parent;
	}else{
		var tag_url=siteurl+"/tour_ajax_functions.php?operation=show_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid;
	}
	
	$.ajax({
	 url: tag_url,
	 success: function(data) {
		 
		 document.getElementById(dispid).innerHTML = data;
	}
  });	
}



function disp_consortium_commission(selected_value,siteurl){
	$.ajax({
		 url: siteurl+"/tour_ajax_functions.php?operation=show_consortium_commission&consortiumid="+selected_value,
		 success: function(data) {
			 document.getElementById('agency_commission').value = data;
		}
	});
}


function show_faq_category(selected_value,siteurl){
	$('#disp_sub').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
		 url: siteurl+"/tour_ajax_functions.php?operation=show_faq_category&catid="+selected_value,
		 success: function(data) {
			 document.getElementById('disp_sub').innerHTML = data;
		}
	});
}






function show_states(countryid,siteurl){
	
	 $('#disp_states').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_states&countryid="+countryid,
     success: function(data) {
		 document.getElementById('disp_states').innerHTML = data;
    }
  });

}
function show_regions(countryid,siteurl){
	$('#disp_region').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_regions&countryid="+countryid,
     success: function(data) {
		 document.getElementById('disp_region').innerHTML = data;
		 if(document.getElementById('disp_province')){
			document.getElementById('disp_province').innerHTML = '<select name="province_id" id"province_id"><option value="">Select Province</option></select>';
		 }

		if(document.getElementById('disp_comune')){
			document.getElementById('disp_comune').innerHTML = '<select name="comune_id" id"comune_id"><option value="">Select Comune</option></select>';
		}
		if(document.getElementById('disp_locality')){
			document.getElementById('disp_locality').innerHTML = '<select name="locality_id" id"locality_id"><option value="">Select Locality</option></select>';
		}
    }
  });
}

function show_provinces(regionid,siteurl){
	$('#disp_province').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_provinces&regionid="+regionid,
     success: function(data) {
		 document.getElementById('disp_province').innerHTML = data;
		 if(document.getElementById('disp_comune')){
			document.getElementById('disp_comune').innerHTML = '<select name="comune_id" id"comune_id"><option value="">Select Comune</option></select>';
		}
		if(document.getElementById('disp_locality')){
			document.getElementById('disp_locality').innerHTML = '<select name="locality_id" id"locality_id"><option value="">Select Locality</option></select>';
		}
    }
  });
}

function show_comune(provinceid,siteurl){
	$('#disp_comune').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_comunes&provinceid="+provinceid,
     success: function(data) {
		 document.getElementById('disp_comune').innerHTML = data;
		 if(document.getElementById('disp_locality')){
			document.getElementById('disp_locality').innerHTML = '<select name="locality_id" id"locality_id"><option value="">Select Locality</option></select>';
		}
    }
  });
}

function show_region_comune(regionid,siteurl){
	$('#disp_comune').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_region_comunes&regionid="+regionid,
     success: function(data) {
		 document.getElementById('disp_comune').innerHTML = data;
		 
    }
  });
}

function show_locality(comuneid,siteurl){
	$('#disp_locality').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_locality&comuneid="+comuneid,
     success: function(data) {
		 document.getElementById('disp_locality').innerHTML = data;
    }
  });
}

function show_exchange_rates(siteurl) {
  var net_cost=document.getElementById('net_cost').value;
  var net_commission=document.getElementById('commission').value;
  if(net_cost==""){
	alert("Please enter NET cost.");
	return false;
  }else if(u_currency==""){
	alert("Please select input currency.");
	return false;
  }
  var u_currency=document.getElementById('u_currency').value;
  $('#tour_price').html('<img src="'+siteurl+'/images/ajax-loading_green.gif" />');
  $.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_exchange_price&amount="+net_cost+"&commission="+net_commission+"&currency="+u_currency,
     success: function(data) {
		 document.getElementById('show_commission_prices').innerHTML = data;
    }
  });
 }


 function show_location_region_tags(tagid,siteurl){
	$('#disp_region').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_location_region_tags&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_region').innerHTML = data;
	}
  });	
}

function show_city_tags_dropdown(tagid,siteurl){
	$('#disp_city').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_city_tags_dropdown&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_city').innerHTML = data;
	}
  });	
}

function disp_price_strategy(strategy){
	document.getElementById('price_a').style.display = 'none';
	document.getElementById('price_b').style.display = 'none';
	document.getElementById('price_c').style.display = 'none';
	if(strategy=="A"){
		document.getElementById('price_a').style.display = 'block';
	}else if(strategy=="B"){
		document.getElementById('price_b').style.display = 'block';
	}else if(strategy=="C"){
		document.getElementById('price_c').style.display = 'block';
	}
}

function show_cal(month,year,siteurl,tour_id,disp_id,edit_mode){
	
	$('#'+disp_id).html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_cal&month="+month+"&year="+year+"&tour_id="+tour_id+"&disp_id="+disp_id+"&edit_mode="+edit_mode,
     success: function(data) {
		 document.getElementById(disp_id).innerHTML = data;
    }
  });
}

function show_action_cal(month,year,siteurl,id,disp_id,edit_mode){
	
	$('#'+disp_id).html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_action_cal&month="+month+"&year="+year+"&id="+id+"&disp_id="+disp_id+"&edit_mode="+edit_mode,
     success: function(data) {
		 document.getElementById(disp_id).innerHTML = data;
    }
  });
}

function change_cal(month,year,siteurl,tour_id,disp_id,edit_mode){
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=change_cal&month="+month+"&year="+year+"&tour_id="+tour_id+"&disp_id="+disp_id+"&edit_mode="+edit_mode,
     success: function(data) {
		 document.getElementById(disp_id).innerHTML = data;
    }
  });
}

function show_cruise_cal(month,year,siteurl,cruise_id,disp_id,edit_mode){
	
	$('#'+disp_id).html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_cruise_cal&month="+month+"&year="+year+"&cruise_id="+cruise_id+"&disp_id="+disp_id+"&edit_mode="+edit_mode,
     success: function(data) {
		 document.getElementById(disp_id).innerHTML = data;
    }
  });

}

function show_package_cal(month,year,siteurl,package_id,disp_id,edit_mode){
	
	$('#'+disp_id).html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_package_cal&month="+month+"&year="+year+"&package_id="+package_id+"&disp_id="+disp_id+"&edit_mode="+edit_mode,
     success: function(data) {
		 document.getElementById(disp_id).innerHTML = data;
    }
  });

}


function show_location_tags_dropdown(tagid,fieldname,dispid,siteurl,level,prefix,funtion_to_call){
	funtion_to_call = (typeof funtion_to_call == 'undefined') ? '' : funtion_to_call;
	level = (typeof level == 'undefined') ? '' : level;
	prefix = (typeof prefix == 'undefined') ? '' : prefix;
	
	$('#'+dispid).html('<img src="'+siteurl+'/images/loading.gif" />');
	
	var tag_url=siteurl+"/tour_ajax_functions.php?operation=show_location_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid+"&level="+level+"&prefix="+prefix+"&funtion_to_call="+funtion_to_call;
	
	$.ajax({
	 url: tag_url,
	 success: function(data) {
		 var next_level=parseInt(level)+1;
		 for(var i=next_level;i<10;i++){
			document.getElementById('disp'+prefix+i).innerHTML='';
		 }
		 document.getElementById(dispid).innerHTML = data;
	}
  });	
}

function show_location_tags_for_accommodation(tagid,fieldname,dispid,siteurl,level,prefix){
	level = (typeof level == 'undefined') ? '' : level;
	prefix = (typeof prefix == 'undefined') ? '' : prefix;
	
	$('#'+dispid).html('<img src="'+siteurl+'/images/loading.gif" />');
	
	var tag_url=siteurl+"/tour_ajax_functions.php?operation=show_location_tags_for_accommodation&fieldname="+fieldname+"&tagid="+tagid+"&level="+level+"&prefix="+prefix;
	
	$.ajax({
	 url: tag_url,
	 success: function(data) {
		 var next_level=parseInt(level)+1;
		 
		 for(var i=next_level;i<10;i++){
			document.getElementById('disp'+prefix+i).innerHTML='';
		 }
		 document.getElementById(dispid).innerHTML = data;
	}
  });	
}


function show_accommodation_list(siteurl,keyword){
	$('#disp_results').html('<img src="'+siteurl+'/images/loading.gif" />');
	var accom_keyword = (typeof keyword == 'undefined') ? '' : keyword;

	var accom_type='';
	var location_tag='';
	var start_date='';
	var end_date='';
	if(document.getElementById('checkin_date').value==""){
		alert('Please select checkin date.');
		return false;
	}else if(document.getElementById('checkout_date').value==""){
		alert('Please select checkout date.');
		return false;
	}
	start_date=document.getElementById('checkin_date').value;
	end_date=document.getElementById('checkout_date').value;	
	if(accom_keyword==""){
		var accom_type=document.getElementById('dmc_accommodation_type').value;
		var location_tag='';
		for(var i=0;i<10;i++){
			if(document.getElementById('level'+i) && document.getElementById('level'+i).value!=''){
				location_tag=location_tag+","+document.getElementById('level'+i).value;
			}
		}
	}
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_accmmodation_result&accom_type="+accom_type+"&location_tag="+location_tag+"&keyword="+accom_keyword+"&start_date="+start_date+"&end_date="+end_date,
     success: function(data) {
		 document.getElementById('disp_results').innerHTML = data;
    }
  });
}


function show_other_transer(transfer_type,siteurl){
	  var country_parent='';
	  if(transfer_type=="218"){
		  country_parent='163';
	  }else if(transfer_type=="219"){
		  country_parent='162';
	  }else if(transfer_type=="220"){
		  country_parent='164';
	  }else if(transfer_type=="221"){
		  country_parent='222';
	  }

	$('#disp_other_country').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_other_transer&transfer_type="+country_parent,
     success: function(data) {
		 document.getElementById('disp_other_country').innerHTML = data;
		
    }
  });

}


function show_arrival_transer_country(transfer_type,siteurl){
	  var country_parent='';
	  if(transfer_type=="218"){
		  country_parent='163';
	  }else if(transfer_type=="219"){
		  country_parent='162';
	  }else if(transfer_type=="220"){
		  country_parent='164';
	  }else if(transfer_type=="221"){
		  country_parent='222';
	  }

	$('#disp_other_country').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_arrival_transer_country&transfer_type="+country_parent,
     success: function(data) {
		 document.getElementById('disp_arrival_country').innerHTML = data;
		
    }
  });

}


function show_transfer_region_tags_dropdown(tagid,fieldname,siteurl){
	$('#disp_region').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_transfer_region_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_region').innerHTML = data;
		 document.getElementById('disp_pickup').innerHTML = '<select><option value="">Select</option></select>';
		 document.getElementById('disp_dropoff').innerHTML = '<select><option value="">Select</option></select>';
	}
  });	
}

function show_other_transfer_region_tags_dropdown(tagid,fieldname,siteurl){
	$('#disp_region_other').html('<img src="'+siteurl+'/images/loading.gif" />');

	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_other_transfer_region_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_region_other').innerHTML = data;
		 document.getElementById('disp_departure').innerHTML = '<select><option value="">Select</option></select>';
		 
	}
  });	
}


function show_ferry_region_tags_dropdown(tagid,fieldname,siteurl){
	$('#disp_region_other').html('<img src="'+siteurl+'/images/loading.gif" />');

	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_ferry_region_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_region_other').innerHTML = data;
		 
	}
  });	
}

function show_ferry_arrival_region_tags_dropdown(tagid,fieldname,siteurl){
	$('#disp_arrival_region').html('<img src="'+siteurl+'/images/loading.gif" />');

	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_ferry_region_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_arrival_region').innerHTML = data;
		 
	}
  });	
}


function show_arrival_transfer_region_tags_dropdown(tagid,fieldname,siteurl){
	$('#disp_arrival_region').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_arrival_transfer_region_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_arrival_region').innerHTML = data;
		 document.getElementById('disp_arrival').innerHTML = '<select><option value="">Select</option></select>';
		 
	}
  });	
}

function show_online_transfer_pickup_tags(tagid,fieldname,siteurl){
	$('#disp_pickup').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_online_transfer_pickup_tags&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_pickup').innerHTML = data;
		  document.getElementById('disp_dropoff').innerHTML = '<select required="" title="Dropoff" class="form-control" id="fk_dropoff_id" name="fk_dropoff_id"><option value="">Select</option></select>';
		  $("#show_vehicle_price").html('');
		  $("#disp_continue_button").hide();
	}
  });	
}
function show_online_transfer_dropoff_tags(tagid,fieldname,siteurl){
	$('#disp_dropoff').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_online_transfer_dropoff_tags&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_dropoff').innerHTML = data;
		 $("#show_vehicle_price").html('');
		 $("#disp_continue_button").hide();
	}
  });	
}





function show_transfer_pickup_tags(tagid,fieldname,siteurl){
	$('#disp_pickup').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_transfer_pickup_tags&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_pickup').innerHTML = data;
	}
  });	
}
function show_transfer_dropoff_tags(tagid,fieldname,siteurl){
	$('#disp_dropoff').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_transfer_dropoff_tags&fieldname="+fieldname+"&tagid="+tagid,
	 success: function(data) {
		 document.getElementById('disp_dropoff').innerHTML = data;
	}
  });	
}
function show_transfer_tags_dropdown(tagid,fieldname,dispid,siteurl){
	
	$('#'+dispid).html('<img src="'+siteurl+'/images/loading.gif" />');
	var tag_url=siteurl+"/tour_ajax_functions.php?operation=show_transfer_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid;
	$.ajax({
	 url: tag_url,
	 success: function(data) {
		 
		 document.getElementById(dispid).innerHTML = data;
	}
  });	
}

function show_transfer_price(siteurl){
	
	var fk_pickup_id = $("#fk_pickup_id").val();
	var fk_dropoff_id = $("#fk_dropoff_id").val();
	var no_pax = $("#no_pax").val();
	
	//$('#'+dispid).html('<img src="'+siteurl+'/images/loading.gif" />');
	var tag_url=siteurl+"/tour_ajax_functions.php?operation=show_transfer_price&fk_pickup_id="+fk_pickup_id+"&fk_dropoff_id="+fk_dropoff_id+"&no_pax="+no_pax;
	$.ajax({
	 url: tag_url,
	 success: function(data) {	
		//alert(data);
		 $("#engine_booking_price").val(data);
	}
  });	
}
function show_vehicle_price(siteurl){	
	var fk_pickup_id = $("#fk_pickup_id").val();
	var fk_dropoff_id = $("#fk_dropoff_id").val();	
	var tag_url=siteurl+"/tour_ajax_functions.php?operation=show_vehicle_price&fk_pickup_id="+fk_pickup_id+"&fk_dropoff_id="+fk_dropoff_id;
	$.ajax({
	 url: tag_url,
	 success: function(data) {
		 data=$.trim(data);
		 if(data==''){
			$("#show_vehicle_price").html('');
			$("#disp_continue_button").hide();
		 }else if(data=='No'){
			$("#show_vehicle_price").html('<font style="color:red">Please <a href="contact-us.html">email us</a> for this transfer. We will get back to you soon with a quote.</font>');
			$("#disp_continue_button").hide();
		 }else{
			$("#show_vehicle_price").html(data);
			$("#disp_continue_button").show();
		 }
	}
  });	
}


function show_transfer_supplier(siteurl){
	$('#disp_results').html('<img src="'+siteurl+'/images/loading.gif" />');
	var transfer_country=document.getElementById('transfer_country').value;
	var fk_region_id=document.getElementById('fk_region_id').value;
	var pickup=document.getElementById('fk_pickup_id').value;
	var dropoff=document.getElementById('fk_dropoff_id').value;
	var dmc_transfer_type=document.getElementById('dmc_transfer_type').value;
	var pax=''
	if (document.getElementById('total_pax_on_file'))
	{
		pax=document.getElementById('total_pax_on_file').value;
	}
	
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_transfer_result&dmc_transfer_type="+dmc_transfer_type+"&fk_region_id="+fk_region_id+"&fk_pickup_id="+pickup+"&fk_dropoff_id="+dropoff+"&transfer_country="+transfer_country+"&pax="+pax,
     success: function(data) {
		 document.getElementById('disp_results').innerHTML = data;
		 if(data !=''){
			 $('a.jt').cluetip({
				width: 400,
				cursor: 'pointer',
				cluetipClass: 'jtip',
				arrows: true,
				dropShadow: false,
				sticky: false,
				mouseOutClose: true,
				closePosition: 'title',
				clickThrough:     true,
				closeText: '<img src="../facefiles/closelabel.png" alt="close" />'
			  });
		 }
    }
  });
}


function show_other_transfer_supplier(siteurl){
	$('#disp_results_other').html('<img src="'+siteurl+'/images/loading.gif" />');
	var dmc_transfer_type=document.getElementById('dmc_transfer_type').value;

	var dep_transfer_country=document.getElementById('dep_transfer_country').value;
	var dep_region_id=document.getElementById('dep_region_id').value;
	var fk_pickup_station_id="";
	var fk_dropoff_station_id="";
	if(document.getElementById('fk_pickup_station_id')){
		fk_pickup_station_id=document.getElementById('fk_pickup_station_id').value;
	}
	if(document.getElementById('fk_dropoff_station_id')){
		fk_dropoff_station_id=document.getElementById('fk_dropoff_station_id').value;
	}
	var arr_transfer_country=document.getElementById('arr_transfer_country').value;
	var arr_region_id=document.getElementById('arr_region_id').value;

	if(dmc_transfer_type=="219"){
		fk_pickup_station_id=dep_region_id;
		fk_dropoff_station_id=arr_region_id;
	}
	
	



	
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_other_transfer_result&dmc_transfer_type="+dmc_transfer_type+"&dep_region_id="+dep_region_id+"&fk_pickup_station_id="+fk_pickup_station_id+"&arr_transfer_country="+arr_transfer_country+"&arr_region_id="+arr_region_id+"&fk_dropoff_station_id="+fk_dropoff_station_id+"&dep_transfer_country="+dep_transfer_country,
     success: function(data) {
		 document.getElementById('disp_results_other').innerHTML = data;
		
    }
  });
}


function show_supplier_list(siteurl,keyword){
	$('#disp_supplier_list').html('<img src="'+siteurl+'/images/loading.gif" />');
	var supplier_keyword = (typeof keyword == 'undefined') ? '' : keyword;
	var business_type=document.getElementById('business_type').value;
	var location_tag='';
	for(var i=0;i<10;i++){
		if(document.getElementById('level'+i) && document.getElementById('level'+i).value!=''){
			location_tag=location_tag+","+document.getElementById('level'+i).value;
		}
	}
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_supplier_result&business_type="+business_type+"&location_tag="+location_tag+"&keyword="+supplier_keyword,
     success: function(data) {
		 document.getElementById('disp_supplier_list').innerHTML = data;
    }
  });
}

function show_route_pricing_option(siteurl,route_id){
	$('#disp_pricing_option').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_route_pricing_option&route_id="+route_id,
     success: function(data) {
		 document.getElementById('disp_pricing_option').innerHTML = data;
		 
    }
  });
}


function disp_vehicles(val){
	if(val=="PP"){
		document.getElementById('disp_vehicle_list').style.display = 'none';
	}else{
		document.getElementById('disp_vehicle_list').style.display = 'block';
	}

}


function show_flight_city_dropdown(countryid,siteurl){
	$('#disp_region').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_flight_city_dropdown&countryid="+countryid,
	 success: function(data) {
		 document.getElementById('disp_region').innerHTML = data;
		 document.getElementById('disp_airport').innerHTML ='<select name="fk_airport_id" id"fk_airport_id"><option value="">Select Airport</option></select>';
	}
  });	
}


function show_flight_airport_dropdown(cityid,siteurl){
	$('#disp_airport').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_flight_airport_dropdown&cityid="+cityid,
	 success: function(data) {
		 document.getElementById('disp_airport').innerHTML = data;
	}
  });	
}



function show_flight_arrival_city_dropdown(countryid,siteurl){
	$('#disp_arrival_region').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_flight_arrival_city_dropdown&countryid="+countryid,
	 success: function(data) {
		 document.getElementById('disp_arrival_region').innerHTML = data;
		 document.getElementById('disp_arrival_airport').innerHTML ='<select name="fk_arrival_airport_id" id"fk_arrival_airport_id"><option value="">Select Airport</option></select>';
	}
  });	
}


function show_flight_arrival_airport_dropdown(cityid,siteurl){
	$('#disp_arrival_airport').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
	 url: siteurl+"/tour_ajax_functions.php?operation=show_flight_arrival_airport_dropdown&cityid="+cityid,
	 success: function(data) {
		 document.getElementById('disp_arrival_airport').innerHTML = data;
	}
  });	
}


function show_flight_supplier(siteurl){
	if(document.getElementById('disp_results_other')){
		$('#disp_results_other').html('<img src="'+siteurl+'/images/loading.gif" />');
		var dep_airport_id=document.getElementById('fk_airport_id').value;
		var arrival_airport_id=document.getElementById('fk_arrival_airport_id').value;
		
		$.ajax({
		 url: siteurl+"/tour_ajax_functions.php?operation=show_flight_supplier&dep_airport_id="+dep_airport_id+"&arrival_airport_id="+arrival_airport_id,
		 success: function(data) {
			 document.getElementById('disp_results_other').innerHTML = data;
		}
	  });
	}
}



function booking_engine_country_operation(siteurl){
	var counter=document.getElementById('theValue').value;
	$.ajax({
		 url: siteurl+"/tour_ajax_functions.php?operation=booking_engine_country_operation&counter="+counter,
		 success: function(data) {
			 var num=parseInt(counter);
			 var ni = document.getElementById('myDiv');
			 var divIdName = "div_"+num;
			 var newdiv = document.createElement('div');
			 newdiv.setAttribute("id",divIdName);
			 newdiv.innerHTML=data;
			 ni.appendChild(newdiv);
			 document.getElementById('theValue').value=parseInt(counter)+1;
		}
	 });


}


function show_other_option(val){
	if(val=="4"){
		document.getElementById('disp_other').style.display='block';
	}else{
		document.getElementById('disp_other').style.display='none';
	}
}


function getCheckedValue(radioObj) {
		if(!radioObj)
			return "";
		var radioLength = radioObj.length;
		if(radioLength == undefined)
			if(radioObj.checked)
				return radioObj.value;
			else
				return "";
		for(var i = 0; i < radioLength; i++) {
			if(radioObj[i].checked) {
				return radioObj[i].value;
			}
		}
		return "";
	}


function show_cruise_list(siteurl,file_id,keyword){
	show_cruise_excursion(siteurl,keyword);
	$('#disp_cruises').html('<img src="'+siteurl+'/images/loading.gif" />');
	$('#disp_rooms').html('<img src="'+siteurl+'/images/loading.gif" />');
	$('#disp_dates').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_cruises&file_id="+file_id+"&keyword="+keyword,
     success: function(data) {
		 document.getElementById('disp_cruises').innerHTML = data;
		 document.getElementById('disp_rooms').innerHTML = '<select name="fk_room_id"><option value="">Select</option></select>';
		 document.getElementById('disp_dates').innerHTML = '<select name="check_in_date"><option value="">Select</option></select>';
    }
  });
}
function show_cruise_excursion(siteurl,supplier_id){
	$('#disp_excrusion').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_excursion&supplier_id="+supplier_id,
     success: function(data) {
		 document.getElementById('disp_excrusion').innerHTML = data;
    }
  });
}

function show_cruise_rooms(fk_cruise_id,file_id,siteurl){
	$('#disp_rooms').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_cruises_room&file_id="+file_id+"&fk_cruise_id="+fk_cruise_id,
     success: function(data) {
		 document.getElementById('disp_rooms').innerHTML = data;
    }
  });

}

function show_cruise_dates(fk_cruise_id,file_id,siteurl){
	$('#disp_dates').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_cruises_date&file_id="+file_id+"&fk_cruise_id="+fk_cruise_id,
     success: function(data) {
		 document.getElementById('disp_dates').innerHTML = data;
    }
  });

}

function show_price_mode(fk_cruise_id,file_id,siteurl){
	
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=show_price_mode&file_id="+file_id+"&fk_cruise_id="+fk_cruise_id,
     success: function(data) {
		 
		 if(data=="Fluid"){
			document.getElementById('disp_price_mode').style.display = 'block';
			document.getElementById('disp_price_method').style.display = 'none';
		 }else{
			document.getElementById('disp_price_mode').style.display = 'none';
			document.getElementById('disp_price_method').style.display = 'block';
		 }
    }
  });

}

function display_multiday_tour_hotels(package_id,activity_segment,siteurl,leave_hotel){
	
	$('#disp_multiday_tour_hotels').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=display_multiday_tour_hotels&package_id="+package_id+"&activity_segment="+activity_segment+"&leave_hotel="+leave_hotel,
     success: function(data) {
		 document.getElementById('disp_multiday_tour_hotels').innerHTML = data;
    }
  });

}


function display_multiday_package_hotels(package_id,siteurl){
	
	$('#disp_multiday_package_hotels').html('<img src="'+siteurl+'/images/loading.gif" />');
	$.ajax({
     url: siteurl+"/tour_ajax_functions.php?operation=disp_multiday_package_hotels&package_id="+package_id,
     success: function(data) {
		 document.getElementById('disp_multiday_package_hotels').innerHTML = data;
    }
  });

}


function display_package_segment_hotel(disp_id,package_id,hotel_id,segment_id,prev_rooms,prev_qty,new_room,new_quantity,siteurl){
	

	var hotel_room_list=prev_rooms+","+new_room;
	var hotel_room_qty_list=prev_qty+","+new_quantity;

	parent.document.getElementById(disp_id).innerHTML='<img src="'+siteurl+'/images/loading.gif" />';
	$.ajax({
     async: false,
	 url: siteurl+"/tour_ajax_functions.php?operation=disp_segment_hotels&room_list="+hotel_room_list+"&qty_list="+hotel_room_qty_list,
     success: function(data) {
		 parent.document.getElementById(disp_id).innerHTML = data;
		 parent.document.getElementById('segment_room_'+segment_id).value=hotel_room_list;
		 parent.document.getElementById('segment_room_qty_'+segment_id).value=hotel_room_qty_list;
    }
  });

}

function show_location_map_lat_lon(tagid,siteurl){
	
	var tag_url=siteurl+"/tour_ajax_functions.php?operation=show_location_map_lat_lon&tagid="+tagid+"";	
	$.ajax({
	 url: tag_url,
	 success: function(data) {		 
		 var arr = data.split(':');	          
		 document.getElementById('map_latitude').value = arr[0];
		 document.getElementById('map_longitude').value = arr[1];
	}
  });	
}

function show_location_multi_map_lat_lon(tagid,siteurl,mid){
	
	var tag_url=siteurl+"/tour_ajax_functions.php?operation=show_location_map_lat_lon&tagid="+tagid+"";	
	$.ajax({
	 url: tag_url,
	 success: function(data) {		 
		 var arr = data.split(':');	          
		 document.getElementById('map_multiple_latitude_'+mid).value = arr[0];
		 document.getElementById('map_multitple_longitude_'+mid).value = arr[1];
	}
  });	
}

function show_multi_location_tags_dropdown(tagid,fieldname,dispid,siteurl,level,prefix,funtion_to_call,mid){
	funtion_to_call = (typeof funtion_to_call == 'undefined') ? '' : funtion_to_call;
	level = (typeof level == 'undefined') ? '' : level;
	prefix = (typeof prefix == 'undefined') ? '' : prefix;
	
	$('#'+dispid).html('<img src="'+siteurl+'/images/loading.gif" />');
	
	var tag_url=siteurl+"/tour_ajax_functions.php?operation=show_multilocation_tags_dropdown&fieldname="+fieldname+"&tagid="+tagid+"&level="+level+"&prefix="+prefix+"&funtion_to_call="+funtion_to_call+"&mid="+mid;
	
	$.ajax({
	 url: tag_url,
	 success: function(data) {
		 var next_level=parseInt(level)+1;
		 for(var i=next_level;i<10;i++){
			document.getElementById('disp'+prefix+i).innerHTML='';
		 }
		 document.getElementById(dispid).innerHTML = data;
	}
  });	
}