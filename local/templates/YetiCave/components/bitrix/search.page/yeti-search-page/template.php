<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
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


?>
<div class="container">
	<section class="lots">
		<h2>Результаты поиска по запросу «<span><?= $arResult["REQUEST"]["QUERY"] ?></span>»</h2>
		<ul class="lots__list">
<?if($arResult["REQUEST"]["QUERY"] === false && $arResult["REQUEST"]["TAGS"] === false):?>
<?elseif($arResult["ERROR_CODE"]!=0):?>
	<p><?=GetMessage("SEARCH_ERROR")?></p>
	<?ShowError($arResult["ERROR_TEXT"]);?>
	<p><?=GetMessage("SEARCH_CORRECT_AND_CONTINUE")?></p>
	<br /><br />
	<p><?=GetMessage("SEARCH_SINTAX")?><br /><b><?=GetMessage("SEARCH_LOGIC")?></b></p>
	<table border="0" cellpadding="5">
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_OPERATOR")?></td><td valign="top"><?=GetMessage("SEARCH_SYNONIM")?></td>
			<td><?=GetMessage("SEARCH_DESCRIPTION")?></td>
		</tr>
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_AND")?></td><td valign="top">and, &amp;, +</td>
			<td><?=GetMessage("SEARCH_AND_ALT")?></td>
		</tr>
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_OR")?></td><td valign="top">or, |</td>
			<td><?=GetMessage("SEARCH_OR_ALT")?></td>
		</tr>
		<tr>
			<td align="center" valign="top"><?=GetMessage("SEARCH_NOT")?></td><td valign="top">not, ~</td>
			<td><?=GetMessage("SEARCH_NOT_ALT")?></td>
		</tr>
		<tr>
			<td align="center" valign="top">( )</td>
			<td valign="top">&nbsp;</td>
			<td><?=GetMessage("SEARCH_BRACKETS_ALT")?></td>
		</tr>
	</table>
<?elseif(count($arResult["SEARCH"])>0):?>
	<?foreach($arResult["SEARCH"] as $arItem):?>	
	<? $res = CIBlockElement::GetList( array(), array("ID" => $arItem['ITEM_ID']), false, false, array('ID', 'NAME', 'IBLOCK_ID', 'IBLOCK_SECTION_ID', 'LANG_DIR', 'PREVIEW_PICTURE', 'DETAIL_PAGE_URL', 'PROPERTY_PRICE_START', 'PROPERTY_PRICE_GAP', 'PROPERTY_PRICE_CURRENT', 'PROPERTY_FINISH_DATE', 'PROPERTY_HISTORY_PLAYER_ID', 'PROPERTY_HISTORY_DATE', 'PROPERTY_HISTORY_PRICE'));
	while($ob = $res->GetNextElement())
	{
	 $arFields = $ob->GetFields();
	 $img_path = CFile::GetPath($arFields["PREVIEW_PICTURE"]);
	} ?>
	

	<li class="lots__item lot">
		<div class="lot__image">
			<img src="<?= $img_path ?>" width="350" height="260" alt="Сноуборд">
		</div>
		<div class="lot__info">
			<span class="lot__category">
				<? 
				$section_res = CIBlockSection::GetByID($arFields["IBLOCK_SECTION_ID"]);
				if($ar_res = $section_res->GetNext()) {
		  			echo $ar_res['CODE'];
				} ?>				
			</span>
			<h3 class="lot__title"><a class="text-link" href="<?= SITE_TEMPLATE_PATH . "/" . $ar_res['CODE'] . "/?ELEMENT_ID=" . $arFields['ID'] ?>"><?=$arFields['NAME']?></a></h3>
			<div class="lot__state">
				<div class="lot__rate">
					<?php
						$lot_price = $arFields['PROPERTY_PRICE_CURRENT_VALUE'];
						$bets_arr = $arFields['PROPERTY_HISTORY_PRICE_VALUE'];
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
					$lot_time = strtotime($arFields["PROPERTY_FINISH_DATE_VALUE"]);

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
	<? if($arParams["DISPLAY_BOTTOM_PAGER"] != "N") echo $arResult["NAV_STRING"]; ?>
<?else:?>
	<?ShowNote(GetMessage("SEARCH_NOTHING_TO_FOUND"));?>
<?endif;?>

</section>
</div>