<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/local/vendor/autoload.php");
CModule::IncludeModule('iblock');

$IBLOCK_ID = $GLOBALS['stuff_iblock'];  //ID нужного информационного блока
$arFilter = array("IBLOCK_ID"=>$IBLOCK_ID);
$res = CIBlockElement::GetList(array("ID"=>"ASC"), $arFilter, false, false, array("ID", "NAME", "IBLOCK_ID" ,"PROPERTY_FINISH_DATE", "PROPERTY_WINNER", "PROPERTY_HISTORY_PLAYER_ID"));

$properties = CIBlockProperty::GetList(array("sort"=>"asc", "name"=>"asc"), array("ACTIVE"=>"Y", "IBLOCK_ID"=>$IBLOCK_ID, "CODE"=>"%WINNER"));
while ($prop_fields = $properties->GetNext()) {
    $winner_prop_id = $prop_fields["ID"];
}


while ($arElement = $res->GetNext()) {
    $current_date = date($DB->DateFormatToPHP(CSite::GetDateFormat("SHORT")), time());
    $item_date = $arElement['PROPERTY_FINISH_DATE_VALUE'];
    $winner = $arElement['PROPERTY_WINNER_VALUE'];
    $players = $arElement['PROPERTY_HISTORY_PLAYER_ID_VALUE'];

    $result = $DB->CompareDates($item_date, $current_date);

    if ($result < 1 && $winner === "0" && $players && $arElement['ID'] !== $id) {
        $element_id = $arElement['ID'];
        $lot_name = $arElement['NAME'];

        $rsUser = CUser::GetByID($players);
        $arUser = $rsUser->Fetch();
        $winner_email = $arUser['EMAIL'];
        $winner_name = $arUser['NAME'];


        CIBlockElement::SetPropertyValuesEx(
            $element_id,
            false,
            array(
                $winner_prop_id => $winner_email
            )
        );

        $id = $arElement['ID'];

        // Сообщения электронной почты отправляются по протоколу SMTP. Поэтому нам понадобятся данные для доступа к SMTP-серверу. Указываем его адрес и логин с паролем.
        $transport = new Swift_SmtpTransport("smtp.sendgrid.net", 25);
        $transport->setUsername("apikey");
        $transport->setPassword("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");

        // Создадим главный объект библиотеки SwiftMailer, ответственный за отправку сообщений. Передадим туда созданный объект с SMTP-сервером.
        $mailer = new Swift_Mailer($transport);

        // Чтобы иметь максимально подробную информацию о процессе отправки сообщений мы попросим SwiftMailer журналировать все происходящее внутри массива.
        $logger = new Swift_Plugins_Loggers_ArrayLogger();
        $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));


        // Установим параметры сообщения: тема, отправитель и список его получателей
        $message = new Swift_Message();
        $message->setSubject("Уведомление от сервиса «Yeti Cave»");
        $message->setFrom(['keks@phpdemo.ru' => 'YetiCave']);

        $message->setTo($winner_email);
        $msg_content = "Уважаемый, $winner_name! Вы выйграли лот $lot_name ";
        $message->setBody($msg_content, 'text/html');

        $mailer->send($message);
    }
}
