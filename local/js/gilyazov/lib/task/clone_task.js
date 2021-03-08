BX.cloneTask = function () {
    this.taskId = '';
    this.fields = '';
    this.href = window.location.href;

    let _this = this;

    this.run = async function () {
        let response = await _this.getTaskInfo()
        if (response.status === 'success') {
            _this.fields = response.data;

            _this.popupMenuAction()
        }
    };
    this.getTaskId = function () {
        let matches;
        if (matches = _this.href.match(/\/task\/view\/([\d]+)\//i)) {
            _this.taskId = matches[1];
        }
    }
    this.getTaskInfo = async function () {
        _this.getTaskId()
        return await BX.ajax.runAction('gilyazov:core.api.task.info',
            {
                data: {
                    post: {
                        taskId: _this.taskId
                    }
                }
            });
    }
    this.popupMenuAction = function () {
        BX.addCustomEvent('onPopupFirstShow', BX.delegate(function (p) {
            const menuId = 'task-view-b';

            if (p.uniquePopupId === 'menu-popup-' + menuId) {
                const menu = BX.PopupMenu.getMenuById(menuId);
                const url = _this.buildUrl();

                menu.addMenuItem({
                    text: 'Клонировать',
                    href: url,
                    className: 'menu-popup-item-copy'
                });
            }
        }));
    }
    this.buildUrl = function (){
        const baseUrl = new URL(window.location.origin + '/company/personal/user/'+_this.fields.CREATED_BY+'/tasks/task/edit/0/');

        baseUrl.searchParams.append("PARENT_ID", _this.fields.ID);
        baseUrl.searchParams.append("TITLE", _this.fields.TITLE);
        baseUrl.searchParams.append("DESCRIPTION", _this.fields.DESCRIPTION);
        baseUrl.searchParams.append("RESPONSIBLE_ID", _this.fields.RESPONSIBLE_ID);
        baseUrl.searchParams.append("CREATED_BY", _this.fields.CREATED_BY);

        return baseUrl.href
    }
}

BX.ready(function(){
    BX.CloneTask = new BX.cloneTask();
    BX.CloneTask.run();
});