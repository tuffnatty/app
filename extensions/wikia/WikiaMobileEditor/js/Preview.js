/*global define */
/**
 * handling mobile preview displayed in modal
 *
 * @author Bartłomiej Kowalczyk
 */

define( 'Preview', ['jquery', 'wikia.mustache', 'wikia.loader', 'toast'], function($, mustache, loader, toast){
    'use strict';

    var previewWindow = document.getElementById( 'wpMobilePreviewWindow' );

    //rendering Preview from string
    function render( wikitext ){
        //jQuery ajax

    }

    function show( markup ){
        previewWindow.innerHTML = markup;
    }


    //module initializer
    function init(){

        editor.init( handleSnippets );

        loader({

            type: loader.MULTI,
            resources: {
                mustache: '/extensions/wikia/WikiaMobileEditor/templates/WikiaMobileEditorController_tagList.mustache'
                    + ',/extensions/wikia/WikiaMobileEditor/templates/WikiaMobileEditorController_customTags.mustache'
                    + ',/extensions/wikia/WikiaMobileEditor/templates/WikiaMobileEditorController_customTag.mustache'
            }
        }).done(function(resp){

                wrapper.innerHTML = mustache.render(resp.mustache[0], taglist)
                    + mustache.render(resp.mustache[1], customTags);

                custMarkup = document.getElementById('custom');
                clear = document.getElementById('clearCustom');
                custTmpl = resp.mustache[2];
                custMarkup.innerHTML = mustache.render( custTmpl, customTags );
                initLinks();
            });

    }

    return {

        init: init
    };

} );

document.addEventListener('DOMContentLoaded', function(){

    require(['config'], function(config){

        config.init();
    });
});