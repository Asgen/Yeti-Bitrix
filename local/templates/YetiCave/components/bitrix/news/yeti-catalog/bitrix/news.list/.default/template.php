<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>

<? if ($APPLICATION->GetCurPage(false) === '/'): ?>
	<h2>Открытые лоты</h2>
<? else : ?>
	<h2>Все лоты в категории «<?= $arResult['SECTION']['PATH'][0]['NAME'] ?>»</h2>
<? endif; ?>
<ul class="lots__list">		
	<?foreach($arResult["ITEMS"] as $item):?>
		<?//Включает возможность изменять элемент из публички
		$this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('Подтверждаете удаление?')));
		?>    
		<li class="lots__item lot">
			<div class="lot__image">
				<img src="<?=$item['PREVIEW_PICTURE']['SRC']?>" width="350" height="260" alt="Сноуборд">
			</div>
			<div class="lot__info">
				<span class="lot__category">
					<? 
					$res = CIBlockSection::GetByID($item["IBLOCK_SECTION_ID"]);
					if($ar_res = $res->GetNext()) {
			  			echo $ar_res['NAME'];
					} ?>				
				</span>
				<h3 class="lot__title"><a class="text-link" href="<?=$item['DETAIL_PAGE_URL']?>"><?=$item['NAME']?></a></h3>
				<div class="lot__state">
					<div class="lot__rate">
						<?php
							$lot_price = $item['PROPERTIES']['PRICE_CURRENT']['VALUE'];
							$bets_arr = $item['PROPERTIES']['HISTORY_PRICE']['VALUE'];
							$pitch = count($bets_arr) > 4 ? ' ставок' : ' ставки';
							if (count($bets_arr) === 1) {
								$pitch = ' ставка';
							}

						?>
						<span class="lot__amount"><?= $bets_arr ? count($bets_arr) . $pitch : 'Стартовая цена' ?></span>
						<span class="lot__cost"><?= number_format($lot_price, 0, '.', ' ')?><b class="rub">р</b></span>
					</div>
					<?php
						$today_unix = strtotime('today');
						$lot_time = strtotime($item["PROPERTIES"]["FINISH_DATE"]["VALUE"]);

						$is_finishing = $lot_time - $today_unix <= 86400 && $lot_time - $today_unix > 0 ? 1 : 0;
						$is_finished = $lot_time - $today_unix <= 0 ? 1 : 0;
					?>
					<div class="lot-item__timer timer <?= $is_finishing ? 'timer--finishing' : '' ?><?= $is_finished ? 'timer--end' : '' ?>">
			        <?php
                    if ($is_finished) {
                    	echo "Торги закончены";
                    } else {
                    	$left_seconds = ($lot_time - time());                 
	                    $left_hours = ($left_seconds / 3600) % 24;
	                    $left_days = floor($left_seconds / 86400);
	                    $left_minutes = ($left_seconds / 60) % 60;
			          	echo "$left_days:$left_hours:$left_minutes"; 
			        } ?>				
		        </div>
				</div>
			</div>
		</li>


		
	<?endforeach;?>
</ul>

<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?>
		<br /><?=$arResult["NAV_STRING"]?>
<?endif;?> 
 