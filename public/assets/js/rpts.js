$(document).ready(function() {

var province 			 = $("#province_id"); 
var district 			 = $("#district_id"); 
var municipality	 = $("#municipality_id"); 
var sector         = $("#sector_id");
var subsector      = $("#sub_sector_id");

// Disable Elements on load
	$(district).attr({disabled:"disabled"});
	$(municipality).attr({disabled:"disabled"});
  $(subsector).attr({disabled:"disabled"});

  // Onchange Event
	
	$(province).change( function(){
			if(this.value == ''){
					$(district).html("<option value='' disabled selected>-- Select --</option>").attr({disabled:"disabled"});
					$(municipality).html("<option value='' disabled selected>-- Select --</option>").attr({disabled:"disabled"});
			}else{		 	
					getDistricts(district,this.value,false); 
					$(district).removeAttr("disabled"); 	
					$(municipality).html("<option value='' disabled selected>-- Select --</option>").attr({disabled:"disabled"});
			}
	});

	$(district).change( function(){
			if(this.value == ''){					
					$(municipality).html("<option value='' disabled selected>-- Select --</option>").attr({disabled:"disabled"});
			}else{				  	
					getMunicipalities(municipality,$(province).val(),this.value, false);
					$(municipality).removeAttr("disabled"); 
			}
	});

  $(sector).change( function(){
    if(this.value == ''){
        $(subsector).html("<option value='' disabled selected>-- Select --</option>").attr({disabled:"disabled"});
    } else {
        getSubSectors(subsector, this.value, false);
        $(subsector).removeAttr("disabled");
    }


  });


  function getDistricts(el, provinceValue, preselect){
		var selectedDistrict 	=	$("#sdistrict_id"); 
		$(el).html("<option value=''>Loading Districts...</option>");

    $.ajax({
        url: "{{ route('location.getDistricts') }}",
        data: {
          province_id: provinceValue
        },
        success: function (data) {

          $(el).html('<option value="" disabled selected>-- Select --</option>');
         
          $.each(data, function (id, value){
            $(el).append('<option value="' + value.id + '">' + value.district_name + '</option>')
          });
          $(el).removeAttr('disabled');
        }
    });
  }

  function getMunicipalities(el, provinceValue, districtValue, preselect) {
    $(el).html("<option value=''>Loading City/Municipality</option>");

    $.ajax({
      url: "{{ route('location.getMunicipalities') }}",
      data: {
        province_id: provinceValue,
        district_id: districtValue
      },
      success: function(data) {

        // console.log(data);
        $(el).html("<option value='' disabled selected>-- Select --</option>");

        $.each(data, function(id, value) {
          $(el).append('<option value="' + value.id + '">' + value.municipality_name + '</option>');
        });
        $(el).removeAttr('disabled');
      }

    });
  }

  function getSubSectors(el, sectorValue, preselect) {

    $(el).html("<option value=''>Loading Sub-Sector</option>");

    $.ajax({
        url: "{{ route('projects.getSubSectors') }}",
        data: {
          sector_id: sectorValue
        },
        success: function (data) {

          $(el).html('<option value="" disabled selected>-- Select --</option>');
         
          $.each(data, function (id, value){
            $(el).append('<option value="' + value.id + '">' + value.subsector_name + '</option>')
          });
          $(el).removeAttr('disabled');
        }
    });

  }
});

$(document).ready(function() {
    $('.js-rdp-chapter-multiple').select2({
        placeholder: 'Select RDP Chapters'
    });

    $('.js-province-multiple').select2({
        placeholder: 'Select Provinces'
    });
});

$(document).ready(function(){

  // On Load Hide
  $("#locationSpecific").hide();
  $("#interProvince").hide();
  $('input[name="location"]').on("click", function(){
      
    var location = $('input[name="location"]:checked').val();
   
      if(location == "regionwide") {
        $("#locationSpecific").hide();
        $("#interProvince").hide();
      } else if(location == "interprovince"){
        $("#locationSpecific").hide();
        $("#interProvince").show();
      } else {
        $("#locationSpecific").show();
        $("#interProvince").hide();
      }
  });
});
