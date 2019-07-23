<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

CJSCore::Init();
?>

<?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $form = $_POST;
  $required = ['USER_LOGIN', 'USER_PASSWORD'];
  $errors = [];

  foreach ($form as $key => $value) {
    $form[$key] = trim($value);
  }

  // Проверяем заполненность полей
  foreach ($required as $key) {
    if (empty($form[$key])) {
      $errors[$key] = 'Это поле надо заполнить';
    }
  }
}
?>

<? if($arResult["FORM_TYPE"] == "login"):?>

<form class="form container" name="system_auth_form<?=$arResult["RND"]?>" method="post" target="_top" action="<?=$arResult["AUTH_URL"]?>">
  <h2>Вход</h2>
<?if($arResult["BACKURL"] <> ''):?>
  <input type="hidden" name="backurl" value="<?=$arResult["BACKURL"]?>" />
<?endif?>
<?foreach ($arResult["POST"] as $key => $value):?>
  <input type="hidden" name="<?=$key?>" value="<?=$value?>" />
<?endforeach?>
  <input type="hidden" name="AUTH_FORM" value="Y" />
  <input type="hidden" name="TYPE" value="AUTH" />

  <div class="form__item <?= !empty($errors['USER_LOGIN']) ? ' form__item--invalid' : '' ?>">
    <label for="email"><?=GetMessage("AUTH_LOGIN")?></label>
    <input id="email" type="text" name="USER_LOGIN" placeholder="Введите e-mail" value="<?=htmlspecialcharsbx($_POST['USER_LOGIN'])?>">
    <? if(!empty($errors['USER_LOGIN'])) : ?>
      <span class="form__error">Введите e-mail</span>
    <? endif ?>
  </div>

  <div class="form__item form__item--last <?= !empty($errors['USER_PASSWORD']) ? ' form__item--invalid' : '' ?>">
    <label for="password"><?=GetMessage("AUTH_PASSWORD")?></label>
    <input id="password" type="password" name="USER_PASSWORD" placeholder="Введите пароль" autocomplete="off" value="<?=htmlspecialcharsbx($_POST['USER_PASSWORD'])?>">
    <? if(!empty($errors['USER_PASSWORD'])) : ?>
      <span class="form__error">Введите пароль</span>
    <? endif ?>
  </div>

  <?
  if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'] && empty($errors))
    ShowMessage($arResult['ERROR_MESSAGE']);
  ?>

  <input type="submit" class="button" name="Login" value="<?=GetMessage("AUTH_LOGIN_BUTTON")?>">

</form>
<? else: ?>

<? LocalRedirect("/"); ?>
<? endif; ?>