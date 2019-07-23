<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

// заменяем $arResult эпилога значением, сохраненным в шаблоне
if (isset($arResult['arResult'])) {
    $arResult =& $arResult['arResult'];
    // подключаем языковой файл
    global $MESS;
    include_once(GetLangFileName(dirname(__FILE__).'/lang/', '/template.php'));
} else {
    return;
}

CModule::IncludeModule('iblock');
$this->setFrameMode(true);

global $USER;
$user_id = $USER->GetID();
$user_name = $USER->GetFullName();
$creator = $arResult['FIELDS']['CREATED_BY'];

use Bitrix\Main\Application;

$request = Application::getInstance()->getContext()->getRequest();
$new_price = $request->getPost("cost");
$sent_user = $request->getPost("sent_user");

$start_priceArr = $arResult["PROPERTIES"]["PRICE_START"];
$current_priceArr = $arResult["PROPERTIES"]["PRICE_CURRENT"];
$price_gapArr = $arResult["PROPERTIES"]["PRICE_GAP"];

$historyPlyers = $arResult["PROPERTIES"]["HISTORY_PLAYER_ID"];
$last_player = $historyPlyers["VALUE"] ? $historyPlyers["VALUE"][0] : '';
$historyBets = $arResult["PROPERTIES"]["HISTORY_PRICE"];
$historyDates = $arResult["PROPERTIES"]["HISTORY_DATE"];

if (empty($current_priceArr['VALUE'])) {
    $current_priceArr["VALUE"] = $start_priceArr["VALUE"];
}

$current_page = $APPLICATION->GetCurUri();
?>

<section class="lot-item container">
	<?if ($arParams["DISPLAY_NAME"]!="N" && $arResult["NAME"]):?>
		<h2><?=$arResult["NAME"]?></h2>
	<?endif;?>
  	<div class="lot-item__content">
  		<div class="lot-item__left">
			<div class="lot-item__image">
		        <img class="detail_picture"
				border="0"
				src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
				width="<?=$arResult["DETAIL_PICTURE"]["WIDTH"]?>"
				height="<?=$arResult["DETAIL_PICTURE"]["HEIGHT"]?>"
				alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
				title="<?=$arResult["DETAIL_PICTURE"]["TITLE"]?>">
		    </div>
			<p class="lot-item__category">Категория: <span><?= $arResult["SECTION"]["PATH"][0]["NAME"]?></span></p>
			<p class="lot-item__description"><?echo $arResult["DETAIL_TEXT"];?></p>
		</div>
		<div class="lot-item__right">
			<div class="lot-item__state">
				<?php
					$today_unix = strtotime('today');
					$lot_time = strtotime($arResult["PROPERTIES"]["FINISH_DATE"]["VALUE"]);

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

		        <div class="lot-item__cost-state">
		          <div class="lot-item__rate">
		            <span class="lot-item__amount">Текущая цена</span>
		            <span class="lot-item__cost">
		            	<?= empty($new_price) ? number_format($current_priceArr["VALUE"], 0, '.', ' ') : number_format($new_price, 0, '.', ' ') ?>
	            	</span>
		          </div>
		          <div class="lot-item__min-cost">Мин. ставка
		          	<span>
		          		<?php	$min_bet = $current_priceArr["VALUE"] + $price_gapArr["VALUE"];
                            echo number_format($min_bet, 0, '.', ' ');
                         ?> р
		            </span>
		          </div>
		        </div>
		        <?php
                if ($USER->IsAuthorized() && $user_id !== $creator && $user_id !== $last_player && !$is_finished) : ?>
			        <form class="lot-item__form" action="<?= $current_page ?>" method="post">
			          <p class="lot-item__form-item">
			            <label for="cost">Ваша ставка</label>
			            <input id="cost" min="<?= (int)$min_bet; ?>" type="number" name="cost" placeholder="<?= $min_bet; ?>" required>
			            <input type="hidden" id="sent_user" name="sent_user" value="<?= $user_id ?>">
			          </p>
			          <button type="submit" class="button">Сделать ставку</button>
			        </form>
			     <?php endif ?>
				
		        <?php
                if ($new_price && $sent_user) {
                    $el = new CIBlockElement;
                    $element_id = $arResult["ID"];
                    $bet_prop_id = (int)$current_priceArr["ID"]; // ID Свойства "Начальная ставка"
                    $players_prop_id = (int)$historyPlyers["ID"]; // Игроки
                    $bets_prop_id = (int)$historyBets["ID"]; // Ставки
                    $dates_prop_id = (int)$historyDates["ID"]; // Даты

                    $players_list = $historyPlyers["VALUE"] ? $historyPlyers["VALUE"] : array();
                    array_unshift($players_list, $user_id);
                    $bets_list = $historyBets["VALUE"] ? $historyBets["VALUE"] : array();
                    array_unshift($bets_list, $new_price);
                    $dates_list = $historyDates["VALUE"] ? $historyDates["VALUE"] : array();
                    array_unshift($dates_list, time());

                    // Обновление элемента инфоблока
                    if ($user_id && $new_price) {
                        CIBlockElement::SetPropertyValuesEx(
                            $element_id,
                            false,
                            array(
                                $bet_prop_id => $_POST["cost"],
                                $players_prop_id => $players_list,
                                $bets_prop_id => $bets_list,
                                $dates_prop_id => $dates_list
                            )
                        );
                    }

                    // Обновляем страницу после обновления элемента
                    header("Location: $current_page");
                }
                ?>

			</div>
			<?php if ($historyPlyers["VALUE"]) : ?>
			<div class="history">
		        <h3>История ставок (<span>10</span>)</h3>
		        <table class="history__list">
		        	<?php foreach ($historyPlyers["VALUE"] as $key => $value) : ?>
		        		<?
						$rsUser = CUser::GetByID($value);
						$arUser = $rsUser->Fetch();
						$name = $arUser['NAME'];
						?>
						<tr class="history__item">
							<td class="history__name"><?= $name ?></td>
							<td class="history__price"><?= number_format($historyBets["VALUE"][$key], 0, '.', ' ') ?></td>
							<td class="history__time">
							<?php
                                $date_item = $historyDates["VALUE"][$key];
                                $date_formated = date("m.d.y в H:i", $date_item);
                                $diff_time = time() - $date_item;
                                if ($diff_time < 60) {
                                    $render_date = 'Только что';
                                } else {
                                    $render_date = $diff_time > 3600 ? $date_formated : FormatDate(iago, $date_item);
                                }
                                echo $render_date;
                            ?>
							</td>
						</tr>
					<?php endforeach; ?>
		        </table>
		    </div>
		<?php endif; ?>
		</div>
</div>
</section>