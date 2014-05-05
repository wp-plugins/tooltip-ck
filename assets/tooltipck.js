/**
 * copyright	Copyright (C) 2010 Cedric KEIFLIN alias ced1870 & Ghazal
 * http://www.joomlack.fr
 * license		GNU/GPL
 * Tooltip GC
**/

(function($){
    $.fn.Tooltipck = function(options){
        var defaults = {
            fxtransition : 'linear',
            fxduration : 500,
            dureeIn : 0,
            dureeBulle : 500,
            // largeur : '150',
            opacite : 0.8
            // offsetx: '0',
            // offsety: '0'
            // dureebulle : 500
            };
        var options = $.extend(defaults, options);
        var tooltip = this;
        //        var showing;
           
        $('.infotip').each(function(i, tooltip){
            tooltip = $(tooltip);
            tooltip.tip = $('> .tooltipck_tooltip', tooltip);
            getTooltipParams(tooltip);
            tooltip.mouseover(function() {	
                showTip(tooltip);
            });      
            tooltip.mouseleave(function() {                 
                hideTip(tooltip);
            });
            
            function showTip(el) {
                clearTimeout (el.timeout);
                el.timeout = setTimeout (function() {
                    openTip(el);
                }, options.dureeIn);
            }
            
            function hideTip(el) {
                $(el).data('status','hide')
                clearTimeout (el.timeout);
                el.timeout = setTimeout (function() {
                    closeTip(el);
                }, tooltip.dureeBulle);
            }
            
            function openTip(el) {
                tip = $(el.tip); 
                if(el.data('status') == 'opened') return;
                tip.stop(true, true);
                tip.show(parseInt(tooltip.fxduration), options.fxtransition, {
                    complete: function() {
                        el.data('status','opened');
                    }
                });
            }
            
            function closeTip(el) {
                tip = $(el.tip);
                tip.stop(true, true);
                tip.hide(0, options.fxtransition, {
                    complete: function() {
                        el.data('status','closed');
                    }   
                });    
            } 
            
            function getTooltipParams(tooltip) {
                if (tooltip.attr('rel').length) {
                    var params = tooltip.attr('rel').split('|');
                    for (var i=0;i<params.length;i++) {
                        param = params[i];
//                    params.each( function(param) {
                        // if (param.indexOf('w=') != -1) largeur = param.replace("w=", "");
                        if (param.indexOf('mood=') != -1) tooltip.fxduration = param.replace("mood=", "");
                        if (param.indexOf('tipd=') != -1) tooltip.dureeBulle = param.replace("tipd=", "");
                        if (param.indexOf('offsetx=') != -1) tooltip.offsetx = parseInt(param.replace("offsetx=", ""));
                        if (param.indexOf('offsety=') != -1) tooltip.offsety = parseInt(param.replace("offsety=", ""));
//                    });
                    }
                
                    $(tooltip.tip).css({
                        'opacity' : options.opacite,
                        'marginTop' : tooltip.offsety,
                        'marginLeft' : tooltip.offsetx
                    });	
                }
            }
        });
    }
})(jQuery);