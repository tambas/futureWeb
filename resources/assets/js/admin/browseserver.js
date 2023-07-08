var urlobj;
function BrowseServer(obj, url)
{
    urlobj = obj;
    OpenServerBrowser(
        url,
        screen.width * 0.7,
        screen.height * 0.7 ) ;
}
function OpenServerBrowser( url, width, height )
{
    var iLeft = (screen.width - width) / 2 ;
    var iTop = (screen.height - height) / 2 ;
    var sOptions = "type=image,toolbar=no,status=no,resizable=yes,dependent=yes" ;
    sOptions += ",width=" + width ;
    sOptions += ",height=" + height ;
    sOptions += ",left=" + iLeft ;
    sOptions += ",top=" + iTop ;
    var oWindow = window.open( url, "BrowseWindow", sOptions ) ;
}

function SetUrl( url, width, height, alt )
{
    document.getElementById(urlobj).value = url ;
    $('#main_image').removeClass('hidden').attr("src", url);
    $('.image-message').hide();
    oWindow = null;
}

var url;
function BrowseServerUrl(url)
{
    OpenServerBrowserUrl(
        url,
        screen.width * 0.7,
        screen.height * 0.7 ) ;
}
function OpenServerBrowserUrl( url, width, height )
{
    var iLeft = (screen.width - width) / 2 ;
    var iTop = (screen.height - height) / 2 ;
    var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes" ;
    sOptions += ",width=" + width ;
    sOptions += ",height=" + height ;
    sOptions += ",left=" + iLeft ;
    sOptions += ",top=" + iTop ;
    var oWindow = window.open( url, "BrowseWindow", sOptions ) ;
}