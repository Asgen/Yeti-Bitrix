<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <?$APPLICATION->ShowHead()?>
  <title><?$APPLICATION->ShowTitle()?></title>
</head>
<body>
<div id="panel"><?$APPLICATION->ShowPanel();?></div>
<div class="page-wrapper">

  <header class="main-header">
    <div class="main-header__container container">
      <h1 class="visually-hidden">YetiCave</h1>
      <a class="main-header__logo" href="<?= SITE_DIR ?>">
        <img src="<?= SITE_TEMPLATE_PATH ?>/img/logo.svg" width="160" height="39" alt="Логотип компании YetiCave">
      </a>
      <?$APPLICATION->IncludeComponent("bitrix:search.form", "yeti-search-form", Array(
      	"USE_SUGGEST" => "N",	// Показывать подсказку с поисковыми фразами
      		"PAGE" => "#SITE_DIR#search/index.php",	// Страница выдачи результатов поиска (доступен макрос #SITE_DIR#)
      	),
      	false
      );?> 
      <a class="main-header__add-lot button" href="<?= SITE_TEMPLATE_PATH ?>/add_lot.php">Добавить лот</a>

      <?
      global $USER;
      if ($USER->IsAuthorized()) :?>
        <nav class="user-menu">
          <div class="user-menu__image">

            <? $by = "ID";
              $order = "ASC";
              $rsUser = CUser::GetList(($by="ID"), ($order="desc"), array("ID"=>$USER->GetID()),array("SELECT"=>array("UF_*")));

              if ($arUser = $rsUser->Fetch()) {
                $ID_PICTURE = $arUser["PERSONAL_PHOTO"];
                $URL = CFile::GetPath($ID_PICTURE);
            } ?>

            <img src="<?= !empty($URL) ? $URL : 'https://via.placeholder.com/50.png' ?>" width="40" height="40" alt="Пользователь">
          </div>
          <div class="user-menu__logged">
            <p><?= $USER->GetFirstName(); ?></p>
            <a href="/?logout=yes">Выйти</a>
          </div>
        </nav>

      <? else : ?>
        <nav class="user-menu">
            <ul class="user-menu__list">
              <li class="user-menu__item">
				  <a href="/local/auth/registration.php">Регистрация</a>
              </li>
              <li class="user-menu__item">
				  <a href="/local/auth/index.php">Вход</a>
              </li>
            </ul>
        </nav>
      <? endif ?>



    </div>
  </header>

  <main <?= CSite::InDir('/index.php') ? 'class="container"' : '' ?>>
    <? if ($APPLICATION->GetCurPage(false) !== '/'): ?>  
      <nav class="nav">

        <?$APPLICATION->IncludeComponent(
	"bitrix:menu", 
	"yeti-menuhi", 
	array(
		"ROOT_MENU_TYPE" => "top",
		"MAX_LEVEL" => "1",
		"CHILD_MENU_TYPE" => "top",
		"USE_EXT" => "N",
		"DELAY" => "N",
		"ALLOW_MULTI_SELECT" => "N",
		"MENU_CACHE_TYPE" => "N",
		"MENU_CACHE_TIME" => "3600",
		"MENU_CACHE_USE_GROUPS" => "Y",
		"MENU_CACHE_GET_VARS" => array(
		),
		"COMPONENT_TEMPLATE" => "yeti-menuhi"
	),
	false
);
        ?>
      </nav>
  <? endif; ?>
