$(function() {
    
    var $sidebar = $('#sidebar'), $splitter = $('#splitter');
    
    $splitter.toggle(
        function() { 
            $sidebar.animate({ width: 'hide' }, 10);
            $splitter.html('&raquo;');
        },
        function() {
            $sidebar.animate({ width: 'show' }, 10);
            $splitter.html('&laquo;');
        }
    );
        
    $splitter.height( $('#main').height() );
    
});


function setHeight(){
var theheight=document.getElementById('main').offsetHeight;
document.getElementById('top1').style.height=theheight-50+'px';
document.getElementById('top2').style.height=theheight-50+'px';
}
window.onload=setHeight;
window.onresize=setHeight;


function hideLoadLayer()
{
layer = document.getElementById("loadingLayer")
layer.style.visibility = "hidden";
}