(()=>{let e={init:function(){this.loadPluginOptions(),this.eventListeners(),this.updateSubmenuLinks()},loadPluginOptions:function(){var e=wpgen_forms?.available_plugins||[],o=wpgen_forms?.available_service_providers||[];let n="";n+='<option value="">Select a Plugin</option>',e.forEach(e=>{n+=`<option value="${e.value}">${e.label}</option>`});let r="";r+='<option value="">Select a Service Provider</option>',console.log("availableServiceProviders : ",o),o.forEach(e=>{let o=e.is_active?"":"disabled";console.log("isDisabled : ",o),r+='<option value="'+e.value+'" '+o+">"+e.label+"</option>"}),jQuery("#wpgen-forms__form-plugin").html(n),jQuery("#wpgen-forms__form-service-provider").html(r)},eventListeners:function(){jQuery("#wpgen-forms__generative-action").on("click",function(){console.log("\uD83E\uDD16 WordPress Generative Forms: Button Clicked!");let e=jQuery("#wpgen-forms__form-name").val(),o=jQuery("#wpgen-forms__form-plugin").val(),n=jQuery("#wpgen-forms__form-service-provider").val();if(console.log("formName : ",e),console.log("pluginType : ",o),!e||!o||!n){alert("Please fill out the form name and select a plugin!");return}console.log("wpgen_forms.nonce : ",wpgen_forms.nonce),jQuery.ajax({url:wpgen_forms.ajax_url,type:"POST",data:{action:"wpgen_forms_generate_form",nonce:wpgen_forms.wpgen_forms_generate_form_nonce,form_name:e,plugin_type:o,service_provider:n},success:function(e){console.log("\uD83E\uDD16 WPGenerative Forms: Form Generated!"),console.log(e),"success"==e.status?(alert(e.message),window.location.href=e.redirect_url):alert("Form Generation Failed!")},error:function(e){console.log("\uD83E\uDD16 WPGenerative Forms: Form Generation Failed!"),console.log(e),alert("Form Generation Failed!. Please check the console for more details. And please report the issue to the plugin author.")}})})},updateSubmenuLinks:function(){jQuery("#toplevel_page_wpgen-forms").find("ul li").each(function(){var e=jQuery(this),o=e.find("a").attr("href");console.log("link : ",o),"wpgen-forms-support-forum"===o&&e.find("a").attr("href",wpgen_forms.support_forum_url).attr("target","_blank")})}};jQuery(function(){console.log("\uD83E\uDD16 WordPress Generative Forms: Main JS Ready! "),e.init()})})();
//# sourceMappingURL=main.js.map