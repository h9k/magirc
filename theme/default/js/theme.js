var menu;
$(document).ready(function() {
    $("#loading").ajaxStart(function(){
        $(this).show();
    }).ajaxStop(function(){
        $(this).hide();
    });
    $("#locale").change(function() {
        Cookies.set("magirc_locale", $("#locale").val(), { expires: 30, path: '/' });
        window.location.reload();
    });
    menu = $('#chanmenu').menu({
        select: function(event, ui) {
            $(this).hide();
            var chan = encodeURIComponent(menu.data('channel'));
            switch (ui.item.data('action')) {
                case 'irc':
                    location.href = 'irc://'+net_roundrobin+':'+net_port+'/'+chan.replace('%23', '');
                    break;
                case 'ircs':
                    location.href = 'ircs://'+net_roundrobin+':'+net_port_ssl+'/'+chan.replace('%23', '');
                    break;
                case 'webchat':
                    location.href = service_webchat + chan;
                    break;
                case 'webchat2':
                    location.href = service_webchat + menu.data('channel');
                    break;
                case 'mibbit':
                    location.href = 'http://widget.mibbit.com/?settings='+service_mibbit+'&server='+net_roundrobin+'&channel='+chan+'&promptPass=true';
                    break;
            }
        }
    }).hide().css({position: 'absolute', zIndex: 1});
    $(document).on('mouseenter', '.chanbutton', function() {
        $(this).removeClass('ui-state-default').addClass('ui-state-focus');
    });
    $(document).on('mouseleave', '.chanbutton', function() {
        $(this).removeClass('ui-state-focus').addClass('ui-state-default');
    });
});

function openChanMenu(element) {
    menu.data('channel', $(element).parent().parent().attr('id'));
    if (menu.is(':visible') ){
        menu.hide();
        return false;
    }
    menu.show();
    menu.position({
        my: "right top",
        at: "right bottom",
        of: element
    });
    $(document).one("click", function() {
        menu.hide();
    });
    return false;
}

function getUserStatus(user) {
    if (user['away']) return '<img src="theme/'+theme+'/img/status/user-away.png" alt="away" title="'+mLang.AwayAs+' '+user['nickname']+'" \/>';
    else if (user['online']) return '<img src="theme/'+theme+'/img/status/user-online.png" alt="online" title="'+mLang.OnlineAs+' '+user['nickname']+'" \/>';
    else return '<img src="theme/'+theme+'/img/status/user-offline.png" alt="offline" title="'+mLang.Offline+'" \/>';
}
function getUserExtra(user) {
    var out = '';
    if (user['bot']) out += ' <img src="theme/'+theme+'/img/status/bot.png" alt="bot" title="'+mLang.Bot+'" \/>';
    if (user['service']) out += ' <img src="theme/'+theme+'/img/status/service.png" alt="service" title="'+mLang.Service+'" \/>';
    if (user['operator']) out += ' <img src="theme/'+theme+'/img/status/operator.png" alt="oper" title="'+user['operator_level']+'" \/>';
    if (user['helper']) out += ' <img src="theme/'+theme+'/img/status/help.png" alt="help" title="'+mLang.Helper+'" \/>';
    return out;
}
function getChannelLinks() {
    if (net_roundrobin || service_webchat) {
        return '<button type="button" title="'+mLang.Join+'..." class="chanbutton ui-button ui-corner-all ui-widget ui-button-icon-only" style="height:18px; width:30px; margin:0; vertical-align:middle;"><span class="ui-button-icon ui-icon ui-icon-triangle-1-s"></span><span class="ui-button-icon-space"> </span></button>';
    } else {
        return '';
    }
}
