BX.ready(function(){
    let input = BX.findChild(BX('workarea-content'), {
        tag: 'input',
        property: {
            name: 'apply'
        }},true)

    BX.adjust(input, {props: { value: 'Сохранить'}});
});