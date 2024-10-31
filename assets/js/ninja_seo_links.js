function count($this) {
   jQuery.ajax({
	   type: "POST",
	    data:{
	        action:'ninja_seo_links_plus',
	        href:$this.href,
	        text:$this.text
	    },
	    url: ajax_script.ajaxurl
	});         
};     