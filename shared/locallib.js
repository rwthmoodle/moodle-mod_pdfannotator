/**
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen, Rabea de Groot and Ahmad Obeid (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
require(['jquery'], function($) {
   $(document).ready(function () 
   {    
        /**
         * Function for a fixed Toolbar, if the 
         * @returns {undefined}
         */
        (function (){
            var top = $('#pdftoolbar').offset().top - parseFloat($('#pdftoolbar').css('marginTop').replace(/auto/, 0));
           
            var fixedTop = 0;
            if($('.fixed-top').length > 0){
                fixedTop = $('.fixed-top').outerHeight();
            }else if($('.navbar-static-top').length > 0){
                fixedTop = $('.navbar-static-top').outerHeight();
            }  
            
            var oldTop = $('#pdftoolbar').css('top');
            
            $(window).scroll(function (event) {
                var y = $(this).scrollTop();
                
                if (y >= top + 1 - fixedTop) {
                    
                    $('#pdftoolbar').addClass('fixtool');
                    document.getElementById("pdftoolbar").style.top = fixedTop+"px";
                } else {
                    
                    $('#pdftoolbar').removeClass('fixtool');    
                    document.getElementById("pdftoolbar").style.top = oldTop;
                }
            });
        })();
    });
});

function closeComment(){
    document.querySelector('.comment-list-form').setAttribute('style','display:none');
    document.getElementById('commentSubmit').value = M.util.get_string('answerButton','pdfannotator');
    document.getElementById('myarea').value = "";
    document.querySelector('.comment-list-container').innerHTML = '';
}
var oldHeight = -1;
function makeFullScreen(){
    document.querySelector('body').classList.toggle('fullscreenWrapper');
    //if it is now in fullscreen, the image should be the collapse fullscreen image
    //else it should be the fullscreen image
    if(document.querySelector('body').classList.contains('fullscreenWrapper')){
        oldHeight = document.querySelector('#body-wrapper').style.height;
        let img = document.querySelector('img[title="'+M.util.get_string('fullscreen','pdfannotator')+'"]');
        img.title = M.util.get_string('fullscreenBack','pdfannotator');
        img.alt = M.util.get_string('fullscreenBack','pdfannotator');
        img.parentNode.title = M.util.get_string('fullscreenBack','pdfannotator');
        img.src = M.util.image_url('fullscreen_collapse','pdfannotator');
        var height = document.querySelector('html').getBoundingClientRect().height;
        document.querySelector('#body-wrapper').style.height = (height - 142)+'px';
    }else{
        let img = document.querySelector('img[title="'+M.util.get_string('fullscreenBack','pdfannotator')+'"]');
        img.title = M.util.get_string('fullscreen','pdfannotator');
        img.alt = M.util.get_string('fullscreen','pdfannotator');
        img.parentNode.title = M.util.get_string('fullscreen','pdfannotator');
        img.src = M.util.image_url('fullscreen','pdfannotator');
        document.querySelector('#body-wrapper').style.height = oldHeight;
    }
};
