//code for uk phone

! function(factory) {

    "function" == typeof define && define.amd ? define(["../inputmask"], factory) : "object" == typeof exports ? module.exports = factory(require("../inputmask")) : factory(window.Inputmask);

}(function(Inputmask) {

    return Inputmask.extendAliases({

        telephoneuk: {

            alias: "abstractphone",

            countrycode: "44",

            phoneCodes: 

            [

                {

                    mask: "+44(##)##-##-###",

                    cc: "UK",

                    cd: "United Kingdom",

                    city: "Liverpool"

                }

            ]

        }

    }), Inputmask;

});





! function(factory) {

    "function" == typeof define && define.amd ? define(["../inputmask"], factory) : "object" == typeof exports ? module.exports = factory(require("../inputmask")) : factory(window.Inputmask);

}(function(Inputmask) {

    return Inputmask.extendAliases({

        mobileuk: {

            alias: "abstractphone",

            countrycode: "44",

            phoneCodes: 

            [

                {

                    mask: "+44##########",

                    cc: "UK",

                    cd: "United Kingdom",

                    city: "Liverpool"

                }

            ]

        }

    }), Inputmask;

});

// Abdul wahid's code start here
function template_hide($) {
    $("#summernoteHeader").removeAttr('required');
    $("#summernoteFooter").removeAttr('required');
    $("#whatsapp_message").hide();
    $("#email_message").hide();
  }
  template_hide($);

$( "#template_id" ) .change(function () {  
      
    var selectedOption = $(this).children("option:selected").val();
    
        if(selectedOption==1){
          $("#summernoteHeader").removeAttr('required');
          $("#summernoteFooter").removeAttr('required');
          $("#message_template").show();
            $("#whatsapp_message").hide();
            $("#email_message").hide();
            
         
        } 
        else if(selectedOption==2){
                  
                   $("#title_id").removeAttr('required');
                   $("#message_id").removeAttr('required');
                   $("#summernoteFooter").removeAttr('required');
                  $("#whatsapp_message").show();
                  $("#email_message").hide();
                  $("#message_template").hide();
         
        } 
        else if(selectedOption==3){
          $("#message_template").hide();
                  $("#whatsapp_message").hide();
                  $("#summernoteHeader").removeAttr('required');
                  $("#email_message").show();
        }
        else{
          alert("select a template");
        }
});  