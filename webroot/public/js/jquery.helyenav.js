(function($) {
    // PLUGIN DEFINITION
    $.fn.helyenav = function(options) {
        // init plugin and build options before elemnts iteration
        var opts = $.extend({}, $.fn.helyenav.defaults, options);
        $.helyenav.opts = opts;
        $.helyenav.current = opts.current;      
        
        return this.each(function() {
            // execute plugin on each elt
            $.helyenav.init(this, opts);
            $(this).click(onClick);
        });
        
        // private
        function onClick() {
            return $.cookie(opts.cookieName, $.helyenav.current(), { path: opts.cookiePath});
        }
    };

    $.helyenav = {
        init: function(container, opts) {
            var current = $.helyenav.current();
            this.container = container;
            var subnavs = $(container).find(opts.childBlockElt);
            var subnavsNames = $.map(subnavs, function(el) {
                return $(el).parent().attr('class');
            });
            $(subnavs).hide();
            if($.cookie(opts.cookieName) == current){
                subnav(current).show();
            } else {
              $.each(subnavsNames, function(i, name){
                 if($.cookie(opts.cookieName)==name){
                       subnav(name).show();
                       subnav(name).css('display', 'block'); /* for FF2, in case it's inline */
                       subnav(name).slideUp(opts.speed);
                       
                    }
                    if(current==name){
                       subnav(name).slideDown(opts.speed);
                       
                       $.cookie(opts.cookieName, current, { path: opts.cookiePath});
                    }
              });
           }
       
           // private
           function subnav(name){
               return $(this.container).find("." + name + ' > ' + $.helyenav.opts.childBlockElt);
           }
        }
    };
    
    // PLUGIN DEFAULTS
    $.fn.helyenav.defaults = {
        current: function(){ return $('body').attr('class'); },
        speed: 'slow',
        cookieName: 'last',
        cookiePath: '/',
        childBlockElt: 'ul'
    };
    
})(jQuery);