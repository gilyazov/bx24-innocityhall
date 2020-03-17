BX.ready(function(){
    BX.addCustomEvent('onPopupFirstShow', function(p) {
        var menuId = p.uniquePopupId.substr(11),
            href = window.location.href,
            procId, elementId;

        if (matches = href.match(/\/processes\/([\d]+)\//i)) {
            procId = matches[1];
        }

        // заявление на отпуск
        if (procId === '31')
        {
            var menu = BX.PopupMenu.getMenuById(menuId);
            if (matches = href.match(/\/element\/0\/([\d]+)\//i)) {
                elementId = matches[1];
            }
            //добавляем пункт меню, полученному по id
            menu.addMenuItem({
                text: 'Скачать шаблон заявление',
                href: href + (href.indexOf('?') === -1 ? '?' : '&') + 'element=' + elementId + '&' + 'vacation=1&sessid=' + BX.bitrix_sessid(),
                className: 'menu-popup-item-create'
            });
        }
    });
});