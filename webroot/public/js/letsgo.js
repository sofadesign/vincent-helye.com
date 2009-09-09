$(document).ready(function($) {
   /* open external links in a new window */
   $('a[rel="external"], a[href^=http]').each(function(){
     if(this.href.indexOf(location.hostname) == -1) { 
        $(this).attr('target', '_blank');
     }
   });
   $('#nav > ul').helyenav();
});