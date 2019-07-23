<?
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2014 Bitrix
 */

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)
	die();
?>

<?if($USER->IsAuthorized()):?>

<p><?echo GetMessage("MAIN_REGISTER_AUTH")?></p>

<?else:?>

<form class="form container" method="post" action="<?=POST_FORM_ACTION_URI?>" name="regform" enctype="multipart/form-data">
	<h2>Регистрация нового аккаунта</h2>

<?
if($arResult["BACKURL"] <> ''):
?>
	<input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?
endif;

foreach ($arResult["SHOW_FIELDS"] as $FIELD):?>
<?
	switch ($FIELD)
	{

		case "EMAIL":?>
			<div class="form__item <?= !empty($arResult["ERRORS"]["EMAIL"]) ? ' form__item--invalid' : '' ?>">
		        <label for="email1">E-mail*</label>
		        <input id="email1" type="text" name="REGISTER[<?=$FIELD?>]" placeholder="Введите e-mail" value="<?=$arResult["VALUES"][$FIELD]?>">
		        <?php if (!empty($arResult["ERRORS"]["EMAIL"])) : ?>
		        	<span class="form__error">Введите e-mail</span>
		        <?php endif ?>
		        <?php
					$stack = str_replace(' ', '', $arResult["ERRORS"][0]);
					$needle = str_replace(' ', '', "Пользователь с таким email");
						if(mb_stripos($stack, $needle) !== false) { ?>
							<span class="form__error">Этот e-mail уже зарегистирирован</span>
						<? }

					$stack = str_replace(' ', '', $arResult["ERRORS"][0]);
					$needle = str_replace(' ', '', "Неверный email");
						if(mb_stripos($stack, $needle) !== false) { ?>
							<span class="form__error">Неверный email</span>
						<? }
				?>
		    </div><?
			break;

		case "LOGIN":?>
			<input type="hidden" name="REGISTER[LOGIN]" value="tmp_name">
			<? break;

		case "NAME":?>
			<div class="form__item <?= !empty($arResult["ERRORS"]["NAME"]) ? ' form__item--invalid' : '' ?>">
		        <label for="name">Имя*</label>
		        <input id="name" type="text" name="REGISTER[<?=$FIELD?>]" placeholder="Введите имя" value="<?=$arResult["VALUES"][$FIELD]?>">
		        <?php if (!empty($arResult["ERRORS"]["NAME"])) : ?>
		        	<span class="form__error">Введите имя</span>
		        <?php endif ?>
		    </div><?
			break;

		case "PASSWORD":?>
			<div class="form__item <?= !empty($arResult["ERRORS"]["PASSWORD"]) ? ' form__item--invalid' : '' ?>">
	            <label for="password">Пароль*</label>
	            <input id="password" type="password" name="REGISTER[<?=$FIELD?>]" placeholder="Введите пароль" value="<?=$arResult["VALUES"][$FIELD]?>" autocomplete="off">
	            <?php if (!empty($arResult["ERRORS"]["PASSWORD"])) : ?>
	            	<span class="form__error">Введите пароль</span>
	            <?php endif ?>
	            <?php
					$stack = str_replace(' ', '', $arResult["ERRORS"][0]);
					$needle = str_replace(' ', '', $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"]);
						if(mb_stripos($stack, $needle) !== false) { ?>
							<span class="form__error"><?= $arResult["GROUP_POLICY"]["PASSWORD_REQUIREMENTS"] ?></span>
						<? }
				?>
	        	</div><?
			break;

		case "CONFIRM_PASSWORD":
			?><input type="hidden" name="REGISTER[<?=$FIELD?>]" value=""/><?
			break;

		case "PERSONAL_PHOTO":
			?>
			<div class="form__item form__item--file form__item--last">
		        <label>Аватар</label>
		        <div class="preview">
		          <button class="preview__remove" type="button">x</button>
		          <div class="preview__img">
		            <img src="<? $FIELD["tmp_name"] ?>" width="113" height="113" alt="Ваш аватар">
		          </div>
		        </div>
		        <div class="form__input-file">
		          <input class="visually-hidden" name="REGISTER_FILES_<?=$FIELD?>" type="file" id="photo2" value="">
		          <label for="photo2">
		            <span>+ Добавить</span>
		          </label>
		        </div>
		      </div><?

			break;
		case "WORK_NOTES":
			?>			
			<div class="form__item <?= !empty($arResult["ERRORS"]["WORK_NOTES"]) ? ' form__item--invalid' : '' ?>">
		        <label for="message">Контактные данные*</label>
		        <textarea id="message" name="REGISTER[<?=$FIELD?>]" placeholder="Напишите как с вами связаться"><?=$arResult["VALUES"][$FIELD]?></textarea>
		        <?php if (!empty($arResult["ERRORS"]["WORK_NOTES"])) : ?>
		        	<span class="form__error">Напишите как с вами связаться</span>
		        <?php endif ?>
		    </div>
			<?
			break;
		default: ?>
			<input size="30" type="text" name="REGISTER[<?=$FIELD?>]" value="<?=$arResult["VALUES"][$FIELD]?>" /><?
				if ($FIELD == "PERSONAL_BIRTHDAY")
					$APPLICATION->IncludeComponent(
						'bitrix:main.calendar',
						'',
						array(
							'SHOW_INPUT' => 'N',
							'FORM_NAME' => 'regform',
							'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
							'SHOW_TIME' => 'N'
						),
						null,
						array("HIDE_ICONS"=>"Y")
					);
				?><?
	}?>
	
<?endforeach?>

<?php if (count($arResult["ERRORS"])) : ?>
	<span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
<?php endif ?>


<input type="submit" name="register_submit_button" class="button" value="Зарегистрироваться"/>
<a class="text-link" href="/auth/">Уже есть аккаунт</a>

</form>

<?endif?>

<script>
	var previewBlock = document.querySelector('.preview');
	var removeButton = previewBlock.querySelector('.preview__remove');	
	var previewImg = previewBlock.querySelector('.preview__img img');
	var avatarInput = document.querySelector('input[name="REGISTER_FILES_PERSONAL_PHOTO"]');

	avatarInput.onchange = function(event) {
	   var file = avatarInput.files[0];
	   if (file) {
	      var fileName = file.name.toLowerCase();     
          var reader = new FileReader();
          reader.addEventListener('load', function () {

            previewImg.src = reader.result;
            previewBlock.style.display = 'block';
            previewBlock.style.position = 'relative';
          });

          reader.readAsDataURL(file);
	      
	    }
	}
	removeButton.onclick = function(evt) {
		previewImg.src = '';
        previewBlock.style.display = 'none';
        previewBlock.style.position = 'absolute';
	}


	var pass = document.querySelector('input[name="REGISTER[PASSWORD]"');
	var confirmPass = document.querySelector('input[name="REGISTER[CONFIRM_PASSWORD]"');

	confirmPass.value = pass.value;
	pass.onchange = function () {
		confirmPass.value = pass.value;
	}

	
	
</script>