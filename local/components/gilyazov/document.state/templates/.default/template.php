<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
/*echo '<pre>';
print_r($arResult);
echo '</pre>';*/
?>
<?if ($arResult['STATE']):?>
    <div id="states"></div>
    <script>
        (function() {
            var button = new BX.UI.Button({
                text: "<?=$arResult['STATE']['STATE_TITLE']?>",
                icon: BX.UI.Button.Icon.BUSINESS,
                color: BX.UI.Button.Color.LIGHT_BORDER,
                dropdown: <?=($arResult['STATE']['STATE_PARAMETERS'] ? 'true' : 'false')?>,
                <?if(!$arResult['STATE']['STATE_PARAMETERS']):?>
                    state: BX.UI.Button.State.DISABLED,
                <?endif;?>

                menu: {
                    items: [
                        <?foreach ($arResult['STATE']['STATE_PARAMETERS'] as $comand):?>
                            {
                                text: "<?=$comand['TITLE']?>",
                                onclick: function(event, item) {
                                    command('<?=$arResult['STATE']['TEMPLATE_ID']?>', '<?=$comand['NAME']?>');
                                    item.getMenuWindow().close();
                                }
                            },
                        <?endforeach;?>
                    ],
                    closeByEsc: true,
                },
            });
            button.renderTo(BX('states'));

            let command = (workflowId, commandId, iblockId) => {
                let input = $('[value = "'+workflowId+'"]')

                input.next().val(commandId)
                $('[name = apply]').click()
            };
        })();
    </script>
<?endif;?>